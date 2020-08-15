<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 
use App\User;
use Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use File;
use Intervention\Image\Facades\Image;
class AuthContorller extends Controller
{

    public $successStatus = 200;

    public function register(request $request){

        $validator= Validator::make($request->all(), [
            'fullname' => 'required',
            'username' => 'required|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['success' => false, 'error'=>$validator->errors()], 401);            
        }
        $validatedData = $request->all();
        $validatedData['s_password'] = $request->password;
        $validatedData['password'] = bcrypt($request->password);
        $user = User::create($validatedData);
        $accessToken = $user->createToken('authToken')->accessToken;
        $user->profile;
        return response()->json(['success' => true, 'user' => $user, 'accessToken' => $accessToken], $this->successStatus);
    }


    public function login(request $request){

        $logindData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(!auth()->attempt($logindData)){
          return response()->json(['success' => false, 'message' => 'Invalid login details'], 401); 
        }

        $user = auth()->user();
        $accessToken = $user->createToken('authToken')->accessToken;
        $user->profile;

        return response()->json(['success' => true, 'user' => $user, 'accessToken' => $accessToken], $this->successStatus);

    }



    public function updatePhone(Request $request){

        $validator= Validator::make($request->all(), [
            'phone_number' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['success' => false, 'error'=>$validator->errors()], 401);            
        }

        $validatedData = $request->all();
        $user = auth()->user();
        $checkPhone = User::where('phone_number', '=', $request->phone_number)->first();
        if($checkPhone === null || $checkPhone->id == $user->id){
            //no user with that phone number or the number belongs to the user
        $user->update($validatedData);
        $user->profile;
        return response()->json(['success' => true, 'message' => 'information has been updated successfully', 'user' => $user], $this->successStatus);
        }else{
            return response()->json(['success' => false, 'error'=> 'The Phone number has already been taken'], 401);
        }
    }


    public function completeRegistration(Request $request){
        $user = auth()->user();
        $validator= Validator::make($request->all(), [
            'fullname' => 'required',
            'location' => '',
            'latitude' => '',
            'longitude' => '',
            'image' => 'required|image'
        ]);

        if ($validator->fails()) { 
            return response()->json(['success' => false, 'error'=>$validator->errors()], 401);            
        }

        if($request->hasFile('image')) {
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
            $imageArray = ['profile_image' => $imagePath,
                           'profile_image_thumbnail' => $thumbPath];


            $image_path = public_path("storage/{$user->profile->profile_image}");
            $thumbnail_image_path = public_path("storage/{$user->profile->profile_image_thumbnail}");
            
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
            if (File::exists($thumbnail_image_path)) {
                File::delete($thumbnail_image_path);
            }

        }

        $validatedData = $request->all();
        

        $user->update([
            'fullname' => $request->fullname,
            ]);

            


        $update = $user->profile->update(array_merge([
            'title' => $request->fullname], 
            $imageArray ?? []   ));

        if($update){
        //$user->update($validatedData);
        $user->profile;
        return response()->json(['success' => true, 'message' => 'information has been updated successfully', 'user' => $user], $this->successStatus);
        }else{

            


            return response()->json(['success' => false, 'error'=> 'Unkown Error Occure'], 401);
        }
    }



    public function logout(request $request){
        if(!User::checkToken($request)){
            return response()->json(['success' => false, 'message' => 'Token is required'], 422);
        }
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
