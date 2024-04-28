<?php

namespace App\Http\Controllers\FrontOffice\Provider;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Echantillon;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function updateSelfData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => [
                'required',
                'string',
                Rule::unique('users'),
                'email'
            ],
            'phone' => ['required', 'regex:/^[0-9]{8}$/'],
            'street'=> 'nullable|string',
            'city'=> 'nullable|string',
            'post_code'=> ['nullable', 'regex:/^[0-9]{4}$/'],
            'CIN'=> ['nullable', 'regex:/^[0-9]{8}$/'],
            'TAXNumber'=> 'nullable|regex:/^[0-9]{8}$/',
            'companyName'=> 'nullable|string',
            'companyUnderConstruction'=> 'nullable|boolean',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                "status" => 400
            ]);
        }
    
        $user = User::findOrFail(Auth::user()->id);
    
        if (is_null($user)) {
            return response()->json(
                [
                    'message' => 'utilisateur introuvable',
                    "status" => "404"
                ]
            );
        }
    
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->street = $request->street;
        $user->city = $request->city;
        $user->post_code = $request->post_code;
        $user->CIN = $request->CIN;
        $user->companyName = $request->companyName;
        $user->companyUnderConstruction = $request->companyUnderConstruction;
        if ($request->companyUnderConstruction == false) {
                $user->TAXNumber  = $request->TAXNumber;
            } 
     
    
        $user->save();
    
        return response()->json([
            "message" => "Updated Successfully",
            "status" => 200,
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
    public function sendMessage(Request $request)
    {
        $rules = [
            'message' => 'required|string',
            
           
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        $messages = new Message();
        $messages->message  = $request->message;
        $messages->sender_id  = Auth::user()->id;
       
        $messages->save();
        return response()->json([
            'message' => 'Message created!',
            "status" => Response::HTTP_CREATED,
            "data" => new MessageResource($messages)
        ]);
    }
    
}
