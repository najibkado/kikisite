<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{

    protected $fillable = ['message', 'reciever_id', 'type', 'unread', 'isLiked', 'image', 'location', 'longitude','latitude'];

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'reciever_id');
    }

    public function allUserChats(){
        return DB::select(' SELECT t1.* FROM chats AS t1 
        INNER JOIN ( SELECT LEAST(sender_id, reciever_id) 
        AS sender_id, GREATEST(sender_id, reciever_id)
         AS reciever_id, MAX(id) 
         AS max_id FROM chats 
         GROUP BY LEAST(sender_id, reciever_id), GREATEST(sender_id, reciever_id) ) 
         AS t2 ON LEAST(t1.sender_id, t1.reciever_id) = t2.sender_id 
         AND GREATEST(t1.sender_id, t1.reciever_id) = t2.reciever_id 
         AND t1.id = t2.max_id WHERE t1.sender_id = ? 
         OR t1.reciever_id = ? ', [$this->user->id, $this->user->id]);
    }
}
