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
use App\Dealer;
 
use App\Products;
use App\Variation;
use App\Discount;
use App\Order;
use App\OrderDetails;
use App\AdminOrderNotes;
use Datatables;


class AccessoryOrderController extends Controller
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
	public function adminAccessoryOrderList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.accessory.accessoryOrderList')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function adminAccessoryOrderdetail($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$orderidarray= explode('&', base64_decode($id));
			return view('admin.accessory.accessoryorderDetials')->with('sessionData',$sessionData)->with('accessory_order_ID',$orderidarray)->with('accessory_order_IDEncoded',$id);
		}else{
			 return Redirect::to('/');
		}
    }
	public function adminUpdateAccesoryOrder(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			if(isset($post) && !empty($post)){
				  
				//print_r($post);exit;
				$delivery_date='';
				 if(isset($post['delivery_date']) && !empty($post['delivery_date'])){$delivery_date = date('Y-m-d',strtotime($post['delivery_date']));}else{$delivery_date='';;}
				 
				 $today=date('d-m-Y');
				//print_r($dealerData);exit;
				
				 $updateOrderArray=array(
					'orderStatus'=>$post['orderStatus'],
					'delivery_date'=>$delivery_date,
					'updated_at'=>Carbon::now()->toDateTimeString()
					
				 );
				 
				 
				 // exit;
				 $orderData=DB::table('accessory_order')->where('accessory_order_ID','=',base64_decode($post['orderToken']))->first();
				 
				 $invoiceData=DB::table('accessory_order')->where('dealerID','=', $orderData->dealerID)->orderBy('updated_at','desc')->first();
				 
				 //print_r($invoiceData);exit;
				 
				 $dealerData=DB::table('dealer')->where('id','=',$orderData->dealerID)->first();
				 $orderNumber='';
				 if($post['orderStatus']!='pending'){
				 	if(!empty($invoiceData->invoiceAccNumber)){
						$newOrderNumber=substr($invoiceData->invoiceAccNumber, -4);
						$orderNumber=$dealerData->invoicePrefix .sprintf("%04s", $newOrderNumber + 1);
					 
					}else{
						$orderNumber=$dealerData->invoicePrefix .sprintf("%04s", 1);
					}
					$updateOrderArray['invoiceAccNumber']=$orderNumber;
				 }
				if(!empty(Input::file('invoicepdf'))){
				 
					$destinationPath = 'uploads/accessoryInvoice'; // upload path
					if(!empty($orderData->invoicepdf)){
						File::delete($destinationPath.'/'.$orderData->invoicepdf);
					} 
					$file = array('image' => Input::file('invoicepdf'));
					 
					
					$extension = Input::file('invoicepdf')->getClientOriginalExtension(); // getting image extension
					$fileName = $dealerData->first_name .'_'.$today.'_'.$orderData->accessories_order_number.'_'.rand(11111,99999).'.'.$extension; // renameing image
				   
					//print_r($post);exit;
					Input::file('invoicepdf')->move($destinationPath, $fileName); // uploading file to given path
					// sending back with message
					$updateOrderArray['invoicepdf']=$fileName;
					//$updateOrderArray['invoiceNumber']=$invoiceNumber;
					
				}
				
				
			 
				$orderDataUpdate=DB::table('accessory_order')->where("accessory_order_ID",'=',base64_decode($post['orderToken']))->update($updateOrderArray);
				 
				$getAccOrderTranzData=DB::table('accessory_order_tranz')->where("accessory_order_ID",'=',base64_decode($post['orderToken']))->get();
				$main_mailarray=array();
				$child_mailarray=array();
				$main_invoice_array = array();
				$invoice_array = array();
				$orderDate='';
				foreach($getAccOrderTranzData as $orderTranzData){
					 
					$child_mailarray = array();
					
					$accessoryData=DB::table('product_accessories')->where('accessoryID','=',	$orderTranzData->accessoryID)->first();
					 
					$child_mailarray[$orderTranzData->accessory_order_tranz_ID]['accessoryName']=$accessoryData->accessory_name;
					$child_mailarray[$orderTranzData->accessory_order_tranz_ID]['order_qty']=$orderTranzData->order_qty;
					$child_mailarray[$orderTranzData->accessory_order_tranz_ID]['price']=$orderTranzData->price;
					$orderDate=date('d-m-Y',strtotime($orderTranzData->created_at));
					array_push($main_mailarray,$child_mailarray);
				}
				
				 $invoice_array['orderDate'] = $orderDate;
				 $invoice_array['orderStatus'] = $post['orderStatus'];
				 $invoice_array['dealername'] = $dealerData->first_name;
				 if(!empty($orderNumber)){
					 $invoice_array['invoiceNumber']=$orderNumber;
				 }
				 if(!empty($delivery_date)){
					 $invoice_array['delivery_date']=date('d-m-Y',strtotime($delivery_date));
				 }
				 /*  return view('email_templates.accessoryOrderUpdate')->with('main_mail',$main_mailarray)->with('invoice_ar',$invoice_array);								
							exit;   */
							
					$emails=$dealerData->emailID;
							
					Mail::send('email_templates.accessoryOrderUpdate',['main_mail'=>$main_mailarray,'invoice_ar'=>$invoice_array], function($message)use ($emails)
						{
							$message->to($emails)->subject('Stock Update alert!');
						});		
							
				 unset($updateOrderArray['invoicepdf']);
				 unset($updateOrderArray['invoiceAccNumber']);
				 
				 
				 $orderTranzDataUpdate=DB::table('accessory_order_tranz')->where("accessory_order_ID",'=',base64_decode($post['orderToken']))->update($updateOrderArray);
				 
				 if($orderDataUpdate > 0 || $orderTranzDataUpdate > 0){
					 Session::flash('operationSucess','Order updated Successfully !');
				 }else{
					  
				 }
				 return Redirect::to('/admin/accessoryorderslist/');
			}else{
				Session::flash('operationFaild','Some thing went wrong');
				return Redirect::to('/admin/accessoryorderslist/');
			}
		}else{
			 return Redirect::to('/');
		}
    }
	public function adminDeleteAccesoryOrder(Request $request){
		$post=$request->all();
		
		$orderTranzData=DB::table('accessory_order_tranz')->where("accessory_order_ID",'=',$post['order'])->get();
			
			foreach($orderTranzData as $roder){
				
				$accessoryData=DB::table('product_accessories')->where('accessoryID','=',$roder->accessoryID)->first();
				
				$updateStockArray=array(
					'accessory_qty'=> $accessoryData->accessory_qty + $roder->order_qty ,
					'updated_at'=>Carbon::now()->toDateTimeString()
				);
				$accessoryDataUpdate=DB::table('product_accessories')->where('accessoryID','=',$roder->accessoryID)->update($updateStockArray);
			}
		
		$deleteorderArray=array(
			'deleted_at'=>Carbon::now()->toDateTimeString()
		);
		
		$orderTranzDataUpdate=DB::table('accessory_order')->where("accessory_order_ID",'=',$post['order'])->update($deleteorderArray);
		//print_r($updateStockArray); exit;
		
		$rderDataUpdate=DB::table('accessory_order_tranz')->where("accessory_order_ID",'=',$post['order'])->update($deleteorderArray);
		
    }
}
