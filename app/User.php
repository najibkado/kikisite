<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Mail;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     

    protected $fillable = [
        'username', 'fullname', 'phone_number', 'email', 's_password', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['profile_pix',];
    

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->profile()->create([
                'title' => $user->username,
            ]);

           // Mail::to($user->email)->send(new NewUserWelcomeMail());
        });
    }

    

    public function following()
    {
       return $this->belongsToMany(Profile::class, 'follower', 'user_id', 'profile_id');
    }

    public function talkedTo()
    {
       return $this->hasMany(Chat::class, 'sender_id');
    }

    public function relatedTo()
    {
       return $this->hasMany(Chat::class, 'reciever_id');
    }

    public function allChats(){
        $chat = DB::select(' SELECT t1.* FROM chats AS t1 
        INNER JOIN ( SELECT LEAST(sender_id, reciever_id) 
        AS sender_id, GREATEST(sender_id, reciever_id)
         AS reciever_id, MAX(id) 
         AS max_id FROM chats 
         GROUP BY LEAST(sender_id, reciever_id), GREATEST(sender_id, reciever_id) ) 
         AS t2 ON LEAST(t1.sender_id, t1.reciever_id) = t2.sender_id 
         AND GREATEST(t1.sender_id, t1.reciever_id) = t2.reciever_id 
         AND t1.id = t2.max_id WHERE t1.sender_id = ? 
         OR t1.reciever_id = ? ', [$this->id, $this->id]);

         
         return $chat;
    }


    public function allmyChats(){
        $chat = DB::table('chats AS t1')
        ->select('t1.*')
        ->Join(DB::raw('( SELECT LEAST(sender_id, reciever_id) 
        AS sender_id, GREATEST(sender_id, reciever_id)
         AS reciever_id, MAX(id) 
         AS max_id FROM chats 
         GROUP BY LEAST(sender_id, reciever_id), GREATEST(sender_id, reciever_id) ) as t2 '), 
         't1.id', '=', 't2.max_id'

         )
         ->where('t1.sender_id', $this->id)
         ->orWhere('t1.reciever_id', $this->id)
        
        ->latest()->get();

            
         return $chat;
    }
    
    public function chats()
    {
       return $this->hasMany(Chat::class)->latest();
    }


    


    public function profile(){
        return $this->hasOne(Profile::class);
    }

    public function profiles(){
        return $this->belongsToMany(Profile::class);
    }


    public function getProfilePixAttribute(){
        $profile = Profile::where('id', $this->id)->first();
        return ['profile_thumbnail' => $profile->profile_image_thumbnail,
                'profile_image' => $profile->profile_image];
    }

    
    
    
}
