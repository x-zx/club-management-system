<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $guarded = ['id'];

    public function club(){
    	return $this->beLongsto('App\Club');
    }

}
