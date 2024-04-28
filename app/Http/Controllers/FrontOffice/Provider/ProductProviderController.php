<?php

namespace App\Http\Controllers\FrontOffice\Provider;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Echantillon;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Support\Str; 
use App\Models\SubCategory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:provider-extern');

    }
    public function index()
    {
        $produits = Product::all();
        return response()->json($produits); 
    }   
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'required',
            'quantity' => 'required|numeric',
            'priceSale' => 'required|numeric',
            'priceFav' => 'required|numeric',
            'priceMax' => 'required|numeric',
           // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
           'brand' => 'required|string',
            'category' => ['required', 'in:CLOTHING,ACCESSOIRIES,HOME,SPORT,BEAUTY,ELECTRONICS,PETS'],
            'status' => ['required', 'in:INSTOCK,OUTSTOCK'],
            'echantillon' => ['required', 'in:FREE,PAID,REFUNDED'],
            
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
        $product->priceFav = $request->priceFav;
        $product->priceMax = $request->priceMax;
        $product->category = $request->category;
        $product->status = $request->status;
        $product->provider_id =Auth::user()->id;
        $product->brand = $request->brand;
        $product->echantillon = $request->echantillon;
        $product->reference = Str::random(8);

        $product->save();
        foreach ($request->colors as $color_id) {
            $color = Color::find($color_id);
            $product->colors()->attach($color);
        }
        foreach ($request->sizes as $size_id) {
            $size = Size::find($size_id);
            $product->sizes()->attach($size);
        }
        foreach ($request->subcategories as $subcategory_id) {
            $subcategory = SubCategory::find($subcategory_id);
            $product->subcategories()->attach($subcategory);
        }
           // Enregistrer les images
           foreach ($request->file('photo') as $image) {
            $imagePath = $image->store('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
             $image->move(public_path('storage/products'), $imageName);
    
            // Créer une nouvelle image associée au product
            $productImage = new Image();
            $productImage->product_id = $product->id;
            $productImage->path = env('APP_URL') . '/storage/products/' . $imageName;
            $productImage->save();
        }


        return response()->json([
            'message' => 'product created!',
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
