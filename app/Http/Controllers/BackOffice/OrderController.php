<?php

namespace App\Http\Controllers\BackOffice;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|superadmin']);
    }
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders); 
    }   
    public function store(Request $request)
    {
        $rules = [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^[0-9]{8}$/'],
            'city' => 'required|string',
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
            //'totalPrice' => 'required|numeric',
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
        $totalPrice = $request->quantity * $product->priceSale;

        // Calculer la TVA comme 7% du prix du produit
        $TVA = $product->priceSale * 0.07;
        $totalPrice += $TVA;

        // Ajouter le shippingCost de 6 au totalPrice
    $totalPrice += 6;
       
        $orders = new Order();
        $orders->firstName  = $request->firstName;
        $orders->lastName  = $request->lastName;
        $orders->email  = $request->email;
        $orders->phone  = $request->phone;
        $orders->city  = $request->city;
        $orders->post_code  = $request->post_code;
        $orders->cardNumber  = $request->cardNumber;
        $orders->securityCode  = $request->securityCode;
        $orders->CVV  = $request->CVV;
        $orders->quantity  = $request->quantity;
        $orders->shippingCost  = 6;
        $orders->TVA  = $TVA;
        $orders->payment  = $request->payment;
        $orders->totalPrice  = $totalPrice;
        //$orders->status  = $request->status;
        $orders->product_id  = $request->product_id;
        $orders->save();

         // Mise à jour de la quantité du produit
         $product->quantity -= $request->quantity;
         $product->save();
        return response()->json('Order created!');
    }
    public function show($id)
    {
        $orders = Order::find($id);
        return response()->json($orders);
    }
    public function updateOrder(Request $request, $id)
    {
        // Trouver la commande à mettre à jour
        $order = Order::findOrFail($id);

        // Vérifier si la commande existe
        if (!$order) {
            return response()->json(['message' => 'order non trouvée'], 404);
        }

        // Mettre à jour les champs de la commande
        $order->user_id = $request->input('user_id', $order->user_id);
        $order->price = $request->input('price', $order->price);
        $order->status = $request->input('status', $order->status);
        $order->save();

        // Retourner la commande mise à jour
        return response()->json($order);
    }
    public function update(Request $request, $id)
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
