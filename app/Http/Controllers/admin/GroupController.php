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
use App\Dealer;
use App\Discount;
use Datatables;
use URL;
 

class GroupController extends Controller
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
            return View::make('admin.group.groupList');
        }else{
            return Redirect::to('/');
        }

    }

    public function dataGroup()
    {
        $group = new Group;

        $data = Group::select('*')->orderBy('groupID','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('Group Name', function ($data) {
                if($data->name != ''){
                    return $data->name;
                }else{
                    return '---';
                }
            })

            ->addColumn('Discount', function ($data) {
				 
                    return $data->discount;
                
            })
			->addColumn('Total Dealer', function ($data) {
				$dealer=DB::table('dealer')->where('groupID','=',$data->groupID)->count();
                return $dealer;
            })

            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/edit-group', $data->groupID);
                $html = '';
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";
                $html.= '&nbsp;&nbsp;';
                $deleted = $data->groupID;
                $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted($deleted)\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-close\"></i></a>";
                return $html;
            })
            ->make(true);
    }

    public function addGroup()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['name'] = '';
            $data['discount'] = '';

            if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['name'] = $session_data['name'];
                $data['discount'] = $session_data['discount'];
            }

            return View::make('admin.group.groupAdd',$data);
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
            $group = new Group;

            $data_group = Input::all();
            $rules = array(
                'name'=>'required|min:2|max:100|Unique:group|not_in:0',
                 
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                if(isset($messages->get('name')[0])){
                    Session::flash('operationFaild','The Group Name has already been taken.');
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                }
                Session::set('session_data',$data_group);
                return Redirect::to('/admin/groupadd');
            }else
            {
                /*print_r($data_group);
                exit;*/

                $group->name = $data_group['name'];
                $group->discount = $data_group['discount'];
                $group->save();

                Session::forget('session_data');
                Session::save();

                Session::flash('operationSucess','Successfully Add group');
                return Redirect::to('/admin/grouplist');
                exit;
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function editGroup($id)
    {
        $result_data = array();
        $data = array();
        $group_id = $id;

        $group = new Group();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['name'] = '';

            $result_data = DB::table('group')->where('groupID','=',$group_id)->first();

            if(!empty($result_data)){
                $data['group'] = $result_data;
                return View::make('admin.group.groupEdit',$data);
            }else{
                return Redirect::to('/admin/grouplist');
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
            $group = new Group();

            $data_group = Input::all();
			//print_r($data_group);exit;
            $rules = array(
                'name'=>'required|min:2|max:100|not_in:0',
                 
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
                $update_array['name'] = $data_group['name'];
                $update_array['discount'] = $data_group['discount'];

                Session::forget('session_data');
                Session::save();

                $response = $group->where('groupID', $data_group['groupID'])->first();
                if (!empty($response)) {
                    $group->where('groupID', $data_group['groupID'])->update($update_array);
						Session::flash('operationSucess','Successfully Edit group');
						return Redirect::to('/admin/grouplist');
						exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/grouplist');
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
            $group = new Group();
            $data_group = Input::all();
            $groupID = $data_group['groupID'];
            $group->where('groupID', '=', $groupID)->delete();

            $dealer = new Dealer();
            $update_array['groupID'] = 0;
            $dealer->where('groupID', '=', $groupID)->update($update_array);

            $discount = new Discount();
            $discount->where('groupID', '=', $groupID)->delete();
        }
    }
}
