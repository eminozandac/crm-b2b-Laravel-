<?php

namespace App\Http\Controllers\dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use View;
use Validator;
use Hash;
use Carbon\Carbon;
use Input;
use Mail;
use Form;
use Auth;
use File;
use Config;
use App\Dealer;

class DealerNewsLatterController extends Controller
{
    function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    function getTokenProduct(){
        $length=4;
        $token = "";
        $codeAlphabet= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
        }
        return $token.rand(111111,999999);
    }


    function mailchimp_request($method,$type, $data = false)
    {
        $apiKey = '9a5c99873b7df2d46e8fd08b7f1f0ca2-us10';
        $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);

        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0'.$method;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if($data){
            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function index()
    {
        $dt = Carbon::now();

        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        $data = array();

        if(isset($id) && ($id != 0))
        {
            $total = 0 ;
            $method = '/campaigns';
            $response = $this->mailchimp_request($method,'GET',array(''));
            $data_result_total = json_encode($response,true);
            $data_result_total = json_decode($data_result_total,true);
            $data_result_total = json_decode($data_result_total,true);
            if(isset($data_result_total['total_items']))
            {
                $total = $data_result_total['total_items'];
            }

            if($total != 0){
                $method = '/campaigns?status=sent&count='.$total;
            }else{
                $method = '/campaigns';
            }

            $response = $this->mailchimp_request($method,'GET',array(''));
            $data_result = json_encode($response,true);
            $data_result = json_decode($data_result,true);
            $data['campaignslist'] = json_decode($data_result,true);
			return View::make('dealer/newslatter/newslatterList',$data);
        }else{
            return View::make('dealer/index');
        }
    }

    public function getContain($id)
    {
        if(!empty($id) && $id != '')
        {
            $method = '/campaigns/'.$id.'/content';
            $response = $this->mailchimp_request($method,'GET',array(''));
            $data_result = json_encode($response,true);
            $data_result = json_decode($data_result,true);
            $data = json_decode($data_result,true);
            if(isset($data['html']) && $data['html'] != '')
            {
                echo $data['html'];
            }else{
                echo 'Result Not Found';
            }
        }
    }

}
