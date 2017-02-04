<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class YTX extends Controller
{
    protected $url = "https://app.cloopen.com:8883";
    protected $version = "2013-12-26";
    protected $sid = '';
    protected $appid = '';
    protected $token = '';

    function __construct($sid='',$token='',$appid=''){
        $this->sid = env('YTX_SID',$sid);
        $this->token = env('YTX_TOKEN',$token);
        $this->appid = env('YTX_APPID',$appid);
    }

    function curl_post($url,$data,$header,$post=1){
        $ch = curl_init();
        $res = curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if($post){
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result = curl_exec ($ch);
        curl_close($ch);
        return $result;
     } 

    function sig(){
        return md5($this->sid . $this->token . date("YmdHis"));
    }

    function auth(){
        return base64_encode($this->sid . ':' . date("YmdHis"));
    }

    public function sendTemplateSMS($to,$datas,$temp_id){
        $header = ["Accept:application/json","Content-Type:application/json;charset=utf-8","Authorization:" . $this->auth()];
        $body= ['to'=>$to,'templateId'=>$temp_id,'appId'=>$this->appid,'datas'=>$datas];
        $sms_url="$this->url/$this->version/Accounts/$this->sid/SMS/TemplateSMS?sig={$this->sig()}";
        $result = $this->curl_post($sms_url,json_encode($body),$header);
        $res_json = json_decode($result,true);
        //var_dump($result);
        return $res_json['statusCode'] == '000000';
    }

}
