<?php

namespace App\Http\Controllers\dealer;

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
use App\Dealer;
use App\Staff;

use Cart;
use App\DealerTask;
use App\Http\Controllers\dealer\DealerCalenderController;

 

class DealerController extends Controller
{

    function  dashboardData()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $data = array();

        $data['sessionData'] = $sessionData;

        $cartitem=Cart::items();
        $cart=$cartitem->count();
		
		\Session::set('cartcount' , $cart);
        Session::save();
		
        $data['cart'] = $cart;
        $data['dealerID'] = $id;

        //admin Order Note
        $data['note'] = '';

        $result =  DB::table('admin_order_notes')->join('product_order', 'product_order.orderID', '=', 'admin_order_notes.orderID')
            ->select('admin_order_notes.admin_order_notesID', 'admin_order_notes.orderID', 'admin_order_notes.product_id', 'admin_order_notes.name', 'admin_order_notes.description', 'admin_order_notes.created_at')
            ->where('product_order.dealerID', '=', $id)
            ->orderBy('admin_order_notes.created_at','desc')
            ->limit(8)
            ->get();

        $data['note'] = $result;
        return $data;

    }

    public function dashboard()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $data = $this->dashboardData();

            $event = (new DealerCalenderController)->calenderData();
            $data['event'] = $event;

            (new DealerCalenderController)->today_task();

            $data['task_data'] =  (new DealerCalenderController)->calenderDragData();

            return View::make('dealer/dashboard',$data);
            /*return View::make('dealer/dashboard')->with('cart',$cart)->with('dealerID', $id);*/
        }else{
            return Redirect::to('/');
        }
    }

    public function index()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $data = $this->dashboardData();

            $event = (new DealerCalenderController)->calenderData();
            $data['event'] = $event;

            (new DealerCalenderController)->today_task();

            $data['task_data'] =  (new DealerCalenderController)->calenderDragData();

            
            return View::make('dealer/dashboard',$data);
        }else{
            return View::make('dealer/index',$data);
        }
    }

	public function loginform(Request $request)
    {
		if( isset($request) ){
            $emailID = $request->input('emailID');
            $password = $request->input('password');
			$result= DB::table('dealer')->where('emailID',$emailID)->first();
			if(count($result) != 0){
				//echo $result->password;
				if (Hash::check($request->input('password'), $result->password)) {
					$userData=array(
						'dealerID'=>$result->id,
                        'company_name'=>$result->company_name,
						'first_name'=>$result->first_name,
                        'last_name'=>$result->last_name,
						'emailID'=>$result->emailID,
						'role'=>$result->role
					);
					\Session::set('dealerLog' , $userData);
                    Session::save();
					 return Redirect::to('/dealer/dashboard');
				}else{
					Session::flash('operationFaild','Wrong Username or Password !');
					return Redirect::to('/');
				}
			}else{
				Session::flash('operationFaild','Wrong Username or Password !');
			}
        }else {	
           Session::flash('operationFaild','Wrong Username or Password !');
        } 
		   return Redirect::to('/');
	}

	public function logout(){
        \Session::forget('dealerLog');
        \Session::save();
        return Redirect::to('/');
	}


	public function forgotPasswordAdmin(Request $request){
		$post=$request->all();
			if(!empty($post)){
			$email= $post['emailID'];
			$qryPasswordUpdate= DB::table('dealer')->where('emailID',$email)->first();
			 $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
			 $password = '';
			 for ($i = 0; $i < 10; $i++) {
				  $password .= $characters[rand(0, strlen($characters) - 1)];
			 }
			if(!empty($qryPasswordUpdate->id) && $qryPasswordUpdate->id > 0){
				$getUserFirstName= DB::table('dealer')->where('id',$qryPasswordUpdate->id)->first();
				$updatePassword= array(
					'password'=>Hash::make($password)
				);
				$qryUpdatePassword= DB::table('dealer')->where('id','=',$qryPasswordUpdate->id)->update($updatePassword);
				if($qryUpdatePassword>0){
					 $data_user_password_mail =array(
                        'first_name' => $getUserFirstName->first_name,
                        'password' =>  $password,
                        'email' => $qryPasswordUpdate->emailID,
                        'loginUrl' => URL::to('/'),
					 );
					Mail::send('email_templates.adminForgotPassword',['data_user_password_mail'=>$data_user_password_mail], function($message)use ($email)
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
		}
		return Redirect::to('/');
	}

    public function profile()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $data = array();

            $data['dealerName']= '';
            $data['companyName']= '';
            $data['firstname']= '';
            $data['lastname']= '';
            $data['phone']= '';
            $data['address']= '';
            $data['brandNM']= '';
            $data['shipping_address']= '';
          
            $data['emailID']= '';
            $cavatar = 'assets/img/placeholder300x300.png';

            $userdata = DB::table('dealer')->where('id','=',$id)->first();
            if(!empty($userdata))
            {

                $data['dealerName']= $userdata->first_name.'&nbsp;'.$userdata->last_name;
                $data['companyName']= $userdata->company_name;
                $data['emailID']= $userdata->emailID;
                $data['firstname']= $userdata->first_name;
                $data['lastname']= $userdata->last_name;
                $data['phone']= $userdata->phone;
                $data['address']= $userdata->address;
                $data['shipping_address']= $userdata->shipping_address;
                $data['contactperson1']= $userdata->contactperson1;
                $data['contactperson2']= $userdata->contactperson2;
                $data['contactperson3']= $userdata->contactperson3;
            
                $brandNm  = '';
                if($userdata->categoryID != ''){

                    $brand_nm = array();
                    $categoryID = explode(',',$userdata->categoryID);
                    if(!empty($categoryID))
                    {
                        foreach($categoryID as $key => $value){
                            $result_brand_nm = DB::table('category')->where('id','=',$value)->first();
                            $brand_nm[] = $result_brand_nm->categoryName;
                        }
                        $brandNm = implode(',',$brand_nm);
                    }
                }
                $data['brandNM']= $brandNm;

                if(!empty($userdata->dealerAvatar)){
                    $cavatar='uploads/dealer/'.$userdata->dealerAvatar;
                } else{
                    $cavatar='assets/img/placeholder300x300.png';
                }
            }
            $data['dealerAvatart'] = $cavatar;
			 $cartitem=Cart::items();
			$cart=$cartitem->count();
            return View::make('dealer/profile/profile',$data)->with('cart',$cart);
        }else{
            return Redirect::to('/');
        }
    }

    function  updateData($update_array){

        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        $dealer = new Dealer;

        $response = $dealer->where('id', $id)->first();
        if (!empty($response)) {
            session(['emailID' => $update_array['emailID']]);
            $dealer->where('id', $id)->update($update_array);
            return true;
        } else {
            return false;
        }
    }

    public  function  profileUpdate(){

        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $dealer_session_emailID = $sessionData['emailID'];
        if(isset($id) && ($id != 0))
        {
            $dealer = new Dealer;
            $staff = new Staff();

            $data_dealer = Input::all();
            $rules = array(
                'company_name'=>'required|min:3|max:30|not_in:0',
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
                'phone'=>'required|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
              
                
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                return Redirect::to('/dealer/profiledata');
            }else
            {
                $update_array = array();

                $update_array['first_name'] = $data_dealer['first_name'];
                $update_array['last_name'] = $data_dealer['last_name'];
                $update_array['emailID'] = $data_dealer['emailID'];
                $update_array['company_name'] = $data_dealer['company_name'];
                $update_array['phone'] = $data_dealer['phone'];
                $update_array['address'] = $data_dealer['address'];
                $update_array['shipping_address'] = $data_dealer['shipping_address'];
                $update_array['contactperson1'] = $data_dealer['contactperson1'];
                $update_array['contactperson2'] = $data_dealer['contactperson2'];
                $update_array['contactperson3'] = $data_dealer['contactperson3'];
                $update_array['role'] = 'dealer';

                $return_data = false;

                if(($dealer_session_emailID != '') && ($dealer_session_emailID == $data_dealer['emailID']))
                {
                    $return_data = $this->updateData($update_array);
                }else{

                    $check_email = $data_dealer['emailID'];
                    $staff_email = $staff->where('emailID', $check_email)->count();
                    $dealer_email = $dealer->where('emailID', $check_email)->count();

                    if(($staff_email == 0) && ($dealer_email == 0)){
                        $return_data =  $this->updateData($update_array);
                    }else{
                        Session::flash('operationFaild','The email address has already been taken for Staff/Dealer');
                        return Redirect::to('dealer/profiledata');
                    }
                }

                if($return_data  == true){
                    Session::flash('operationSucess','Successfully Edit Your Profile');
                    return Redirect::to('/dealer/profiledata');
                    exit;
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('dealer/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/');
        }
    }

    public function  passwordUpdate(){
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0)) {
            $dealer = new Dealer;
            $data_dealer = Input::all();
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
                return Redirect::to('/dealer/profiledata');
            }else {
                $update_array = array();
                $update_array['password'] = Hash::make($data_dealer['password']);

                $response = $dealer->where('id', $id)->first();
                if (!empty($response))
                {
                    if( Hash::check($data_dealer['opassword'],$response->password) ) {
                        $dealer->where('id', $id)->update($update_array);
                        Session::flash('operationSucess', 'Successfully Update Your Password');
                        return Redirect::to('/dealer/profiledata');
                        exit;
                    }else {
                        Session::flash('operationFaild','Old Password Not Matched');
                        return Redirect::to('dealer/profiledata');
                        exit;
                    }

                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('dealer/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/');
        }
    }

    public function updateDealerAvatar()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $file = array('image' => Input::file('dealerAvatar'));
            $rules = array(
                'image' => 'required | mimes:jpeg,bmp,png | max:10000',
            );

            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {

                Session::flash('operationFaild','Invalid File type or Invalid File size !');
                return Redirect::to('/dealer/profiledata');

            }else{

                if (Input::file('dealerAvatar')->isValid())
                {
                    $destinationPath = 'uploads/dealer'; // upload path
                    $oldAvatar=DB::table('dealer')->where('id','=',$id)->first();
                    if(!empty($oldAvatar->dealerAvatar)){
                        File::delete($destinationPath.'/'.$oldAvatar->dealerAvatar);
                    }

                    $extension = Input::file('dealerAvatar')->getClientOriginalExtension(); // getting image extension
                    $fileName = 'dealerAvatar-'.rand(11111,99999).'.'.$extension; // renameing image
                    Input::file('dealerAvatar')->move($destinationPath, $fileName); // uploading file to given path
                    // sending back with message

                    $avatarupdate=array(
                        'dealerAvatar' => $fileName
                    );
                    $add= DB::table('dealer')->where('id','=',$id)->update($avatarupdate);
                    Session::flash('operationSucess','Avatar uploaded Successfully !');

                }else{

                    Session::flash('operationFaild','Some thing went wrong.try again.');
                }
                return Redirect::to('/dealer/profiledata');
            }
        }else{
            return Redirect::to('/');
        }
    }

    public function billingAddress()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $dealer = new Dealer;

            $data_dealer = Input::all();
            print_r($data_dealer);
            $rules = array(
                'billing_firstname'=>'required|min:3|max:30|not_in:0',
                'billing_lastname'=>'required|min:3|max:30|not_in:0',
                'billing_emailID'=>'required|min:3|max:30|not_in:0',
                'billing_address'=>'required|min:3|max:200|not_in:0',
                'billing_city'=>'required|min:3|max:30|not_in:0',
                'billing_state'=>'required|min:3|max:30|not_in:0',
                'billing_zipcode'=>'required|min:3|max:30|not_in:0',
                'billing_country'=>'required|min:1|max:30|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {
                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                return Redirect::to('/dealer/profiledata');
            }else
            {
                $update_array = array();
                $update_array['billing_firstname'] = $data_dealer['billing_firstname'];
                $update_array['billing_lastname'] = $data_dealer['billing_lastname'];
                $update_array['billing_emailID'] = $data_dealer['billing_emailID'];
                $update_array['billing_address'] = $data_dealer['billing_address'];
                $update_array['billing_city'] = $data_dealer['billing_city'];
                $update_array['billing_state'] = $data_dealer['billing_state'];
                $update_array['billing_zipcode'] = $data_dealer['billing_zipcode'];
                $update_array['billing_country'] = $data_dealer['billing_country'];

                $update_array['shipping_firstname'] = $data_dealer['shipping_firstname'];
                $update_array['shipping_lastname'] = $data_dealer['shipping_lastname'];
                $update_array['shipping_emailID'] = $data_dealer['shipping_emailID'];
                $update_array['shipping_address'] = $data_dealer['shipping_address'];
                $update_array['shipping_city'] = $data_dealer['shipping_city'];
                $update_array['shipping_state'] = $data_dealer['shipping_state'];
                $update_array['shipping_zipcode'] = $data_dealer['shipping_zipcode'];
                $update_array['shipping_country'] = $data_dealer['shipping_country'];

                $response = $dealer->where('id', $id)->first();
				
                if (!empty($response)) {
                    $dealer->where('id', $id)->update($update_array);
                    Session::flash('operationSucess','Successfully Edit Your Billing Address');
                    return Redirect::to('/dealer/profiledata');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('dealer/profiledata');
                    exit;
                }
            }
        }else{
            return Redirect::to('/');
        }
    }
}
