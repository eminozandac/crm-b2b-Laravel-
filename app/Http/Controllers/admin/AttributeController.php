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
use View;
use Mail;
use Form;
use File;
use Cache;
use Validator;
use Carbon\Carbon;
use App\Group;
use App\Attribute;
use Datatables;
use URL;
 

class AttributeController extends Controller
{
    public function index()
    {
        Session::forget('session_data');
        Session::save();
        
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            return View::make('admin.attribute.attributeList');
        }else{
            return Redirect::to('/');
        }

    }

    public function dataattribute()
    {
        $attribute = new Attribute;

        $data = Attribute::select('*')->orderBy('attributeID','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('attributeName', function ($data) {
                if($data->attributeName != ''){
                    return $data->attributeName;
                }else{
                    return '---';
                }
            })


            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/editAttribute', $data->attributeID);
                $html = '';
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";
                $html.= '&nbsp;&nbsp;';
                $deleted = $data->attributeID;
                $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted($deleted)\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-close\"></i></a>";
                return $html;
            })
            ->make(true);
    }

    public function addattribute()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['attributeName'] = '';

            if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['attributeName'] = $session_data['attributeName'];
            }

            return View::make('admin.attribute.attributeAdd',$data);
        }else{
            return Redirect::to('/');
        }

    }

    public function  saveData(Request $request)
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		$post=$request->all();
        if(isset($id) && ($id != 0) && isset($post) && !empty($post))
        {
            $attribute = new Attribute;
			$check=$attribute->where('attributeName','=',$post['attributeName'])->first();
			if(!empty($check)){
				Session::flash('operationFaild','attribute already exist');
				return Redirect::to('/admin/attributeList');
				exit;
			}else{
				$arrayatr=array(
					'attributeName'=>$post['attributeName'],
					'created_at'=>Carbon::now()->toDateTimeString()
				);
				$addattr=DB::table('attribute')->insert($arrayatr);
				
			Session::flash('operationSucess','Successfully Add attribute');
			}
           

			return Redirect::to('/admin/attributeList');
			exit;
            

        }else{
            return Redirect::to('/');
        }
    }

    public function editAttribute($id)
    {
        $result_data = array();
        $data = array();
        $attribute_id = $id;

        $attribute = new Attribute;
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['name'] = '';

            $result_data = DB::table('attribute')->where('attributeID','=',$attribute_id)->first();

            if(!empty($result_data)){
                $data['attribute'] = $result_data;
                return View::make('admin.attribute.attributeEdit',$data);
            }else{
                return Redirect::to('/admin/attributeList');
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
            $attribute = new Attribute;

            $data_group = Input::all();
            $rules = array(
                'attributeName'=>'required|min:3|max:12|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$data_group);
                return Redirect::to('/admin/groupadd');
            }else
            {
                $update_array = array();
                $update_array['attributeName'] = $data_group['attributeName'];

                Session::forget('session_data');
                Session::save();

                $response = $attribute->where('attributeID', $data_group['attributeID'])->first();
                if (!empty($response)) {
                    $attribute->where('attributeID', $data_group['attributeID'])->update($update_array);
                    Session::flash('operationSucess','Successfully Edit attribute');
                    return Redirect::to('/admin/attributeList');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/attributeList');
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
        if (isset($id) && ($id != 0)) {
          $attribute = new Attribute;
            $data_group = Input::all();
            $attributeID = $data_group['attributeID'];
            $attribute->where('attributeID', '=', $attributeID)->delete();
        }
    }
}
