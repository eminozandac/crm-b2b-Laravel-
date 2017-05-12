<?php

namespace App\Http\Controllers\admin;

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
use App\MediaCategory;
use App\MediaCategoryFile;
 

class MediaSubCategoryController extends Controller
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
        
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data  = array();
        if(isset($id) && ($id != 0))
        {
            $result_category = DB::table('media_category')->select('*')->where('parent_id','!=','0')->orderBy('id','asc')->get();
            $data['category_list'] = $result_category;

            return View::make('admin.mediacategory.subcategory.mediacategorySubList',$data);
        }else{
            return Redirect::to('/');
        }

    }

    public function dataMediaSubCateogry()
    {
        $mediacategory = new MediaCategory();

        $data = MediaCategory::select('*')->orderBy('parent_id','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('Media Category', function ($data) {
                if($data->name != ''){
                    return $data->name;
                }else{
                    return '---';
                }
            })

            ->addColumn('Total Files', function ($data)
            {
				$count = DB::table('media_category_file')->where('media_uniqueID','=',$data->media_uniqueID)->count();
                return $count;
            })

            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/edit-mediasubcategory', $data->media_uniqueID);
                $html = '';
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";
                $html.= '&nbsp;&nbsp;';
                $deleted = $data->media_uniqueID;
                $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('".$deleted."')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";
                return $html;
            })
            ->make(true);
    }

    public function addMediaSubCategory()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $Unique='';
            $data['clients_project_uniqueID'] = '';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }
            $data['media_uniqueID'] = $Unique;

            $data['name'] = '';
            $data['dealerID'] = '';


            $data['dealer'] = '';
            $result_dealer = DB::table('dealer')->select('id','first_name','last_name')->get();
            $data['dealer'] = $result_dealer;

            $data['parent_category'] = '';
            $result_parent_category = DB::table('media_category')->select('id','parent_id','name')->where('parent_id','=','0')->get();
            $data['parent_category'] = $result_parent_category;

            if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['name'] = $session_data['name'];
                //$data['dealerID'] = $session_data['dealerID'];
            }

            return View::make('admin.mediacategory.subcategory.mediacategorySubAdd',$data);
        }else{
            return Redirect::to('/');
        }

    }

    public function  saveData()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $update_array = array();
            $mediacategory = new MediaCategory();

            $data_media_category = Input::all();
            $rules = array(
              //  'name'=>'required|min:3|max:100|Unique:media_category|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                if(isset($messages->get('name')[0])){
                    Session::flash('operationFaild','The Media Sub Category Name has already been taken.');
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                }
                Session::set('session_data',$data_media_category);
                return Redirect::to('/admin/mediasubcategoryadd');
            }else
            {
                $primary_key = array('media_uniqueID' => $data_media_category['media_uniqueID']);

                $update_array['parent_id'] = $data_media_category['parent_id'];
                $update_array['name'] = htmlentities($data_media_category['name']);
                $update_array['status'] = 1;

                $update_array['file_name'] = $data_media_category['hidden_file_name'];
                $update_array['old_file_name'] = $data_media_category['hidden_old_file_name'];

                MediaCategory::updateOrCreate($primary_key, $update_array);

                Session::forget('session_data');
                Session::save();

                Session::flash('operationSucess','Successfully Add Media Sub Category');
                return Redirect::to('/admin/mediacategorysublist');
                exit;
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function editMediaSubCategory($id)
    {
        $result_data = array();
        $data = array();
        $mediacategory_id = $id;

        $mediacategory = new MediaCategory();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['name'] = '';

            $data['dealer'] = '';
            $result_dealer = DB::table('dealer')->select('id','first_name','last_name')->get();
            $data['dealer'] = $result_dealer;

            $data['parent_category'] = '';
            $result_parent_category = DB::table('media_category')->select('id','parent_id','name')->get();
            $data['parent_category'] = $result_parent_category;

            $result_data = DB::table('media_category')->where('media_uniqueID','=',$mediacategory_id)->first();

            if(!empty($result_data))
            {
                $data['mediacategory'] = $result_data;
                return View::make('admin.mediacategory.subcategory.mediacategorySubEdit',$data);
            }else{
                return Redirect::to('/admin/mediacategorysublist');
            }
        }else{
            return Redirect::to('/');
        }
    }


    public function  editData()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $mediacategory = new MediaCategory();

            $data_media_category = Input::all();

            $rules = array(
                'name'=>'required|min:3|max:100|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {
                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$data_media_category);
                return Redirect::to('/admin/mediacategorylist');
            }else
            {
                $update_array = array();
                $response = $mediacategory->where('media_uniqueID', $data_media_category['media_uniqueID'])->first();
                if (!empty($response))
                {
                    $primary_key = array('media_uniqueID' => $data_media_category['media_uniqueID']);

                    $update_array['media_uniqueID'] = $data_media_category['media_uniqueID'];
                    $update_array['parent_id'] = $data_media_category['parent_id'];
                    $update_array['name'] = htmlentities($data_media_category['name']);
                    $update_array['status'] = 1;

                    $update_array['file_name'] = $data_media_category['hidden_file_name'];
                    $update_array['old_file_name'] = $data_media_category['hidden_old_file_name'];

                    $mediacategory->where('media_uniqueID', $data_media_category['media_uniqueID'])->update($update_array);

                    Session::forget('session_data');
                    Session::save();

                    Session::flash('operationSucess','Successfully Edit Media Category');
                    return Redirect::to('/admin/mediacategorysublist');
                    exit;

                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/mediacategorysublist');
                    exit;
                }
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function  deleteData()
    {
        $id = 0;
        $sessionData = Session::get('adminLog');
        $id = $sessionData['adminID'];
        if (isset($id) && ($id != 0))
        {
            $mediacategory = new MediaCategory();
            $data_media_category = Input::all();
            $media_uniqueID = $data_media_category['media_uniqueID'];

            $category_id = 0;
            $parent_id = 0;
            $result_file_images = DB::table('media_category')->where('media_uniqueID','=',$media_uniqueID)->first();
            $category_id = $result_file_images->id;
            $parent_id = $result_file_images->parent_id;

            //update
            DB::table('media_category')->where('parent_id', '=', $category_id)->update(array('parent_id' => $parent_id));

            //delete
            $mediacategory->where('media_uniqueID', '=', $media_uniqueID)->delete();

            /*$mediacategoryfile = new MediaCategoryFile();
            $mediacategoryfile->where('media_uniqueID', '=', $media_uniqueID)->delete();*/

            // upload path
            $destinationPath = 'uploads/mediafile';
            $thumb_destinationPath = 'uploads/mediafile/thumb';

            $result_file_images = DB::table('media_category_file')->where('media_uniqueID','=',$media_uniqueID)->first();
            if(!empty($result_file_images->file_name))
            {
                $file_name = explode(',',$result_file_images->file_name);
                foreach($file_name as $key => $value)
                {
                    File::delete($destinationPath.'/'.$value);
                    File::delete($thumb_destinationPath.'/'.$value);
                }
            }
            MediaCategoryFile::where('media_uniqueID','=',$media_uniqueID)->delete();
        }
    }
}
