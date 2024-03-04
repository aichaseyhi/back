<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use Illuminate\Http\Request;

class BoutiqueController extends Controller
{
    public function index()
    {
        $boutiques = Boutique::all();
        return response()->json($boutiques); 
    }   
    public function store(Request $request)
    {
        $boutiques = new Boutique([
            'name' => $request->input('name'),
        ]);
        $boutiques->save();
        return response()->json('Boutique created!');
    }
    public function show($id)
    {
        $contact = Boutique::find($id);
        return response()->json($contact);
    }
    public function update(Request $request, $id)
    {
       $boutiques = Boutique::find($id);
       $boutiques->update($request->all());
       return response()->json('Boutique updated');
    }
    public function destroy($id)
    {
        $boutiques = Boutique::find($id);
        $boutiques->delete();
        return response()->json('Boutique deleted!');
    }
}
