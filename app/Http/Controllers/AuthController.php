<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	public function getIndex(Request $request){
		echo json_encode(['result'=>Auth::check(),'user'=>Auth::user()]);
	}

	public function postIndex(Request $request){
		$user = null;
		$input =  $request->input();
		if(isset($input['openid'])){
			$user = \App\User::where(['openid'=>$input['openid']])->first();
			if(!$user){
				$user = new \App\User(['openid'=>$input['openid']]);
				$user->role = '';
				$user->save();
			}
		}

		if(isset($input['phone']) && isset($input['passwd'])){
			$user = \App\User::where('passwd','<>','')->where(['phone'=>$input['phone'],'passwd'=>md5($input['passwd'])])->first();
		}

    	if($user){
    		Auth::login($user, true);
    	}
    	echo json_encode(['result'=>Auth::check(),'user'=>Auth::user()]);
	}

}
