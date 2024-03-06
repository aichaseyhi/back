<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $boutiques = Store::all();
        return response()->json($boutiques); 
    }   
    public function store(Request $request)
    {
        $boutiques = new Store([
            'name' => $request->input('name'),
        ]);
        $boutiques->save();
        return response()->json('Store created!');
    }
    public function show($id)
    {
        $contact = Store::find($id);
        return response()->json($contact);
    }
    public function update(Request $request, $id)
    {
       $boutiques = Store::find($id);
       $boutiques->update($request->all());
       return response()->json('Store updated');
    }
    public function destroy($id)
    {
        $boutiques = Store::find($id);
        $boutiques->delete();
        return response()->json('Store deleted!');
    }
}
