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
use App\Employee;
use Datatables;
use URL;
use XeroLaravel;
 

class ManageEmployeeController extends Controller
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
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            return View::make('admin.employeeManage.employeeList');
        }else{
            return Redirect::to('/');
        }

    }

    public function dataEmployee()
    {
        $employee = new Employee();

        $data = Employee::select('*')->orderBy('id','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('Employee', function ($data) {
                if($data->first_name != ''){
                    return $data->first_name.' '.$data->last_name;
                }else{
                    return '---';
                }
            })

            ->addColumn('Email', function ($data) {
                if($data->emailID != ''){
                    return $data->emailID;
                }else{
                    return '---';
                }
            })

            ->addColumn('Phone', function ($data) {
                if($data->phone != ''){
                    return $data->phone;
                }else{
                    return '---';
                }
            })

            ->addColumn('Active', function ($data) {
                if($data->status == 1){
                    return "<button class=\"btn btn-primary btn-xs\" type=\"button\">Active</button>";
                }else{
                    return "<button class=\"btn btn-warning btn-xs\" type=\"button\">InActive</button>";
                }
            })

            ->addColumn('Action', function ($data)
            {
                $html = '';

                $url = URL::to('admin/edit-employee', $data->employee_id);
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";

                $html.= '&nbsp;&nbsp;';
                $url = URL::to('admin/password-employee', $data->employee_id);
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Change Password\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-key\"></i></a>";

                $html.= '&nbsp;&nbsp;';
                $url = "javascript:void(0);";
                $ID = $data->employee_id;
                $html.= "<a href=\"$url\" id=\"no_$ID\" onclick=\"deleteEmployee('$ID')\"  data-toggle=\"tooltip\" title=\"Delete Employee\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash-o\"></i></a>";

                return $html;
            })
            ->make(true);
    }

    public function addEmployee()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $Unique='';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }
            $data['employee_id'] = $Unique;

            $data['first_name'] = '';
            $data['last_name'] = '';
            $data['address'] = '';
            $data['phone'] = '';

            if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['first_name'] = $session_data['first_name'];
                $data['last_name'] = $session_data['last_name'];
                $data['address'] = $session_data['address'];
                $data['phone'] = $session_data['phone'];
                $data['country'] = $session_data['country'];
            }

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');

            return View::make('admin.employeeManage.employeeAdd',$data);
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
            $employee = new Employee();

            $data_employee = Input::all();
			
			if(isset($data_employee['status'])){
				$data_employee['status']=1;
			}else{
				$data_employee['status']=0;	
			}
			
            $rules = array(
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
                'emailID'=>'required|email|Unique:staff|Unique:employee|Unique:dealer|not_in:0',
                'password'=>'required|min:6|max:12|not_in:0',
                'txt_confirmpassword'=>'required|same:password|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
                'phone'=>'required|min:6|max:30|not_in:0',
                'country'=>'required|min:1|max:30|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                if(isset($messages->get('emailID')[0])){
                    Session::flash('operationFaild','The email address has already been taken for Staff/Customer');
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                }
                Session::set('session_data',$data_employee);
                return Redirect::to('/admin/employeeadd');
            }else
            {
                $email = $data_employee['emailID'];
                $password = $data_employee['password'];
                $toemail= $email;

                $employee->employee_id = $data_employee['employee_id'];
                $employee->first_name = $data_employee['first_name'];
                $employee->last_name = $data_employee['last_name'];
                $employee->emailID = $data_employee['emailID'];
                $employee->password = Hash::make($data_employee['password']);
                $employee->address = $data_employee['address'];
                $employee->phone = $data_employee['phone'];
                $employee->country = $data_employee['country'];
                $employee->role = 'employee';
                $employee->status = $data_employee['status'];
                $employee->save();

                $employee_info = array(
                    "first_name" => $data_employee['first_name'],
                    "last_name" => $data_employee['last_name'],
                    "emailID" => $data_employee['emailID'],
                    "password" => $data_employee['password'],
                    "phone" => $data_employee['phone'],
                    "loginUrl" => URL::to('/'),
                );

                Mail::send('email_templates.employeeCreate',['data_employee' => $employee_info], function($message) use ($email)
                {
                    $message->to($email)->subject('CRM - Login Details');
                });

                Session::forget('session_data');
                Session::save();

                Session::flash('operationSucess','Successfully Add Employee');
                return Redirect::to('/admin/employeelist');
                exit;
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function editEmployee($id)
    {
        $result_data = array();
        $data = array();
        $employee_id = $id;

        $employee = new Employee();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['first_name'] = '';
            $data['last_name'] = '';
            $data['address'] = '';
            $data['phone'] = '';

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');

            $result_data = DB::table('employee')->where('employee_id','=',$employee_id)->first();

            Session::forget('session_data');
            Session::save();


            if(!empty($result_data)){
                $data['employee'] = $result_data;
                return View::make('admin.employeeManage.employeeEdit',$data);
            }else{
                return Redirect::to('/admin/employeelist');
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
            $employee = new Employee();

            $data_employee = Input::all();
			if(isset($data_employee['status'])){
				$data_employee['status']=1;
			}else{
				$data_employee['status']=0;	
			}
				
            $rules = array(
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
                'phone'=>'required|min:6|max:30|not_in:0',
                'country'=>'required|min:1|max:30|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$data_employee);
                return Redirect::to('/admin/employeeadd');
            }else
            {
                $update_array = array();
				
				

                $update_array['employee_id'] = $data_employee['employee_id'];
                $update_array['first_name'] = $data_employee['first_name'];
                $update_array['last_name'] = $data_employee['last_name'];
                $update_array['address'] = $data_employee['address'];
                $update_array['phone'] = $data_employee['phone'];
                $update_array['country'] = $data_employee['country'];
                $update_array['role'] = 'employee';
                $update_array['status'] = $data_employee['status'];

                Session::forget('session_data');
                Session::save();

                $response = $employee->where('id', $data_employee['id'])->first();
                if (!empty($response)) {
                    $employee->where('id', $data_employee['id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Edit Employee');
                    return Redirect::to('/admin/employeelist');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/employeelist');
                    exit;
                }
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function passwordEmployee($id)
    {
        $result_data = array();
        $data = array();
        $employee_id = $id;

        $employee = new Employee();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $result_data = DB::table('employee')->where('employee_id','=',$employee_id)->first();

            if(!empty($result_data)){
                $data['employee'] = $result_data;
                return View::make('admin.employeeManage.employeePassword',$data);
            }else{
                return Redirect::to('/admin/employeelist');
            }
        }else{
            return Redirect::to('/');
        }
    }


    public function updatePassworddata()
    {
        $id = 0;
        $sessionData = Session::get('adminLog');
        $id = $sessionData['adminID'];
        if (isset($id) && ($id != 0))
        {
            $employee = new Employee();


            $data_employee = Input::all();
            $rules = array(
                'password'=>'required|min:6|max:12|not_in:0',
                'txt_confirmpassword'=>'required|same:password|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {

                $messages = $validator->messages();
                Session::flash('operationFaild', 'New Password and Confirm Password not match');
                Session::set('session_data', $data_employee);
                return Redirect::to('/admin/employeelist');
            } else {

                $update_array = array();
                $update_array['password'] = Hash::make($data_employee['password']);

                $result_data = DB::table('employee')->where('id','=',$data_employee['id'])->first();

                $response = $employee->where('id', $data_employee['id'])->first();
                if (!empty($response))
                {
                    $employee->where('id', $data_employee['id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Password Change');


                    $email = '';
                    $info = array(
                        "first_name" => $result_data->first_name,
                        "last_name" =>  $result_data->last_name,
                        "emailID" =>    $result_data->emailID,
                        "password" =>   $data_employee['password'],
                        "loginUrl" => URL::to('/')
                    );

                    $email = $result_data->emailID;

                    Mail::send('email_templates.changePassword',['data_info' => $info], function($message) use ($email)
                    {
                        $message->to($email)->subject('CRM Employee - New Password Details');
                    });

                    return Redirect::to('/admin/employeelist');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/employeelist');
                    exit;
                }
            }

        }
    }

    public function  deleteData()
    {
        $id = 0;
        $sessionData = Session::get('adminLog');
        $id = $sessionData['adminID'];
        if (isset($id) && ($id != 0))
        {
            $employee = new Employee();
            $data_post = Input::all();
            $employee_id = $data_post['from_employee_data'];
            $employee->where('employee_id', '=', $employee_id)->delete();
        }
    }

}
