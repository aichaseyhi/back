<?php

namespace App\Http\Controllers\BackOffice;
use App\Http\Controllers\Controller;
use App\Models\Order;
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
            'secondName' => 'required|string',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^[0-9]{8}$/'],
            'city' => 'required|string',
            'post_code' => ['required', 'regex:/^[0-9]{4}$/'],
            'cardNumber' => 'required|numeric',
            'securityCode' => ['nullable', 'regex:/^[0-9]{4}$/'],
            'CVV' => 'required|numeric',
            'quantity' => 'required|numeric',
            'totalPrice' => 'required|numeric',
            'status' => ['nullable', 'in:ACCEPTED,REFUSED,PENDING,CANCEL'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        $orders = new Order();
        $orders->firstName  = $request->firstName;
        $orders->secondName  = $request->secondName;
        $orders->email  = $request->email;
        $orders->phone  = $request->phone;
        $orders->city  = $request->city;
        $orders->post_code  = $request->post_code;
        $orders->cardNumber  = $request->cardNumber;
        $orders->securityCode  = $request->securityCode;
        $orders->CVV  = $request->CVV;
        $orders->quantity  = $request->quantity;
        $orders->totalPrice  = $request->totalPrice;
        $orders->status  = $request->status;
       
        $orders->save();
        return response()->json('Order created!');
    }
    public function show($id)
    {
        $orders = Order::find($id);
        return response()->json($orders);
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
}
