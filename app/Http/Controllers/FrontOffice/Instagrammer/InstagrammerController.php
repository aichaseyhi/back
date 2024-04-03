<?php

namespace App\Http\Controllers\FrontOffice\Instagrammer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Echantillon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class InstagrammerController extends Controller
{
    
    public function addEchantillon(Request $request)
    {
        //valdiate
        $rules = [
            'payment' => ['required', 'in:Free,Credit,CashOnDelivery'],
            'status' => ['required', 'in:PENDING,SUCCESS,FAILED'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }

        $echantillon = new Echantillon();
        $echantillon->payment = $request->payment;
        $echantillon->status = $request->status;
        $echantillon->product_id = $request->product_id;
        $echantillon->instagrammer_id = $request->instagrammer_id;

        $echantillon->save();

        return response()->json([
            'message' => "successfully registered",
            "status" => Response::HTTP_CREATED
        ]);
    }
}
