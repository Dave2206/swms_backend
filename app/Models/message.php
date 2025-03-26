<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'subject',
        'message',
        'attachment',
    ];

     // Define sender relationship
     public function sender()
     {
         return $this->belongsTo(User::class, 'sender_id');
     }
 
     // Define recipient relationship
     public function recipient()
     {
         return $this->belongsTo(User::class, 'recipient_id');
     }
}
