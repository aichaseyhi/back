<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::all();
        return response()->json([
            'message' => 'List messages !',
            "status" => Response::HTTP_CREATED,
            "data" =>  MessageResource::collection($messages)
        ]);
       ; 
    }   
   
    public function show($id)
    {
        $messages = Message::find($id);
        return response()->json($messages);
    }
    public function update(Request $request, $id)
    {
       $messages = Message::find($id);
       $messages->update($request->all());
       return response()->json('Message updated');
    }
    public function destroy($id)
    {
        $messages = Message::find($id);
        $messages->delete();
        return response()->json('Message deleted!');
    }
}
