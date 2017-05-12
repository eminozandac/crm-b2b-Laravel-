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
use URL;
use File;
use App\WarrantyProduct;
use App\WarrantyProductNote;
use App\Http\Controllers\logdata\LogController;

class LoginController extends Controller
{
    public function index()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            return View::make('admin/dashboard');

        }else{
            return View::make('admin/index');
        }
    }

	public function errorpage()
    {
        return View::make('admin/404');
    }

	public function loginform(Request $request)
    {
		if( isset($request) )
        {
            $emailID = $request->input('email');
            $password = $request->input('password');
			$result= DB::table('admin')->where('email',$emailID)->first();

			if(count($result) != 0){

				if (Hash::check($request->input('password'), $result->password))
                {
					$userData=array(
						'adminID'=>$result->id,
                        'unique_ID'=>'',
						'name'=>$result->name,
						'email'=>$result->email,
						'role'=>$result->role
					);
					\Session::set('adminLog' , $userData);
                     Session::save();

                    $description = $result->name.' was login';
                    $logdata = array();
                    $logdata['role'] = 'admin';
                    $logdata['role_id'] = $result->id;
                    $logdata['operation'] = 'Login';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    $result_logdata = (new LogController)->index($logdata);

					 return Redirect::to('/admin/dashboard');
				}else{
					Session::flash('operationFaild','Wrong Username or Password !');
					return Redirect::to('/');
				}

			}else{

                $result= DB::table('dealer')->where('emailID',$emailID)->first();
                if(count($result) != 0)
                {
                    if (Hash::check($request->input('password'), $result->password))
                    {
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


                        $description = $result->first_name.' '.$result->last_name.' was login';
                        $logdata = array();
                        $logdata['role'] = 'dealer';
                        $logdata['role_id'] = $result->id;
                        $logdata['operation'] = 'Login';
                        $logdata['description'] = $description;
                        $logdata['role_date'] = date('Y-m-d');

                        $result_logdata = (new LogController)->index($logdata);


                        return Redirect::to('/dealer/dashboard');
                    }else{
                        Session::flash('operationFaild','Wrong Username or Password !');
                        return Redirect::to('/');
                    }
                }else{

					$emailID = $request->input('email');
                    $password = $request->input('password');
                    $result= DB::table('staff')->where('emailID',$emailID)->where('status',1)->first();
                    if(count($result) != 0)
                    {
                        if (Hash::check($request->input('password'), $result->password))
                        {
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

                        $emailID = $request->input('email');
                        $password = $request->input('password');
                        $result= DB::table('employee')->where('emailID',$emailID)->where('status',1)->first();
                        if(count($result) != 0)
                        {
                            if (Hash::check($request->input('password'), $result->password))
                            {
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
                        }else {
                            Session::flash('operationFaild', 'Wrong Username or Password !');
                            return Redirect::to('/');
                        }
					}
				}
			}
        }else {	
            Session::flash('operationFaild','Wrong Username or Password !');
            return Redirect::to('/');
        } 
	}

	public function logout(){
        \Session::forget('adminLog');
        \Session::save();
        return Redirect::to('/');
	}


	public function dashboard()
    {
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.dashboard')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }

	public function forgotPasswordAdmin(Request $request){
		$post=$request->all();
			if(!empty($post)){
			$email= $post['email'];
			$qryPasswordUpdate= DB::table('admin')->where('email',$email)->first();
			
			 $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
			 $password = '';
			 for ($i = 0; $i < 10; $i++) {
				  $password .= $characters[rand(0, strlen($characters) - 1)];
			 }
			if(!empty($qryPasswordUpdate->id) && $qryPasswordUpdate->id > 0){
				$getUserFirstName= DB::table('admin')->where('id',$qryPasswordUpdate->id)->first();
				$updatePassword= array(
					'password'=>Hash::make($password),
					'updated_at' =>Carbon::now()->toDateTimeString()
				);
				$qryUpdatePassword= DB::table('admin')->where('id','=',$qryPasswordUpdate->id)->update($updatePassword);
				if($qryUpdatePassword>0){
					 $data_user_password_mail =array(
						'first_name' => $getUserFirstName->	name,
						'password' =>  $password,
						'email' => $qryPasswordUpdate->email,
                         "loginUrl" => URL::to('/'),
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
				$qryPasswordUpdateDealer= DB::table('dealer')->where('emailID',$email)->first();
				//print_r($qryPasswordUpdateDealer);
				if(!empty($qryPasswordUpdateDealer->id) && $qryPasswordUpdateDealer->id > 0){
					
					$getUserFirstName= DB::table('dealer')->where('id',$qryPasswordUpdateDealer->id)->first();
					$updatePassword= array(
						'password'=>Hash::make($password),
						'updated_at' =>Carbon::now()->toDateTimeString()
					);
					$qryUpdatePassword= DB::table('dealer')->where('id','=',$qryPasswordUpdateDealer->id)->update($updatePassword);
					if($qryUpdatePassword>0){
						 $data_user_password_mail =array(
							'first_name' => $getUserFirstName->first_name,
							'password' =>  $password,
							'email' => $qryPasswordUpdateDealer->emailID,
							 "loginUrl" => URL::to('/'),
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
					$qryPasswordUpdateDealer= DB::table('staff')->where('emailID',$email)->first();
					
					if(!empty($qryPasswordUpdateDealer->id) && $qryPasswordUpdateDealer->id > 0){
						
						$getUserFirstName= DB::table('staff')->where('id',$qryPasswordUpdateDealer->id)->first();
						$updatePassword= array(
							'password'=>Hash::make($password),
							'updated_at' =>Carbon::now()->toDateTimeString()
						);
						$qryUpdatePassword= DB::table('staff')->where('id','=',$qryPasswordUpdateDealer->id)->update($updatePassword);
						if($qryUpdatePassword>0){
							 $data_user_password_mail =array(
								'first_name' => $getUserFirstName->first_name,
								'password' =>  $password,
								'email' => $qryPasswordUpdateDealer->emailID,
								 "loginUrl" => URL::to('/'),
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
						
						$qryPasswordUpdateDealer= DB::table('employee')->where('emailID',$email)->first();
						if(!empty($qryPasswordUpdateDealer->id) && $qryPasswordUpdateDealer->id > 0){
						
							$getUserFirstName= DB::table('employee')->where('id',$qryPasswordUpdateDealer->id)->first();
							$updatePassword= array(
								'password'=>Hash::make($password),
								'updated_at' =>Carbon::now()->toDateTimeString()
							);
							$qryUpdatePassword= DB::table('employee')->where('id','=',$qryPasswordUpdateDealer->id)->update($updatePassword);
							if($qryUpdatePassword>0){
								 $data_user_password_mail =array(
									'first_name' => $getUserFirstName->first_name,
									'password' =>  $password,
									'email' => $qryPasswordUpdateDealer->emailID,
									 "loginUrl" => URL::to('/'),
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
					}
				}
			}
		}else{
			Session::flash('operationFaild','Some thing went wrong!');
		}
		return Redirect::to('/');
	}


    public function warrantyHeaderMessage()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        $data = array();
        if(isset($id) && ($id != 0))
        {
            $count = WarrantyProductNote::select('id')
                ->where('role','!=','admin')
                ->count();

            $today = date('Y-m-d');
            //->where('created_at','like',$today.'%')
            $result_warranty_note = WarrantyProductNote::select('*')
                ->where('role','!=','admin')
                ->orderBy('id','DESC')->get();

            $data['warranty_notedash'] = $result_warranty_note;
            return View::make('admin/dashboard',$data);
        }
    }
}
