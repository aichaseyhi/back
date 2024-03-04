<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::all();
        return response()->json($produits); 
    }   
    public function store(Request $request)
    {
        $produits = new Produit([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price'),
            'image' => $request->input('image'),

        ]);
        $produits->save();
        return response()->json('Produit created!');
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
