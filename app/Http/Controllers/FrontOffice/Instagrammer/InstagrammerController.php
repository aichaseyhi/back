<?php

namespace App\Http\Controllers\FrontOffice\Instagrammer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Echantillon;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InstagrammerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:provider-intern');

    }
    public function getInstagrammerProducts()
    {
        $products = Product::where("instagrammer_id", "=", auth()->user()->id)->get();
        return response()->json([
            "message" => "all Instagrammer products ",
            "Products" => $products,
            "satus" => 200,
        ]);
    } 

    public function addEchantillon(Request $request)
    {
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }
        $echantillon = new Echantillon();
        $echantillon->payment = $request->payment;
        $echantillon->status = "PENDING";
        $echantillon->product_id = $product->id;
        $echantillon->instagrammer_id = Auth::user()->id;

        $echantillon->save();

        return response()->json([
            'message' => "Successfully ",
            "status" => Response::HTTP_CREATED
        ]);
    }

    public function addProductProvider(Request $request){

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }
        if($product->quantity >= $request->quantity && $product->status == "Available"){
            $product->quantity -= $request->quantity; 
            $product->save();
            $store = new Store();
            $store->quantity = $request->quantity;
            $store->price =$request->price;
            $store->product_id = $product->id;
            $store->instagrammer_id =  Auth::user()->id;
            $store->save();
            return response()->json([
                'message' => "Successfully ",
                "status" => Response::HTTP_CREATED
            ]); 
        } else {
            return response()->json([
                'message' => 'Product quantity is insufficient or status is not "disponible"',
                'status' => Response::HTTP_BAD_REQUEST
            ]);
        }
    }
}
