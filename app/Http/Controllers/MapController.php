<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function show($key){
    	$kv = \App\Map::where(['key'=>$key])->first();
    	if($kv){
    		if(substr($key,0,1) != '_')
    		echo $kv->toJson();
    	}
    }

    public function save(Request $request,$key){
    	$kv = \App\Map::where(['key'=>$key])->first();
    	if(!$kv)
    		$kv = new \App\Map();
    	$value = $request->input('value');
    	$tag = $request->input('tag');
    	$kv->key = $key;
    	$kv->value = $value;
    	$kv->tag = $tag;
    	$kv->save();
    	echo $kv->toJson();
    }
}
