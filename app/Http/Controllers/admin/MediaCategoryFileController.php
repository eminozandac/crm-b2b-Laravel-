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
use App\Category;
use App\MediaCategoryFile;
use Image;


class MediaCategoryFileController extends Controller
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
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['parent_category'] = '';
            $result_parent_category = DB::table('media_category')->select('id','media_uniqueID','parent_id','name','file_name')->where('parent_id','=','0')->get();
            $data['parent_category'] = $result_parent_category;

            return View::make('admin.mediacategoryfile.mediacategoryfileList',$data);
        }else{
            return Redirect::to('/');
        }
    }

    public function mediaList($parent_id)
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $media_uniqueID = '';
            $result_media = DB::table('media_category')->select('id','media_uniqueID','parent_id','name','file_name')->where('id','=',$parent_id)->first();
            if(!empty($result_media))
            {
                $media_uniqueID = $result_media->media_uniqueID;
            }else{
                return Redirect::to('admin/mediacategoryfile');
            }

            $data['categoryID'] = $parent_id;


            $data['medialFile'] = '';
            $result_medialFile_images = DB::table('media_category_file')->select('*')->where('media_uniqueID','=',$media_uniqueID)->get();
            $data['medialFile'] = $result_medialFile_images;

            $data['parent_category'] = '';
            $result_sub_category = DB::table('media_category')->select('id','media_uniqueID','parent_id','name','file_name')->where('parent_id','=',$parent_id)->get();
            $data['parent_category'] = $result_sub_category;


            return View::make('admin.mediacategoryfile.mediacategoryfileList',$data);
        }else{
            return Redirect::to('/');
        }
    }

    public  function  medialFileAdd()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $Unique='';
            $data['media_file_uniqueID'] = '';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }
            $data['media_file_uniqueID'] = $Unique;

            $data['parent_category'] = '';
            $result_parent_category = DB::table('media_category')->select('id','media_uniqueID','parent_id','name','file_name')->where('parent_id','=','0')->get();
            $data['parent_category'] = $result_parent_category;

            return View::make('admin.mediacategoryfile.mediacategoryfileAdd',$data);
        }else{
            return Redirect::to('/');
        }
    }

    public function fileupload()
    {
        $input = Input::all();
        $rules = array(
            'file' => 'image|max:2097152',
        );

        $success_ar_file_name = array();

        $validation = Validator::make($input, $rules);
        if ($validation->fails())
        {
            echo 'error';
            exit;
        }else{
            $files = Input::file('note_file');
            $destinationPath = 'uploads/mediafile/';
            $thumb_destinationPath = 'uploads/mediafile/thumb/';
            $file_type_ar = array('jpg','jpeg','png','JPG','JPEG','PNG');
            foreach($files as $file)
            {
                $extension = File::extension($file->getClientOriginalName());
                $filename = 'note_file_'.sha1(rand(11,9999).rand(1,999).date("Ymdhis")).rand(1111,9999).".{$extension}";
                $upload_success = $file->move($destinationPath, $filename);
                if($upload_success)
                {
                    if(in_array($extension,$file_type_ar))
                    {
                        $images_name = $destinationPath.$filename;
                        $large_img = Image::make($images_name);
                        //$large_img->resize(600, 600);

                        $width = Image::make($images_name)->width();
                        $height = Image::make($images_name)->height();
                        if($height > 2500)
                        {
                            $height = 1800;
                        }else if($height > 1500)
                        {
                            $height = 1200;
                        }else if($height > 1200)
                        {
                            $height = 900;
                        }else if($height > 1000)
                        {
                            $height = 800;
                        }else{
                            $height = 600;
                        }

                        // resize the image to a height of 200 and constrain aspect ratio (auto width)
                        $large_img->resize(null, $height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $large_img->save($images_name);

                        /*create thumb*/
                        $thub_images_name = $thumb_destinationPath.$filename;
                        $thumb_img = Image::make($images_name);
                        $thumb_img->resize(240, 140);
                        $thumb_img->save($thub_images_name);
                    }

                    $filename_ar = array();
                    $filename_ar['oldname'] = $file->getClientOriginalName();
                    $filename_ar['newname'] = $filename;
                    $filename_ar['file_type'] = $extension;
                    $filename_ar['downloadurl'] = URL::to("uploads/warranty/".$filename);
                    $success_ar_file_name[] = $filename_ar;
                } else {
                    echo 'error'; exit;
                }
            }
            if(!empty($success_ar_file_name)){
                //echo implode(',', $success_ar_file_name);
                echo json_encode($success_ar_file_name,true);
            }
        }
    }

    public  function fileRemove()
    {
        $file_path =  'uploads/mediafile/';
        $file_path_thumb =  'uploads/mediafile/thumb/';
        $data = Input::all();
        if(isset($data['file']) && !empty($data['file']))
        {
            $file_name = $data['file'];
            File::delete($file_path.$file_name);
            File::delete($file_path_thumb.$file_name);
        }
    }

    public function saveData()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $data_post = Input::all();
            if(!empty($data_post) && ($data_post['media_file_uniqueID'] != '') && ($data_post['media_uniqueID'] != '') && (!empty($data_post['media_uniqueID'])) )
            {
                $rules = array(
                    'media_uniqueID'=>'required|not_in:0',
                    'hidden_file_name'=>'required|not_in:0',
                );

                $validator = Validator::make(Input::all(), $rules);

                if ($validator->fails())
                {
                    Session::flash('operationFaild','Some thing went wrong!');
                    return Redirect::to('/admin/mediacategoryfileadd');
                }else
                {
                    $update_post = array();
                    $filename_ar_old = explode(',',$data_post['hidden_old_file_name']);
                    $filename_ar = explode(',',$data_post['hidden_file_name']);
                    $a = 0;
                    foreach($filename_ar as $key => $value)
                    {
                        $update_post = array();
                        $ext = pathinfo($value, PATHINFO_EXTENSION);

                        $update_post['media_uniqueID'] = $data_post['media_uniqueID'];
                        $update_post['file_type'] = $ext;
                        $update_post['file_name'] = $value;
                        $update_post['old_file_name'] = $filename_ar_old[$a];
                        $a++;

                        $media_file_uniqueID = rand(111, 999) . $data_post['media_file_uniqueID'] . rand(11, 99);
                        $primary_key = array('media_file_uniqueID' => $media_file_uniqueID);
                        MediaCategoryFile::updateOrCreate($primary_key, $update_post);
                    }
                    Session::flash('operationSucess','Your Medial File Successfully Added');
                    return Redirect::to('/admin/mediacategoryfileadd');
                }
            }else{
                Session::flash('operationFaild','Some thing went wrong!');
                return Redirect::to('/admin/mediacategoryfileadd');
            }
        }else{
            return Redirect::to('/admin');
        }
    }

    public  function deleteData()
    {
        $data_post = Input::all();

        // upload path
        $destinationPath = 'uploads/mediafile';
        $thumb_destinationPath = 'uploads/mediafile/thumb';

        $result_file_images = DB::table('media_category_file')->where('media_file_uniqueID','=',$data_post['media_file_uniqueID'])->first();
        if(!empty($result_file_images->file_name))
        {
            $file_name = explode(',',$result_file_images->file_name);
            foreach($file_name as $key => $value)
            {
                File::delete($destinationPath.'/'.$value);
                File::delete($thumb_destinationPath.'/'.$value);
            }
        }
        MediaCategoryFile::where('media_file_uniqueID','=',$data_post['media_file_uniqueID'])->delete();
    }


}
