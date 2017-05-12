<?php

namespace App\Http\Controllers\dealer;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Redirect;
use Response;
use Illuminate\Support\Facades\Session;
use Input;
use Auth;
use Hash;
use Symfony\Component\Translation\Interval;
use View;
use Mail;
use Form;
use File;
use Cache;
use Validator;
use Carbon\Carbon;
use Datatables;
use URL;
use App\Category;
use App\MediaCategoryFile;
use Image;


class DealerMediaCategoryFileController extends Controller
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

    public function index()
    {
        Session::forget('session_data');
        Session::save();

        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $data['parent_category'] = '';
            /*$result_parent_category = DB::table('media_category')
                                    ->select('id','media_uniqueID','parent_id','name','dealer_id')
                                    ->where('parent_id','=','0')
                                    ->where('status','=','1')
                                    ->get();*/

            $result_parent_category = DB::select("SELECT * FROM media_category WHERE (FIND_IN_SET(".$id.", dealer_id) OR dealer_id = 'all') AND dealer_id != '' AND parent_id = 0 AND status = 1 ");

            $data['parent_category'] = $result_parent_category;

            return View::make('dealer.mediacategoryfile.mediacategoryfileList',$data);
        }else{
            return Redirect::to('/');
        }
    }

    public function mediaList($parent_id)
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $media_uniqueID = '';
            $result_media = DB::table('media_category')->select('id','media_uniqueID','parent_id','name')->where('id','=',$parent_id)->first();
            if(!empty($result_media))
            {
                $media_uniqueID = $result_media->media_uniqueID;
            }else{
                return Redirect::to('dealer/mediacategoryfile');
            }

            $data['categoryID'] = $parent_id;


            $data['medialFile'] = '';
            $result_medialFile_images = DB::table('media_category_file')->select('*')->where('media_uniqueID','=',$media_uniqueID)->get();
            $data['medialFile'] = $result_medialFile_images;

            $data['parent_category'] = '';
            $result_sub_category = DB::table('media_category')->select('id','media_uniqueID','parent_id','name','file_name')->where('parent_id','=',$parent_id)->get();
            $data['parent_category'] = $result_sub_category;


            return View::make('dealer.mediacategoryfile.mediacategoryfileList',$data);
        }else{
            return Redirect::to('/');
        }
    }
}
