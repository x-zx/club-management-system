<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $guarded = ['id'];

    public function users(){
    	return $this->hasMany('App\User');
    }

    public function rooms(){
    	return $this->hasMany('App\Room');
    }

    public function reservations(){
    	return $this->hasMany('App\Reservation');
    }

}
