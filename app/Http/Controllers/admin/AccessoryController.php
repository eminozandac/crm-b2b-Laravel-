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
use App\Brand;
use App\Category;
use App\Products;
use App\Variation;
use Datatables;
use URL;
use Image;
 

class AccessoryController extends Controller
{
	public function crypto_rand_secure($min, $max)
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
	public function getTokenProduct(){
		$length=4;
		$token = "";
		$codeAlphabet= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet) - 1;
		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
		}
		return $token;
	}
	
	public function accessoryCategoriesList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			$categoryData=DB::table('accessory_category')->get();
			return view('admin.accessory.accessoryCategoriesList')->with('categoryData',$categoryData);
		}else{
			 return Redirect::to('/');
		}
    }
	
	public function accessoryCategoriesAdd(Request $request){
		$post=$request->all();
		if(!empty($post['parentCategory'])){
			$post['parent_id']=base64_decode($post['parentCategory']);
		}
		unset($post['_token']);
		unset($post['parentCategory']);
	 
		$post['created_at'] = Carbon::now()->toDateTimeString();
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(!empty($post) || !empty(Input::file('categoryAvatar'))){
				if(!empty(Input::file('categoryAvatar'))){
					$file = array('image' => Input::file('categoryAvatar'));
					$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:20000',
					);
					$validator = Validator::make($file, $rules);
					if ($validator->fails()) {
						 Session::flash('operationFaild','Invalid File type or Invalid File size !');
						  return Redirect::to('/admin/accessorycategorieslist');
					}else{
						if (Input::file('categoryAvatar')->isValid()) {
							
						$destinationPath = 'uploads/accessoriescategories/'; // upload path
						 $thumb_destinationPath = 'uploads/accessoriescategories/thumb/';
						 
						  $file_type_ar = array('jpg','jpeg','png','JPG','JPEG','PNG');
						  
						  $extension = Input::file('categoryAvatar')->getClientOriginalExtension(); // getting image extension
						  $fileName = 'crmaccessory_'.rand(11111,99999).".{$extension}";  // renameing image
						  $post['categoryAvatar']= $fileName;
						   $upload_success = Input::file('categoryAvatar')->move($destinationPath, $fileName); // uploading file to given path
						    if($upload_success)
							{
								if(in_array($extension,$file_type_ar))
								{
									$images_name = $destinationPath.$fileName;
									$large_img = Image::make($images_name);
									$large_img->resize(900, 900);
									$large_img->save($images_name);

									/*create thumb*/
									$thub_images_name = $thumb_destinationPath.$fileName;
									$thumb_img = Image::make($images_name);
									$thumb_img->resize(800, 600);
									$thumb_img->save($thub_images_name);
								}

							} else {
								echo 'error'; exit;
							}
						  // sending back with message
						}	
					}
				}
				$avatarupdate=array();
				foreach($post as $k=>$v){
					$avatarupdate = array_add($avatarupdate, $k, $v);
				}
				$add= DB::table('accessory_category')->insert($avatarupdate);
				if($add > 0){
					Session::flash('operationSucess','Brand Updated Successfully !');
				}else{
					  Session::flash('operationFaild','Some thing Went wrong!');
				}
			}else{
				Session::flash('operationFaild','Some thing Went wrong!');
			}
			return Redirect::to('/admin/accessorycategorieslist');
		}else{
			return Redirect::to('/');
		}
    }

	public function accessoryEditCategory($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
		 
			$categoryData=DB::table('accessory_category')->where('id','=',base64_decode($id))->first();
			return view('admin.accessory.editAccessoryCategory')->with('categoryData',$categoryData);
		}else{
			 return Redirect::to('/');
		}
	}

	public function accessoryUpdateCategory(Request $request){
		$post=$request->all();
		$ctaegoryid=base64_decode($post['categoryToken']);
		if(!empty($post['parentCategory'])){
			$post['parent_id']=base64_decode($post['parentCategory']);
		}
		 
		unset($post['categoryToken']);
		unset($post['_token']);
		unset($post['parentCategory']);
	 
		 
		$post['updated_at'] = Carbon::now()->toDateTimeString();
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(!empty($post) || !empty(Input::file('categoryAvatar'))){
				if(!empty(Input::file('categoryAvatar'))){
					$file = array('image' => Input::file('categoryAvatar'));
					$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:20000',
					);
					$validator = Validator::make($file, $rules);
					if ($validator->fails()) {
						 Session::flash('operationFaild','Invalid File type or Invalid File size !');
						  return Redirect::to('/admin/accessorycategorieslist');
					}else{
						if (Input::file('categoryAvatar')->isValid()) 
						{							
							// upload path
							$destinationPath = "uploads/accessoriescategories/"; 
							$thumb_destinationPath = "uploads/accessoriescategories/thumb/";
							
							// getting image extension
							$extension = Input::file('categoryAvatar')->getClientOriginalExtension(); 
							
							$file_type_ar = array('jpg','jpeg','png','JPG','JPEG','PNG');
							// renameing image
							$fileName = 'crmaccessory_'.rand(11111,99999).".{$extension}"; 
							
							$post['categoryAvatar'] = $fileName;
							$categoryData=DB::table('accessory_category')->where('id','=',$ctaegoryid)->first();
							  if(!empty($categoryData->categoryAvatar))
							  {
								   File::delete($destinationPath.'/'.$categoryData->categoryAvatar);
								   File::delete($thumb_destinationPath.'/'.$categoryData->categoryAvatar);
							  } 
						  
						  // uploading file to given path
						   $upload_success = Input::file('categoryAvatar')->move($destinationPath, $fileName); 
						    if($upload_success)
							{
								if(in_array($extension,$file_type_ar))
								{
									$images_name = $destinationPath.$fileName;
									$large_img = Image::make($images_name);
									$large_img->resize(900, 900);
									$large_img->save($images_name);

									/*create thumb*/
									$thub_images_name = $thumb_destinationPath.$fileName;
									$thumb_img = Image::make($images_name);
									$thumb_img->resize(800, 600);
									$thumb_img->save($thub_images_name);
								}
							} else {
								echo 'error'; exit;
							}
						}	
					}
				}
					
				$avatarupdate=array();
				  foreach($post as $k=>$v){
					$avatarupdate = array_add($avatarupdate, $k, $v);
					}
				  $add= DB::table('accessory_category')->where('id','=',$ctaegoryid)->update($avatarupdate);
				  if($add > 0){
					Session::flash('operationSucess','Category Updated Successfully !');
				  }else{
					  Session::flash('operationFaild','Some thing Went wrong!');
				  }
			}else{
				Session::flash('operationFaild','Invalid File type or Invalid File size !');
			
			}
			return Redirect::to('/admin/accessorycategorieslist');
 			 
		}else{
			 return Redirect::to('/');
		}
    }

	public function accessoryDeleteCategory($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$catid=base64_decode($id);
 
			$getchild=DB::table('accessory_category')->where('parent_id','=',$catid)->get();
			$getimg=DB::table('accessory_category')->where('id','=',$catid)->first();
			//print_r($getchild);
			//exit;
			if($getimg->parent_id !=0){
				$parent=array('parent_id' => $getimg->parent_id);
			}else{
				$parent=array('parent_id' => 0);
			}
			$destinationPath = 'uploads/accessoriescategories/'; // upload path
			$thumb_destinationPath = "uploads/accessoriescategories/thumb/";
			foreach($getchild as $child){
				//echo $child->id;
				$updt=DB::table('accessory_category')->where('parent_id','=',$catid)->update($parent);
			}
			if(!empty($getimg->categoryAvatar)){
				 File::delete($destinationPath.$getimg->categoryAvatar);
				 File::delete($thumb_destinationPath.$getimg->categoryAvatar);
			}
			$del=array(
			'deleted_at' => Carbon::now()->toDateTimeString()
			);
			$del=DB::table('accessory_category')->where('id','=',$catid)->update($del);
			if($del > 0){
				Session::flash('operationSucess','Category Deleted Successfully !');
			}else{
				Session::flash('operationFaild','Some thing Went wrong');
			}
			return Redirect::to('/admin/accessorycategorieslist');
		}else{
			 return Redirect::to('/');
		}
	}
	
	public function accessoryList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			return view('admin.accessory.accessoryList')->with('sessionData',$sessionData);
			
			
		}else{
			return Redirect::to('/');
		}
	}
	public function addAccessory(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.accessory.addAccessories')->with('sessionData',$sessionData);
			
		}else{
			return Redirect::to('/');
		}
	}
	public function addAccessoryDB(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			if(isset($post) && !empty($post)){
				if(!empty($post['sku'])){
				$getSame=DB::table('product_accessories')->where('deleted_at','=',NULL)->where('sku','=',$post['sku'])->first();
				if(!empty($getSame)){
					//return view('admin.accessory.editAccessory')->with('accessoryID',$id);
					Session::flash('operationFaild','SKU already exist in system');
				    return Redirect::to('/admin/accessorylist/');
					exit;
				}
			}
				if(Input::file('accessory_image') && !empty(Input::file('accessory_image'))){
					 
					$file = array('image' => Input::file('accessory_image'));
					$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:20000',
					);
					$validator = Validator::make($file, $rules);
					if ($validator->fails()) {
						 Session::flash('operationFaild','Invalid File type or Invalid File size !');
						  return Redirect::to('/admin/accessorylist');
					}else{
						if (Input::file('accessory_image')->isValid()) {
							
						$destinationPath = 'uploads/accessories/'; // upload path
						 $thumb_destinationPath = 'uploads/accessories/thumb/';
						   $file_type_ar = array('jpg','jpeg','png','JPG','JPEG','PNG');
						  $extension = Input::file('accessory_image')->getClientOriginalExtension(); // getting image extension
						 $fileName = 'crmaccessory_'.rand(11111,99999).".{$extension}";
						  $accessoryImage= $fileName;
						  
						 
						  $upload_success = Input::file('accessory_image')->move($destinationPath, $fileName); // uploading file to given path
						    if($upload_success)
							{
								if(in_array($extension,$file_type_ar))
								{
									$images_name = $destinationPath.$fileName;
									$large_img = Image::make($images_name);
									$large_img->resize(900, 900);
									$large_img->save($images_name);

									/*create thumb*/
									$thub_images_name = $thumb_destinationPath.$fileName;
									$thumb_img = Image::make($images_name);
									$thumb_img->resize(800, 600);
									$thumb_img->save($thub_images_name);
								}

							} else {
								echo 'error'; exit;
							}
						  // sending back with message
						}	
					}
				}else{
					$accessoryImage='';
				}
				
				
				$accessoriesUnique='';
				for($j=0;$j < 4;$j++){
					if($j!=3){$dash='-';}else{$dash='';}
					$accessoriesUnique .= $this->getTokenProduct().$dash;
				}
				
				//print_r($post);
				if(isset($post['visibility'])){
					$visibility=1;
				}else{
					$visibility=0;
				}
				$dataSaveDataArray=array(
					'accessory_name'=>$post['accessory_name'],
					'category_id'=>$post['category'],
					'accessoriesToken'=>$accessoriesUnique,
					'brand_id'=>$post['brand'],
					'visibility'=>$visibility,
					'price'=>$post['price'],
					'accessory_qty'=>$post['accessory_qty'],
					'sku'=>$post['sku'],
					'warehouse'=>$post['warehouse'],
					'accessory_description'=>$post['accessory_description'],
					'accessory_image'=>$accessoryImage,
					'created_at'=>Carbon::now()->toDateTimeString()
				);
			 
				$dataSaveDataInsert=DB::table('product_accessories')->insert($dataSaveDataArray);
				if($dataSaveDataInsert > 0){
					
					Session::flash('operationSucess','Accessory Added successfully!');
				}else{
					Session::flash('operationFaild','Some thing went wrong.try again.');
				}
				
				return Redirect::to('/admin/accessorylist');
			}else{
				Session::flash('operationFaild','Some thing went wrong.try again.');
				return Redirect::to('/admin/addAccessories');
			}
		}else{
			
			return Redirect::to('/');
		}
	}
	public function deleteAccessory(Request $request){
		$post=$request->all();
		$newData=array(
			'deleted_at'=> Carbon::now()->toDateTimeString()
		);
		if(isset($post) && !empty($post)){
			$product_accessoriesUpadte=DB::table('product_accessories')->where('accessoryID','=',$post['accessory'])->update($newData);
		}
	}
	 public function editAccessory($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			return view('admin.accessory.editAccessory')->with('accessoryID',$id);
			 
		}else{
			
			return Redirect::to('/');
		}
	} 
	public function updateAccessory(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			$post= $request->all();
			//print_r($post);exit;
			if(!empty($post['sku'])){
				$getSame=DB::table('product_accessories')->where('accessoryID','!=',base64_decode($post['accessory']))->where('deleted_at','=',NULL)->where('sku','=',$post['sku'])->first();
				if(!empty($getSame)){
					//return view('admin.accessory.editAccessory')->with('accessoryID',$id);
					Session::flash('operationFaild','SKU already exist in system');
				   return Redirect::to('/admin/editaccessory/'.$post['accessory']);
					exit;
				}
			}
			if(isset($post) && !empty($post)){
				
				if(isset($post['visibility'])){
					$visibility=1;
				}else{
					$visibility=0;
				}
				
				$updateArray=array(
					'accessory_name'=>$post['accessory_name'],
					'category_id'=>$post['category'],
					'brand_id'=>$post['brand'],
					'price'=>$post['price'],
					'sku'=>$post['sku'],
					'warehouse'=>$post['warehouse'],
					'accessory_qty'=>$post['accessory_qty'],
					'visibility'=>$visibility,
					'accessory_description'=>$post['accessory_description'],
					'updated_at'=>Carbon::now()->toDateTimeString(),
				);
				
				if(Input::file('accessory_image') && !empty(Input::file('accessory_image'))){
					 
					$file = array('image' => Input::file('accessory_image'));
					$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:20000',
					);
					$validator = Validator::make($file, $rules);
					if ($validator->fails()) {
						 Session::flash('operationFaild','Invalid File type or Invalid File size !');
						  return Redirect::to('/admin/accessorylist');
					}else{
						if (Input::file('accessory_image')->isValid()) {
							
							$destinationPath = 'uploads/accessories/'; // upload path
							$thumb_destinationPath = "uploads/accessories/thumb/";
							  $file_type_ar = array('jpg','jpeg','png','JPG','JPEG','PNG');
							$extension = Input::file('accessory_image')->getClientOriginalExtension(); // getting image extension
							$fileName = 'crmaccessory_'.rand(11111,99999).".{$extension}";// renameing image
							$updateArray['accessory_image']= $fileName;
							$accessoryData=DB::table('product_accessories')->where('accessoryID','=',base64_decode($post['accessory']))->first();
							if(!empty($accessoryData->accessory_image)){
								File::delete($destinationPath.$accessoryData->accessory_image);
								File::delete($thumb_destinationPath.$accessoryData->accessory_image);
							}
							 
							
							$upload_success = Input::file('accessory_image')->move($destinationPath, $fileName); // uploading file to given path
						    if($upload_success)
							{
								if(in_array($extension,$file_type_ar))
								{
									$images_name = $destinationPath.$fileName;
									$large_img = Image::make($images_name);
									$large_img->resize(900, 900);
									$large_img->save($images_name);

									/*create thumb*/
									$thub_images_name = $thumb_destinationPath.$fileName;
									$thumb_img = Image::make($images_name);
									$thumb_img->resize(800, 600);
									$thumb_img->save($thub_images_name);
								}

							} else {
								echo 'error'; exit;
							}
						}	
					}
				}else{
					 
				}
				
				
				
				$updateAccessoryData=DB::table('product_accessories')->where('accessoryID','=',base64_decode($post['accessory']))->update($updateArray);
				
				Session::flash('operationSucess','Accessory Updated successfully.');
				return Redirect::to('/admin/accessorylist');
			}else{
				Session::flash('operationFaild','Some thing went wrong.try again.');
				return Redirect::to('/admin/accessorylist');
			}
		}else{
			
			return Redirect::to('/');
		}
	} 
}
