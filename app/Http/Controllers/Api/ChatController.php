<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Chat;
use Validator;
use File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ChatCollection;
use App\Profile;
use Intervention\Image\Facades\Image;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id;

        $send = auth()->user()->talkedTo()->get();
        

    
        $chat = auth()->user()->allmyChats();
        if($chat == null){
            return response()->json(['success' => false, 'message' => 'No chat history'], 401);    
        }
        return  new ChatCollection($chat);
        
        
        //return response()->json(['success' => true, 'chats' => $chat]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request('image')) {
            $validator = Validator::make($request->all(), [
                'message' => '',
                'sender_id' => 'required',
                'reciever_id' => 'required',
                'type' => 'required',
                'unread' => 'required',
                'isLiked' => '',
                'location' => '',
                'latitude' => '',
                'longitude' => '',
                'image' => 'image|required',
                ]);
        }else{
            $validator = Validator::make($request->all(), [
                'message' => 'required',
                'sender_id' => 'required',
                'reciever_id' => 'required',
                'type' => 'required',
                'unread' => 'required',
                'isLiked' => 'required',
                'location' => '',
                'latitude' => '',
                'longitude' => '',
                'image' => '',
                ]);
        }

        if ($validator->fails()) { 
            return response()->json(['success' => false, 'error'=>$validator->errors()], 401);            
        }

        $validatedData = $request->all();
        
            if (request('image')) {
                $imagePath = request('image')->store('chats', 'public');
                $imageArray = ['image' => $imagePath];
            }

           
            $user = auth()->user();
            $chat = $user->talkedTo()->create(array_merge(
                $validatedData,
                $imageArray ?? []
            ));


            return response()->json(['success' => true, 'message'=>'sent sucessfully'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_id = auth()->user()->id;
        $messages = Chat::where(function ($query) use ($id){
            $query->where('sender_id', '=', auth()->user()->id)->where('reciever_id', '=', $id);})->orWhere(function ($query) use ($id){
                $query->where('sender_id', '=', $id)->where('reciever_id', '=', auth()->user()->id);
            })->latest()->get();

            if(count($messages) < 1){
                return response()->json(['success' => false, 'message' => 'No chat history'], 401);    
            }
    
            return response()->json(['success' => true, 'chats' => $messages]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
