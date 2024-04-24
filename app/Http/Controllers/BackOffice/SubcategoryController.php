<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::all();
        return response()->json($subcategories); 
    }   
    public function store(Request $request)
    {
        $subcategories = new SubCategory([
            'name' => $request->input('name'),
            'matiere_id' => $request->input('matiere_id'),
        ]);
        $subcategories->save();
        return response()->json('SubCategory created!');
    }
    public function show($id)
    {
        $subcategories = SubCategory::find($id);
        return response()->json($subcategories);
    }
    public function update(Request $request, $id)
    {
       $subcategories = SubCategory::find($id);
       $subcategories->update($request->all());
       return response()->json('SubCategory updated');
    }
    public function destroy($id)
    {
        $subcategories = SubCategory::find($id);
        $subcategories->delete();
        return response()->json('SubCategory deleted!');
    }
}
