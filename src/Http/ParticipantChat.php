<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;

class ParticipantChat extends Model
{
    protected $fillable = ["chat_id", "user_id"];

    public function chat() {
        return $this->belongsTo('Techsmart\Chat\Chat', 'id', 'chat_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'id', 'user_id');
    }
}
