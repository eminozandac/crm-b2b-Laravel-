<?php

namespace App\Http\Controllers\admin;

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
use Response;
use URL;
use App\Staff;
use App\Customer;
use App\WarrantyProduct;
use App\WarrantyProductNote;
use PDF;


class WarrantyAdminController extends Controller
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
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $warranty_status = Config('warranty.WARRANTY_STATUS');
            $data['warranty_status'] = $warranty_status;

            $warranty_status_color = Config('warranty.WARRANTY_STATUS_COLOR');
            $data['warranty_status_color'] = $warranty_status_color;

            $data['total_pending_warranty'] = WarrantyProduct::select('id')->where('warranty_status','!=','complete')->orderBy('id','DESC')->count();
            $data['total_complete_warranty'] = WarrantyProduct::select('id')->where('warranty_status','=','complete')->orderBy('id','DESC')->count();

            $result_warrantyList = WarrantyProduct::select('*')->where('warranty_status','!=','complete')->orderBy('id','DESC')->get();
            $data['warrantyList_pending'] = $result_warrantyList;

            $result_warrantyList = WarrantyProduct::select('*')->where('warranty_status','=','complete')->orderBy('id','DESC')->get();
            $data['warrantyList_complete'] = $result_warrantyList;
			//print_r($data);exitl
            return View::make('warranty/admin/adminWarrantyList',$data);
        }else{
            return Redirect::to('/admin');
        }
    }

    public function warrantyAdd()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $Unique='';
            $data['clients_project_uniqueID'] = '';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }

            $warranty_status = Config('warranty.WARRANTY_STATUS');
            $data['warranty_status'] = $warranty_status;

            $warranty_status_color = Config('warranty.WARRANTY_STATUS_COLOR');
            $data['warranty_status_color'] = $warranty_status_color;

            $data['warranty_assign_staff'] = Staff::select('staff_id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))->orderBy('first_name')->lists('full_name', 'staff_id');

            $data['warranty_assign'] = '';
            $data['role'] = '';
            $data['user_id'] = '';
            $data['name'] = '';
            $data['address'] = '';
            $data['postcode'] = '';
            $data['emailID'] = '';
            $data['phone'] = '';
            $data['product_name'] = '';
            $data['product_model'] = '';
            $data['purchase_date'] = date('m/d/Y');
            $data['product_serial_number'] = '';
            $data['product_fault'] = '';
            $data['part_require'] = '';
            $data['note'] = '';

            if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['warranty_assign'] = $session_data['warranty_assign'];
                $data['role'] = $session_data['role'];
                $data['user_id'] = $session_data['user_id'];
                $data['name'] = $session_data['name'];
                $data['address'] = $session_data['address'];
                $data['postcode'] = $session_data['postcode'];
                $data['emailID'] = $session_data['emailID'];
                $data['phone'] = $session_data['phone'];
                $data['product_name'] = $session_data['product_name'];
                $data['product_model'] = $session_data['product_model'];
                $data['purchase_date'] = $session_data['purchase_date'];
                $data['product_serial_number'] = $session_data['product_serial_number'];
                $data['product_fault'] = $session_data['product_fault'];
                $data['part_require'] = $session_data['part_require'];
                $data['note'] = $session_data['note'];
            }

            $data['warranty_uniqueID'] = $Unique;
            return View::make('warranty/admin/adminWarrantyAdd',$data);
        }else{
            return Redirect::to('admin/index');
        }
    }

    public function fileupload()
    {
        $input = Input::all();
        $rules = array(
            'file' => 'image|max:3000',
        );

        $success_ar_file_name = array();

        $validation = Validator::make($input, $rules);
        if ($validation->fails())
        {
            echo 'error';
            exit;
        }else{
            $files = Input::file('note_file');
            $destinationPath = 'uploads/warranty/';
            foreach($files as $file)
            {
                $extension = File::extension($file->getClientOriginalName());
                $filename = 'note_file_'.sha1(rand(11,9999).rand(1,999).date("Ymdhis")).rand(1111,9999).".{$extension}";
                $upload_success = $file->move($destinationPath, $filename);
                if($upload_success)
                {
                    $filename_ar = array();
                    $filename_ar['oldname'] = $file->getClientOriginalName();
                    $filename_ar['newname'] = $filename;
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
        $file_path =  'uploads/warranty/';
        $data = Input::all();
        if(isset($data['file']) && !empty($data['file'])) {
            $file_name = $data['file'];
            File::delete($file_path.$file_name);
        }
    }

    public  function  deleteData()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data_post = Input::all();
            $destinationPath = 'uploads/warranty'; // upload path
            $result_file_images = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$data_post['warranty_uniqueID'])->first();
            if(!empty($result_file_images->file_images))
            {
                $file_name = explode(',',$result_file_images->file_images);
                foreach($file_name as $key => $value){
                    File::delete($destinationPath.'/'.$value);
                }
            }
            WarrantyProduct::where('warranty_uniqueID','=', $data_post['warranty_uniqueID'])->delete();
        }
    }

    public function saveCustomer($data_ar)
    {
        $customer = new Customer();

        $Unique='';
        $data_ar['clients_project_uniqueID'] = '';
        for($j=0;$j < 4;$j++)
        {
            if($j!=3){$dash='-';}else{$dash='';}
            $Unique .= $this->getTokenProduct().$dash;
        }
        $data_ar['customer_id'] = $Unique;

        if($data_ar['emailID'] != ''){
            $result_customer = DB::table('customer')->where('emailID', $data_ar['emailID'])->get();
        }else{
            $result_customer = DB::table('customer')->where('phone', $data_ar['phone'])->get();
        }
        if(empty($result_customer))
        {
            if(!isset($data_ar['password']) && $data_ar['password'] == '')
            {
                $data_ar['password'] = 'crm'.rand(11111,99999);
            }

            if(isset($data_ar['password']) && $data_ar['password'] == '')
            {
                $data_ar['password'] = 'crm'.rand(11111,99999);
            }


            $customer->customer_id = $data_ar['customer_id'];
            $customer->first_name = htmlentities($data_ar['name']);
            $customer->emailID = $data_ar['emailID'];
            $customer->phone = $data_ar['phone'];
            $customer->password = Hash::make($data_ar['password']);
            $customer->address = $data_ar['address'];
            $customer->role = 'customer';
            $customer->status = 1;
            $customer->save();

            $customer_info = array(
                "first_name" => $data_ar['name'],
                "last_name" => '',
                "emailID" => $data_ar['emailID'],
                "phone" => $data_ar['phone'],
                "password" => $data_ar['password'],
                "loginUrl" => URL::to('/customer'),
            );
            $email = $data_ar['emailID'];

            if($email != '')
            {
                Mail::send('email_templates.customerCreate',['data_customer' => $customer_info], function($message) use ($email)
                {
                    $message->to($email)->subject('CRM - Login Details');
                });
            }
            $result = DB::table('customer')->where('customer_id', $data_ar['customer_id'])->first();
            return $result->id;
        }else{
            if($data_ar['emailID'] != '')
            {
                $result = DB::table('customer')->where('emailID', $data_ar['emailID'])->first();
            }else{
                $result = DB::table('customer')->where('phone', $data_ar['phone'])->first();
            }
            return $result->id;
        }
    }

    public function warrantySaveData()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $data_post = Input::all();
			//print_r($data_post); exit;
            if(!empty($data_post) && ($data_post['warranty_uniqueID'] != '') && ($data_post['method_process'] != '')
               && ($data_post['role'] != '') && (($data_post['method_process'] == 'add') || ($data_post['method_process'] == 'edit')) )
            {
                $rules = array(
                    'name'=>'required|not_in:0',
                );

                $validator = Validator::make(Input::all(), $rules);

                if ($validator->fails())
                {
                    Session::flash('operationFaild','Some thing went wrong!');
                    Session::set('session_data',$data_post);
                    return Redirect::to('/admin/warrantyadd');
                }else
                {
                    $update_post = array();
                    $update_post['role']  = $data_post['role'];

                    $name = $emailID = $email = '';
                    $warranty_info = array();
                    $primary_key = array();

                    $login_url = URL::to('/');

                    if($data_post['role'] == 'dealer')
                    {
                        $result = DB::table('dealer')->where('id', $data_post['user_id'])->first();
						//print_r($result);exit;
                        $name = $result->first_name.' '.$result->last_name;
                        $emailID =  $result->emailID;
                        $update_post['user_id']  = $data_post['user_id'];
                        $login_url = URL::to('/');
                    }

                    if($data_post['role'] == 'customer')
                    {
                        /*$result = DB::table('customer')->where('id', $data_post['user_id'])->first();
                        $name = $result->first_name.' '.$result->last_name;*/
                        if($data_post['method_process'] == 'add'){
                            $update_post['user_id'] = $this->saveCustomer($data_post);
                        }else{
                            $update_post['user_id']  = $data_post['user_id'];
                        }

                        $name =  $data_post['name'];
                        $emailID =  $data_post['emailID'];
                        $login_url = URL::to('/customer');
                    }

                    $update_post['name'] = htmlentities($data_post['name']);
                    $update_post['address'] = htmlentities($data_post['address']);
                    $update_post['postcode'] = htmlentities($data_post['postcode']);
                    $update_post['emailID'] = htmlentities($data_post['emailID']);
                    $update_post['phone'] = htmlentities($data_post['phone']);
                    $update_post['product_name'] = htmlentities($data_post['product_name']);
                    $update_post['product_model'] = htmlentities($data_post['product_model']);
                    if($data_post['purchase_date'] != ''){
                        $update_post['purchase_date'] = date('Y-m-d',strtotime($data_post['purchase_date']));
                    }
                    $update_post['product_serial_number'] = htmlentities($data_post['product_serial_number']);
                    $update_post['product_fault'] = htmlentities($data_post['product_fault']);
                    $update_post['part_require'] = htmlentities($data_post['part_require']);
                    $update_post['note'] = htmlentities($data_post['note']);


                    if($data_post['hidden_file_images'] != ''){
                        $update_post['file_images'] = htmlentities($data_post['hidden_file_images']);
                    }

                    if($data_post['method_process'] == 'add')
                    {
                        $primary_key = array(
                            'warranty_uniqueID' => $data_post['warranty_uniqueID'],
                            'role' => $data_post['role'],
                        );
                        $update_post['warranty_status'] = 'new_claim';
                        $update_post['assign_role'] = $data_post['assign_role'];
                        $update_post['warranty_assign'] = $data_post['warranty_assign'];

                        $email = $emailID;
						$getLastWarrnty=DB::table('warrantyproduct')->orderBy('id','DESC')->first();
						$warrentyClaimNumber=$getLastWarrnty->claimNumber + 1;
						$update_post['claimNumber']=$warrentyClaimNumber;
                        $warranty_status = Config('warranty.WARRANTY_STATUS');
                        $warranty_status_color = Config('warranty.WARRANTY_STATUS_COLOR');

                        $warranty_info = array(
                            'title_message' => 'Add Your Warranty Product Details',
                            'to_name' => $name,
                            'name' => $name,
                            'emailID' => $emailID,
                            'product_model' => $update_post['product_model'],
                            'product_serial_number' => $update_post['product_serial_number'],
                            'note' => $update_post['note'],
                            'claimNumber' => $warrentyClaimNumber,
                            'address' => $update_post['address'],
                            'phone' => $update_post['phone'],
                            'warranty_status' => $warranty_status[$update_post['warranty_status']],
                            'warranty_status_color' => $warranty_status_color[$update_post['warranty_status']],
                            'loginUrl' => $login_url,
                        );
						//print_r($warranty_info);exit;
                    }

                    if($data_post['method_process'] == 'edit')
                    {
                        $login_url = URL::to('/');

                        $update_post['warranty_status'] = $data_post['warranty_status'];
                        $update_post['assign_role'] = $data_post['assign_role'];
                        $update_post['warranty_assign'] = $data_post['warranty_assign'];

                        $primary_key = array(
                            'warranty_uniqueID' => $data_post['warranty_uniqueID'],
                            'role' => $data_post['role'],
                        );
                        $result_warranty_product = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$data_post['warranty_uniqueID'])->first();

                        $name = $emailID = '';
                        if($result_warranty_product->role == 'dealer')
                        {
                            $result = DB::table('dealer')->where('id',$result_warranty_product->user_id)->first();
                            $name = $result->first_name.' '.$result->last_name;
                            $emailID = $result->emailID;

                            $login_url = URL::to('/');
                        }
                        if($result_warranty_product->role == 'customer')
                        {
                            /*$result = DB::table('customer')->where('id',$result_warranty_product->user_id)->first();
                            $name = $result->first_name.' '.$result->last_name;
                            $emailID = $result->emailID;*/
                            $name =  $update_post['name'];
                            $emailID =  $update_post['emailID'];

                            $login_url = URL::to('/customer');
                        }
                        $email = $emailID;

                        $warranty_status = Config('warranty.WARRANTY_STATUS');
                        $warranty_status_color = Config('warranty.WARRANTY_STATUS_COLOR');

                        $warranty_info = array(
                            'title_message' => 'Update Your Warranty Product Details',
                            'to_name' => $name,
                            'name' => $name,
                            'emailID' => $emailID,
                            'product_model' => $update_post['product_model'],
                            'product_serial_number' => $update_post['product_serial_number'],
                            'note' => $update_post['note'],
                            'address' => $update_post['address'],
                            'phone' => $update_post['phone'],
                            'warranty_status' => $warranty_status[$update_post['warranty_status']],
                            'warranty_status_color' => $warranty_status_color[$update_post['warranty_status']],
                            'loginUrl' => $login_url,
                        );
                    }

                    if(!empty($warranty_info) && ($email != ''))
                    {
                        Mail::send('email_templates.WarrantyProductmessage',['data_info' => $warranty_info], function($message) use ($email)
                        {
                            $message->to($email)->subject('CRM - Warranty Product Details');
                        });
                    }

                    Session::forget('session_data');
                    Session::save();

                    if(!empty($primary_key))
                    {
                        WarrantyProduct::updateOrCreate($primary_key, $update_post);

                        $result_warranty_product = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$data_post['warranty_uniqueID'])->first();
                        if(!empty($result_warranty_product))
                        {
                            $warranty_status_name = $result_warranty_product->warranty_status;
                            $staff_emailID_result = array();
                            if($result_warranty_product->assign_role == 'staff')
                            {
                                $staff_emailID_result = DB::table('staff')->select('emailID','first_name','last_name')->where('staff_id','=',$result_warranty_product->warranty_assign)->first();
                            }
                            if($result_warranty_product->assign_role == 'employee')
                            {
                                $staff_emailID_result = DB::table('employee')->select('emailID','first_name','last_name')->where('employee_id','=',$result_warranty_product->warranty_assign)->first();
                            }

                            if(!empty($staff_emailID_result))
                            {
                                $assign_name = $staff_emailID_result->first_name . ' ' . $staff_emailID_result->last_name;
                                $assign_email = $staff_emailID_result->emailID;
                                $email = $assign_email;

                                $warranty_status = Config('warranty.WARRANTY_STATUS');
                                $warranty_status_color = Config('warranty.WARRANTY_STATUS_COLOR');

                                $warranty_info = array(
                                    'title_message' => 'Admin Change Warranty Product Details',
                                    'to_name' => $assign_name,
                                    'name' => $update_post['name'],
                                    'emailID' => $update_post['emailID'],
                                    'product_model' => $update_post['product_model'],
                                    'product_serial_number' => $update_post['product_serial_number'],
                                    'note' => $update_post['note'],
                                    'address' => $update_post['address'],
                                    'phone' => $update_post['phone'],
                                    'warranty_status' => $warranty_status[$warranty_status_name],
                                    'warranty_status_color' => $warranty_status_color[$warranty_status_name],
                                    'loginUrl' => URL::to('/'),
                                );

                                if (!empty($warranty_info) && ($email != ''))
                                {
                                    Mail::send('email_templates.WarrantyProductmessage', ['data_info' => $warranty_info], function ($message) use ($email) {
                                        $message->to($email)->subject('CRM - Update Warranty Product Details');
                                    });
                                }
                            }
                        }
                        Session::flash('operationSucess','Your Warranty Successfully Manage.');
                        return Redirect::to('/admin/warranty');
                    }else{
                        Session::flash('operationFaild','Some thing went wrong!');
                        if($data_post['method_process'] == 'add')
                        {
                            return Redirect::to('/admin/warrantyadd');

                        }else if(($data_post['method_process'] == 'edit') && ($data_post['warranty_uniqueID'] != ''))
                        {
                            return Redirect::to('/admin/warrantyedit/'.$data_post['warranty_uniqueID']);
                        }else{
                            return Redirect::to('/admin/warranty');
                        }
                    }
                }
            }else{
                Session::flash('operationFaild','Some thing went wrong!');
                if($data_post['method_process'] == 'add')
                {
                    return Redirect::to('/admin/warrantyadd');

                }else if(($data_post['method_process'] == 'edit') && ($data_post['warranty_uniqueID'] != ''))
                {
                    return Redirect::to('/admin/warrantyedit/'.$data_post['warranty_uniqueID']);
                }else{
                    return Redirect::to('/admin/warranty');
                }
            }
        }else{
            Session::flash('operationFaild','Please Login');
            return Redirect::to('/admin');
        }
    }

    public function warrantyEdit($editid)
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $warranty_status = Config('warranty.WARRANTY_STATUS');
            $data['warranty_status'] = $warranty_status;

            $warranty_status_color = Config('warranty.WARRANTY_STATUS_COLOR');
            $data['warranty_status_color'] = $warranty_status_color;

            $data['warranty_assign_staff'] = Staff::select('staff_id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))->orderBy('first_name')->lists('full_name', 'staff_id');

            $result = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$editid)->first();
            if(!empty($result)){
                $data['warrantyData'] = $result;
                return View::make('warranty/admin/adminWarrantyEdit',$data);
            }else{
                return Redirect::to('admin/warranty');
            }
        }else{
            return Redirect::to('admin/index');
        }
    }

    public function getFileWarranty()
    {
        $data_post = Input::all();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];

        $hidden_file_name_string = '';
        $hidden_file_name = '';
        $hidden_file_list = array();
        $file_path = '';

        if(isset($id) && ($id != 0))
        {
            $result = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$data_post['data_warranty_uniqueID'])->first();

            if(!empty($result))
            {
                $hidden_file_name = $result->file_images;
                if($hidden_file_name != '')
                {
                    $hidden_file_name_ar = explode(',', $hidden_file_name);

                    foreach( $hidden_file_name_ar as $key_file => $value_file)
                    {
                        $file_list = array();
                        $file_name = str_replace(' ','',$value_file);
                        $hidden_file_name_string[] = $file_name;
                        $file_path = 'uploads/warranty/'.$file_name;
                        $file_full_path = URL::to($file_path);
                        $file_list['name'] = $file_name;

                        foreach (glob("uploads/warranty/*.*") as $filename)
                        {
                            if($filename == $file_path)
                            {
                                $file_list['size'] = filesize($filename);
                                $file_list['type'] = "";
                            }else{
                                $file_list['size'] = filesize($filename);
                                $file_list['type'] = "";
                            }
                        }
                        $file_list['file'] = $file_path;
                        $file_list['url'] = $file_full_path;
                        $hidden_file_list[] = $file_list;
                    }
                    $hidden_file_name  = implode(',',$hidden_file_name_string);
                    echo json_encode($hidden_file_list,true);
                }
            }
        }
    }

    public function warrantyPDF($editid)
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $result = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$editid)->first();
            if(!empty($result))
            {
                $data['warrantyData'] = $result;
                $data['return_url'] = URL::to('admin/warranty');

                $pdf_name =  '';
                if($result->name != ''){
                    $customer_name = $result->name;
                    $pdf_name = $customer_name.'_warranty'.'.pdf';
                }else{
                    $pdf_name = 'warranty.pdf';
                }
                $data['data_print'] = 'fail';

                $pdf = PDF::loadView('warranty/admin/pdfFormate', $data);
                return $pdf->download($pdf_name);
                /*return View::make('warranty/admin/pdfFormate',$data);*/

            }else{
                return Redirect::to('admin/warranty');
            }
        }else{
            return Redirect::to('admin/warranty');
        }
    }

    public function warrantyPrint($editid)
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $result = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$editid)->first();
            if(!empty($result))
            {
                $data['warrantyData'] = $result;
                $data['return_url'] = URL::to('admin/warranty');

                $pdf_name =  '';
                if($result->name != '')
                {
                    $customer_name = $result->name;
                    $pdf_name = $customer_name.'_warranty'.'.pdf';
                }else{
                    $pdf_name = 'warranty.pdf';
                }
                $data['data_print'] = 'pass';
                return View::make('warranty/admin/pdfFormate',$data);

            }else{
                return Redirect::to('admin/warranty');
            }
        }else{
            return Redirect::to('admin/warranty');
        }
    }

    public function getUsers()
    {
        $data_post = Input::all();
        $html = '';
        if($data_post['data_role'] == 'dealer')
        {

            $result = DB::table('dealer')->select('id','first_name','last_name')->get();
            if(!empty($result)){
                foreach($result as $key => $value){
                    $name = $value->first_name.' '.$value->last_name;
                    $html.= '<option value="'.$value->id.'">';
                    $html.= $name;
                    $html.= '</option>';
                }
            }
        }else if($data_post['data_role'] == 'customer')
        {
            $result = DB::table('customer')->select('id','first_name','last_name')->get();
            if(!empty($result)){
                foreach($result as $key => $value){
                    $name = $value->first_name.' '.$value->last_name;
                    $html.= '<option value="'.$value->id.'">';
                    $html.= $name;
                    $html.= '</option>';
                }
            }
        }else{
            $html.= '<option value="">not found</option>';
        }
        echo $html;
    }


    public function noteDataForm()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $data['login_role'] = $sessionData['role'];
            $data['login_user_id'] = $id;

            $data_post = Input::all();
            $warranty_uniqueID = $data_post['warranty_uniqueID'];
            $result = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$warranty_uniqueID)->first();
            if(!empty($result))
            {
                $data['warrantyData'] = $result;
                $result_note = DB::table('warrantyproduct_note')->where('warranty_uniqueID','=',$warranty_uniqueID)->orderBy('id','DESC')->get();
                $data['warrantyNote'] = $result_note;
                return View::make('warranty/admin/noteWarranty',$data);
            }else{
                echo '444';
                exit;
            }
        }else{
            echo '404';
            exit;
        }
    }

    public function noteAdd()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $Unique='';
            $data['clients_project_uniqueID'] = '';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }

            $data_post = Input::all();
            $warranty_uniqueID = $data_post['warranty_uniqueID'];
            $data_comment = $data_post['data_comment'];

            $primary_key = array(
                'warranty_note_uniqueID' => $Unique
            );

            $update_post['role'] = $sessionData['role'];
            $update_post['user_id'] = $id;

            $update_post['warranty_uniqueID'] = $warranty_uniqueID;
            $update_post['note'] = htmlentities($data_comment);

            WarrantyProductNote::updateOrCreate($primary_key, $update_post);

            if(isset($data_post['data_send_mail']) && ($data_post['data_send_mail'] == 1))
            {
                $main_role = 'admin';
                $email_ar = array();

                $emailID_admin = $emailID_assign = $emailID_dealer = $emailID_customer = '';
                $assign_role = $assign_uniqueID = '';
                $result_admin = DB::table('admin')->where('deleted_at','=',NULL)->first();
                if(!empty($result_admin)){
                    $emailID_admin = $result_admin->emailID;
                }

                $warranty_status = Config('warranty.WARRANTY_STATUS');
                $warranty_status_color = Config('warranty.WARRANTY_STATUS_COLOR');

                $result_warranty = DB::table('warrantyproduct')->where('warranty_uniqueID','=',$warranty_uniqueID)->first();
                if(!empty($result_warranty))
                {
                   $assign_role = $result_warranty->assign_role;
                   $assign_uniqueID = $result_warranty->warranty_assign;
                   $create_role = $result_warranty->role;
                   $create_id = $result_warranty->user_id;
                   $warranty_status_name = $result_warranty->warranty_status;

                    $warranty_info = array(
                        'message_note' => $update_post['note'],
                        'title_message' => 'Warranty Product Message',
                        'name' => $result_warranty->name,
                        'emailID' => $result_warranty->emailID,
                        'product_model' => $result_warranty->product_model,
                        'product_serial_number' => $result_warranty->product_serial_number,
                        'warranty_status' => $warranty_status[$warranty_status_name],
                        'warranty_status_color' => $warranty_status_color[$warranty_status_name],
                        'loginUrl' => URL::to('/'),
                    );

                    $search_id = '';
                    if($assign_role != '' && $assign_uniqueID != '')
                    {
                        if($assign_role == 'staff'){
                            $search_id = 'staff_id';
                        }
                        if($assign_role == 'employee'){
                            $search_id = 'employee_id';
                        }
                        $result_assign = DB::table($assign_role)->where($search_id,'=',$assign_uniqueID)->where('deleted_at','=',NULL)->first();
                        if(!empty($result_assign))
                        {
                            $emailID_assign = $result_assign->emailID;
                            if($emailID_assign != '')
                            {
                                if($main_role != $assign_role) {
                                    array_push($email_ar, $emailID_assign);
                                }
                            }
                        }
                    }


                    if(($create_role == 'dealer') || ($create_role == 'customer'))
                    {
                        if($create_role == 'customer'){
                            $warranty_info['loginUrl'] = URL::to('/customer');
                        }

                        $result_create = DB::table($create_role)->where('id','=',$create_id)->where('deleted_at','=',NULL)->first();
                        if(!empty($result_create))
                        {
                            if($result_create->emailID != '')
                            {
                                if($main_role != $create_role){
                                    array_push($email_ar,$result_create->emailID);
                                }
                            }
                        }
                    }
                }

                if(!empty($email_ar))
                {
                    foreach($email_ar as $key => $value)
                    {
                        $warranty_info['to_name'] = $value;
                        $email = $value;
                        Mail::send('email_templates.WarrantyProductmessage_note', ['data_info' => $warranty_info], function ($message) use ($email) {
                            $message->to($email)->subject('CRM - Warranty Product Message');
                        });
                    }
                }
            }

        }else{
            echo '404';
            exit;
        }
    }

    public  function getAssignUser()
    {
        $data_post = Input::all();
        if(!empty($data_post) && ($data_post['data_assign_role'] != ''))
        {
            $result = array();
            $unique_ID = '';
            $selected_unique_ID = '';

            if(isset($data_post['data_selected_user']) && ($data_post['data_selected_user'] != '') ){
                $selected_unique_ID = $data_post['data_selected_user'];
            }

            if($data_post['data_assign_role'] == 'staff')
            {
                $result = DB::table('staff')->where('deleted_at','=',NULL)->get();
                $unique_ID = 'staff_id';

            }else if($data_post['data_assign_role'] == 'employee')
            {
                $result = DB::table('employee')->where('deleted_at','=',NULL)->get();
                $unique_ID = 'employee_id';
            }else{
                echo '404';
                exit;
            }
            $html = '';
            if(!empty($result))
            {
                foreach($result as $key => $value)
                {
                    $ID = '';
                    if($unique_ID == 'staff_id')
                    {
                        $ID = $value->staff_id;
                    }if($unique_ID == 'employee_id')
                {
                    $ID = $value->employee_id;
                }

                    $selected = '';
                    if($selected_unique_ID != '' && $selected_unique_ID == $ID){
                        $selected = 'selected';
                    }

                    $html.= '<option value="'.$ID.'" '.$selected.'>';
                    $html.= $value->first_name.' '.$value->last_name;
                    $html.= '</option>';

                }
            }else{
                $html = '';
                $html.= '<option value="">No Any User</option>';
            }
            echo $html;
        } 
    }
}
