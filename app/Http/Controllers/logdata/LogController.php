<?php

namespace App\Http\Controllers\logdata;

use App\Http\Controllers\Controller;
use App\LogData;
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
use Artisan;
use Cache;
use Datatables;

class LogController extends Controller
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

    public function index($data)
    {
        $logdata = new LogData();
        $Unique='';
        for($j=0;$j < 4;$j++)
        {
            if($j!=3){$dash='-';}else{$dash='';}
            $Unique .= $this->getTokenProduct().$dash;
        }
        $data['logID'] = $Unique;

        LogData::updateOrCreate(array('logID' => $data['logID']), $data);
    }


    public function loglist()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            return View::make('admin.logdata.logList');
        }else{
            return Redirect::to('/');
        }
    }

    public function dataLog()
    {
        $logdata = new LogData();

        $data = LogData::select('*')->orderBy('id','desc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('User', function ($data) {
                if($data->role != ''){
                    return $data->role;
                }else{
                    return '---';
                }
            })

            ->addColumn('Info', function ($data) {
                if($data->operation != ''){
                    return $data->operation;
                }else{
                    return '---';
                }
            })

            ->addColumn('Description', function ($data) {
                if($data->description != ''){
                    return $data->description;
                }else{
                    return '---';
                }
            })

            ->addColumn('Date', function ($data) {
                if($data->role_date != ''){
                    return $data->role_date;
                }else{
                    return '---';
                }
            })
            ->make(true);
    }
}
