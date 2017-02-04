<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class Pay extends Controller
{
    public static function pay($user,$amount = 0){
    	$result = false;
    	if($user){
    		if($user->amount + $amount >= 0){
    			$user->amount = $user->amount + $amount;
    			$result = $user->save();
    			if($result){
    				$log = new \App\Log();
	        		$log->source_id = $user->id;
	        		$log->tag = 'pay';
	        		$log->content = $amount;
	        		$log->save();
    			}
    		}
    	}
    	return $result;
    }
}
