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
 

class ProductController extends Controller
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
	public function productList()
    {
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
				$products= new Products ();
			$productData=$products->all();
			return view('admin.product.productList')->with('productData',$productData);
		}else{
			 return Redirect::to('/');
		}
    }

	public function productDetail(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			return view('admin.product.productDetail')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function brandList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$brand= new Brand();
			$branddata=$brand->where('deleted_at','=',NULL)->get();
 			return view('admin.product.brandList')->with('branddata',$branddata);
		}else{
			 return Redirect::to('/');
		}
    }
	public function productCategoriesList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$category= new Category();
			$categoryData=$category->all();
			return view('admin.product.productCategoriesList')->with('categoryData',$categoryData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function addProducts(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			return view('admin.product.addProducts')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function addBrand(Request $request){
		$post=$request->all();
		unset($post['_token']);
		$post['created_at'] = Carbon::now()->toDateTimeString();
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(!empty($post) || !empty(Input::file('brandAvatar'))){
				$file = array('image' => Input::file('brandAvatar'));
				$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:10000',
					);
				$validator = Validator::make($file, $rules);
				if ($validator->fails()) {
					 Session::flash('operationFaild','Invalid File type or Invalid File size !');
					  return Redirect::to('/admin/brandList');
				}else{
					if (Input::file('brandAvatar')->isValid()) {
						$destinationPath = 'uploads/brands'; // upload path
						  $extension = Input::file('brandAvatar')->getClientOriginalExtension(); // getting image extension
						  $fileName = 'crmbrand-'.rand(11111,99999).'.'.$extension; // renameing image
						  $post['brandAvatar']= $fileName;
						//print_r($post);exit;
						  Input::file('brandAvatar')->move($destinationPath, $fileName); // uploading file to given path
						  // sending back with message
						  $avatarupdate=array();
						  foreach($post as $k=>$v){
							$avatarupdate = array_add($avatarupdate, $k, $v);
							}
						  $add= DB::table('brand')->insert($avatarupdate);
						  if($add > 0){
							Session::flash('operationSucess','Brand Updated Successfully !');
						  }else{
							  Session::flash('operationFaild','Some thing Went wrong!');
						  }
					}else{
						Session::flash('operationFaild','Invalid File type or Invalid File size !');
					}
				}
			}else{
				Session::flash('operationFaild','Some thing Went wrong!');
			}
			return Redirect::to('/admin/brandList');
 			 
		}else{
			 return Redirect::to('/');
		}
    }

	public function editBrand($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
				$brand= new Brand();
			$branddata=$brand->where('id','=',base64_decode($id))->first();
			return view('admin.product.editBrand')->with('brandData',$branddata);
		}else{
			 return Redirect::to('/');
		}
	}
    
	public function updateBrand(Request $request){
		$post=$request->all();
		$btandid=base64_decode($post['brandToken']);
		unset($post['brandToken']);
		unset($post['_token']);
		$brand= new Brand();
		$post['updated_at'] = Carbon::now()->toDateTimeString();
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(!empty($post) || !empty(Input::file('brandAvatar'))){
				if(Input::file('brandAvatar')){
					$file = array('image' => Input::file('brandAvatar'));
					$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:10000',
					);
					$validator = Validator::make($file, $rules);
					if ($validator->fails()) {
						 Session::flash('operationFaild','Invalid File type or Invalid File size !');
						  return Redirect::to('/admin/brandList');
					}else{
						if (Input::file('brandAvatar')->isValid()) {
							
						$destinationPath = 'uploads/brands'; // upload path
						  $extension = Input::file('brandAvatar')->getClientOriginalExtension(); // getting image extension
						  $fileName = 'crmbrand-'.rand(11111,99999).'.'.$extension; // renameing image
						  $post['brandAvatar']= $fileName;
						  $branddata=$brand->where('id','=',$btandid)->first();
						  if(!empty($branddata->brandAvatar)){
							   File::delete($destinationPath.'/'.$branddata->brandAvatar);
						  }
						  Input::file('brandAvatar')->move($destinationPath, $fileName); // uploading file to given path
						  // sending back with message
						}	
					}
				}
					
				$avatarupdate=array();
				  foreach($post as $k=>$v){
					$avatarupdate = array_add($avatarupdate, $k, $v);
					}
				  $add= DB::table('brand')->where('id','=',$btandid)->update($avatarupdate);
				  if($add > 0){
					Session::flash('operationSucess','Brand Added Successfully !');
				  }else{
					  Session::flash('operationFaild','Some thing Went wrong!');
				  }
			}else{
				Session::flash('operationFaild','Invalid File type or Invalid File size !');
			
			}
			return Redirect::to('/admin/brandList');
 			 
		}else{
			 return Redirect::to('/');
		}
    }

	public function deleteBrand($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$bndid=base64_decode($id);
			$brand= new Brand();
			 $destinationPath = 'uploads/brands'; // upload path
			$getimg=$brand->where('id','=',$bndid)->first();
			
			if(!empty($getimg->brandAvatar)){
				 File::delete($destinationPath.'/'.$getimg->brandAvatar);
			}
			$del=array(
				'deleted_at' => Carbon::now()->toDateTimeString()
				);
			$del=$brand->where('id','=',$bndid)->update($del);
			if($del > 0){
				Session::flash('operationSucess','Brand Deleted Successfully !');
			}else{
				Session::flash('operationFaild','Some thing Went wrong !');
			
			}
			return Redirect::to('/admin/brandList');
		}else{
			 return Redirect::to('/');
		}
	}

	public function productCategoriesAdd(Request $request){
		$post=$request->all();
		if(!empty($post['parentCategory'])){
			$post['parent_id']=base64_decode($post['parentCategory']);
		}
		unset($post['_token']);
		unset($post['parentCategory']);
		if(isset($post['showforall']) && !empty($post['showforall'])){}else{$post['showforall']=0;}
		$post['created_at'] = Carbon::now()->toDateTimeString();
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(!empty($post) || !empty(Input::file('categoryAvatar'))){
				if(!empty(Input::file('categoryAvatar'))){
					$file = array('image' => Input::file('categoryAvatar'));
					$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:10000',
					);
					$validator = Validator::make($file, $rules);
					if ($validator->fails()) {
						 Session::flash('operationFaild','Invalid File type or Invalid File size !');
						  return Redirect::to('/admin/productCategoriesList');
					}else{
						if (Input::file('categoryAvatar')->isValid()) {
							
						$destinationPath = 'uploads/categories'; // upload path
						  $extension = Input::file('categoryAvatar')->getClientOriginalExtension(); // getting image extension
						  $fileName = 'crmcategoty-'.rand(11111,99999).'.'.$extension; // renameing image
						  $post['categoryAvatar']= $fileName;
						  Input::file('categoryAvatar')->move($destinationPath, $fileName); // uploading file to given path
						  // sending back with message
						}	
					}
				}
				$avatarupdate=array();
				foreach($post as $k=>$v){
					$avatarupdate = array_add($avatarupdate, $k, $v);
				}
				$add= DB::table('category')->insert($avatarupdate);
				if($add > 0){
					Session::flash('operationSucess','Brand Updated Successfully !');
				}else{
					  Session::flash('operationFaild','Some thing Went wrong!');
				}
			}else{
				Session::flash('operationFaild','Some thing Went wrong!');
			}
			return Redirect::to('/admin/productCategoriesList');
		}else{
			return Redirect::to('/');
		}
    }

	public function editCategory($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$category= new Category();
			$categoryData=$category->where('id','=',base64_decode($id))->first();
			return view('admin.product.editCategory')->with('categoryData',$categoryData);
		}else{
			 return Redirect::to('/');
		}
	}

	public function updateCategory(Request $request){
		$post=$request->all();
		$ctaegoryid=base64_decode($post['categoryToken']);
		if(!empty($post['parentCategory'])){
			$post['parent_id']=base64_decode($post['parentCategory']);
		}
		if(isset($post['showforall']) && !empty($post['showforall'])){}else{$post['showforall']=0;}
		unset($post['categoryToken']);
		unset($post['_token']);
		unset($post['parentCategory']);
	 
		$category= new Category();
		$post['updated_at'] = Carbon::now()->toDateTimeString();
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(!empty($post) || !empty(Input::file('categoryAvatar'))){
				if(!empty(Input::file('categoryAvatar'))){
					$file = array('image' => Input::file('categoryAvatar'));
					$rules = array(
					'image' => 'required | mimes:jpeg,bmp,png,jpg | max:10000',
					);
					$validator = Validator::make($file, $rules);
					if ($validator->fails()) {
						 Session::flash('operationFaild','Invalid File type or Invalid File size !');
						  return Redirect::to('/admin/brandList');
					}else{
						if (Input::file('categoryAvatar')->isValid()) {
							
						$destinationPath = 'uploads/categories'; // upload path
						  $extension = Input::file('categoryAvatar')->getClientOriginalExtension(); // getting image extension
						  $fileName = 'crmbrand-'.rand(11111,99999).'.'.$extension; // renameing image
						  $post['categoryAvatar']= $fileName;
						  $categoryData=$category->where('id','=',$ctaegoryid)->first();
						  if(!empty($categoryData->categoryAvatar)){
							   File::delete($destinationPath.'/'.$categoryData->categoryAvatar);
						  }
						  Input::file('categoryAvatar')->move($destinationPath, $fileName); // uploading file to given path
						  // sending back with message
						}	
					}
				}
					
				$avatarupdate=array();
				  foreach($post as $k=>$v){
					$avatarupdate = array_add($avatarupdate, $k, $v);
					}
				  $add= DB::table('category')->where('id','=',$ctaegoryid)->update($avatarupdate);
				  if($add > 0){
					Session::flash('operationSucess','Category Updated Successfully !');
				  }else{
					  Session::flash('operationFaild','Some thing Went wrong!');
				  }
			}else{
				Session::flash('operationFaild','Invalid File type or Invalid File size !');
			
			}
			return Redirect::to('/admin/productCategoriesList');
 			 
		}else{
			 return Redirect::to('/');
		}
    }

	public function deleteCategory($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$catid=base64_decode($id);
			$category= new Category();
			$getchild=$category->where('parent_id','=',$catid)->get();
			$getimg=$category->where('id','=',$catid)->first();
			//print_r($getchild);
			//exit;
			if($getimg->parent_id !=0){
				$parent=array('parent_id' => $getimg->parent_id);
			}else{
				$parent=array('parent_id' => 0);
			}
			$destinationPath = 'uploads/categories'; // upload path
			foreach($getchild as $child){
				//echo $child->id;
				$updt=$category->where('parent_id','=',$catid)->update($parent);
			}
			if(!empty($getimg->categoryAvatar)){
				 File::delete($destinationPath.'/'.$getimg->categoryAvatar);
			}
			$del=array(
			'deleted_at' => Carbon::now()->toDateTimeString()
			);
			$del=$category->where('id','=',$catid)->update($del);
			if($del > 0){
				Session::flash('operationSucess','Category Deleted Successfully !');
			}else{
				Session::flash('operationFaild','Some thing Went wrong');
			}
			return Redirect::to('/admin/productCategoriesList');
		}else{
			 return Redirect::to('/');
		}
	}

	public function addProductsDB(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			unset($post['_token']);
			 $post['created_at'] = Carbon::now()->toDateTimeString();
			 if(isset($post['visibility']) && $post['visibility']=='on'){$post['visibility']='1';}else{$post['visibility']='0';}
			  
				$productUnique='';
				for($j=0;$j < 4;$j++){
					if($j!=3){$dash='-';}else{$dash='';}
					$productUnique .= $this->getTokenProduct().$dash;
				}
				$post['ID']= $productUnique;
				 
			if(!empty($post)){
				//print_r($post); 
				$productadd=array();
				foreach($post as $k=>$v){
					$productadd = array_add($productadd, $k, $v);
				}
				$add= DB::table('products')->insertGetId($productadd);
				if($add > 0){
					Session::flash('operationSucess','Product Added Successfully !');
				}else{
					  Session::flash('operationFaild','Some thing Went wrong!');
				}
			}else{
				Session::flash('operationFaild','Some thing Went wrong');
			}
			return Redirect::to('/admin/editProducts/'.base64_encode($add));
		}else{
			 return Redirect::to('/');
		}
	}

	public function editProducts($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$products= new Products ();
			$productData=$products->where('product_id','=',base64_decode($id))->first();
		// print_r($productData);exit;
			return view('admin.product.editProducts')->with('productData',$productData);
		}else{
			 return Redirect::to('/');
		}
    }

	public function updateProductsinfo(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			$id = base64_decode($post['productToken']);
			unset($post['_token']);
			unset($post['productToken']);
			 $post['updated_at'] = Carbon::now()->toDateTimeString();
			 if(isset($post['visibility']) && $post['visibility']=='on'){$post['visibility']='1';}else{$post['visibility']='0';}
			if(!empty($post)){
				//print_r($post); exit;
				 
				$productadd=array();
				foreach($post as $k=>$v){
					$productadd = array_add($productadd, $k, $v);
				}
				 
				$add= DB::table('products')->where('product_id','=',$id)->update($productadd);
			 
				//print_r($add);
				if($add > 0){
					Session::flash('operationSucess','Product Updated Successfully !');
				}else{
					  Session::flash('operationFaild','Some thing Went wrong!');
				}
			}else{
				Session::flash('operationFaild','Some thing Went wrong');
			}

            $url = '/admin/editProducts/'.base64_encode($id);
            return Redirect::to($url);
		}else{
			 return Redirect::to('/');
		}
	}

	public function addProductsVariation(Request $request){
		$sessionData=Session::get('adminLog');
		
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
				//print_r($post);exit;
				if(!empty($post['batch']) && !empty($post['product_color']))
				{
					$dup=DB::table('variation')->where('product_id','=',base64_decode($post['productToken']))->where('batch','=',$post['batch'])->where('product_color','=',$post['product_color'])->where('product_status','=',$post['product_status'])->where('deleted_at','=',NULL)->first();
					$batch=DB::table('products')->where('product_id','=',base64_decode($post['productToken']))->first();
				   
						$variationToken='';
					//print_r($dup);exit; 
					for($j=0;$j < 4;$j++){
					    $variationToken .= $this->getTokenProduct();
					}
					if(!empty($post['stockdate'])){
									$stockdate=date('Y-m-d', strtotime($post['stockdate']));
							 }else{
								$stockdate=NULL;
								
							 }
							 
							//print_r($dup);exit;
					if(!empty($dup)){
						//Session::flash('operationFaild','Duplicate Data');
						$datavar=array(
								'product_id'=>base64_decode($post['productToken']),
								'batch'=>$post['batch'],
								'product_status'=>$post['product_status'],
								'productStock'=>$post['productStock'],
								'stockdate'=>$stockdate,
								'variationToken'=>$variationToken,
								'model'=>$post['model'],
								'product_color'=>$post['product_color'],
								'sku'=>$post['sku'],
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							$ins=DB::table('variation')->where('variationID','=',$dup->variationID)->update($datavar);
							for($i=1;$i<=$post['productStock'];$i++){
								
								$varTranzArray=array(
									'variationID'=>$dup->variationID,
									'variationTranzToken'=>$variationToken.'_'.$dup->variationID.'_'.$i,
									'product_id'=>base64_decode($post['productToken']),
									'product_status'=>$post['product_status'],
									'qty'=>1,
									'stockdate'=>$stockdate,
									'created_at'=>Carbon::now()->toDateTimeString()
								);
								//print_r($varTranzArray); echo '<br/>';
								$addVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
							}
						
						
						Session::flash('variation','Product Updated Successfully !');
						return Redirect::to('/admin/editProducts/'.$post['productToken']);
					}else{
						
						$colorsame=DB::table('variation')->where('product_id','=',base64_decode($post['productToken']))->where('product_color','=',$post['product_color'])->where('product_status','=',$post['product_status'])->first();
						//print_r($colorsame);exit;
						//$colorsame=''; 
						
							 
						 
						if($post['product_status']=='outofstock'){
							$datavar=array(
								'product_id'=>base64_decode($post['productToken']),
								'batch'=>$post['batch'],
								'product_status'=>$post['product_status'],
								'productStock'=>0,
								'variationToken'=>$variationToken,
								'stockdate'=>$stockdate,
								'product_color'=>$post['product_color'],
								'sku'=>$post['sku'],
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							$ins=DB::table('variation')->where('product_color','=',$post['product_color'])->where('product_id','=',base64_decode($post['productToken']))->update($datavar);
						}else{
							
							$datavar=array(
								'product_id'=>base64_decode($post['productToken']),
								'batch'=>$post['batch'],
								'product_status'=>$post['product_status'],
								'productStock'=>$post['productStock'],
								'stockdate'=>$stockdate,
								'variationToken'=>$variationToken,
								'model'=>$post['model'],
								'product_color'=>$post['product_color'],
								'sku'=>$post['sku'],
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							$ins=DB::table('variation')->insertGetId($datavar);
							for($i=1;$i<=$post['productStock'];$i++){
								
								$varTranzArray=array(
									'variationID'=>$ins,
									'variationTranzToken'=>$variationToken.'_'.$ins.'_'.$i,
									'product_id'=>base64_decode($post['productToken']),
									'product_status'=>$post['product_status'],
									'qty'=>1,
									'stockdate'=>$stockdate,
									'created_at'=>Carbon::now()->toDateTimeString()
								);
								//print_r($varTranzArray); echo '<br/>';
								$addVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
							}
							//exit;
						}
						//	print_r($stkarray);exit;
						if($ins > 0){
							Session::flash('operationSucess','Product Updated Successfully !');
							
						}else{
							Session::flash('operationFaild','Some thing Went wrong');
						}
					}
				}else{
					if(empty($post['batch'])){
						Session::flash('operationFaild','Batch cannot be empty !');
					}elseif(empty($post['product_color'])){
						Session::flash('operationFaild','Color cannot be empty !');
					}else{
						
						Session::flash('operationFaild','Some thing Went wrong');
					}
				}
				Session::flash('variation','Product Updated Successfully !');
				 return Redirect::to('/admin/editProducts/'.$post['productToken']);
		}else{
			 return Redirect::to('/');
		}
	}
	 public function variationdata(Request $request)
    {
		$post=$request->all();
        $variation = new Variation;

        $data = Variation::select('*')->where('product_id','=',base64_decode($post['id']))->orderBy('variationID','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('Status', function ($data) {
                if($data->product_status != ''){
					if($data->product_status == 'instock'){
						return '<label class="label label-info"> In Stock</label>';
					}elseif($data->product_status == 'inproduction'){
						return '<label class="label label-success"> In Production</label>';
					}else{
						return '<label class="label label-danger"> Out of Stock</label>';
					}
                    //return $data->product_status;
                }else{
                    return '---';
                }
            })

            ->addColumn('Date', function ($data) {
                if(($data->stockdate != '0000-00-00')){
					$date=date('d/m/Y', strtotime($data->stockdate));
                    return $date;
                }else{
                    return '---';
                }
            })

            ->addColumn('Batch No', function ($data) {
                if($data->batch != ''){
                    return $data->batch;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Color', function ($data) {
                if($data->product_color != ''){
                    return $data->product_color;
                }else{
                    return '---';
                }
            })
			
			->addColumn('SKU', function ($data) {
                if($data->sku != ''){
                    return $data->sku;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Product Qty', function ($data) {
                if($data->productStock != ''){
                    return $data->productStock;
                }else{
                    return '---';
                }
            })

            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/editvariation', base64_encode($data->variationID));
                $delurl = URL::to('admin/deletevariation', base64_encode($data->variationID));
                return '<a href="'.$url.'"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a><a href="'.$delurl.'"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>';
            })
            ->make(true);
    }
	public function editvariation($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			// $variation = new Variation;
			//$variationData=$variation->where('variationID','=',base64_decode($id))->first();
			return view('admin.product.editvariation')->with('id',$id);
		}else{
			 return Redirect::to('/');
		}
	}
	public function updateVariation(Request $request){
		$sessionData=Session::get('adminLog');
		$post=$request->all();
		$post['updated_at'] = Carbon::now()->toDateTimeString();
		  $variationid=base64_decode($post['variationToken']);
		  $productid=$post['productToken'];
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			  if(!empty($post['variationToken'])){
					
					if(!isset($post['stockdate'])){
						$stockdate=NULL;
					}else{
						$stockdate=date('Y-m-d', strtotime($post['stockdate']));
					}
					//echo $stockdate;
					//print_r($post); exit;
						
					$colorsame=DB::table('variation')->where('product_id','=',base64_decode($post['productToken']))->where('product_status','=',$post['product_status'])->where('batch','=',$post['batch'])->where('product_color','=',$post['product_color'])->first();
					
					$getOldVariationData=DB::table('variation')->where('variationID','=',$variationid)->first();
					//print_r($colorsame);
					//exit;
					if(!empty($colorsame)){
						
						if($post['product_status'] == 'instock' || $post['product_status'] == 'factorystock' || $post['product_status'] == 'inproduction' || $post['product_status'] == 'onseaukarrival'){
							 
							$variationupd=array(
								'product_id'=>base64_decode($post['productToken']),
								'product_status'=>$post['product_status'],
								'productStock'=> $post['productStock'],
								'stockdate'=>$stockdate,
								'model'=>$post['model'],
								'batch'=>$post['batch'],
								'sku'=>$post['sku'],
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							//print_r($variationupd); exit;
							if($getOldVariationData->productStock > $post['productStock']){
								
								
								$getDiff=$getOldVariationData->productStock - $post['productStock'];
								
								for($i=1;$i<=$getDiff;$i++){
									
									$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->orderBy('variationTranzToken','DESC')->first();
									if(!empty($getVariationTranz)){
										
										$deleteTranz=DB::table('variation_tranz')->where('variationTranzToken','=',$getVariationTranz->variationTranzToken)->delete();
									}
									
								}
							}
							if($getOldVariationData->productStock < $post['productStock']){
								
								
								$getDiff=$post['productStock'] - $getOldVariationData->productStock;
								
	$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->orderBy('variationTranzToken','DESC')->first();
									$lastTranzTockenArray=explode('_',$getVariationTranz->variationTranzToken);
									 
								for($i=1;$i<=$getDiff;$i++){
									$lastRecord=$lastTranzTockenArray[2];
									$lastRecord= $lastRecord + $i; 
									$variationTranzToken=$getOldVariationData->variationToken .'_'.$variationid.'_'.$lastRecord;
									$tranzUpdateArray=array(
										'variationID'=>$variationid,
										'variationTranzToken'=>$variationTranzToken,
										'product_id'=>base64_decode($post['productToken']),
										'product_status'=>$post['product_status'],
										'qty'=>1,
										'stockdate'=>$stockdate,
										'created_at'=>Carbon::now()->toDateTimeString()
									);
									$addVarTranz=DB::table('variation_tranz')->insert($tranzUpdateArray);
									
								}
							}
							if($getOldVariationData->productStock == $post['productStock']){
								$tranzUpdateArray=array(
									'variationID'=>$variationid,
									'product_id'=>base64_decode($post['productToken']),
									'product_status'=>$post['product_status'],
									'qty'=>1,
									'stockdate'=>$stockdate,
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
								
								
								$addVarTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->update($tranzUpdateArray);
							}
							//exit;
							
						}else{
							
							$variationupd=array(
								'product_id'=>base64_decode($post['productToken']),
								'product_status'=>$post['product_status'],
								'productStock'=> 0,
								'model'=>$post['model'],
								'batch'=>$post['batch'],
								'stockdate'=>$stockdate,
								'sku'=>$post['sku'],
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							
							$addVarTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->delete();
						}
						
						$add= DB::table('variation')->where('variationID','=',$colorsame->variationID)->update($variationupd);
						
					}else{
						
						if($post['product_status'] == 'instock' || $post['product_status'] == 'factorystock' ||  $post['product_status'] == 'inproduction'  || $post['product_status'] == 'onseaukarrival'){
							$variationupd=array(
								'product_id'=>base64_decode($post['productToken']),
								'product_status'=>$post['product_status'],
								'productStock'=>$post['productStock'],
								'stockdate'=>$stockdate,
								'model'=>$post['model'],
								'batch'=>$post['batch'],
								'sku'=>$post['sku'],
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							 
							if($getOldVariationData->productStock > $post['productStock']){
								
								
								$getDiff=$getOldVariationData->productStock - $post['productStock'];
								
								for($i=1;$i<=$getDiff;$i++){
									
									$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->orderBy('variationTranzToken','DESC')->first();
									if(!empty($getVariationTranz)){
										
										$deleteTranz=DB::table('variation_tranz')->where('variationTranzToken','=',$getVariationTranz->variationTranzToken)->delete();
									}
									
								}
							}
							if($getOldVariationData->productStock < $post['productStock']){
								
								
								$getDiff=$post['productStock'] - $getOldVariationData->productStock;
								
								$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->orderBy('variationTranzToken','DESC')->first();
									$lastTranzTockenArray=explode('_',$getVariationTranz->variationTranzToken);
									 
								for($i=1;$i<=$getDiff;$i++){
									$lastRecord=$lastTranzTockenArray[2];
									$lastRecord= $lastRecord + $i; 
									$variationTranzToken=$getOldVariationData->variationToken .'_'.$variationid.'_'.$lastRecord;
									$tranzUpdateArray=array(
										'variationID'=>$variationid,
										'variationTranzToken'=>$variationTranzToken,
										'product_id'=>base64_decode($post['productToken']),
										'product_status'=>$post['product_status'],
										'qty'=>1,
										'stockdate'=>$stockdate,
										'created_at'=>Carbon::now()->toDateTimeString()
									);
									$addVarTranz=DB::table('variation_tranz')->insert($tranzUpdateArray);
									
								}
							}
							if($getOldVariationData->productStock == $post['productStock']){
								$tranzUpdateArray=array(
									'variationID'=>$variationid,
									'product_id'=>base64_decode($post['productToken']),
									'product_status'=>$post['product_status'],
									'qty'=>1,
									'stockdate'=>$stockdate,
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
								
								
								$addVarTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->update($tranzUpdateArray);
							}
							
						}else{
							
							$variationupd=array(
								'product_id'=>base64_decode($post['productToken']),
								'product_status'=>$post['product_status'],
								'productStock'=> 0,
								'model'=>$post['model'],
								'batch'=>$post['batch'],
								'stockdate'=>$stockdate,
								'sku'=>$post['sku'],
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							$addVarTranz=DB::table('variation_tranz')->where('variationID','=',$variationid)->delete();
						}
						$add= DB::table('variation')->where('variationID','=',$variationid)->update($variationupd);
						
					}
					
					//print_r($add);
					if($add > 0){
						Session::flash('operationSucess','Product Updated Successfully !');
					}else{
						  Session::flash('operationFaild','Some thing Went wrong!');
					}
				}else{
					 
						Session::flash('operationFaild','Some thing Went wrong');
					 
				}
				Session::flash('variation','Product Updated Successfully !');
				return Redirect::to('/admin/editProducts/'.$productid);
		}else{
			 return Redirect::to('/');
		}
	}
	public function deletevariation($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$decodeID=base64_decode($id);
			$varIDarr= explode("&", $decodeID, 2);
			$varID = $varIDarr[0];
			$product_id = $varIDarr[1];
			 	$del=array(
				'deleted_at' => Carbon::now()->toDateTimeString()
				);
				$vardel=DB::table('variation')->where('variationID','=',$decodeID)->update($del);
				
				//$varTranzDel=DB::table('variation_tranz')->where('variationID','=',$decodeID)->update($del);
				$varTranzDel=DB::table('variation_tranz')->where('variationID','=',$decodeID)->delete();
		 
			if($vardel > 0){
				Session::flash('operationSucess','Variation deleted Successfully !');
			}else{
				  Session::flash('operationFaild','Some thing Went wrong!');
			}
			Session::flash('variation','Product Updated Successfully !');
			return Redirect::to('/admin/editProducts/'.base64_encode($product_id));
		}else{
			 return Redirect::to('/');
		}
	}
	 public function productdatalist()
    {
        $products = new Products;

        $data = Products::select('*')->where('deleted_at','=',NULL)->orderBy('product_id','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('Product Name', function ($data) {
                if($data->productName != ''){
					if(!empty($data->productName)){
						
                     return $data->productName;
					}else{
						return '--';
					}
                }else{
                    return '---';
                }
            })

            ->addColumn('Description', function ($data) {
                if(($data->description != '')){
                   
					if(!empty($data->description)){
					$word_limit=50;
						 $words = explode(" ",$data->description);
						return implode(" ", array_splice($words, 0, $word_limit));
                    // return $data->description;
					}else{
						return '--';
					}

                }else{
                    return '---';
                }
            })

            ->addColumn('Category', function ($data) {
                if($data->category_id != ''){
					$category = new Category();
					$catename=$category->where('id','=',$data->category_id)->first();
					if(!empty($catename->categoryName)){
						
                    return $catename->categoryName;
					}else{
						return '--';
					}

                }else{
                    return '---';
                }
            })
			
			->addColumn('Brand', function ($data) {
                if($data->category_id != ''){
					$brand = new Brand();
					$brandname=$brand->where('id','=',$data->brand_id)->first();
                   
					if(!empty($brandname->brandName)){
						
                    return $brandname->brandName;
					}else{
						return '--';
					}
                   
                }else{
                    return '---';
                }
            })
			
			->addColumn('Batch', function ($data) {
                 if(($data->batch != '')){
						
                     return $data->batch;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Status', function ($data) {
                if($data->visibility != '0'){
					if($data->visibility == '1'){
						$stuts='<span class="label label-primary">Enable</span>';
					}else{
						$stuts='<span class="label label-danger">Disable</span>';
						
					}
                    return $stuts;
                }else{
                    return $stuts='<span class="label label-danger">Disable</span>';
                }
            })
			

            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/editProducts', base64_encode($data->product_id));
                $urldel = URL::to('admin/deleteProducts', base64_encode($data->product_id));
                //$delurl = URL::to('admin/deletevariation', base64_encode($data->variationID));
                return '<a href="'.$url.'"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a><a href="'.$urldel.'"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>';
            })
            ->make(true);
    }
	public function updateProductimage(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			 $file = array('image' => Input::file('productimage'));
			// print_r($file);exit;
				  $rules = array(
					'image' => 'required | mimes:jpeg,jpg,bmp,png | max:10000000',
					);
				 $validator = Validator::make($file, $rules);
				 //print_r($validator->messages()); exit;
				 if ($validator->fails()) {
						Session::flash('operationFaild','Invalid File type or Invalid File size !');
						return Redirect::to('/admin/editProducts/'.$post['productToken']);
				 }else{
					if (Input::file('productimage')->isValid()) {
						
						$destinationPath = 'uploads/products'; // upload path
						
						$oldAvatar=DB::table('productimages')->where('product_id','=',base64_decode($post['productToken']))->first();
						
						if(!empty($oldAvatar->productimage)){
							 File::delete($destinationPath.'/'.$oldAvatar->productimage);
						}
						
						$extension = Input::file('productimage')->getClientOriginalExtension(); // getting image extension
						$fileName = 'products-'.rand(11111,99999).'.'.$extension; // renameing image
						Input::file('productimage')->move($destinationPath, $fileName); // uploading file to given path
					//	exit;
						
						if(!empty($oldAvatar->productimage)){
							$avatarupdate=array(
								'productimage' => $fileName,
								'updated_at' => Carbon::now()->toDateTimeString(),
							);
							 
							$add= DB::table('productimages')->where('productimagesID','=',$oldAvatar->productimagesID)->update($avatarupdate);
						}else{
							$avatarupdate=array(
								'productimage' => $fileName,
								'product_id' => base64_decode($post['productToken']),
								'created_at' => Carbon::now()->toDateTimeString(),
							);
							$add= DB::table('productimages')->insert($avatarupdate);
							
						}
					  // sending back with message
					  if($add > 0)
						Session::flash('operationSucess','image uploaded Successfully !');
					}else{
						
						Session::flash('operationFaild','Some thing went wrong.try again.');
					}
					return Redirect::to('/admin/editProducts/'.$post['productToken']);
			 }
		}else{
			 return Redirect::to('/');
		}
    }
	public function addGroupToProducts(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post =$request->all();
			if(!empty($post)){
				if(isset($post['discountToken'])){
					$discount=array(
						'discountPer'=>$post['discountPer'],
						'updated_at' => Carbon::now()->toDateTimeString(),
					);
					$add=DB::table('discount')->where('discountID','=',base64_decode($post['discountToken']))->update($discount);
					if($add > 0){
						Session::flash('operationSucess','group updated successfully !');
					}else{
						
						Session::flash('operationFaild','Some thing went wrong.try again.');
					}
				}else{
					$discount=array(
						'groupID'=>$post['group'],
						'product_id'=>base64_decode($post['productToken']),
						'discountPer'=>$post['discountPer'],
						'created_at' => Carbon::now()->toDateTimeString(),
					);
					$add=DB::table('discount')->insert($discount);
					if($add > 0){
						Session::flash('operationSucess','group added successfully !');
					}else{
						
						Session::flash('operationFaild','Some thing went wrong.try again.');
					}
				}
				return Redirect::to('/admin/editProducts/'.$post['productToken']);
			} 
		}else{
			 return Redirect::to('/');
		}
	}
	public function deleteProducts($id){
		//print_r($id);exit;
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$products= new Products();
			$variation= new Variation();
			$ProductsIMage=DB :: table('productimages')->where('product_id','=',base64_decode($id))->first();
			if(!empty($ProductsIMage->productimage)){
				 File::delete('uploads/products/'.$ProductsIMage->productimage);
			}
			$del=array(
				'deleted_at' => Carbon::now()->toDateTimeString()
			);
			$upd=$products->where('product_id','=',base64_decode($id))->update($del);
			$updvar=$variation->where('product_id','=',base64_decode($id))->update($del);
			if($upd > 0){
				Session::flash('operationSucess','product deleted successfully !');
			}else{
				
				Session::flash('operationFaild','Some thing went wrong.try again.');
			}
			 
			 return Redirect::to('admin/productList');
		}else{
			 return Redirect::to('/');
		}
	}

	public function addproductsattributes(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			if(isset($post) && !empty($post)){
				//print_r($post); exit;
				$getdup=DB::table('product_attribute')->where('product_id','=',base64_decode($post['productToken']))->where('attributeID','=',$post['product_attributes'])->first();
				if(!empty($getdup)){
					Session::flash('operationFaild','This Attribute Already added');
					return Redirect::to('/admin/editProducts/'.$post['productToken']);
					exit;
				}
				$attrs=array(
					'attributeID'=>$post['product_attributes'],
					'product_id'=>base64_decode($post['productToken']),
					'created_at'=>Carbon::now()->toDateTimeString()
				);
				$insert=DB::table('product_attribute')->insert($attrs);
				if($insert > 0){
					Session::flash('operationSucess','Attributes added successfully !');
				}else{
					Session::flash('operationFaild','Some thing went wrong.try again.');
				}
			}else{
				Session::flash('operationFaild','Some thing went wrong.try again.');
			}
				Session::flash('attribute','Some thing went wrong.try again.');
				return Redirect::to('/admin/editProducts/'.$post['productToken']);
		}else{
			return Redirect::to('/');
		}
	}
	public function deleteAtributes($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$attrId=base64_decode($id);
			$atid=explode('&',$attrId,2);
			//echo $atid[0];exit;
			$delattr=DB::table('product_attribute')->where('product_attributeID','=', $atid[0])->delete();
			if($delattr > 0){
				Session::flash('operationSucess','Attributes Deleted successfully!');
			}else{
				Session::flash('operationFaild','Some thing went wrong.try again.');
			}
			Session::flash('attribute','Some thing went wrong.try again.');
			return Redirect::to('/admin/editProducts/'.base64_encode($atid[1]));
		}else{
			return Redirect::to('/');
		}
	}
	public function getproductsorder(Request $request){
		 $post=$request->all();
		/*if(isset($post['batch']) && !empty($post['batch'])){
			$products = new Products();
			$items = $products->where('batch','=',$post['batch'])->where('deleted_at','=',NULL)->get(); 
			foreach($items as $item){
				$dealersOrder=DB::table('product_order')->where('product_id','=',$item->product_id)->get();
				foreach($dealersOrder as $order){
				echo '<option value="">Select Product</option>';
					
				$dealers=DB::table('dealer')->where('id','=',$order->dealerID)->first();

				echo '<option value="'.$dealers->id.'">'.$dealers->first_name.'&nbsp;'.$dealers->last_name.'</option>';
				}
			}
		} */
		if(isset($post['variation']) && !empty($post['variation'])){
		 
			$dealersOrders=DB::table('product_order')->where('variationID','=',$post['variation'])->get();
			 
			foreach($dealersOrders as $dealersOrder)	{
				
			$dealers=DB::table('dealer')->where('id','=',$dealersOrder->dealerID)->get();
			 foreach($dealers as $dealer){
				echo '<option value="'.$dealer->id.'">'.$dealer->first_name.'&nbsp;'.$dealer->last_name.'</option>';
				}
			} 
			//print_r($dealers);

			
		}
	}
	public function addVariation(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.product.addvariation')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
	}
	public function addProductsVariationDirect(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			//print_r($post); exit;	
			foreach($post['colorStock'] as $k=>$v){
				if(isset($post['stockdate']) && !empty($post['stockdate'])){$stockdate=date('Y-m-d',strtotime($post['stockdate']));}else{$stockdate=NULL;}
				 
					$variationToken='';
					//print_r($cartData);exit; 
					for($j=0;$j < 4;$j++){
					    $variationToken .= $this->getTokenProduct();
					}
				if($v != ''){
					//echo $k.'->'.$v.'<br/>';
					$getVariation=DB::table('variation')->where('product_id','=',$post['product_name'])->where('product_status','=',$post['product_status'])->where('batch','=',$post['batch'])->where('product_color','=',$post['colorName'][$k])->where('batch','=',$post['batch'])->where('deleted_at','=',NULL)->first();
					if(!empty($getVariation)){
						//print_r($getVariation);
						$updateVariation=array(
							'productStock'=>$v,
							'stockdate'=>$stockdate,
							'updated_at'=>Carbon::now()->toDateTimeString()
						);
						$getOldVariationData=DB::table('variation')->where('variationID','=',$getVariation->variationID)->first();
						
						$updateVariation=DB::table('variation')->where('variationID','=',$getVariation->variationID)->update($updateVariation);
						
						if($getOldVariationData->productStock > $v){
 
							$getDiff=$getOldVariationData->productStock - $v;
							
							for($i=1;$i<=$getDiff;$i++){
								
								$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$getVariation->variationID)->orderBy('variationTranzToken','DESC')->first();
							
								$deleteTranz=DB::table('variation_tranz')->where('variationTranzToken','=',$getVariationTranz->variationTranzToken)->delete();
								 
							} 
							
						}
						
						if($getOldVariationData->productStock < $v){
							//exit;
							$getDiff=$v - $getOldVariationData->productStock;
							
							$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$getVariation->variationID)->orderBy('variationTranzToken','DESC')->first();
								$lastTranzTockenArray=explode('_',$getVariationTranz->variationTranzToken);
								 
							for($i=1;$i<=$getDiff;$i++){
								$lastRecord=$lastTranzTockenArray[2];
								$lastRecord= $lastRecord + $i; 
								$variationTranzToken=$getOldVariationData->variationToken .'_'.$getVariation->variationID.'_'.$lastRecord;
								$tranzUpdateArray=array(
									'variationID'=>$getVariation->variationID,
									'variationTranzToken'=>$variationTranzToken,
									'product_id'=>$post['product_name'],
									'product_status'=>$post['product_status'],
									'qty'=>1,
									'stockdate'=>$stockdate,
									'created_at'=>Carbon::now()->toDateTimeString()
								);
								$addVarTranz=DB::table('variation_tranz')->insert($tranzUpdateArray);
								
							}
							
						}
						
						
					}else{
						
						$updateVariation=array(
							'product_id'=>$post['product_name'],
							'product_status'=>$post['product_status'],
							'productStock'=>$v,
							'stockdate'=>$stockdate,
							'variationToken'=>$variationToken,
							'batch'=>$post['batch'],
							'product_color'=>$post['colorName'][$k],
							'model'=>$post['model'],
							'sku'=>$post['sku'],
							'created_at'=>Carbon::now()->toDateTimeString()
						);
						$updateVariation=DB::table('variation')->insertGetId($updateVariation);
						
						
						for($i=1;$i<=$v;$i++){
							
							$varTranzArray=array(
								'variationID'=>$updateVariation,
								'variationTranzToken'=>$variationToken.'_'.$updateVariation.'_'.$i,
								'product_id'=>$post['product_name'],
								'product_status'=>$post['product_status'],
								'qty'=>1,
								'stockdate'=>$stockdate,
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							//print_r($varTranzArray); echo '<br/>';
							$addVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
						}
					}
				}
			}
			if(isset($updateVariation) && $updateVariation > 0){
				Session::flash('operationSucess','Variation added successfully !');
			}else{
				
				Session::flash('operationFaild','Some thing went wrong.try again.');
			}
			return Redirect::to('/admin/addvariation/');
		}else{
			return Redirect::to('/');
		}
	}
	public function accessoryList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			return view('admin.product.accessoryList')->with('sessionData',$sessionData);
			
			
		}else{
			return Redirect::to('/');
		}
	}
	public function addAccessory(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.product.addAccessories')->with('sessionData',$sessionData);
			
		}else{
			return Redirect::to('/');
		}
	}
	public function addAccessoryDB(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			if(isset($post) && !empty($post)){
				
				//print_r($post);
				if(isset($post['visibility'])){
					$visibility=1;
				}else{
					$visibility=0;
				}
				$dataSaveDataArray=array(
					'accessory_name'=>$post['accessory_name'],
					'category_id'=>$post['category'],
					'brand_id'=>$post['brand'],
					'visibility'=>$visibility,
					'accessory_description'=>$post['accessory_description'],
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
			 
			return view('admin.product.editAccessory')->with('accessoryID',$id);
			 
		}else{
			
			return Redirect::to('/');
		}
	} 
	public function updateAccessory(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			$post= $request->all();
			//print_r($post);exit;
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
					'visibility'=>$visibility,
					'accessory_description'=>$post['accessory_description'],
					'updated_at'=>Carbon::now()->toDateTimeString(),
				);
				
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
