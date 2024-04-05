<?php

namespace App\Http\Controllers\FrontOffice\Provider;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Echantillon;
use App\Models\Product;
use Illuminate\Http\Response;

class ProviderController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:provider-extern');

    }
    
    public function getProviderProducts()
    {
        $products = Product::where("provider_id", "=", auth()->user()->id)->get();
        return response()->json([
            "message" => "all Provider products ",
            "Products" => $products,
            "satus" => 200,
        ]);
    } 

    public function updateEchantillon(Request $request, $id)
    {
        $echantillon = Echantillon::find($id);
    
        if (!$echantillon) {
            return response()->json([
                'message' => 'Echantillon not found',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }
    
        $echantillon->status = $request->status;
        $echantillon->save();
    
        return response()->json([
            'message' => "Echantillon status updated successfully",
            "status" => Response::HTTP_OK
        ]);
    }
}
