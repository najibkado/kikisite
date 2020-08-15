<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\User as UserResource;
use Intervention\Image\Facades\Image;
use File;
use Validator;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
       
        $user = User::where('id', $id)->with('profile')->first();
        
        if(!$user){
            return response()->json(['success' => false, 'message' => 'User not found'], 401);    
        }
        
            return response()->json(['success' => true, 'user_details' => $user]);
            //return new UserResource($user);
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
    public function update(Request $request)
    {
        $user = auth()->user();

        if (request('image')) {
            $validator = Validator::make($request->all(), [
                'fullname' => 'required',
                'username' => 'required',
                'description' => '',
                'url' => '',
                'location' => '',
                'latitude' => '',
                'longitude' => '',
                'image' => 'image|required',
                ]);
        }else{
        $validator= Validator::make($request->all(), [
                'fullname' => 'required',
                'username' => 'required',
                'description' => '',
                'url' => '',
                'location' => '',
                'latitude' => '',
                'longitude' => '',
                'image' => ''
        ]);

        }

        if ($validator->fails()) { 
            return response()->json(['success' => false, 'error'=>$validator->errors()], 401);            
        }
        
        if (request('image')) {
            //get filename with extension
        $filenamewithextension = $request->file('image')->getClientOriginalName();
  
        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
  
        //get file extension
        $extension = $request->file('image')->getClientOriginalExtension();
  
        //filename to store
        $filenametostore = $user->username.'dp.'.$extension;
 
        //small thumbnail name
        $smallthumbnail = $user->username.'_dp_small_.'.$extension;
 

            //Upload File
            $thumbPath = request('image')->storeAs('profile_images/thumbnail', $smallthumbnail);
            $smallthumbnailpath = public_path('storage/profile_images/thumbnail/'.$smallthumbnail);
            $this->createThumbnail($smallthumbnailpath, 150, 93);

            $imagePath = request('image')->store('profile_images', 'public');
            $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000, 1000);
            $image->save();
            
            if (File::exists(public_path("storage/{$user->profile->profile_image}"))) {
                File::delete(public_path("storage/{$user->profile->profile_image}"));
            }
           
            if (File::exists(public_path("storage/{$user->profile->profile_image_thumbnail}"))) {
                File::delete(public_path("storage/{$user->profile->profile_image_thumbnail}"));
            }

            $imageArray = ['profile_image' => $imagePath,
            'profile_image_thumbnail' => $thumbPath];
            
        }

        $validatedData = $request->all();

        
        $user->update([
            'username' => request('username'),
            'fullname' => request('fullname')
        ]);
        $data = [
            'title' => $request->fullname,
            'description' => $request->description,
            'url' => $request->url,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];
        $update = $user->profile->update(array_merge(
            $data,
            $imageArray ?? []
        ));

        if($update){
        return response()->json(['success' => true, 'message' => 'Profile updated successfully', 'user' => $user], 200);
        }else{
            return response()->json(['success' => false, 'error'=> 'Unkown Error Occure'], 401);
        }

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


      /**
 * Create a thumbnail of specified size
 *
 * @param string $path path of thumbnail
 * @param int $width
 * @param int $height
 */
public function createThumbnail($path, $width, $height)
{
    $img = Image::make($path)->resize($width, $height, function ($constraint) {
        $constraint->aspectRatio();
    });
    $img->save($path);
}
}
