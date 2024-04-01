<?php

namespace App\Http\Controllers\FrontOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Produit;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductInstagrammerController extends Controller
{

      public function __construct()
    {
        $this->middleware('role:provider_intern');

    }

    public function index()
    {
        $produits = Produit::all();
        return response()->json($produits); 
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
            'instagrammer_id' =>'required'
            
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }

        $produit = new Produit();
        $produit->name = $request->name;
        $produit->description = $request->description;
        $produit->quantity = $request->quantity;
        $produit->priceSale = $request->priceSale;
        $produit->category = $request->category;
        $produit->status = $request->status;
        

        $produit->save();

           // Enregistrer les images
           foreach ($request->file('photo') as $image) {
            $imagePath = $image->store('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
             $image->move(public_path('storage/products'), $imageName);
    
            // Créer une nouvelle image associée au produit
            $productImage = new Image();
            $productImage->produit_id = $produit->id;
            $productImage->path = env('APP_URL') . '/storage/products/' . $imageName;
            $productImage->save();
        }
        if ($produit->status === 'Available'){
            $store = new Store();
            $store->quantity = $produit->quantity;
            $store->product_id = $produit->id;
            $store->intagrammer_id = $request->id;
            $store->save();
        }

        return response()->json([
            'message' => 'Produit created!',
            "status" => Response::HTTP_CREATED
        ]);
    }
    public function show($id)
    {
        $contact = Produit::find($id);
        return response()->json($contact);
    }
    public function update(Request $request, $id)
    {
       $produits = Produit::find($id);
       $produits->update($request->all());
       return response()->json('Produit updated');
    }
    public function destroy($id)
    {
        $produits = Produit::find($id);
        $produits->delete();
        return response()->json('Produit deleted!');
    }
}
