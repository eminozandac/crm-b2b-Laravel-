<?php

namespace App\Http\Controllers\customer;

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
use XeroLaravel;
use Cart;
use Artisan;
use Cache;
use App\Customer;
use App\Dealer;
use App\Staff;
use App\WarrantyProduct;
use App\Http\Controllers\logdata\LogController;

class CustomerController extends Controller
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
        Cache::flush();
        $exitCode = Artisan::call('cache:clear');

        $dt = Carbon::now();
        $today = $dt->toDateString();
        $data = array();

        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            /*$result_warrantyList = WarrantyProduct::select('*')->where('user_id','=',$id)->where('role','=',$sessionData['role'])->orderBy('id','DESC')->get();
            $data['warrantyList'] = $result_warrantyList;
            return View::make('warranty/customer/customerWarrantyList',$data);*/

            $data['sessionData'] = $sessionData;

            return View::make('customer/dashboard',$data);
        }else{
            return View::make('customer/index',$data);
        }
    }

    public function registerForm()
    {
        Cache::flush();
        $exitCode = Artisan::call('cache:clear');

        $dt = Carbon::now();
        $today = $dt->toDateString();
        $data = array();

        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $result_warrantyList = WarrantyProduct::select('*')->where('user_id','=',$id)->where('role','=',$sessionData['role'])->orderBy('id','DESC')->get();
            $data['warrantyList'] = $result_warrantyList;
            return View::make('warranty/customer/customerWarrantyList',$data);
        }else{
            return View::make('customer/registration',$data);
        }
    }

    public  function  registerData()
    {
        Cache::flush();
        $exitCode = Artisan::call('cache:clear');

        $dt = Carbon::now();
        $today = $dt->toDateString();
        $data = array();

        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $result_warrantyList = WarrantyProduct::select('*')->where('user_id','=',$id)->where('role','=',$sessionData['role'])->orderBy('id','DESC')->get();
            $data['warrantyList'] = $result_warrantyList;
            return View::make('warranty/customer/customerWarrantyList',$data);
        }else{
            $data_post = Input::all();
            $customer_id = '';
            $Unique='';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }
            $customer_id = $Unique;
            if(!empty($data_post))
            {
                $rules = array(
                    'first_name'=>'required|min:3|max:30|not_in:0',
                    'last_name'=>'required|min:3|max:30|not_in:0',
                    'emailID'=>'required|email|Unique:customer|Unique:dealer|Unique:staff|Unique:admin|not_in:0',
                    'password'=>'required|min:6|max:12|not_in:0',
                    'confirm_password'=>'required|same:password|not_in:0',
                );

                $validator = Validator::make(Input::all(), $rules);

                if ($validator->fails())
                {
                    $messages = $validator->messages();
                    if(isset($messages->get('emailID')[0])){
                        Session::flash('operationFaild', 'Email ID All ready exists..');
                    }else{
                        Session::flash('operationFaild','Something is Wrong');
                    }
                    return Redirect::to('/customer/register');
                    exit;
                }else{

                    $update_post = array();
                    $update_post['first_name'] = $data_post['first_name'];
                    $update_post['last_name'] = $data_post['last_name'];
                    $update_post['emailID'] = $data_post['emailID'];
                    $update_post['password'] = Hash::make($data_post['password']);
                    $update_post['status'] = 1;
                    $update_post['role'] = 'customer';
                    $primary_key = array(
                        'customer_id' => $customer_id
                    );
                    Customer::updateOrCreate($primary_key, $update_post);

                    Session::flash('operationSucess', 'Successfully Your Registration..Please Login');
                    return Redirect::to('/customer/login');
                    exit;
                }
            }else{
                Session::flash('operationFaild','Something is Wrong');
                return Redirect::to('/customer/register');
                exit;
            }
        }
    }

	public function loginform(Request $request)
    {
		if( isset($request) ){
            $emailID = $request->input('emailID');
            $password = $request->input('password');
			$result= DB::table('customer')
                ->where('emailID',$emailID)
                ->orWhere('phone',$emailID)
                ->where('status',1)->first();
			if(count($result) != 0){
				//echo $result->password;
				if (Hash::check($request->input('password'), $result->password))
                {
                    $id = $result->id;
					$userData=array(
						'customerID'=>$result->id,
						'unique_ID'=>$result->customer_id,
                        'first_name'=>$result->first_name,
                        'last_name'=>$result->last_name,
						'emailID'=>$result->emailID,
						'role'=>$result->role
					);

                    \Session::set('customerLog' , $userData);
                    Session::save();

                    $description = $result->first_name.' '.$result->last_name.' was login';
                    $logdata = array();
                    $logdata['role'] = 'customer';
                    $logdata['role_id'] = $result->id;
                    $logdata['operation'] = 'Login';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    $result_logdata = (new LogController)->index($logdata);

                    return Redirect::to('/customer/dashboard');
				}else{
					Session::flash('operationFaild','Wrong Username or Password !');
					return Redirect::to('/customer');
				}
			}else{
				Session::flash('operationFaild','Wrong Username or Password !');
                return Redirect::to('/customer');
			}
        }else {	
           Session::flash('operationFaild','Wrong Username or Password !');
            return Redirect::to('/customer');
        } 

	}

	public function logout()
    {
        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];

        $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Logout';
        $logdata = array();
        $logdata['role'] = 'customer';
        $logdata['role_id'] = $id;
        $logdata['operation'] = 'Logout';
        $logdata['description'] = $description;
        $logdata['role_date'] = date('Y-m-d');

        \Session::forget('customerLog');
        \Session::save();
        Cache::flush();
        $exitCode = Artisan::call('cache:clear');
        return Redirect::to('/customer');
	}

	public function forgotPasswordCustomer(Request $request)
    {
        $post=$request->all();
        if(!empty($post))
        {
            $email= $post['emailID'];
            $qryPasswordUpdate= DB::table('customer')->where('emailID',$email)->first();
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $password = '';

            for ($i = 0; $i < 10; $i++) {
                $password .= $characters[rand(0, strlen($characters) - 1)];
            }

			if(!empty($qryPasswordUpdate->id) && $qryPasswordUpdate->id > 0)
            {
				$getUserFirstName= DB::table('customer')->where('id',$qryPasswordUpdate->id)->first();
				$updatePassword= array(
					'password'=>Hash::make($password)
				);
				$qryUpdatePassword= DB::table('customer')->where('id','=',$qryPasswordUpdate->id)->update($updatePassword);
				if($qryUpdatePassword>0){
					 $data_user_password_mail =array(
						'first_name' => $getUserFirstName->first_name,
						'password' =>  $password,
						'email' => $qryPasswordUpdate->emailID,
                         'loginUrl' => URL::to('/'),
					 );
					Mail::send('email_templates.customerForgotPassword',['data_user_password_mail'=>$data_user_password_mail], function($message)use ($email)
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
            return Redirect::to('/customer/profiledata/');
		}
	}

    public function profile()
    {
        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];
        if(isset($id) && ($id != 0))
        {
            $data = array();

            $data['customerName']= '';
            $data['companyName']= '';
            $data['firstname']= '';
            $data['lastname']= '';
            $data['phone']= '';
            $data['address']= '';
            $data['city']= '';
            $data['state']= '';
            $data['brandNM']= '';
            $data['customer_country']= '';

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');
            $data['country_A'] = $data['country'];


            $data['emailID']= '';
            $cavatar = 'assets/img/placeholder300x300.png';

            $userdata = DB::table('customer')->where('id','=',$id)->first();
            if(!empty($userdata))
            {

                $data['customerName']= $userdata->first_name.'&nbsp;'.$userdata->last_name;
                $data['emailID']= $userdata->emailID;
                $data['customer_id']= $userdata->customer_id;
                $data['firstname']= $userdata->first_name;
                $data['lastname']= $userdata->last_name;
                $data['phone']= $userdata->phone;
                $data['address']= $userdata->address;
                $data['customer_country']= $userdata->country;



                if(!empty($userdata->customerAvatar)){
                    $cavatar='uploads/customer/'.$userdata->customerAvatar;
                } else{
                    $cavatar='assets/img/placeholder300x300.png';
                }
            }
            $data['customerAvatar'] = $cavatar;

            return View::make('customer/profile/profile',$data);
        }else{
            return Redirect::to('/customer');
        }
    }

    function updateData($update_array)
    {
        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];
        $customer = new Customer();

        $response = $customer->where('id', $id)->first();
        if (!empty($response)) {
            session(['emailID' => $update_array['emailID']]);
            $customer->where('id', $id)->update($update_array);
            return true;
        } else {
            return false;
        }
    }

    public  function  profileUpdate(){

        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];

        $customer_session_emailID = $sessionData['emailID'];
        if(isset($id) && ($id != 0))
        {
            $customer = new Customer();
            $dealer = new Dealer();
            $staff = new Staff();

            $data_customer = Input::all();
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
                return Redirect::to('/customer/profiledata');
            }else
            {
                $update_array = array();

                //Contact Details Update
                $update_array['first_name'] = $data_customer['first_name'];
                $update_array['last_name'] = $data_customer['last_name'];
                $update_array['emailID'] = $data_customer['emailID'];
                $update_array['phone'] = $data_customer['phone'];
                $update_array['address'] = $data_customer['address'];
                $update_array['country'] = $data_customer['country'];
                $update_array['role'] = 'customer';

                $return_data = false;

                if(($customer_session_emailID != '') && ($customer_session_emailID == $data_customer['emailID']))
                {
                    $return_data = $this->updateData($update_array);
                }else{

                    $check_email = $data_customer['emailID'];
                    $customer_email = $customer->where('emailID', $check_email)->count();
                    $dealer_email = $dealer->where('emailID', $check_email)->count();
                    $staff_email = $staff->where('emailID', $check_email)->count();

                    if(($dealer_email == 0) || ($staff_email == 0)){
                        $return_data = $this->updateData($update_array);
                    }else{
                        Session::flash('operationFaild','The email address has already been taken..');
                        return Redirect::to('/customer/profiledata');
                    }
                }

                if($return_data  == true){
                    Session::flash('operationSucess','Successfully Edit Your Profile');
                    return Redirect::to('/customer/profiledata');
                    exit;
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('customer/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/customer');
        }
    }

    public function  passwordUpdate(){
        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];
        if(isset($id) && ($id != 0)) {
            $customer = new Customer;
            $data_customer = Input::all();
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
                return Redirect::to('/customer/profiledata');
            }else {
                $update_array = array();
                $update_array['password'] = Hash::make($data_customer['password']);

                $response = $customer->where('id', $id)->first();
                if (!empty($response))
                {
                    if( Hash::check($data_customer['opassword'],$response->password) ) {
                        $customer->where('id', $id)->update($update_array);
                        Session::flash('operationSucess', 'Successfully Update Your Password');
                        return Redirect::to('/customer/profiledata');
                        exit;
                    }else {
                        Session::flash('operationFaild','Old Password Not Matched');
                        return Redirect::to('customer/profiledata');
                        exit;
                    }

                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('customer/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/customer');
        }
    }

    public function updateCustomerAvatar()
    {
        $id = 0;
        $sessionData=Session::get('customerLog');
        $id = $sessionData['customerID'];
        if(isset($id) && ($id != 0))
        {
            $file = array('image' => Input::file('customerAvatar'));
            $rules = array(
                'image' => 'required | mimes:jpeg,bmp,png | max:10000',
            );

            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {

                Session::flash('operationFaild','Invalid File type or Invalid File size !');
                return Redirect::to('/customer/profiledata');

            }else{

                if (Input::file('customerAvatar')->isValid())
                {
                    $destinationPath = 'uploads/customer'; // upload path
                    $oldAvatar=DB::table('customer')->where('id','=',$id)->first();
                    if(!empty($oldAvatar->customerAvatar)){
                        File::delete($destinationPath.'/'.$oldAvatar->customerAvatar);
                    }

                    $extension = Input::file('customerAvatar')->getClientOriginalExtension(); // getting image extension
                    $fileName = 'customerAvatar-'.rand(11111,99999).'.'.$extension; // renameing image
                    Input::file('customerAvatar')->move($destinationPath, $fileName); // uploading file to given path
                    // sending back with message

                    $avatarupdate=array(
                        'customerAvatar' => $fileName
                    );
                    $add= DB::table('customer')->where('id','=',$id)->update($avatarupdate);
                    Session::flash('operationSucess','Avatar uploaded Successfully !');

                }else{

                    Session::flash('operationFaild','Some thing went wrong.try again.');
                }
                return Redirect::to('/customer/profiledata');
            }
        }else{
            return Redirect::to('/customer');
        }
    }
}
