<?php

namespace App\Http\Controllers\admin;

use App\Customer;
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
use App\Customers;
use App\WarrantyProduct;
use App\WarrantyProductNote;
use Datatables;
use URL;
use XeroLaravel;
 

class ManageCustomerController extends Controller
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
            return View::make('admin.customerManage.customerList');
        }else{
            return Redirect::to('/');
        }

    }

    public function dataCustomer()
    {
        $customer = new Customer();

        $data = Customer::select('*')->orderBy('id','asc');

        $no = 0;

        return Datatables::of($data, $no)


            ->addColumn('Customer', function ($data)
            {
                $name = '';
                if($data->first_name != '')
                {
                    $name = $data->first_name.'&nbsp;'.$data->last_name;
                }
                if($name != ''){
                    return $name;
                }
                else{
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

            ->addColumn('Action', function ($data)
            {
                $html = '';
                $url = URL::to('admin/edit-customer', $data->customer_id);
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";

                $html.= '&nbsp;&nbsp;';
                $url = URL::to('admin/password-customer', $data->customer_id);
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Change Password\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-key\"></i></a>";

                $deleted = $data->customer_id;
                $html.= '&nbsp;&nbsp;';
                $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('".$deleted."')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";

                return $html;
            })
            ->make(true);
    }

    public function addCustomer()
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
            $data['customer_id'] = $Unique;


            $data['first_name'] = '';
            $data['last_name'] = '';
            $data['address'] = '';
            $data['city'] = '';
            $data['state'] = '';
            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');
			 
            return View::make('admin.customerManage.customerAdd',$data);
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
            $customer = new Customer();

            $data_customer = Input::all();
            $rules = array(
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
                'emailID'=>'required|email|Unique:staff|Unique:dealer|Unique:customer|Unique:admin|not_in:0',
				'phone'=>'required|min:6|max:12|not_in:0',
                'password'=>'required|min:6|max:12|not_in:0',
                'txt_confirmpassword'=>'required|same:password|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
                'city'=>'required|min:3|max:30|not_in:0',
                'state'=>'required|min:3|max:30|not_in:0',
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
                Session::set('session_data',$data_customer);
                return Redirect::to('/admin/customeradd');
            }else
            {
                /*print_r($data_customer);
                exit;*/

                $email = $data_customer['emailID'];
                $password = $data_customer['password'];
                $toemail = $email;

                $customer->customer_id = $data_customer['customer_id'];
                $customer->first_name = $data_customer['first_name'];
                $customer->last_name = $data_customer['last_name'];
                $customer->emailID = $data_customer['emailID'];
                $customer->password = Hash::make($data_customer['password']);
                $customer->phone = $data_customer['phone'];
                $customer->address = $data_customer['address'];
                $customer->city = $data_customer['city'];
                $customer->state = $data_customer['state'];
                $customer->country = $data_customer['country'];
                $customer->role = 'customer';

                if(isset($data_customer['status'])){
                    $customer->status = 1;
                }else{
                    $customer->status = 0;
                }

                $customer->save();

                $customer_info = array(
                    "first_name" => $data_customer['first_name'],
                    "last_name" => $data_customer['last_name'],
                    "emailID" => $data_customer['emailID'],
                    "phone" => $data_customer['phone'],
                    "password" => $data_customer['password'],
                    "loginUrl" => URL::to('/')
                );

                Mail::send('email_templates.customerCreate',['data_customer' => $customer_info], function($message) use ($email)
                {
                    $message->to($email)->subject('CRM - Login Details');
                });

                Session::forget('session_data');
               // Session::save();

                Session::flash('operationSucess','Successfully Add Customer');
                return Redirect::to('/admin/customerlist');
                exit;
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function editCustomer($id)
    {
        $result_data = array();
        $data = array();
        $customer_id = $id;

        $customer = new Customer();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['first_name'] = '';
            $data['last_name'] = '';
            $data['address'] = '';
            $data['city'] = '';
            $data['state'] = '';

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');

            $result_data = DB::table('customer')->where('customer_id','=',$customer_id)->first();

            Session::forget('session_data');
            //Session::save();

            if(!empty($result_data)){
                $data['customer'] = $result_data;
                return View::make('admin.customerManage.customerEdit',$data);
            }else{
                return Redirect::to('/admin/customerlist');
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
            $customer = new Customer();

            $data_customer = Input::all();
            $rules = array(
                'first_name'=>'required|min:3|max:30|not_in:0',
                'last_name'=>'required|min:3|max:30|not_in:0',
				'emailID'=>'required|email|Unique:staff|Unique:admin|not_in:0',
                'address'=>'required|min:3|max:200|not_in:0',
                'city'=>'required|min:3|max:30|not_in:0',
                'state'=>'required|min:3|max:30|not_in:0',
                'country'=>'required|min:1|max:30|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$data_customer);
                return Redirect::to('/admin/customeradd');
            }else
            {
                $update_array = array();

                $update_array['customer_id'] = $data_customer['customer_id'];
                $update_array['first_name'] = $data_customer['first_name'];
                $update_array['last_name'] = $data_customer['last_name'];
				$update_array['emailID'] = $data_customer['emailID'];
                $update_array['address'] = $data_customer['address'];
                $update_array['city'] = $data_customer['city'];
                $update_array['state'] = $data_customer['state'];
                $update_array['country'] = $data_customer['country'];
                $update_array['role'] = 'customer';

                if(isset($data_customer['status'])){
                    $update_array['status'] = 1;
                }else{
                    $update_array['status'] = 0;
                }

               // Session::flush('session_data');
                //Session::save();

                $response = $customer->where('customer_id', $data_customer['customer_id'])->first();
                if (!empty($response)) {
                    $customer->where('customer_id', $data_customer['customer_id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Edit Customer');
                    return Redirect::to('/admin/customerlist');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/customerlist');
                    exit;
                }
            }

        }else{
            return Redirect::to('/');
        }
    }

    public function passwordCustomer($id)
    {
        $result_data = array();
        $data = array();
        $customer_id = $id;

        $customer = new Customer();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $result_data = DB::table('customer')->where('customer_id','=',$customer_id)->first();
            if(!empty($result_data)){
                $data['customer'] = $result_data;
                return View::make('admin.customerManage.customerPassword',$data);
            }else{
                return Redirect::to('/admin/customerlist');
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
            $customer = new Customer();


            $data_customer = Input::all();
            $rules = array(
                'password'=>'required|min:6|max:12|not_in:0',
                'txt_confirmpassword'=>'required|same:password|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {

                $messages = $validator->messages();
                Session::flash('operationFaild', 'New Password and Confirm Password not match');
                Session::set('session_data', $data_customer);
                return Redirect::to('/admin/customerlist');
            } else {

                $update_array = array();
                $update_array['password'] = Hash::make($data_customer['password']);

                $result_data = DB::table('customer')->where('id','=',$data_customer['id'])->first();

                $response = $customer->where('customer_id', $data_customer['customer_id'])->first();
                if (!empty($response))
                {
                    $customer->where('customer_id', $data_customer['customer_id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Password Change');


                    $email = '';
                    $info = array(
                        "first_name" => $result_data->first_name,
                        "last_name" =>  $result_data->last_name,
                        "emailID" =>    $result_data->emailID,
                        "password" =>   $data_customer['password'],
                        "loginUrl" => URL::to('/')
                    );

                    $email = $result_data->emailID;

                    if($result_data->emailID != '')
                    {
                        Mail::send('email_templates.changePassword',['data_info' => $info], function($message) use ($email)
                        {
                            $message->to($email)->subject('CRM Customer - New Password Details');
                        });
                    }
                    return Redirect::to('/admin/customerlist');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/admin/customerlist');
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
            $customer = new Customer();
            $data_post = Input::all();

            $customer_id = $data_post['customer_id'];
            $response = $customer->where('customer_id', $customer_id)->first();
            $role = $response->role;
            $user_id = $response->id;

            $warranty = new WarrantyProduct();
            $warranty->where('role', '=', $role)->where('user_id', '=', $user_id)->delete();

            $warranty_note = new WarrantyProductNote();
            $warranty_note->where('role', '=', $role)->where('user_id', '=', $user_id)->delete();

            $customer->where('customer_id', '=', $customer_id)->delete();
        }
    }
}
