<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    
    protected $appends = ['followers_count', 'following_count', 'is_following', ];
    
   protected $fillable = ['fullname', 'username', 'title','description','url','profile_image', 'profile_image_thumbnail', 'location', 'longitude','latitude'];
    

   public function profileImage()
    {
     return $this->profile_image;
    }


    public function followers()
    {
        return $this->belongsToMany(User::class, 'follower', 'profile_id', 'user_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follower', 'user_id', 'profile_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    
    public function getFollowersCountAttribute(){ return count($this->followers);}

    

    public function getFollowingCountAttribute(){
        return count($this->following);
    }

    public function getIsFollowingAttribute(){
        return (auth()->user()) ? auth()->user()->following->contains($this) : false;
    }

    
    

    
}
