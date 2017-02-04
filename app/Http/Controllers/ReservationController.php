<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->input();
        $btime = isset($input['btime']) ? $input['btime'] : (string)Carbon::today();
        $etime = isset($input['etime']) ? $input['etime'] : (string)Carbon::tomorrow();
        unset($input['btime']);
        unset($input['etime']);
        $per_page = isset($input['per_page']) ? $input['per_page'] : 15;
        unset($input['page']);
        $reservations = \App\Reservation::where('btime','>=',$btime)->where('btime','<=',$etime)->where($input)->paginate($per_page);
        foreach ($reservations as $reservation) {
            $room = $reservation->room;
        }
        echo $reservations->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth = Auth::check();
        $input = $request->input();
        $btime = isset($input['btime']) ? $input['btime'] : (string)Carbon::now();
        $etime = isset($input['etime']) ? $input['etime'] : (string)Carbon::now();
        $room = \App\Room::find($input['room_id']);
        $reservation = null;
        $result = false;
        if($auth && $room){
        	$role = Auth::user()->role;
        	$reservation = new \App\Reservation();
            $reservation->user_id = Auth::user()->id;
        	$reservation->room_id = $room->id;
        	$reservation->club_id = $room->club_id;
            $reservation->btime = $btime;
            $reservation->etime = $etime;
        	$reservation->state = 'unconfirmed';
        	switch($role){
        		case 'manager':
        			$reservation->amount = 0;
        			break;
        		default:
        			$reservation->amount = $room->consumption;
        			break;
        	}

        	$pay = Pay::pay(Auth::user(),-$reservation->amount);

        	if($pay){
        		$result = $reservation->save();
                $reception = \App\User::where(['club_id'=>$reservation->club_id,'role'=>'reception'])->first();
                if($reception){
                    $user = Auth::user();
                    $room_des = empty($room->tag) ? $room->name : "{$room->name}({$room->tag})";
                    $ytx = new YTX();
                    $ytx->sendTemplateSMS($reception->phone,[$user->name,$user->phone,$reservation->btime,$room_des],144262);
                }
                
        	}
        }
        echo json_encode(['result'=>$result,'reservation'=>$reservation]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $reservation = null;
        $reservation = \App\Reservation::find($id);
        $room = $reservation->room;
        $result = boolval($reservation);
        echo json_encode(['result'=>$result,'reservation'=>$reservation]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	$input = $request->input();
        $auth = Auth::check() && Auth::user()->role == 'reception';
        $result = false;
        $reservation = null;
        unset($input['amount']);
        if($auth){
	        $reservation = \App\Reservation::find($id);
	        if($reservation){
	            $reservation->fill($input);
                $room = $reservation->room;
                if($reservation->state == 'confirmed'){
                    $user = \App\User::find($reservation->user_id);
                    $club = \App\Club::find($reservation->club_id);
                    $room_des = empty($room->tag) ? $room->name : "{$room->name}({$room->tag})";
                    $ytx = new YTX();
                    $ytx->sendTemplateSMS($user->phone,[$reservation->btime,$club->name,$room_des],144261);
                }
                
                $club = \App\Club::find($room->club_id);
                $reservation->club_id = $club->id;
	            $result = $reservation->save();
	        }
        }
        echo json_encode(['result'=>$result,'reservation'=>$reservation]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $auth = Auth::check() && Auth::user()->id == 1;
    //     $result = false;
    //     $reservation = \App\Reservation::find($id);
    //     if($auth){
    //         if($reservation){
    //             $result = \App\Reservation::destroy($id);
    //         }
    //     }
    //     echo json_encode(['result'=>$result]);
    // }
}
