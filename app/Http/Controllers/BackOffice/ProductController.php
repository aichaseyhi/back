<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\ProductResource;
use App\Models\Color;
use App\Models\Size;
use App\Models\SubCategory;
use Illuminate\Support\Str; 

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin');

    }
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'message' => 'List Products !',
            "status" => Response::HTTP_CREATED,
            "data" =>  ProductResource::collection($products)
        ]);

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
           // 'reference' => 'required|string|unique:products,reference',
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
        $product->brand = $request->brand;
        $product->category = $request->category;
        $product->status = $request->status;
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
    
            // Créer une nouvelle image associée au produit
            $productImage = new Image();
            $productImage->product_id = $product->id;
            $productImage->path = env('APP_URL') . '/storage/products/' . $imageName;
            $productImage->save();
        }


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

    public function searchProduct(Request $request)
    {

        $search = $request->has('search') ? $request->input('search') : "";
        $category = $request->has('category') ? $request->input('category') : "";

       
        $products = Product::where('category', 'like', '%' . $category . '%')
            ->where(function ($q) use ($search) {

                $q->Where('name', 'LIKE', "%{$search}%");
                   
            })
            ->get();


        return response()->json($products);
    }
   
    public function filterProduct(Request $request)
{
    // Récupération du paramètre de catégorie
    $category = $request->input('category');

    if (!$category) {
        return response()->json(['error' => 'Le paramètre de catégorie est obligatoire.'], 400);
    }

    // Recherche des produits en fonction de la catégorie
    $products = Product::where('category', $category)->get();

    return response()->json($products);
}
}
