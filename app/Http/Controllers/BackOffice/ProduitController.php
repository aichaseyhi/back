<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


class ProduitController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:admin');

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
            'priceFav' => 'required|numeric',
            'priceMax' => 'required|numeric',
           // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        
        $produit = new Produit();
        $produit->name = $request->name;
        $produit->description = $request->description;
        $produit->quantity = $request->quantity;
        $produit->priceSale = $request->priceSale;
        $produit->priceFav = $request->priceFav;
        $produit->priceMax = $request->priceMax;
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
