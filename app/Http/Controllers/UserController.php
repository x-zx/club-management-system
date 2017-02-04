<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->input();
        $per_page = isset($input['per_page']) ? $input['per_page'] : 15;
        unset($input['page']);
        $users = \App\User::where($input)->paginate($per_page);
        echo $users->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'openid' => 'unique:users',
        //     'phone' => 'unique:users',
        // ]);

        // $user = null;

        // if(!$validator->fails()){
        //     $user = new \App\User($request->input());
        //     $user->save();
        // }
        
        // $result = boolval($user);

        // echo json_encode(['result'=>$result,'user'=>$user,'errors'=>$validator->errors()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = \App\User::find($id);
        $result = boolval($user);
        echo json_encode(['result'=>$result, 'user'=>$user]);
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

        $validator = Validator::make($request->all(), [
            'openid' => 'unique:users',
        ]);
        $user = null;
        if(!$validator->fails()){
            $user = \App\User::find($id);
            if(!$user){
                $user = \App\User::where(['openid'=>$id]);
            }
            $auth = Auth::check() && Auth::user()->id == 1 || Auth::user()->id == $user->id;
            if($auth){
                $input = $request->input();
                if(isset($input['passwd'])){
                    $input['passwd'] = md5($input['passwd']);
                }
                unset($input['amount']);
                $user->fill($input);
                $user->save();
            }
        }

        $result = boolval($user);
        echo json_encode(['result'=>$result,'user'=>$user,'errors'=>$validator->errors()]);
        
    }

    public function postInvite(Request $request,$id){
        $auth = Auth::check() && Auth::user()->role == 'manager';
        $user = null;
        $saleman = null;
        if($auth){
            $amount = 100;
            $manager = Auth::user();
            $saleman = \App\User::find($id);
            $pay = Pay::pay($manager,-$amount);

            if($pay){
                $invitation = new Invitation();
                $invitation->from_user_id = $manager->id;
                $invitation->to_user_id = $saleman->id;
                $invitation->club_id = $manager->club_id;
                $invitation->amount = $amount;
                $invitation->state = 'unconfirmed';
                $invitation->save();
            }
        }

        echo json_encode(['result'=>$pay,'user'=>$saleman]);
    }
  
}
