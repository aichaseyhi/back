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
            'name' => 'required|string',
            'type' => 'required|string',
           
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        $orders = new Order();
        $orders->name  = $request->name;
        $orders->type  = $request->type;
       
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
