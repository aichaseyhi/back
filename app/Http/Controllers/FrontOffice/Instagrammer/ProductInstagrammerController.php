<?php

namespace App\Http\Controllers\FrontOffice\Instagrammer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Color;
use App\Models\Echantillon;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Store;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; 


class ProductInstagrammerController extends Controller
{

      public function __construct()
    {
        $this->middleware('role:provider-intern');

    }

    // public function index()
    // {
    //     $products = Product::all();
    //     return response()->json($products); 
    // }  
   
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'required',
            'quantity' => 'required|numeric',
            'priceSale' => 'required|numeric',
            'brand' => 'required|string',
            'category' => ['required', 'in:CLOTHING,ACCESSOIRIES,HOME,SPORT,BEAUTY,ELECTRONICS,PETS'],
            'status' => ['required', 'in:INSTOCK,OUTSTOCK'],
            
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
        $product->brand = $request->brand;
        $product->category = $request->category;
        $product->status = $request->status;
        $product->reference = Str::random(8);

        $product->save();

        $subcategorie = SubCategory::where('type',$request->category)->first();
        $product->subcategories()->attach($subcategorie);

        foreach ($request->colors as $color_id) {
            $color = Color::find($color_id);
            $product->colors()->attach($color);
        }
        foreach ($request->sizes as $size_id) {
            $size = Size::find($size_id);
            $product->sizes()->attach($size);
        }
       
           // Enregistrer les images
           foreach ($request->file('photo') as $image) {
            $imagePath = $image->store('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
             $image->move(public_path('storage/products'), $imageName);
    
            // Créer une nouvelle image associée au produit
            $productImage = new Image();
            $productImage->product_id = $product->id;
            $productImage->path = 'http://localhost:8000/storage/products/' . $imageName;
            $productImage->save();
        }
      
            $store = new Store();
            $store->quantity = $product->quantity;
            $store->price =$product->priceSale;
            $store->product_id = $product->id;
            $store->instagrammer_id = $product->instagrammer_id;
            $store->save();
        
            return response()->json([
                'message' => 'Product created!',
                "status" => Response::HTTP_CREATED,
                "data" => new ProductResource($product)
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
