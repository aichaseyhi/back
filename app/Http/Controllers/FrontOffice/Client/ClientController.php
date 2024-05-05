<?php

namespace App\Http\Controllers\FrontOffice\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function getProductById($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        return new ProductResource($product);
    }

    public function addOrder(Request $request)
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
        $orders->TVA = $TVA;
        $orders->payment  = $request->payment;
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
    public function updateOrder(Request $request, $id)
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
    public function cancelOrder(Request $request, $id)
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    $order->status = 'CANCEL';
    $order->save();

    return response()->json(['message' => 'Order cancled'], 200);
}

public function confirmOrder(Request $request, $id)
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    $order->status = 'SUCCESS';
    $order->save();

    return response()->json(['message' => 'Order delivered'], 200);
}

    
}
