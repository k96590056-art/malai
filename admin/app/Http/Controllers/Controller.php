<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Validator;
use Illuminate\Support\Facades\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $engamelist = [];
    

   
    public function returnMsg($code, $data = [],$message = '')
    {
        $lang = Request::header('Lang') ?? 'zh';
        
        return response()->json([
            'code'    => $code,
            'message' => $message ?: config('errorcode.code')[$lang][(int) $code],
            'data'    => $data,
        ]);
    }

    public function validate($request, $rules, $message){
        $Validator = Validator::make($request->all(),$rules,$message);
        if($Validator->fails()){
            $result = [];
            foreach(json_decode(json_encode($Validator->errors()),true) as $k => $v){
                $result['code'] = 1000;
                $result['message'] = $v[0];
				header('Content-type:text/json');
                echo json_encode($result);
                exit;
            }
        }
    }
    public function isMobile()
    {
            if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
                return true;
            } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
                return true;
            } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])){
                return true;
            } elseif (isset($_SERVER['HTTP_USER_AGENT']) &&  preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }
            return false;
    }
    public function write_log($data,$filepath=''){
        $data = is_array($data) ? json_encode($data) : $data;
        $data = date('Y-m-d H:i:s') . '   ' . $data;

        $filepath = $filepath ? $filepath : './pay_log.txt';
        if($rsp = fopen($filepath, "a+b")) {
            fwrite($rsp, $data);
            fwrite($rsp, PHP_EOL."--------------------".PHP_EOL);
            fclose($rsp);
        }
    }	
}
