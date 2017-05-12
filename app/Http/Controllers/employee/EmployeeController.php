<?php

namespace App\Http\Controllers\employee;

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
use App\Employee;
use App\Leadreport;
use App\Task;
use App\SpecialOrder;
use App\Dealer;
use App\WarrantyProduct;
use App\WarrantyProductNote;
use XeroLaravel;
use Cart;
use Artisan;
use Cache;
use App\Http\Controllers\employee\EmployeeCalenderController;
use App\Http\Controllers\logdata\LogController;

class EmployeeController extends Controller
{
    public function index()
    {
        Cache::flush();
        $exitCode = Artisan::call('cache:clear');

        $leadreport = new Leadreport();
        $task = new Task();
        $specialOrder = new SpecialOrder();

        $dt = Carbon::now();
        $today = $dt->toDateString();
        $data = array();

        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        $employee_uniqueID = $sessionData['unique_ID'];

        if(isset($id) && ($id != 0))
        {

            /*Total Task*/
            $taks_ar = array('employee_id' => $id, 'assign_date' => $today);
            /*$data['todayTask'] = $task->where($taks_ar)->count();
            $data['totalTask'] = $task->where('employee_id','=',$id)->count();*/

            $data['todayTask'] = $task->where('task_assign','=',$employee_uniqueID)->where('assign_date','like',$today.'%')->orWhere('completion_date','like',$today.'%')->count();
            $data['totalTask'] = $task->where('task_assign','=',$employee_uniqueID)->count();

            $data['totalWarranty'] = WarrantyProduct::select('*')
                ->where('assign_role','=','employee')
                ->where('warranty_assign','=',$sessionData['unique_ID'])->count();


            $event = (new EmployeeCalenderController)->calenderData();
            $data['event'] = $event;

            (new EmployeeCalenderController)->today_task();

            $data['task_data'] =  (new EmployeeCalenderController)->calenderDragData();

            $data['sessionData'] =  $sessionData;

           return View::make('employee.dashboard',$data);
        }else{
            return Redirect::to('/');
        }
    }

	public function loginform(Request $request)
    {
		if( isset($request) ){
            $emailID = $request->input('emailID');
            $password = $request->input('password');
			$result= DB::table('employee')->where('emailID',$emailID)->where('status',1)->first();
			if(count($result) != 0){
				//echo $result->password;
				if (Hash::check($request->input('password'), $result->password))
                {
                    $id = $result->id;
					$userData=array(
						'employeeID'=>$result->id,
						'unique_ID'=>$result->employee_id,
                        'first_name'=>$result->first_name,
                        'last_name'=>$result->last_name,
						'emailID'=>$result->emailID,
						'role'=>$result->role
					);
					\Session::set('employeeLog' , $userData);
                    Session::save();

                    \Session::set('employee_ID' , $result->employee_id);
                    Session::save();

                    $description = $result->first_name.' '.$result->last_name.' was login';
                    $logdata = array();
                    $logdata['role'] = 'employee';
                    $logdata['role_id'] = $result->id;
                    $logdata['operation'] = 'Login';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    $result_logdata = (new LogController)->index($logdata);

                    return Redirect::to('/employee/dashboard');
				}else{
					Session::flash('operationFaild','Wrong Username or Password !');
					return Redirect::to('/');
				}
			}else{
				Session::flash('operationFaild','Wrong Username or Password !');
                return Redirect::to('/');
			}
        }else {	
           Session::flash('operationFaild','Wrong Username or Password !');
            return Redirect::to('/');
        } 

	}

	public function logout()
    {
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];

        $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Logout';
        $logdata = array();
        $logdata['role'] = 'employee';
        $logdata['role_id'] = $id;
        $logdata['operation'] = 'Logout';
        $logdata['description'] = $description;
        $logdata['role_date'] = date('Y-m-d');

        $result_logdata = (new LogController)->index($logdata);

        \Session::forget('employeeLog');
        \Session::save();
        Cache::flush();
        $exitCode = Artisan::call('cache:clear');
        return Redirect::to('/');
	}

	public function forgotPasswordemployee(Request $request)
    {
        $post=$request->all();
        if(!empty($post))
        {
            $email= $post['emailID'];
            $qryPasswordUpdate= DB::table('employee')->where('emailID',$email)->first();
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $password = '';

            for ($i = 0; $i < 10; $i++) {
                $password .= $characters[rand(0, strlen($characters) - 1)];
            }

			if(!empty($qryPasswordUpdate->id) && $qryPasswordUpdate->id > 0)
            {
				$getUserFirstName= DB::table('employee')->where('id',$qryPasswordUpdate->id)->first();
				$updatePassword= array(
					'password'=>Hash::make($password)
				);
				$qryUpdatePassword= DB::table('employee')->where('id','=',$qryPasswordUpdate->id)->update($updatePassword);
				if($qryUpdatePassword>0){
					 $data_user_password_mail =array(
                        'first_name' => $getUserFirstName->first_name,
                        'password' =>  $password,
                        'email' => $qryPasswordUpdate->emailID,
                        'loginUrl' => URL::to('/'),
					 );
					Mail::send('email_templates.employeeForgotPassword',['data_user_password_mail'=>$data_user_password_mail], function($message)use ($email)
					{
						$message->to($email)->subject('Recover Password!');
					});
					 
					 Session::flash('operationSucess','Your Temp. Password has been sent to your mail');
				}else{
					Session::flash('operationFaild','Some thing went wrong!');
				}
			}else{
				Session::flash('operationFaild','This user does not exist!');
			}
		}else{
			Session::flash('operationFaild','Some thing went wrong!');
            return Redirect::to('/employee/profiledata/');
		}
	}

    public function profile()
    {
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        if(isset($id) && ($id != 0))
        {
            $data = array();

            $data['employeeName']= '';
            $data['companyName']= '';
            $data['firstname']= '';
            $data['lastname']= '';
            $data['phone']= '';
            $data['address']= '';
            $data['city']= '';
            $data['state']= '';
            $data['brandNM']= '';
            $data['employee_country']= '';

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');
            $data['country_A'] = $data['country'];


            $data['emailID']= '';
            $cavatar = 'assets/img/placeholder300x300.png';

            $userdata = DB::table('employee')->where('id','=',$id)->first();
            if(!empty($userdata))
            {

                $data['employeeName']= $userdata->first_name.'&nbsp;'.$userdata->last_name;
                $data['emailID']= $userdata->emailID;
                $data['employee_id']= $userdata->employee_id;
                $data['firstname']= $userdata->first_name;
                $data['lastname']= $userdata->last_name;
                $data['phone']= $userdata->phone;
                $data['address']= $userdata->address;
                $data['employee_country']= $userdata->country;



                if(!empty($userdata->employeeAvatar)){
                    $cavatar='uploads/employee/'.$userdata->employeeAvatar;
                } else{
                    $cavatar='assets/img/placeholder300x300.png';
                }
            }
            $data['employeeAvatart'] = $cavatar;

            return View::make('employee/profile/profile',$data);
        }else{
            return Redirect::to('/');
        }
    }

    function updateData($update_array)
    {
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        $employee = new employee();

        $response = $employee->where('id', $id)->first();
        if (!empty($response)) {
            session(['emailID' => $update_array['emailID']]);
            $employee->where('id', $id)->update($update_array);
            return true;
        } else {
            return false;
        }
    }

    public  function  profileUpdate(){

        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];

        $employee_session_emailID = $sessionData['emailID'];
        if(isset($id) && ($id != 0))
        {
            $employee = new employee();
            $dealer = new Dealer();

            $data_employee = Input::all();
            $rules = array(
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
                'emailID'=>'required|email|not_in:0',
                'phone'=>'required|min:5|max:15|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
                'country'=>'required|min:1|max:30|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                return Redirect::to('/employee/profiledata');
            }else
            {
                $update_array = array();

                //Contact Details Update
                $update_array['first_name'] = $data_employee['first_name'];
                $update_array['last_name'] = $data_employee['last_name'];
                $update_array['emailID'] = $data_employee['emailID'];
                $update_array['phone'] = $data_employee['phone'];
                $update_array['address'] = $data_employee['address'];
                $update_array['country'] = $data_employee['country'];
                $update_array['role'] = 'employee';

                $return_data = false;

                if(($employee_session_emailID != '') && ($employee_session_emailID == $data_employee['emailID']))
                {
                    $return_data = $this->updateData($update_array);
                }else{

                    $check_email = $data_employee['emailID'];
                    $employee_email = $employee->where('emailID', $check_email)->count();
                    $dealer_email = $dealer->where('emailID', $check_email)->count();

                    if(($employee_email == 0) && ($dealer_email == 0)){
                        $return_data = $this->updateData($update_array);
                    }else{
                        Session::flash('operationFaild','The email address has already been taken for employee/Customer');
                        return Redirect::to('/employee/profiledata');
                    }
                }

                if($return_data  == true){
                    Session::flash('operationSucess','Successfully Edit Your Profile');
                    return Redirect::to('/employee/profiledata');
                    exit;
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('employee/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/');
        }
    }

    public function  passwordUpdate(){
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        if(isset($id) && ($id != 0)) {
            $employee = new employee;
            $data_employee = Input::all();
            $rules = array(
                'opassword'=>'required|min:3|max:30|not_in:0',
                'password'=>'required|min:3|max:30|not_in:0',
                'cpassword'=>'required|same:password|not_in:0'
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {
                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                return Redirect::to('/employee/profiledata');
            }else {
                $update_array = array();
                $update_array['password'] = Hash::make($data_employee['password']);

                $response = $employee->where('id', $id)->first();
                if (!empty($response))
                {
                    if( Hash::check($data_employee['opassword'],$response->password) ) {
                        $employee->where('id', $id)->update($update_array);
                        Session::flash('operationSucess', 'Successfully Update Your Password');
                        return Redirect::to('/employee/profiledata');
                        exit;
                    }else {
                        Session::flash('operationFaild','Old Password Not Matched');
                        return Redirect::to('employee/profiledata');
                        exit;
                    }

                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('employee/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/');
        }
    }

    public function updateemployeeAvatar()
    {
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        if(isset($id) && ($id != 0))
        {
            $file = array('image' => Input::file('employeeAvatar'));
            $rules = array(
                'image' => 'required | mimes:jpeg,bmp,png | max:10000',
            );

            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {

                Session::flash('operationFaild','Invalid File type or Invalid File size !');
                return Redirect::to('/employee/profiledata');

            }else{

                if (Input::file('employeeAvatar')->isValid())
                {
                    $destinationPath = 'uploads/employee'; // upload path
                    $oldAvatar=DB::table('employee')->where('id','=',$id)->first();
                    if(!empty($oldAvatar->employeeAvatar)){
                        File::delete($destinationPath.'/'.$oldAvatar->employeeAvatar);
                    }

                    $extension = Input::file('employeeAvatar')->getClientOriginalExtension(); // getting image extension
                    $fileName = 'employeeAvatar-'.rand(11111,99999).'.'.$extension; // renameing image
                    Input::file('employeeAvatar')->move($destinationPath, $fileName); // uploading file to given path
                    // sending back with message

                    $avatarupdate=array(
                        'employeeAvatar' => $fileName
                    );
                    $add= DB::table('employee')->where('id','=',$id)->update($avatarupdate);
                    Session::flash('operationSucess','Avatar uploaded Successfully !');

                }else{

                    Session::flash('operationFaild','Some thing went wrong.try again.');
                }
                return Redirect::to('/employee/profiledata');
            }
        }else{
            return Redirect::to('/');
        }
    }
}
