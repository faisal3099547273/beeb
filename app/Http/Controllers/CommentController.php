<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Comment;
class CommentController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }
    public function  store(Request $request){
        $data = $request->all();
        $validator = Validator::make($request->all(), [
          "product_id" => "required|integer",
          "user_id" => "required|integer",
          "comment" => "required|string",
        ]);
        if($validator->fails()){
          return redirect()->back()->withErrors($validator)->withInput();
        }
       $comment = Comment::Create([
        'product_id' => $data['product_id'],
        'user_id' => $data['user_id'],
        'comment' => $data['comment'],
        'created_at' => date("Y-m-d H:i:s"),
       ]);
       return response()->json([
        'status'=>200,
        'message'=>'Commment Added Successfully!',
    ]);

    }
}
