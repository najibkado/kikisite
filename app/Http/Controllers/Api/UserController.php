<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Profile;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //check for people you may know for user
        $Loginuser = auth()->user();
        $not_friends = User::where('id', '!=', $Loginuser->id);
        if($Loginuser->following->count()){
            $not_friends->whereNotIn('id', $Loginuser->following->modelKeys());
        }
        $not_friends = $not_friends->with('profile')->get()->random(10);

        if(count($not_friends) < 1){
            return response()->json(['success' => false, 'message' => 'No User to follow'], 401);    
        }
        return response()->json(['success' => true, 'user' => $not_friends]);
    }


        public function checkUsername($username){
            $users = User::where('username', '=', $username)->first();
            
            if($users && $users->id != auth()->user()->id){
            return response()->json(['success' => false, 'message' => "Username has been taken"], 401);    
            }
            return response()->json(['success' => true, 'message' => 'username is available'], 200);
        }


    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function search($user)
    {
        $users = User::where('username', 'like', "%{$user}%")
        ->with('profile')
        ->latest()->get();

        if(count($users) < 1){
            return response()->json(['success' => false, 'message' => 'No User Found'], 401);    
        }
        return response()->json(['success' => true, 'user' => $users]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
