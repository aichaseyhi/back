<?php

namespace App\Http\Controllers\FrontOffice\Instagrammer;

use App\Jobs\MessageJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ProductResource;
use App\Models\Echantillon;
use App\Models\Message;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class InstagrammerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:provider-intern');

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
            'acountLink'=> 'nullable|string',
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
        $user->acountLink = $request->acountLink;
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
    public function getInstagrammerProducts()
    {
        $products = Product::where("instagrammer_id", "=", auth()->user()->id)->get();
        return response()->json([
            'message' => 'all Instagrammer products',
            "status" => Response::HTTP_OK,
            "data" =>  ProductResource::collection($products)
        ]);
       
    } 

    public function getProviderProducts()
{
    $products = Product::whereNotNull('provider_id')->get();
    return response()->json([
        'message' => 'All provider products',
        "status" => Response::HTTP_OK,
        "data" =>  ProductResource::collection($products)
    ]);
}

public function filterProducts(Request $request)
{
    // Récupération du paramètre de catégorie
    $category = $request->input('category');


    if (!$category) {
        return response()->json(['error' => 'Le paramètre de catégorie est obligatoire.'], 400);
    }

    // Recherche des partenaires en fonction de la catégorie
    $products = Product::where('category', $category)->get();

    return response()->json($products);
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
        if( $product->status == "INSTOCK"){
           // $product->quantity -= $request->quantity; 
            $product->save();
            $store = new Store();
            $store->quantity = $product->quantity;
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
                'message' => 'Product quantity is insufficient',
                'status' => Response::HTTP_BAD_REQUEST
            ]);
        }
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
        dispatch(new MessageJob($messages));
        return response()->json([
            'message' => 'Message created!',
            "status" => Response::HTTP_CREATED,
            "data" => new MessageResource($messages)
        ]);
    }
    
}
