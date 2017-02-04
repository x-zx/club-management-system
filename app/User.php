<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = ['id'];
    protected $hidden =  ['passwd', 'remember_token','openid'];

    public function club(){
        return $this->hasOne('App\Club');
    }

    public function scopeManager($query)
    {
        return $query->where(['role'=>'manager']);
    }

    public function scopeReception($query)
    {
        return $query->where(['role'=>'reception']);
    }

    public function scopeSaleman($query)
    {
        return $query->where(['role'=>'saleman']);
    }

    public function scopeClubhr($query)
    {
        return $query->where(['role'=>'clubhr']);
    }

    public function scopeGeneral($query)
    {
        return $query->Where(['role'=>''])->orWhere(['role'=>'general']);
    }
}
