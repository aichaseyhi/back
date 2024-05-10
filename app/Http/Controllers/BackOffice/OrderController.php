<?php

namespace App\Http\Controllers\BackOffice;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; 


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|superadmin']);
    }
    public function index()
    {
        $orders = Order::all();
        return response()->json([
            'message' => 'List Orders !',
            "status" => Response::HTTP_OK,
            "data" =>  OrderResource::collection($orders)
        ]);
    }   
    public function store(Request $request)
    {
        $rules = [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^[0-9]{8}$/'],
            'city' => 'required|string',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'post_code' => ['required', 'regex:/^[0-9]{4}$/'],
            'cardNumber' => 'nullable|numeric',
            'securityCode' => ['nullable', 'regex:/^[0-9]{4}$/'],
            'CVV' => 'nullable|numeric',
            'quantity' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $remainingQuantity = Product::where('id', $request->input('product_id'))
                        ->value('quantity');
                    if ($value > $remainingQuantity) {
                        $fail($attribute . ' must be less than ' . $remainingQuantity);
                    }
                },
            ],
            'payment' => ['required', 'in:Credit,CashOnDelivery'],
            //'status' => ['required', 'in:SUCCESS,REFUSED,PENDING,CANCEL,INPROGRESS'],
            "product_id" => "required|exists:products,id",
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        $product = Product::findOrFail($request->product_id);
        $totalProduct = $request->quantity * $product->priceSale;

        // Calculer la TVA comme 7% du prix du produit
        $TVA = $product->priceSale * 0.07;
        $totalPrice = $totalProduct+$TVA;

        // Ajouter le shippingCost de 6 au totalPrice
        $totalPrice += 6;
       
        $orders = new Order();
        $orders->firstName  = $request->firstName;
        $orders->lastName  = $request->lastName;
        $orders->email  = $request->email;
        $orders->phone  = $request->phone;
        $orders->reference = str::random(8);
        $orders->city  = $request->city;
        $orders->color  = $request->color;
        $orders->size  = $request->size;
        $orders->post_code  = $request->post_code;
        $orders->cardNumber  = $request->cardNumber;
        $orders->securityCode  = $request->securityCode;
        $orders->CVV  = $request->CVV;
        $orders->quantity  = $request->quantity;
        $orders->shippingCost  = 6;
        $orders->TVA = $TVA;
        $orders->payment  = $request->payment;
        $orders->totalProduct  = $totalProduct;
        $orders->totalPrice  = $totalPrice;
        //$orders->status  = $request->status;
        $orders->product_id  = $request->product_id;
        $orders->save();

         // Mise à jour de la quantité du produit
         $product->quantity -= $request->quantity;
         $product->save();
         return response()->json([
            'message' => 'Order created!',
            "status" => Response::HTTP_CREATED,
            "data" => new OrderResource($orders)
        ]);
    }
    public function show($id)
    {
        $orders = Order::find($id);
        return response()->json($orders);
    }
    public function update(Request $request, $id)
    {
        $rules = [
            'firstName' => 'string',
            'lastName' => 'string',
            'email' => 'email',
            'phone' => ['regex:/^[0-9]{8}$/'],
            'city' => 'string',
            'post_code' => ['regex:/^[0-9]{4}$/'],
            'cardNumber' => 'nullable|numeric',
            'securityCode' => ['nullable', 'regex:/^[0-9]{4}$/'],
            'CVV' => 'nullable|numeric',
            'quantity' => [
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $remainingQuantity = Product::where('id', $request->input('product_id'))
                        ->value('quantity');
                    if ($value > $remainingQuantity) {
                        $fail($attribute . ' must be less than ' . $remainingQuantity);
                    }
                },
            ],
            'payment' => ['in:Credit,CashOnDelivery'],
            "product_id" => "exists:products,id",
            //'status' => ['in:SUCCESS,REFUSED,PENDING,CANCEL,INPROGRESS'],
        ];
           $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        // Trouver la commande à mettre à jour
        $order = Order::findOrFail($id);

        // Vérifier si la commande existe
        if (!$order) {
            return response()->json(['message' => 'order non trouvée'], 404);
        }
        $product = Product::findOrFail($request->product_id);
        $totalPrice = $request->quantity * $product->priceSale;

        // Calculer la TVA comme 7% du prix du produit
        $TVA = $product->priceSale * 0.07;
        $totalPrice += $TVA;

        // Ajouter le shippingCost de 6 au totalPrice
        $totalPrice += 6;
        $quantityDifference = $request->quantity - $order->quantity;

       
        $order->firstName  = $request->firstName;
        $order->lastName  = $request->lastName;
        $order->email  = $request->email;
        $order->phone  = $request->phone;
        $order->city  = $request->city;
        $order->post_code  = $request->post_code;
        $order->cardNumber  = $request->cardNumber;
        $order->securityCode  = $request->securityCode;
        $order->CVV  = $request->CVV;
        $order->quantity  = $request->quantity;
        $order->shippingCost  = 6;
        $order->TVA = $TVA;
        $order->payment  = $request->payment;
        $order->product_id  = $request->product_id;
        $order->totalPrice  = $totalPrice;

        $order->save();

         // Mise à jour de la quantité du produit
         $product->quantity += $quantityDifference;
         $product->save();
        // Mettre à jour les champs de la commande
        $order->save();

        // Retourner la commande mise à jour
        return response()->json($order);
    }
    public function updateOrder(Request $request, $id)
    {
       $orders = Order::find($id);
       $orders->update($request->all());
       return response()->json('Order updated');
    }
    public function destroy($id)
    {
        $orders = Order::find($id);
        $orders->delete();
        return response()->json('Order deleted!');
    }
    
    //Update Status
    public function updateOrderStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:SUCCESS,REFUSED,PENDING,CANCEL,INPROGRESS',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = $request->input('status');
        $order->save();

        return response()->json(['message' => 'Order status updated successfully'], 200);
    }
       //Filtrer commands selon leurs status
       public function filterOrders(Request $request)
       {
           // Récupération du paramètre de catégorie
           $status = $request->input('status');
   
   
           if (!$status) {
               return response()->json(['error' => 'Le paramètre de status est obligatoire.'], 400);
           }
   
           $orders = Order::where('status', $status)->get();
   
           return response()->json($orders);
       }
    
}
