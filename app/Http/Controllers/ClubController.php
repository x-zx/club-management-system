<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClubController extends Controller
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
        $clubs = \App\Club::where($input)->paginate($per_page);
        echo $clubs->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth = Auth::check() && Auth::user()->id == 1;
        $club = null;
        $result = false;
        if($auth){
            $club = new \App\Club();
            $club->fill($request->input());
            $club->code = uniqid();
            $result = $club->save();
        }
        echo json_encode(['result'=>$result,'club'=>$club]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $club = null;
        $club = \App\Club::find($id);
        // if($club){
        //     $club->rooms = \App\Room::where(['club_id'=>$club->id])->get();
        // }
        $result = boolval($club);
        echo json_encode(['result'=>$result,'club'=>$club]);
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
        $auth = Auth::check() && Auth::user()->id == 1;
        $result = false;
        $club = null;
            if($auth){
                $club = \App\Club::find($id);
                if($club){
                    $club->fill($request->input());
                    $result = $club->save();
                }
        }
        echo json_encode(['result'=>$result,'club'=>$club]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auth = Auth::check() && Auth::user()->id == 1;
        $result = false;
        $club = \App\Club::find($id);
        if($auth){
            if($club){
                $result = \App\Club::destroy($id);
            }
        }
        echo json_encode(['result'=>$result]);
    }

    public function getChecked($id){
        $club = \App\User::find($id);
        $checked_id = [0];
        $logs = \App\Log::where(['tag'=>'check'])->where('created_at','>',Carbon::today())->get();
        foreach ($logs as $log) {
            $checked_id[] = $log->source_id;
        }
        $checked_id_str = implode(",",$checked_id);
        $users = \App\User::where(['club_id'=>$club->id,'role'=>'saleman'])->whereRaw("id in ($checked_id_str)")->get();
        // foreach ($users as $user) {
        //     $working_count = \App\Log::where(['tag'=>'work','source_id'=>$user->id,'content'=>'working'])->where('created_at','>',Carbon::today())->count();
        //     $free_count = \App\Log::where(['tag'=>'work','source_id'=>$user->id,'content'=>'working'])->where('created_at','>',Carbon::today())->count();
        // }
        echo $users->toJson();
    }
}
