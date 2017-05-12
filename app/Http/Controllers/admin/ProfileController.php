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
 

class ProfileController extends Controller
{
	public function profile(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.profile.profile')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function profileUpdate(Request $request){
		$post= $request->all();
		$updated_at = Carbon::now()->toDateTimeString();	
		$sessionData= Session::get('adminLog');
		// print_r($post);
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(!empty($post)){
				$dataProfile=array(
					'name'=>$post['name'],
					'updated_at'=>$updated_at
				);
				$add= DB::table('admin')->where('id','=',$sessionData['adminID'])->update($dataProfile);
				//echo $add; exit;
				if($add > 0){
					//$sessionData['name']=$post['name'];
					Session::flash('operationSucess','Profile Updated successfully');
					 
				}else{
					Session::flash('operationFaild','Some thing went wrong.try again.');
				}
			}else{
				Session::flash('operationFaild','Some thing went wrong.try again.');
			}
				return Redirect::to('/admin/profile');
		}else{
			 return Redirect::to('/');
		}
	}
	public function passwordUpdate(Request $request){
		$post=$request->all();
		$updated_at = Carbon::now()->toDateTimeString();	
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
		if(!empty($post)){
			$old=DB::table('admin')->where('id','=',$sessionData['adminID'])->first();
			if(Hash::check($post['opassword'], $old->password)){
				//echo 'match';exit;
				$adminPassword=array(
					'password' => Hash::make($post['password']),
					'updated_at' => $updated_at
				);
				$add= DB::table('admin')->where('id','=',$sessionData['adminID'])->update($adminPassword);
				//echo $add; exit;
				if($add > 0){
					Session::flash('operationSucess','Password Updated successfully');
				}else{
					Session::flash('operationFaild','Some thing went wrong.try again.');
				}
			}else{
				Session::flash('operationFaild','Old Password not match');
			}
			}else{
				Session::flash('operationFaild','Some thing went wrong.try again.');
			}
			return Redirect::to('/admin/profile');
		}else{
			 return Redirect::to('/');
		}
	}
	public function updateAdminAvatar(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 $file = array('image' => Input::file('adminAvatar'));
			 $rules = array(
				'image' => 'required | mimes:jpeg,bmp,png | max:10000',
				);
			 $validator = Validator::make($file, $rules);
			 if ($validator->fails()) {
				 Session::flash('operationFaild','Invalid File type or Invalid File size !');
				  return Redirect::to('/admin/profile');
			 }else{
					 
				if (Input::file('adminAvatar')->isValid()) {
					$destinationPath = 'uploads/admin'; // upload path
					$oldAvatar=DB::table('admin')->where('id','=',$sessionData['adminID'])->first();
						if(!empty($oldAvatar->adminAvatar)){
							 File::delete($destinationPath.'/'.$oldAvatar->adminAvatar);
						}
					  $extension = Input::file('adminAvatar')->getClientOriginalExtension(); // getting image extension
					  $fileName = 'adminAvatar-'.rand(11111,99999).'.'.$extension; // renameing image
					  Input::file('adminAvatar')->move($destinationPath, $fileName); // uploading file to given path
					  // sending back with message
					  $avatarupdate=array(
						'adminAvatar' => $fileName
						);
					  $add= DB::table('admin')->where('id','=',$sessionData['adminID'])->update($avatarupdate);
						Session::flash('operationSucess','Avatar uploaded Successfully !');
					}else{
						
						Session::flash('operationFaild','Some thing went wrong.try again.');
					}
					return Redirect::to('/admin/profile');
			 }
		}else{
		 return Redirect::to('/');
		}
	}
	public function addGroupToProducts(){
		
	}
	

}
