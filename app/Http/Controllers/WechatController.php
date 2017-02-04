<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;


class WechatController extends Controller
{

    private static function post($url,$data=''){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST,true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public function getIndex(){
        echo json_encode(['appid'=>env('WX_APPID','')]);
    }

    public function getOauth(Request $request){
        $appid = env('WX_APPID');
        $secret = env('WX_SECRET');
    	$code = $request->input('code','');
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
        $res = $this->post($url);
        echo $res;
    }

    public function getUser(Request $request){
        $access_token = $request->input('access_token','');
        $openid = $request->input('openid','');
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $res = $this->post($url);
        echo $res;
    }
    
}
