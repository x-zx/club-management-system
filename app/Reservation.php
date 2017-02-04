<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $guarded = ['id'];

    public function room(){
    	return $this->belongsTo('App\Room');
    }

    public function club(){
    	return $this->belongsTo('App\Club');
    }

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
