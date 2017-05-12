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
use App\Dealer;
use Datatables;
use URL;
 
use App\Http\Controllers\logdata\LogController;
 

class ManageDealerController extends Controller
{
    public function index()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
			
			$getdealer=DB::table('dealer')->get();
			$address='';
			//print_r($getdealer);exit; 
			 /*  foreach($getdealer as $dealer){
				$contry=DB::table('apps_countries')->where('country_code','=',$dealer->country)->first();
				$address .=$dealer->address .' - '.$dealer->pincode .', '.$dealer->city .', '.$dealer->state .', '.$contry->country_name;
				
				//echo $address.'<hr/>';
				
				$updateDealerData=array(
					'address'=>$address
				);
				$upadetdealer=DB::table('dealer')->where('id','=',$dealer->id)->update($updateDealerData);
				$address='';
			}   */
			//exit;
            return View::make('admin.dealerManage.dealerList');
        }else{
            return Redirect::to('/');
        }

    }

    public function dataDealer()
    {
        $dealer = new Dealer;

        $data = Dealer::select('*')->where('deleted_at','=',NULL)->orderBy('id','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('Company Name', function ($data) {
                if($data->company_name != ''){
                    return $data->company_name;
                }else{
                    return '---';
                }
            })

            ->addColumn('Dealer', function ($data) {
                if(($data->first_name != '') && ($data->last_name != '')){
                    return $data->first_name.'&nbsp;'.$data->last_name;
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
			->addColumn('Email', function ($data) {
                if($data->emailID != ''){
                    return $data->emailID;
                }else{
                    return '---';
                }
            })

            ->addColumn('Category', function ($data) {
                if($data->categoryID != '')
				{
					$category = array();
                    $category_ar = explode(',',$data->categoryID);
                    foreach($category_ar as $key => $value)
					{
                        $result_data = DB::table('category')->where('id','=',$value)->where('deleted_at', '=', NULL)->first();
                        if(!empty($result_data)){
                            $category[] = $result_data->categoryName;
                        }
                    }
					
                    if(!empty($category)){
                        return implode(',',$category);
                    }else{
                        return '---';
                    }
                }else{
                    return '---';
                }
            })

            ->addColumn('Login', function ($data)
            {
                $html = '';
                $url = URL::to('admin/login-dealer', $data->id);
                $html.= "<a href=\"$url\" target=\"_blank\" data-toggle=\"tooltip\" title=\"Dealer Login\" class=\"btn btn-xs btn-success\">Login</a>";
                return $html;
            })


            ->addColumn('Action', function ($data)
            {
                $html = '';
                $url = URL::to('admin/edit-dealer', $data->id);
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";

                $html.= '&nbsp;&nbsp;';
                $url = URL::to('admin/password-dealer', $data->id);
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Change Password\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-key\"></i></a>";
 $html.= '&nbsp;&nbsp;';
				$html.= "<a href=\"javascript:void(0)\" onclick=\"removedata($data->id)\"  data-toggle=\"tooltip\" title=\"Delete Dealer\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";

                return $html;
            })
            ->make(true);
    }

    public function addDealer()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['company_name'] = '';
            $data['categoryID'] = array();
            $data['first_name'] = '';
            $data['last_name'] = '';
            $data['invoicePrefix'] = '';
            $data['address'] = '';
            $data['pincode'] = '';
            $data['city'] = '';
            $data['state'] = '';
            $data['groupID'] = '';

          /*   if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['company_name'] = $session_data['company_name'];
                $data['categoryID'] = $session_data['categoryID'];
                $data['first_name'] = $session_data['first_name'];
                $data['last_name'] = $session_data['last_name'];
                $data['address'] = $session_data['address'];
                $data['city'] = $session_data['city'];
                $data['state'] = $session_data['state'];
                $data['country'] = $session_data['country'];
                $data['groupID'] = $session_data['groupID'];
            } */

            $data['categories'] = DB::table('category')
                ->where('deleted_at', '=', '0000-00-00 00:00:00')
				->orWhere('deleted_at', '=', NULL)
                ->lists('categoryName','id');

            $data['group'] = DB::table('group')->lists('name','groupID');

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');
			 
            return View::make('admin.dealerManage.dealerAdd',$data);
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
            $dealer = new Dealer;

            $data_dealer = Input::all();
			//print_r($data_dealer);exit;
            $rules = array(
                'company_name'=>'required|min:3|max:30|not_in:0',
                'categoryID'=>'required|min:1|max:30|not_in:0',
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
                'invoicePrefix'=>'required|not_in:0',
                'emailID'=>'required|email|Unique:staff|Unique:dealer|Unique:admin|not_in:0',
                'password'=>'required|min:6|max:12|not_in:0',
                'txt_confirmpassword'=>'required|same:password|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
                'phone'=>'required|not_in:0',
                'groupID'=>'required|min:1|max:30|not_in:0',
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
                Session::set('session_data',$data_dealer);
                return Redirect::to('/admin/dealeradd');
            }else
            {
                /*print_r($data_dealer);
                exit;*/

                $email = $data_dealer['emailID'];
                $password = $data_dealer['password'];
                $toemail= $email;

                $dealer->groupID = $data_dealer['groupID'];
                $dealer->first_name = $data_dealer['first_name'];
                $dealer->last_name = $data_dealer['last_name'];
                $dealer->invoicePrefix = $data_dealer['invoicePrefix'];
                $dealer->emailID = $data_dealer['emailID'];
                $dealer->password = Hash::make($data_dealer['password']);
                $dealer->company_name = $data_dealer['company_name'];
                $dealer->categoryID = implode(',',$data_dealer['categoryID']);
                $dealer->phone = $data_dealer['phone'];
                $dealer->address = $data_dealer['address'];
                $dealer->shipping_address = $data_dealer['shipping_address'];
                $dealer->contactperson1 = $data_dealer['contactperson1'];
                $dealer->contactperson2 = $data_dealer['contactperson2'];
                $dealer->contactperson3 = $data_dealer['contactperson3'];
                $dealer->role = 'dealer';
                $dealer->save();

                $dealer_info = array(
                    "first_name" => $data_dealer['first_name'],
                    "last_name" => $data_dealer['last_name'],
                    "emailID" => $data_dealer['emailID'],
                    "password" => $data_dealer['password'],
                    "company_name" => $data_dealer['company_name'],
                    "loginUrl" => URL::to('/'),
                );

                Mail::send('email_templates.dealerCreate',['data_dealer' => $dealer_info], function($message) use ($email)
                {
                    $message->to($email)->subject('CRM - Login Details');
                });

                Session::forget('session_data');
               // Session::save();

                Session::flash('operationSucess','Successfully Add Dealer');
                return Redirect::to('/admin/dealerlist');
                exit;
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function editDealer($id)
    {
        $result_data = array();
        $data = array();
        $dealer_id = $id;

        $dealer = new Dealer;
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['company_name'] = '';
            $data['categoryID'] = array();
            $data['first_name'] = '';
            $data['last_name'] = '';
            $data['invoicePrefix'] = '';
            $data['address'] = '';
            $data['pincode'] = '';
            $data['city'] = '';
            $data['state'] = '';
            $data['groupID'] = '';

            $data['categories'] = DB::table('category')
                ->where('deleted_at', '=', '0000-00-00 00:00:00')
				->orWhere('deleted_at', '=', NULL)
                ->lists('categoryName','id');

            $data['group'] = DB::table('group')->lists('name','groupID');

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');

            $result_data = DB::table('dealer')->where('id','=',$dealer_id)->first();


            Session::forget('session_data');
            //Session::save();


            if(!empty($result_data)){
                $data['dealer'] = $result_data;
                return View::make('admin.dealerManage.dealerEdit',$data);
            }else{
                return Redirect::to('/admin/dealerlist');
            }
        }else{
            return Redirect::to('/');
        }
    }
	public function dealerDelete(Request $request){
		$post=$request->all();
		$updatearray=array(
			'deleted_at'=>Carbon::now()->toDateTimeString()
		);
		 $dealer = new Dealer;
		 $dealerDelete= $dealer->where('id','=',$post['order'])->update($updatearray);
		//print_r($post);
		
	}

    public function  editData()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $dealer = new Dealer;

            $data_dealer = Input::all();
			 $rules = array(
                'company_name'=>'required|min:3|max:30|not_in:0',
                'categoryID'=>'required|min:1|max:30|not_in:0',
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
                'invoicePrefix'=>'required|not_in:0',
				'emailID'=>'required|email|Unique:staff|Unique:admin|not_in:0',
                'phone'=>'required|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
             
                'groupID'=>'required|min:1|max:30|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$data_dealer);
                return Redirect::to('/admin/dealeradd');
            }else
            {
                $update_array = array();
                

                
				
				 $update_array['groupID'] = $data_dealer['groupID'];
                $update_array['first_name'] = $data_dealer['first_name'];
                $update_array['last_name'] = $data_dealer['last_name'];
                $update_array['invoicePrefix'] = $data_dealer['invoicePrefix'];
				$update_array['emailID'] = $data_dealer['emailID'];
                $update_array['company_name'] = $data_dealer['company_name'];
                $update_array['phone'] = $data_dealer['phone'];
                $update_array['categoryID'] = implode(',',$data_dealer['categoryID']);
                $update_array['address'] = $data_dealer['address'];
                $update_array['shipping_address'] = $data_dealer['shipping_address'];
                $update_array['contactperson1'] = $data_dealer['contactperson1'];
                $update_array['contactperson2'] = $data_dealer['contactperson2'];
                $update_array['contactperson3'] = $data_dealer['contactperson3'];
                $update_array['role'] = 'dealer';

               // Session::flush('session_data');
                //Session::save();

                $response = $dealer->where('id', $data_dealer['id'])->first();
                if (!empty($response)) {
                    $dealer->where('id', $data_dealer['id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Edit Dealer');
                    return Redirect::to('/admin/dealerlist');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/dealerlist');
                    exit;
                }
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function passwordDealer($id)
    {
        $result_data = array();
        $data = array();
        $dealer_id = $id;

        $dealer = new Dealer();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $result_data = DB::table('dealer')->where('id','=',$dealer_id)->first();
            if(!empty($result_data)){
                $data['dealer'] = $result_data;
                return View::make('admin.dealerManage.dealerPassword',$data);
            }else{
                return Redirect::to('/admin/dealerlist');
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
            $dealer = new Dealer();


            $data_dealer = Input::all();
            $rules = array(
                'password'=>'required|min:6|max:12|not_in:0',
                'txt_confirmpassword'=>'required|same:password|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {

                $messages = $validator->messages();
                Session::flash('operationFaild', 'New Password and Confirm Password not match');
                Session::set('session_data', $data_dealer);
                return Redirect::to('/admin/dealerlist');
            } else {

                $update_array = array();
                $update_array['password'] = Hash::make($data_dealer['password']);

                $result_data = DB::table('dealer')->where('id','=',$data_dealer['id'])->first();

                $response = $dealer->where('id', $data_dealer['id'])->first();
                if (!empty($response))
                {
                    $dealer->where('id', $data_dealer['id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Password Change');


                    $email = '';
                    $info = array(
                        "first_name" => $result_data->first_name,
                        "last_name" =>  $result_data->last_name,
                        "emailID" =>    $result_data->emailID,
                        "password" =>   $data_dealer['password'],
                        "loginUrl" => URL::to('/')
                    );

                    $email = $result_data->emailID;

                    Mail::send('email_templates.changePassword',['data_info' => $info], function($message) use ($email)
                    {
                        $message->to($email)->subject('CRM Dealer - New Password Details');
                    });

                    return Redirect::to('/admin/dealerlist');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/dealerlist');
                    exit;
                }
            }

        }
    }


    public function loginDealer($id)
    {
        $result_data = array();
        $data = array();
        $dealer_id = $id;

        $dealer = new Dealer;
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            \Session::forget('dealerLog');
            \Session::save();

            $result = DB::table('dealer')->where('id','=',$dealer_id)->first();

            if(!empty($result))
            {
                $userData=array(
                    'dealerID'=>$result->id,
                    'company_name'=>$result->company_name,
                    'first_name'=>$result->first_name,
                    'last_name'=>$result->last_name,
                    'emailID'=>$result->emailID,
                    'role'=>'dealer'
                );
                \Session::set('dealerLog' , $userData);
                Session::save();


                $description = 'Admin/Staff login With Dealer'.$result->first_name.' '.$result->last_name;
                $logdata = array();
                $logdata['role'] = 'dealer';
                $logdata['role_id'] = $result->id;
                $logdata['operation'] = 'Login';
                $logdata['description'] = $description;
                $logdata['role_date'] = date('Y-m-d');

                $result_logdata = (new LogController)->index($logdata);

                return Redirect::to('/dealer/dashboard');

            }else{
                return Redirect::to('/admin/dealerlist');
            }
        }else{
            return Redirect::to('/');
        }
    }
}
