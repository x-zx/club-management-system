<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
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
        $rooms = \App\Room::where($input)->orderBy('tag')->paginate($per_page);
        echo $rooms->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth = Auth::check() && Auth::user()->id == 1 || Auth::user()->role == 'reception';
        $input = $request->input();
        $room = new \App\Room();
        $room->fill($input);
        $result = $room->save();
        echo json_encode(['result'=>$result, 'room'=>$room]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = \App\Room::find($id);
        $result = boolval($room);
        echo json_encode(['result'=>$result, 'room'=>$room]);
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
        $auth = Auth::check() && Auth::user()->id == 1 || Auth::user()->role == 'reception';
        $room = \App\Room::find($id);
        $result = false;
        if($auth && $room){
            $room->fill($request->input());
            $result = $room->save();
        }
        echo json_encode(['result'=>$result, 'room'=>$room]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auth = Auth::check() && Auth::user()->id == 1 || Auth::user()->role == 'reception';
        $room = \App\Room::find($id);
        $result = false;
        if($auth && $room){
            $result = \App\Room::destroy($id);
        }
        echo json_encode(['result'=>$result]);
    }
}
