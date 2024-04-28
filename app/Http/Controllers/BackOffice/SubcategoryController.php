<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::all();
        return response()->json($subcategories); 
    }   
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'type' => 'required|string',
           
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->errors(),
                "status" => 400
            ]);
        }
        $subcategories = new SubCategory();
        $subcategories->name  = $request->name;
        $subcategories->type  = $request->type;
       
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
