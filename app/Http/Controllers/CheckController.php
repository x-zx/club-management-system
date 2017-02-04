<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class CheckController extends Controller
{
    public function getIndex(Request $request){
        $input = $request->input();
        $user = Auth::user();
        $check = null;
        $club = null;
        $time = null;
        if($user){
            $check = \App\Log::where(['source_id'=>$user->id,'tag'=>'check'])->where('created_at','>',Carbon::today())->latest()->first();
            if($check){
            	$club = \App\Club::where(['code'=>$check->content])->first();
            	$time = $check->created_at->toDateTimeString();
            }
            
        }
        echo json_encode(['result'=>boolval($check),'club'=>$club,'time'=>$time]);
    }

    public function postIndex(Request $request){
        $input = $request->input();
        $user = Auth::user();
        $result = false;
        $time = null;
        $club = null;
        if($user && isset($input['code'])){
            $club = \App\Club::where(['code'=>$input['code']])->where('code','<>','')->first();
            
            $work = \App\Log::where(['source_id'=>$user->id,'tag'=>'work'])->where('created_at','>',Carbon::today())->latest()->first();
            if($club && !$work){
            	$user->club_id = $club->id;
            	$user->role ='saleman';
            	$user->state = 'free';
            	$user->save();
            	
	        	$invitation = \App\Invitation::where(['to_user_id'=>$user->id,'state'=>'unconfirmed'])->first();
	        	if($invitation){
	        		$pre_check = \App\Log::where(['source_id'=>$user->id,'tag'=>'check'])->latest()->first();
	        		$pre_work = \App\Log::where(['source_id'=>$user->id,'tag'=>'work'])->latest()->first();
	        		if($pre_check && !$pre_work){
	        			if($pre_work){
	        				//工作过
	        				$invitation->state = 'confirmed';
	        				$invitation->save();
	        				$manager = \App\User::find($invitation->from_user_id);
	        				Pay::pay($manager,0.9 * $invitation->amount);
	        			}else{
	        				$invitation->state = 'unemployed';
		        			$invitation->save();
		        			$saleman = \App\User::find($invitation->to_user_id);
		        			Pay::pay($saleman,0.9 * $invitation->amount);
	        			}
	        			
	        		}
	        	}

                $check = new \App\Log();
                $check->source_id = $user->id;
                $check->tag = 'check';
                $check->content = $input['code'];
                $result = $check->save();
                
            }
            $time = $check ? $check->created_at->toDateTimeString() : null;
        }

        echo json_encode(['result'=>$result,'club'=>$club,'time'=>$time]);
        
    }

    public function getWork(Request $request){
    	$user = Auth::user();
    	$result = false;
        $state = null;
    	if($user){
        	$state = $user->state;
        }
        $result = boolval($user);
        echo json_encode(['result'=>$result,'state'=>$state]);
    }

    public function putWork(Request $request){
    	$input = $request->input();
        $user = Auth::user();
        $result = false;
        $state = null;
        if($user){
        	$isSaleman = $user->role=='saleman';
	        $isState = isset($input['state']) && in_array($input['state'], ['free','working']);
	        $isCheck = \App\Log::where(['source_id'=>$user->id,'tag'=>'check'])->where('created_at','>',Carbon::today())->latest()->first();
	        if($user && $isCheck && $isSaleman && $isState){
        		$user->state = $input['state'];
        		$state = $user->state;
        		$user->save();
	        	$log = new \App\Log();
	        	$log->source_id = $user->id;
	        	$log->tag = 'work';
	        	$log->content = $input['state'];
	        	$result = $log->save();

	        }
        }

        echo json_encode(['result'=>$result,'state'=>$state]);
        
    }
}
