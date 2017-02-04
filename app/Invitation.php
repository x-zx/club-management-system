<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $guarded = ['id'];

    public function from(){
    	return $this->hasOne('App\User','from_user_id');
    }

    public function to(){
    	return $this->hasOne('App\User','to_user_id');
    }

    public function club(){
    	return $this->hasOne('App\Club','club_id');
    }

}
