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
use Cart;
use Form;
use Auth;
use File;
use Config;
use App\Dealer;
use XeroLaravel;
use App\Products;
use App\Variation;
use App\Discount;
use App\Order;
use App\OrderDetails;
use Datatables;
use URL;

 

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $post = $request->all();
		unset($post['_token']);
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
       if(isset($id ) && !empty( $id ) )
        {
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
			  return View::make('dealer/order/dealerOrdersList')->with('cart',$cart);
		//   return Redirect::to('/dealer/productdetail/'.$post['productToken'])->with('cart',$cart);
        }else{
           return Redirect::to('/');
        }
    }
    public function dealerOrdersInvoiceList(){
		 
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
       if(isset($id ) && !empty( $id ) )
        {
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
			  return View::make('dealer/order/dealerOrdersInvoiceList')->with('cart',$cart);
		//   return Redirect::to('/dealer/productdetail/'.$post['productToken'])->with('cart',$cart);
        }else{
           return Redirect::to('/');
        }
	}
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
	public function dealerorderlist(){
		
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		if(isset($id ) && !empty( $id ) )
        {
			 $products = new Products;
			 $order = new Order;

         
		$data = $order->where('dealerID','=', $id )->orderBy('orderID','asc')->get();
		//print_r($data); exit;
        $no = 0;
        return Datatables::of($data, $no)
			 ->addColumn('Order Number', function ($data) {
                if($data->OrderNumber != ''){
					if(!empty($data->OrderNumber)){
						
                     return $data->OrderNumber;
					}else{
						return '--';
					}
                }else{
                    return '---';
                }
            })
		
            ->addColumn('Product Name', function ($data) {
                if($data->product_id != ''){
					 $products = new Products;
					 $productName=$products->where('product_id','=',$data->product_id)->first();
					 if(!empty($productName->productName)){
						 
                     return $productName->productName;
					 }else{
						 return  '--'; 
					 }
					 
                }else{
                    return '---';
                }
            })

            ->addColumn('Color', function ($data) {
                if(($data->variationID != '')){
                      
					 $variation = new Variation;
					 $colorName=$variation->where('variationID','=',$data->variationID)->first();
					 if(!empty($colorName->product_color)){
						 
                     return $colorName->product_color;
					 }else{
						 return  '--'; 
					 }
					
                }else{
                    return '---';
                }
            })

          			
			->addColumn('Qty', function ($data) {
                if($data->qty != ''){
					return $data->qty;                 
                }else{
                    return '---';
                }
            })
			
						
			->addColumn('Amount', function ($data) {
                if($data->final_price != ''){
					 
                    return '$'.$data->final_price;
                }else{
                    return '--';
                }
            })
			
			->addColumn('Status', function ($data) {
                if($data->orderStatus != ''){
					 if($data->orderStatus=='pending'){
						return '<label class="label label-warning" style="text-transform:capitalize;">'.$data->orderStatus.'</label>';
						 
					 }if($data->orderStatus=='cancelled') {
						 
						return '<label class="label label-danger" style="text-transform:capitalize;">'.$data->orderStatus.'</label>';
					 }else{
						 return '<label class="label label-success" style="text-transform:capitalize;">'.$data->orderStatus.'</label>';
					 }
                }else{
                    return '--';
                }
            })
			
			 ->make(true);
		}else{
            return Redirect::to('/');
        }
	}
	public function editorder($orderId){
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$cartitem=Cart::items();
		$cart=$cartitem->count();
		//$cartData=$cartitem->toArray();
		if(isset($id ) && !empty( $id ) )
        {
			$order = new Order();
			$cartData=$order->where('dealerID','=', $id)->where('orderID','=',base64_decode($orderId))->get();
			 return Redirect::to('dealer/dealerorders')->with('cart',$cart)->with('cartData',$cartData);
		//   return Redirect::to('/dealer/productdetail/'.$post['productToken'])->with('cart',$cart);
        }else{
             return Redirect::to('/');
        }
	}

	public function updateorder(Request $request){
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$cartitem=Cart::items();
		$cart=$cartitem->count();
		//$cartData=$cartitem->toArray();
		$post=$request->all();
		
		//print_r($post);exit;
		if(isset($id ) && !empty( $id ))
        {	
			if(isset($post) && !empty($post)){
				$getOrderdata=DB::table('order_transaction')
				 ->where('order_transactionID','=',base64_decode($post['orderToken']))
				 ->where('deleted_at','=',NULL)->first();
				if(!empty($post['product_name']) || !empty($post['product_nameedit'])){
				 
					if(!empty($getOrderdata->accessoryID)){
						 
						$datacount=0;
						if(!empty($post['product_nameedit']) && !empty($post['qtyedit'])){
							$datacount=count($post['product_nameedit']);
							$newAccessoryArray=array();
							for($i=0;$i<$datacount;$i++){
								$newAccessoryArray[base64_decode($post['product_nameedit'][$i])] = $post['qtyedit'][$i];
							}
							
						}
						if(!empty($post['product_name']) && !empty($post['qty'])){
							 if (array_key_exists(base64_decode($post['product_name']),$newAccessoryArray)){
								 Session::flash('operationFaild','item alredy in order'); 
								 return Redirect::to('dealer/dealerorderdetials/'.base64_encode($getOrderdata->orderID.'&'.$getOrderdata->orderNoteTokenString ));
								 exit;
							 }else{
								 
								$newAccessoryArray[base64_decode($post['product_name'])] = $post['qty'];
							 }
						}
						 $newAccessoryArray = json_encode($newAccessoryArray); 
						  //echo $newAccessoryArray;
					}else{
						//$newAccessoryArray=implode(',',$post['product_name']);
						$newAccessoryArray = array();
						$newAccessoryArray[base64_decode($post['product_name'])]=$post['qty'];
						 
						 $newAccessoryArray = json_encode($newAccessoryArray);
						 //echo $newAccessoryArray;
					}
					
					//exit;
					if(!empty($newAccessoryArray)){
						
						$UpdateorderArray=array(
							'accessoryID'=>$newAccessoryArray,
							'updated_at'=>Carbon::now()->toDateTimeString()
						); 
					}else{
						$UpdateorderArray=array(
							'accessoryID'=>$getOrderdata->accessoryID,
							'updated_at'=>Carbon::now()->toDateTimeString()
						); 
					}
					//print_r($UpdateorderArray);exit;
					$updateorder=DB::table('order_transaction')->where('order_transactionID',base64_decode($post['orderToken']))->update($UpdateorderArray);
					if($updateorder > 0){
						
						Session::flash('operationSucess','Order Updated Successfully !');
						 
					}else{
						Session::flash('operationFaild','Some thing Went wrong');  
					}
				}else{
					Session::flash('operationFaild','Select Accessory item');  
				}
			}else{
				Session::flash('operationFaild','Some thing Went wrong');  
			}
			return Redirect::to('dealer/dealerorderdetials/'.base64_encode($getOrderdata->orderID.'&'.$getOrderdata->orderNoteTokenString ));
        }else{
             return Redirect::to('/');
        }
	}

	public function placeOrderAccessories(Request $request)
	{
		\Session::forget('accessoriesPlaceorder');
        \Session::save();
		$post = $request->all();
		
		$newAccessoryArray=array();
		if( isset($post['accesoryold']) && isset($post['accesoryoldqty']) )
		{
			$accesoryold_ar = $post['accesoryold'];
			$accesoryoldqty_ar = $post['accesoryoldqty'];
			for($i=0; $i<count($accesoryold_ar); $i++)
			{
				$key = base64_decode($accesoryold_ar[$i]);
				$newAccessoryArray[$key] = $accesoryoldqty_ar[$i];
			}
		}
		
		if(!empty($post['newaccessory']) && !empty($post['newaccessoryqty']))
		{
			$newaccessory = base64_decode($post['newaccessory']);
			if(!empty($newAccessoryArray))
			{
				if(!array_key_exists($newaccessory,$newAccessoryArray) )
				{
					$newAccessoryArray[$newaccessory] = $post['newaccessoryqty'];	
				}
			}else{
				$newAccessoryArray[$newaccessory] = $post['newaccessoryqty'];	
			}
			
		}
		
		\Session::set('accessoriesPlaceorder' , $newAccessoryArray);
		\Session::save();
		$sessionDataAccessory=Session::get('accessoriesPlaceorder');
	///	$sessionDataAccessory = Session::get('accessoriesPlaceorder');
		 if(!empty($sessionDataAccessory))
		 {
			$newacc=0;
			foreach($sessionDataAccessory as $new_k => $new_v)
			{
				$newacc++;
				$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$new_k)->first();
				echo'
					<tr>
						<td>'.$newacc.'</td>
						<td>'.$acessoryName->accessory_name.'</td>
						<td>
							<div class="form-group" style="max-width:150px;margin-bottom:0px;">
								<input type="hidden" name="accesoryedit[]" value="'.base64_encode($new_k).'"/>
								<input class="touchspin" style="max-width:150px;" name="accesoryqtyedit[]" type="text" value="'.$new_v.'">
							</div>
						</td>';
						?>
						<td><a href="javascript:void(0);" title="Delete order" 
						onclick="removeaccessory('<?php echo base64_encode($new_k); ?>')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a></td>
						
					</tr>
				<?php 
			} 
		 }else{
			 echo '<tr><td colspan="4">No Accessories Added.</td></tr>';
		 }
		//print_r($post);  
	}	
	public function placeOrderAccessoriesRemove(Request $request){
		$post=$request->all();
		$sessionDataAccessory=Session::get('accessoriesPlaceorder');
		if(!empty($post['accssory'])){
			print_r($sessionDataAccessory);
			unset($sessionDataAccessory[base64_decode($post['accssory'])]);
		}
		//print_r($sessionDataAccessory);
		\Session::set('accessoriesPlaceorder' , $sessionDataAccessory);
		\Session::save();
		$sessionDataAccessory = Session::get('accessoriesPlaceorder');
		 if(!empty($sessionDataAccessory))
		 {
			$newacc=0;
			foreach($sessionDataAccessory as $new_k => $new_v)
			{
				$newacc++;
				$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$new_k)->first();
				echo'
					<tr>
						<td>'.$newacc.'</td>
						<td>'.$acessoryName->accessory_name.'</td>
						<td>
							<div class="form-group" style="max-width:150px;margin-bottom:0px;">
								<input type="hidden" name="accesoryedit[]" value="'.base64_encode($new_k).'"/>
								<input class="touchspin" style="max-width:150px;" name="accesoryqtyedit[]" type="text" value="'.$new_v.'">
							</div>
						</td>';
						?>
						<td><a href="javascript:void(0);" title="Delete order" 
						onclick="removeaccessory('<?php echo base64_encode($new_k); ?>')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a></td>
						
					</tr>
				<?php 
			} 
		 }else{
			 echo '<tr><td colspan="4">No Accessories Added.</td></tr>';
		 }
		
	}
	public function cancelorder($orderId){
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$cartitem=Cart::items();
		$cart=$cartitem->count();
		//$cartData=$cartitem->toArray();
		 
		if(isset($id ) && !empty( $id ) &&  isset($orderId) && !empty($orderId))
        {	
				$order= new Order();
				$variation= new Variation();
				$oldData=$order->where('orderID','=',base64_decode($orderId))->first();
				$oldstock=$variation->where('variationID','=',$oldData->variationID)->first();
				$newstock=$oldstock->productStock + $oldData->qty;
				$cancel=array(
					'orderStatus'=>'cancelled',
					'updated_at'=>Carbon::now()->toDateTimeString()
				);	
				$stock=array(
					'productStock'=>$newstock,
					'updated_at'=>Carbon::now()->toDateTimeString()
				);
				$cancelorder=$order->where('orderID','=',base64_decode($orderId))->update($cancel);
				$updateStock=$variation->where('variationID','=',$oldstock->variationID)->update($stock);
				if($cancelorder > 0){
					Session::flash('operationSucess','Order Cancelled Successfully !');
				}else{
					Session::flash('operationFaild','Some thing Went wrong');  
				}
			return Redirect::to('dealer/dealerorders')->with('cart',$cart);
        }else{
            return Redirect::to('/');
        }
	}
	public function addnotes(Request $request){
		 $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$cartitem=Cart::items();
		$cart=$cartitem->count();
		$post = $request->all();
		// print_r($post); exit;
		if(isset($id ) && !empty( $id ) &&  isset($post) && !empty($post))
        {	
			//print_r($post);
			/* $notes=array(
				'product_id'=>base64_decode($post['productToken']),
				'variationID'=>base64_decode($post['variationToken']),
				'orderID'=>base64_decode($post['orderToken']),
				'orderTokenString'=>$post['orderTokenString'],
				'name'=>$post['name'],
				'discription'=>$post['discription'],
				'created_at'=>Carbon::now()->toDateTimeString()
			);
			$check=DB::table('dealer_order_notes')->where('orderID','=',base64_decode($post['orderToken']))->where('orderTokenString','=',$post['orderTokenString'])->first();
			if(!empty($check)){
				
				$addnots=DB::table('dealer_order_notes')->where('orderID','=',base64_decode($post['orderToken']))->update($notes);
			}else{				
				$addnots=DB::table('dealer_order_notes')->insert($notes);
			} */
			$notes=array(
				'customer_name'=>$post['name'],
				'order_notes_descriptions'=>$post['discription'],
				'updated_at'=>Carbon::now()->toDateTimeString()
				);
			$addnots=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'])->update($notes);
			if($addnots > 0){
				Session::flash('operationSucess','Note saved Successfully !');
			}else{
				Session::flash('operationFaild','Some thing Went wrong');  
			}
			Session::flash('opentab',$post['opentab']);  
			if(isset($post['otdertypepage']) && $post['otdertypepage']=='finance'){
				 return Redirect::to('dealer/delaerfinanceorders')->with('cart',$cart);
			}else{
				
			 return Redirect::to('dealer/dealerorders')->with('cart',$cart);
			}
		}else{
           return Redirect::to('/');
        }
	}
	public function deleteOrder(Request $request){
	$post=$request->all();
		// print_r($post);exit;
		$order = new Order();
		$variation= new Variation();
		$orderdata=DB::table('product_order')->where('orderID','=',$post['order'])->first();
		 
		//print_r($orderdata);
		//exit;
		$orderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderstring'])->where('orderID','=',$post['order'])->first();
		// print_r($orderTranz);exit;
			if($orderTranz->specialOrderID > 0){
				$newordertranData=array(
					'deleted_at'=> Carbon::now()->toDateTimeString()
				);
				
				$updateordertran=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderstring'])->update($newordertranData);
			}else{
		
				if($orderTranz->orderStatus == 'pending' || $orderTranz->orderStatus == 'booked in for delivery' || $orderTranz->orderStatus == 'finance new' || $orderTranz->orderStatus == 'finance link sent' || $orderTranz->orderStatus == 'finance accepted' || $orderTranz->orderStatus == 'finance verified' || $orderTranz->orderStatus == 'finance awaiting delivery slip' || $orderTranz->orderStatus == 'finance completed' || $orderTranz->orderStatus == 'Finance Declined'){
					//print_r($orderdata->qty);
					$ordeqty=$orderdata->qty - 1;
					if($orderdata->qty > 0){
						$final_price=$orderdata->final_price - round($orderdata->final_price / $orderdata->qty);
					}else{
						$final_price=0;
					}
						
					
					if($ordeqty > 0){
						
						$newdataOrder=array(
							'qty'=> $ordeqty,
							'final_price'=> $final_price,
							'updated_at'=>Carbon::now()->toDateTimeString()
							
						);
						
					}else{
						$newdataOrder=array(
							'orderStatus'=> 'cancelled',
							'qty'=> 0,
							'final_price'=> 0,
							'updated_at'=>Carbon::now()->toDateTimeString()
						);
						
					}
						//exit;
					$variationdata=DB::table('variation')->where('variationID','=',$orderdata->variationID)->first();
					
					//print_r($variationdata);exit;
					$admin_order_notes_Update=array(
						'deleted_at'=> Carbon::now()->toDateTimeString()
					);
					
					$admin_order_notes=DB::table('admin_order_notes')->where('orderID','=',$post['order'])->update($admin_order_notes_Update);
					if($orderTranz->qtystatus == 'inproduction'){
							
						
						$inproductionOrderqty=DB::table('inproduction_order')->where('orderID','=',$orderdata->orderID)->first();
						if(!empty($inproductionOrderqty->orderqty) && $inproductionOrderqty->orderqty > 0){
							
							$newinproductionOrder=array(
								'orderqty'=>$inproductionOrderqty->orderqty - 1
							);
							$inproductionOrderqtyUpdate=DB::table('inproduction_order')->where('inproduction_orderID','=',$inproductionOrderqty->inproduction_orderID)->where('orderID','=',$orderdata->orderID)->update($newinproductionOrder);
						} 
					}
					
					
					/********* variation transaction update start *********/
					$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$variationdata->variationID)->orderBy('variationTranzToken','DESC')->first();
					
					 
						//exit;
					$getSameVariationDataStock=DB::table('variation')->where('product_id','=',$orderTranz->product_id)->where('product_status','=',$orderTranz->qtystatus)->where('product_color','=',$orderTranz->product_color)->where('batch','=',$orderTranz->batch)->first();
						
						//print_r($getSameVariationDataStock);exit;
						
						if(!empty($getSameVariationDataStock)){
							
							$varNewArray=array(
								'productStock'=>$getSameVariationDataStock->productStock + 1,
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							$updatevariation=DB::table('variation')->where('variationID','=',$getSameVariationDataStock->variationID)->update($varNewArray);
							
							//print_r($varNewArray);exit;
							$getSameDataVariationTranz=DB::table('variation_tranz')->where('variationID','=',$getSameVariationDataStock->variationID)->orderBy('variationTranzToken','DESC')->first();
							
							if(!empty($getSameDataVariationTranz)){
								
								$lastTranzTockenArray=explode('_',$getSameDataVariationTranz->variationTranzToken);
								$lastRecord=$lastTranzTockenArray[2];
								$lastRecord= $lastRecord + 1; 
								$variationTranzToken=$getSameVariationDataStock->variationToken .'_'.$getSameVariationDataStock->variationID.'_'.$lastRecord;
									 
								$varTranzArray=array(
									'variationID'=>$getSameVariationDataStock->variationID,
									'variationTranzToken'=>$variationTranzToken,
									'product_id'=>$getSameVariationDataStock->product_id,
									'product_status'=>$orderTranz->qtystatus,
									'qty'=>1,
									'stockdate'=>$orderTranz->stockdate,
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
								$insertNewVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
							}else{
								
								$variationTranzToken=$getSameVariationDataStock->variationToken .'_'.$getSameVariationDataStock->variationID.'_1';
								
								$varTranzArray=array(
									'variationID'=>$getSameVariationDataStock->variationID,
									'variationTranzToken'=>$variationTranzToken,
									'product_id'=>$orderTranz->product_id,
									'product_status'=>$orderTranz->qtystatus,
									'qty'=>1,
									'stockdate'=>$orderTranz->stockdate,
									'created_at'=>Carbon::now()->toDateTimeString()
								);
								$insertNewVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
							}
							
							
							
						}else{
							$variationData=DB::table('variation')->where('variationID','=',$orderdata->variationID)->first();
							$variationtoken='';
							for($i=0;$i<4;$i++){
								
							$variationtoken .=$this->getTokenProduct();
							}
							$varNewArray=array(
								'variationToken'=>$variationtoken,
								'product_id'=>$variationData->product_id,
								'product_status'=>$orderTranz->qtystatus,
								'productStock'=>1,
								'stockdate'=>$orderTranz->stockdate,
								'batch'=>$variationData->batch,
								'product_color'=>$variationData->product_color,
								'model'=>$variationData->model,
								'sku'=>$variationData->sku,
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							//print_r($varNewArray); exit;
							$updatevariation=DB::table('variation')->insertGetId($varNewArray);
							$variationTranzToken=$variationtoken.'_'.$updatevariation.'_1';
							$varTranzArray=array(
								'variationID'=>$updatevariation,
								'variationTranzToken'=>$variationTranzToken,
								'product_id'=>$variationData->product_id,
								'product_status'=>$orderTranz->qtystatus,
								'qty'=>1,
								'stockdate'=>$orderTranz->stockdate,
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							$insertNewVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
						}
					 
					
					$newordertranData=array(
							'deleted_at'=> Carbon::now()->toDateTimeString()
						);
					
					$updateordertran=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderstring'])->update($newordertranData);
					
					
					 
					/*  print_r($variationdata);
					exit; */ 
					if($newdataOrder['qty'] > 0){
					$updateorder=$order->where('orderID','=',$post['order'])->update($newdataOrder);
						
					}else{
						$newdataOrder['deleted_at']=Carbon::now()->toDateTimeString();
						$newdataOrder['orderStatus']='cancelled';
						$updateorder=$order->where('orderID','=',$post['order'])->update($newdataOrder);
						$ordernotesupdate['deleted_at']=Carbon::now()->toDateTimeString();
						
						$updateorder=DB::table('product_order_details')->where('orderID','=',$post['order'])->update($ordernotesupdate);
						
					}
					
					
				}
			}
		
	}
	public function deleteOrderAccessory(Request $request){
		$post=$request->all();
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		
		$getOrderTran=DB::table('order_transaction')->where('order_transactionID','=',base64_decode($post['ordertranz']))->first();
			 //print_r($post);
			echo base64_decode($post['accvalID']);
		 if(!empty($getOrderTran->accessoryID)){
			 
			 $acessorydata = json_encode($getOrderTran->accessoryID,true);
			$data = json_decode($acessorydata,true);
			$acessorydata = json_decode($data,true);
			// print_r($acessorydata);
			 //echo $key; 
			 //unset($accessoryArray[$key]);
			// print_r($accessoryArray);
			 unset($acessorydata[base64_decode($post['accvalID'])]);
			 
			// print_r($acessorydata);
			 $accessoryArrayNew= json_encode($acessorydata); ;
			$OrderTranArray=array(
				'accessoryID'=>$accessoryArrayNew,
				'updated_at'=>Carbon::now()->toDateTimeString(),
			);
			$OrderTranUpadate=DB::table('order_transaction')->where('order_transactionID','=',base64_decode($post['ordertranz']))->update($OrderTranArray);
		} 
		 
	}
	public function dealerOrderDetials($orderid){
	 $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			$orderidarray= explode('&', base64_decode($orderid));
			// print_r($orderidarray);exit;
			return view('dealer.order.dealerOrderDetails')->with('sessionData',$sessionData)->with('orderID',$orderidarray);
		}else{
			 return Redirect::to('/');
		}
    }
	
	public function addOrderNotesToAdmin(Request $request){
		$post=$request->all();
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		//print_r($post);exit;
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
				if(isset($post) && !empty($post)){
				 
				//print_r($post);exit;
				$chek=DB::table('admin_order_notes')->where('orderID','=',base64_decode($post['orderToken']))->where('product_id','=',base64_decode($post['productToken']))->where('orderTokenString','=',$post['orderTokenString'])->orderBy('admin_order_notesID','desc')->first();
				if(!empty($chek)){
					$orderTokenStringCount=explode('&', $chek->orderTokenStringCount);
					//print_r($orderTokenStringCount);
					$notecount=$orderTokenStringCount[1]+1;
					//exit;
					$notesdata=array(
						'product_id'=>base64_decode($post['productToken']),
						'orderID'=>base64_decode($post['orderToken']),
						'orderTokenString'=>$post['orderTokenString'],
						'orderTokenStringCount'=>$post['orderTokenString'].'_count&'.$notecount,
						'sender'=>$post['sender'],
						'role'=>$post['sendertype'],
						'dealerID'=>$post['dealerID'],
						'description'=>$post['description'],
						'created_at'=>Carbon::now()->toDateTimeString(),
					);
					$addnotes=DB::table('admin_order_notes')->insert($notesdata);
					//$addnotes=DB::table('admin_order_notes')->where('orderID','=',base64_decode($post['orderToken']))->where('product_id','=',base64_decode($post['productToken']))->where('orderTokenString','=',$post['orderTokenString'])->update($notesdata);
				}else{
					$notesdata=array(
						'product_id'=>base64_decode($post['productToken']),
						'orderID'=>base64_decode($post['orderToken']),
						'orderTokenString'=>$post['orderTokenString'],
						'orderTokenStringCount'=>$post['orderTokenString'].'_count&1',
						'sender'=>$post['sender'],
						'dealerID'=>$post['dealerID'],
						'role'=>$post['sendertype'],
						'description'=>$post['description'],
						'created_at'=>Carbon::now()->toDateTimeString(),
					);
					$addnotes=DB::table('admin_order_notes')->insert($notesdata);
				}
				//$addnotes=DB::table('admin_order_notes')->insert($notesdata);
				//AdminOrderNotes::updateOrCreate($notesdata);
				if($addnotes > 0){
					Session::flash('operationSucess','Order Notes added Successfully !');
				}else{
					Session::flash('operationFaild','Some thing went wrong ! ');
				}
				
			}else{
				Session::flash('operationFaild','Please fill all details ! ');
			}
			Session::flash('opentab',$post['opentab']);
			if(isset($post['otdertypepage']) && $post['otdertypepage']=='finance'){
				 return Redirect::to('dealer/delaerfinanceorders');
			}else if(isset($post['otdertypepage']) && $post['otdertypepage']=='special'){
				return Redirect::to('dealer/specialorders')->with('sessionData',$sessionData);
			}else{
				
				return Redirect::to('dealer/dealerorders')->with('sessionData',$sessionData);
			}
			
		}else{
			 return Redirect::to('/');
		}
	}
	public function dealerPlaceOrder(){
		$post=Input::all();
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		//print_r($post);exit;
		$post['qtystatus']=base64_decode($post['qtystatus']);
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			if(isset($post) && !empty($post)){
				//unset($post['_token']);
				$orderdata=$post;
				return view('dealer.order.placeorder')->with('orderdata',$orderdata);
			}else{
				
			 return Redirect::to('/dealer/product');
			}
		}else{
			 return Redirect::to('/');
		}
	}
	public function placeOrderAccessoriesRedirect(){
		
	}
	public function placeOrderCheckout(Request $request){
		$post=$request->all();
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		/* for($i=0;$i<count($post['finance']);$i++){
			$finance_count=explode('_',$post['finance'][$i]);
			echo $finance_count[1].'<br/>';
		} */
		print_r($post);exit;
		$accesorydataID='';
		if(isset($post['accesoryedit']) && !empty($post['accesoryedit'])){
			$accesorydata=array();
			for($i=0;$i<count($post['accesoryedit']);$i++){
				$accesorydata[base64_decode($post['accesoryedit'][$i])]=$post['accesoryqtyedit'][$i];
			}
			$accesorydataID=json_encode($accesorydata,true);
		}
		
		//print_r($post);exit;
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			if(isset($post) && !empty($post)){
				unset($post['_token']);
				 $post['created_at'] = Carbon::now()->toDateTimeString();
				//print_r($post);exit;  
				$product_id=base64_decode($post['productToken']);
				$variation_id=base64_decode($post['varaintToken']);
				if(isset($post['finance']) && !empty($post['finance'])){}else{$post['finance']=0;}
				 
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
						$token .= $codeAlphabet[crypto_rand_secure(0, $max)];
					}
					return $token;
				}
				$OrderNumber='';
				for($j=0;$j < 4;$j++){
					$OrderNumber .= getTokenProduct();
				}
				$productData=DB::table('products')->where('product_id','=',$product_id)->where('deleted_at','=',NULL)->first();
				$variation= DB::table('variation')->where('variationID','=',$variation_id)->where('deleted_at','=',NULL)->first();
				//print_r($variation);exit;
				$group= DB::table('dealer')->where('id','=',$sessionData['dealerID'])->first();
				$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
				$discount= DB::table('discount')->where('product_id','=',$product_id)->where('groupID','=',$group->groupID)->first();
				$userdata=DB::table('dealer')->where('id','=',$sessionData['dealerID'])->select('shipping_address')->first();
				$user=DB::table('dealer')->where('id','=',$sessionData['dealerID'])->first();
				$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
					$dataArray=array();
					$orderDetail=array();
					foreach($userdata as $dataKey=>$dataValue){
						//echo $dataValue;
						$dataArray[$dataKey]=$dataValue;
					}
					foreach($dataArray as $dataArray_k=>$dataArray_v){
						if(!empty($dataArray[$dataArray_k])){
							$orderDetail=array_add($orderDetail, $dataArray_k,$dataArray_v);
						}
					}
					if($variation->productStock > 0 && $variation->productStock >= $post['qty']){
						if(!empty($productData->real_price)){
							/* if(!empty($productData->sale_price)){
								$orginPrice=$productData->sale_price;
							}else{ */
								$orginPrice=$productData->real_price;
							//}
							if(!empty($discount->discountPer)){
								$discountper=$discount->discountPer;
								$finalprice=round($orginPrice - ($orginPrice * $post['qty'] * $discountper / 100 ) + ($orginPrice * $post['qty'] * 20 / 100));
							}else{
								$discountper='0';
								$finalprice=$orginPrice * $post['qty'] +($orginPrice * $post['qty'] * 20 / 100);
							}
						}else{
							$finalprice=0;
							$discountper=0;
						}
							
						$order=array(
							'OrderNumber' => $OrderNumber,
							'product_id' =>$product_id,
							'qtystatus' =>$post['qtystatus'],
							'dealerID' =>$sessionData['dealerID'],
							'variationID' =>$variation_id,
							'product_color' =>$variation->product_color,
							'batch' =>$variation->batch,
							'qty' =>$post['qty'],
							'orderQty' =>$post['qty'],
							'real_price' =>$productData->real_price,
							'final_price' =>$finalprice,
							'discount' =>$discountper,
							'created_at' =>$post['created_at']
						);
						 
						$orderid=DB::table('product_order')->insertGetId($order);
						 
						$sringqty=0;
						$no = 0;
						if(isset($post['finance']) && !empty($post['finance'])){
							
							$emails='lauren@superiorspas.co.uk';
						$data_user_stocck_update =array(
							'productName' => $productData->productName,
							'color' =>  $variation->product_color,
							'company' => $user->company_name,
							'dealername' =>  $user->first_name,
							'categoryName' =>$categoryData->categoryName,
							'brandName' =>   $brandData->brandName,
							'batch' =>$variation->batch,
							'orderDate' =>  date('d-m-Y'),
							'email' => $user->emailID,
							 
							 
							 
						); 
							 Mail::send('email_templates.financeOrderMail',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
							{
								$message->to($emails)->subject('New Finance order Received !');
							}); 
						}
						if(isset($post['finance']) && !empty($post['finance'])){
							$countfinanschekbox = count($post['finance']);
							$finance_ar = array();
							for($i=0; $i<$countfinanschekbox; $i++)
							{
								$finance_count = explode('_',$post['finance'][$i]);
								$fin_key = $finance_count[1];
								$finance_ar[$fin_key] = $post['finance'][$i];
							}
						}
						$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $variation->variationID)->orderBy('updated_at','DESC')->first();
						if(!empty($getDateTranz)){
							$orderStockDate=$getDateTranz->stockdate;
						}else{ 
							$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $variation->variationID)->orderBy('variationTranzToken','DESC')->first();
							if(!empty($getDateTranz)){
								$orderStockDate=$getDateTranz->stockdate;
							}else{
								$orderStockDate=NULL;
							}
						}
						for($i=0;$i<$post['qty'];$i++){
								$no++;
								$sringqty++;
								$orderstring=$OrderNumber.'_'.$orderid.'_'.$sringqty;
								if(!empty($productData->real_price)){
									$finalprice_pr_QTY= $finalprice / $post['qty'];
								}else{
									$finalprice_pr_QTY=0;
									$discountper=0;
								}
								  
								
								 
								$finance = 0;
								if(isset($finance_ar[$i]))
								{	
									$finance_count = explode('_',$finance_ar[$i]);
									if(!empty($finance_count))
									{
										if(isset($finance_count[1]))
										{
											if($finance_count[1] == $i) 
											{
												$finance=1;
												
											}else{
												$finance = 0;
											}
										}else{
											$finance = 0;
										}
									}
								}
							if(isset($finance) && $finance==1){
								$orderStatus="finance new";
							}else{
								$orderStatus="pending";
								
							}
							if(!empty($post['address'][$i])){
								$address=$post['address'][$i];
							}else{
								
								$getDefAddress=DB::table('dealer')->where('id','=',$sessionData['dealerID'])->first();
								$address=$getDefAddress->address;
							}
							$orderTran=array(
								'orderNoteTokenString' => $orderstring,
								'product_id' =>$product_id,
								'orderID' =>$orderid,
								'qtystatus' =>$post['qtystatus'],
								'orderStatus' =>$orderStatus,
								'stockdate' =>$orderStockDate,
								'finance' =>$finance,
								'dealerID' =>$sessionData['dealerID'],
								'address' =>$address,
								'variationID' =>$variation_id,
								'product_color' =>$variation->product_color,
								'batch' =>$variation->batch,
								'qty' =>1,
								'accessoryID'=>$accesorydataID,
								'real_price' =>$productData->real_price,
								'final_price' =>$finalprice_pr_QTY,
								'discount' =>$discountper,
								'customer_name'=>$post['order_notes_title'][$i],
								'order_notes_descriptions'=>$post['order_notes_descriptions'][$i],
								'created_at' =>$post['created_at']
							);
							//print_r($orderTran);echo '<br/>';
							$orderTransaction=DB::table('order_transaction')->insertGetId($orderTran);
						}
						 
							if($post['qtystatus']=='inproduction'){
								
									$inProOrder=array(
										'product_color'=>$variation->product_color,
										'product_id'=>$product_id,
										'orderID'=>$orderid,
										'variationID' =>$variation_id,
										'orderqty'=> $post['qty'],
										'created_at'=>$post['created_at']
									);
									$inProOrderadd=DB::table('inproduction_order')->insertGetId($inProOrder);
								
							}
								$qtyminus=DB::table('variation')->where('productStock','>','0')->where('variationID','=',$variation_id)->first();
								$itmQty=$post['qty'];
								 
									//echo $qtyminus->productStock.'<br/>';
									$diffrence=0;
									if($qtyminus->productStock >= $itmQty){
										// echo $item['quantity'];
										//exit; 
										$productStock= $qtyminus->productStock - $itmQty;
										$diffrence=$qtyminus->productStock- $productStock;
										
										$stock=array(
											'productStock'=>$productStock,
											'created_at'=>$post['created_at']
											);
											
										for($i=0;$i<$post['qty'];$i++){
											$instockDate='';
											$getDateTranzupdated_at=DB::table('variation_tranz')->where('variationID','=', $variation->variationID)->orderBy('updated_at','DESC')->first();
											if(!empty($getDateTranzupdated_at)){
												
												$instockDate= $getDateTranzupdated_at->stockdate;
												 
												$DeleteTranz=DB::table('variation_tranz')->where('variation_tranzID','=',$getDateTranzupdated_at->variation_tranzID)->delete();
												
											}else{
												
												$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $variation->variationID)->orderBy('variationTranzToken','DESC')->first();
												if(!empty($getDateTranzS)){
													
													$instockDate= $getDateTranzS->stockdate;
													
													 
													$DeleteTranz=DB::table('variation_tranz')->where('variation_tranzID','=',$getDateTranzS->variation_tranzID)->delete();
													
												}else{
													 
												}
											}
											if(!empty($instockDate)){
												$inProOrderarry=array(
													'stockdate'=>$instockDate
												);
												$inProOrderupdate=DB::table('order_transaction')->where('order_transactionID','=',$orderTransaction)->update($inProOrderarry);
											}
										}
										 
										// exit;
										 
										 
										$stockupdate=DB::table('variation')->where('variationID','=',$variation_id)->update($stock);
										if($orderid > 0){
											$orderDetail['orderID']=$orderid;
											$orderDetail['dealerID']=$sessionData['dealerID'];
											$orderdetail=DB::table('product_order_details')->insertGetId($orderDetail);
											if($orderdetail > 0){
												 Cart::clear();
												Session::flash('operationSucess','Order placed Successfully !');
											}else{
												Session::flash('operationFaild','Some thing Went wrong!');
											}
										}else{
											Session::flash('operationFaild','Some thing Went wrong!');
										}
									}else{
										Session::flash('operationFaild','Sorry not enough stock!');
										
									}
						
					}else{
							Session::flash('operationFaild','Sorry not enough stock!');
					}
				
			}else{
				return Redirect::to('/dealer/product');
			}
				return Redirect::to('dealer/product');
		}else{
			return Redirect::to('/');
		}
	}
	public function delaerFinanceOrders(){
		
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		 
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			
			return View::make('dealer/order/financeOrder');
		
		}else{
			return Redirect::to('/');
		}
	}
	public function delaerFinanceOrdersDetail($orderID){
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		 
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			
			return View::make('dealer/order/financeOrder');
		
		}else{
			return Redirect::to('/');
		}
	}
	public function dealerOrderRotaList(){
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		 
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			
			return View::make('dealer/order/dealerOrderListRota');
		
		}else{
			return Redirect::to('/');
		}
	}
	public function ediTableDataPostComment(Request $request){
		$sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		 $post=$request->all();
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			if(isset($post['comment']) && !empty($post['comment'])){
				$updateArray=array(
					'comment'=>$post['comment'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				); 
				$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
				print_r($detailsData);
			}
			else{
				echo '0';
			}
		
		}else{
			return Redirect::to('/');
		}
	}
	public function addCustomerName(Request $request){
		 $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$post=$request->all();
		//print_r($post); exit;
		if(isset($id ) && !empty( $id ) &&  isset($post) && !empty($post))
        {	
			$updatCustomernameArray=array(
				'customer_name'=>$post['order_notes_titles'],	
				'order_notes_descriptions'=>$post['order_notes_descriptionss']
			);
			
			$updatCustomername=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderTokenString'])->update($updatCustomernameArray);
			if($updatCustomername > 0){
				Session::flash('operationSucess','Customer name Added Successfully !');
			}
			Session::flash('opentab',$post['opentab']);  
			if(isset($post['otdertypepage']) && $post['otdertypepage']=='finance'){
				 return Redirect::to('dealer/delaerfinanceorders');
			}else{
				
			 return Redirect::to('dealer/dealerorders');
			}
		}else{
			return Redirect::to('/');
		}
	}
}
