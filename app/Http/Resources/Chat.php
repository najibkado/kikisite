<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class Chat extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        $chat = [
         "id" =>   $this->id,
         'sender_id' => $this->sender_id,
         'reciever_id' => $this->reciever_id,
         'type' => $this->type,
         'message' => $this->message,
         'unread' => $this->unread,
         'isLiked' =>  $this->isLiked, 
         'image' => $this->image,
         'location' => $this->location,
         'latitude' => $this->latitude,
         'longitude' => $this->longitude,
         'created_at' => $this->created_at,
         'updated_at' => $this->updated_at,
         'user' => User::find($this->sender_id == auth()->user()->id ? $this->reciever_id : $this->sender_id )
            ];
            
            
       
             
        
        return $chat;
      
        }
    
}
