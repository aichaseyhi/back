<?php

namespace App\Http\Controllers\FrontOffice\Instagrammer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Echantillon;
use App\Models\Image;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ProductInstagrammerController extends Controller
{

      public function __construct()
    {
        $this->middleware('role:provider-intern');

    }

    public function index()
    {
        $products = Product::all();
        return response()->json($products); 
    }  
   
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'required',
            'quantity' => 'required|numeric',
            'priceSale' => 'required|numeric',
            'category' => ['required', 'in:Clothing,Accessoiries,Home,Sport,Beauty,Electronics,Pets'],
            'status' => ['required', 'in:Available,Unavailable'],
            
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->priceSale = $request->priceSale;
        $product->category = $request->category;
        $product->status = $request->status;
        $product->instagrammer_id =Auth::user()->id;

        $product->save();

           // Enregistrer les images
           foreach ($request->file('photo') as $image) {
            $imagePath = $image->store('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
             $image->move(public_path('storage/products'), $imageName);
    
            // Créer une nouvelle image associée au produit
            $productImage = new Image();
            $productImage->product_id = $product->id;
            $productImage->path = env('APP_URL') . '/storage/products/' . $imageName;
            $productImage->save();
        }

       if ($product->status === 'Available'){
            $store = new Store();
            $store->quantity = $product->quantity;
            $store->price =$product->priceSale;
            $store->product_id = $product->id;
            $store->instagrammer_id = $product->instagrammer_id;
            $store->save();
        }

        return response()->json([
            'message' => 'Product created!',
            "status" => Response::HTTP_CREATED
        ]);
    }

    public function show($id)
    {
        $contact = Product::find($id);
        return response()->json($contact);
    }
    public function update(Request $request, $id)
    {
       $products = Product::find($id);
       $products->update($request->all());
       return response()->json('Product updated');
    }
    public function destroy($id)
    {
        $products = Product::find($id);
        $products->delete();
        return response()->json('Product deleted!');
    }
}
