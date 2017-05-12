<?php

namespace App\Http\Controllers\staff;

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
use App\Staff;
use App\Leadreport;
use App\Task;
use App\SpecialOrder;
use App\Dealer;
use App\Employee;
use XeroLaravel;
use Cart;
use Artisan;
use Cache;
use App\Http\Controllers\staff\StaffCalenderController;
use App\Http\Controllers\logdata\LogController;

class StaffController extends Controller
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
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];

        if(isset($id) && ($id != 0))
        {

            /*Total Lead Report*/
            $lead_ar = array('staff_id' => $id, 'date_create' => $today);
            $data['todayLeads'] = $leadreport->where($lead_ar)->count();
            $data['totalLeads'] = $leadreport->where('staff_id','=',$id)->count();


            /*Total Task*/
            $taks_ar = array('staff_id' => $id, 'assign_date' => $today);
            /*$data['todayTask'] = $task->where($taks_ar)->count();
            $data['totalTask'] = $task->where('staff_id','=',$id)->count();*/
            $taks_ar = array('assign_date' => $today);
            $data['todayTask'] = $task->where('task_assign','=',$sessionData['unique_ID'])->where('assign_date','like',$today.'%')->orWhere('completion_date','like',$today.'%')->count();
            $data['totalTask'] = $task->count();


            /*Total Order*/
            $today_orderCount= DB :: table('product_order')
                ->where('created_at','like',date('Y-m-d').'%')
                ->where('deleted_at','=',NULL)->count();
            $data['todayOrder'] = $today_orderCount;
            $total_orderCount= DB :: table('product_order')->where('deleted_at','=',NULL)->count();
            $data['totalOrder'] = $total_orderCount;

            /*Total SpecialOrder*/
            $speacial_ar = array('today_date' => $today);
            $data['todaySpecialOrder'] = $specialOrder->where('is_noramlOrder','=','0')->where($speacial_ar)->count();
            $data['totalSpecialOrder'] = $specialOrder->where('is_noramlOrder','=','0')->count();

            $event = (new StaffCalenderController)->calenderData();
            $data['event'] = $event;

            (new StaffCalenderController)->today_task();

            $data['task_data'] =  (new StaffCalenderController)->calenderDragData();

           return View::make('staff.dashboard',$data);
        }else{
            return Redirect::to('/');
        }
    }

	public function loginform(Request $request)
    {
		if( isset($request) )
        {
            $emailID = $request->input('emailID');
            $password = $request->input('password');
			$result= DB::table('staff')->where('emailID',$emailID)->where('status',1)->first();
			if(count($result) != 0){
				//echo $result->password;
				if (Hash::check($request->input('password'), $result->password))
                {
                    $id = $result->id;
					$userData=array(
						'adminID'=>$result->id,
						'unique_ID'=>$result->staff_id,
                        'first_name'=>$result->first_name,
                        'last_name'=>$result->last_name,
						'emailID'=>$result->emailID,
						'role'=>$result->role
					);
					\Session::set('adminLog' , $userData);
                    Session::save();

                    \Session::set('staff_ID' , $result->staff_id);
                    Session::save();

                    $description = $result->first_name.' '.$result->last_name.' was login';
                    $logdata = array();
                    $logdata['role'] = 'staff';
                    $logdata['role_id'] = $result->id;
                    $logdata['operation'] = 'Login';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    $result_logdata = (new LogController)->index($logdata);

                    return Redirect::to('/staff/dashboard');
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
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];

        $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Logout';
        $logdata = array();
        $logdata['role'] = 'staff';
        $logdata['role_id'] = $id;
        $logdata['operation'] = 'Logout';
        $logdata['description'] = $description;
        $logdata['role_date'] = date('Y-m-d');

        $result_logdata = (new LogController)->index($logdata);

        \Session::forget('adminLog');
        \Session::save();
        Cache::flush();
        $exitCode = Artisan::call('cache:clear');
        return Redirect::to('/');
	}

	public function forgotPasswordStaff(Request $request)
    {
        $post=$request->all();
        if(!empty($post))
        {
            $email= $post['emailID'];
            $qryPasswordUpdate= DB::table('staff')->where('emailID',$email)->first();
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $password = '';

            for ($i = 0; $i < 10; $i++) {
                $password .= $characters[rand(0, strlen($characters) - 1)];
            }

			if(!empty($qryPasswordUpdate->id) && $qryPasswordUpdate->id > 0)
            {
				$getUserFirstName= DB::table('staff')->where('id',$qryPasswordUpdate->id)->first();
				$updatePassword= array(
					'password'=>Hash::make($password)
				);
				$qryUpdatePassword= DB::table('staff')->where('id','=',$qryPasswordUpdate->id)->update($updatePassword);
				if($qryUpdatePassword>0){
					 $data_user_password_mail =array(
                        'first_name' => $getUserFirstName->first_name,
                        'password' =>  $password,
                        'email' => $qryPasswordUpdate->emailID,
                        'loginUrl' => URL::to('/'),
					 );
					Mail::send('email_templates.staffForgotPassword',['data_user_password_mail'=>$data_user_password_mail], function($message)use ($email)
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
            return Redirect::to('/staff/profiledata/');
		}
	}

    public function profile()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data = array();

            $data['staffName']= '';
            $data['companyName']= '';
            $data['firstname']= '';
            $data['lastname']= '';
            $data['phone']= '';
            $data['address']= '';
            $data['city']= '';
            $data['state']= '';
            $data['brandNM']= '';
            $data['staff_country']= '';

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');
            $data['country_A'] = $data['country'];


            $data['emailID']= '';
            $cavatar = 'assets/img/placeholder300x300.png';

            $userdata = DB::table('staff')->where('id','=',$id)->first();
            if(!empty($userdata))
            {

                $data['staffName']= $userdata->first_name.'&nbsp;'.$userdata->last_name;
                $data['emailID']= $userdata->emailID;
                $data['staff_id']= $userdata->staff_id;
                $data['firstname']= $userdata->first_name;
                $data['lastname']= $userdata->last_name;
                $data['phone']= $userdata->phone;
                $data['address']= $userdata->address;
                $data['staff_country']= $userdata->country;



                if(!empty($userdata->staffAvatar)){
                    $cavatar='uploads/staff/'.$userdata->staffAvatar;
                } else{
                    $cavatar='assets/img/placeholder300x300.png';
                }
            }
            $data['staffAvatart'] = $cavatar;

            return View::make('staff/profile/profile',$data);
        }else{
            return Redirect::to('/');
        }
    }

    function updateData($update_array)
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $staff = new Staff();

        $response = $staff->where('id', $id)->first();
        if (!empty($response)) {
            session(['emailID' => $update_array['emailID']]);
            $staff->where('id', $id)->update($update_array);
            return true;
        } else {
            return false;
        }
    }

    public  function  profileUpdate(){

        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];

        $staff_session_emailID = $sessionData['emailID'];
        if(isset($id) && ($id != 0))
        {
            $staff = new Staff();
            $dealer = new Dealer();

            $data_staff = Input::all();
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
                return Redirect::to('/staff/profiledata');
            }else
            {
                $update_array = array();

                //Contact Details Update
                $update_array['first_name'] = $data_staff['first_name'];
                $update_array['last_name'] = $data_staff['last_name'];
                $update_array['emailID'] = $data_staff['emailID'];
                $update_array['phone'] = $data_staff['phone'];
                $update_array['address'] = $data_staff['address'];
                $update_array['country'] = $data_staff['country'];
                $update_array['role'] = 'staff';

                $return_data = false;

                if(($staff_session_emailID != '') && ($staff_session_emailID == $data_staff['emailID']))
                {
                    $return_data = $this->updateData($update_array);
                }else{

                    $check_email = $data_staff['emailID'];
                    $staff_email = $staff->where('emailID', $check_email)->count();
                    $dealer_email = $dealer->where('emailID', $check_email)->count();

                    if(($staff_email == 0) && ($dealer_email == 0)){
                        $return_data = $this->updateData($update_array);
                    }else{
                        Session::flash('operationFaild','The email address has already been taken for Staff/Customer');
                        return Redirect::to('/staff/profiledata');
                    }
                }

                if($return_data  == true){
                    Session::flash('operationSucess','Successfully Edit Your Profile');
                    return Redirect::to('/staff/profiledata');
                    exit;
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('staff/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/');
        }
    }

    public function  passwordUpdate(){
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0)) {
            $staff = new Staff;
            $data_staff = Input::all();
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
                return Redirect::to('/staff/profiledata');
            }else {
                $update_array = array();
                $update_array['password'] = Hash::make($data_staff['password']);

                $response = $staff->where('id', $id)->first();
                if (!empty($response))
                {
                    if( Hash::check($data_staff['opassword'],$response->password) ) {
                        $staff->where('id', $id)->update($update_array);
                        Session::flash('operationSucess', 'Successfully Update Your Password');
                        return Redirect::to('/staff/profiledata');
                        exit;
                    }else {
                        Session::flash('operationFaild','Old Password Not Matched');
                        return Redirect::to('staff/profiledata');
                        exit;
                    }

                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('staff/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/');
        }
    }

    public function updateStaffAvatar()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $file = array('image' => Input::file('staffAvatar'));
            $rules = array(
                'image' => 'required | mimes:jpeg,bmp,png | max:10000',
            );

            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {

                Session::flash('operationFaild','Invalid File type or Invalid File size !');
                return Redirect::to('/staff/profiledata');

            }else{

                if (Input::file('staffAvatar')->isValid())
                {
                    $destinationPath = 'uploads/staff'; // upload path
                    $oldAvatar=DB::table('staff')->where('id','=',$id)->first();
                    if(!empty($oldAvatar->staffAvatar)){
                        File::delete($destinationPath.'/'.$oldAvatar->staffAvatar);
                    }

                    $extension = Input::file('staffAvatar')->getClientOriginalExtension(); // getting image extension
                    $fileName = 'staffAvatar-'.rand(11111,99999).'.'.$extension; // renameing image
                    Input::file('staffAvatar')->move($destinationPath, $fileName); // uploading file to given path
                    // sending back with message

                    $avatarupdate=array(
                        'staffAvatar' => $fileName
                    );
                    $add= DB::table('staff')->where('id','=',$id)->update($avatarupdate);
                    Session::flash('operationSucess','Avatar uploaded Successfully !');

                }else{

                    Session::flash('operationFaild','Some thing went wrong.try again.');
                }
                return Redirect::to('/staff/profiledata');
            }
        }else{
            return Redirect::to('/');
        }
    }
}
