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

use App\Products;
use App\Variation;
use App\Discount;
 

class CartController extends Controller
{
    public function index(Request $request)
    {
        $post = $request->all();
		//print_r($post);exit;
		unset($post['_token']);
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($post) && !empty($post) && isset($id ) && !empty( $id ) )
        {
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
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
				$post['id']=getTokenProduct();
			//print_r($post);exit;
		 
			$productData=DB::table('product_accessories')->where('accessoryID','=',base64_decode($post['accessoryToken']))->first();
			//print_r($productData); exit;
			 
			$dealer= new Dealer();
			$dealerGroup=$dealer->where('id','=',$id)->first();
			$discountData=DB::table('group')->where('groupID','=',$dealerGroup->groupID)->first();
			//$discount= new Discount();
			
			 
			$AccesoriesPrice=0;
			//print_r($discountGroup);exit;
			if($discountData->discount != 0){
				
				$AccesoriesPrice= $AccesoriesPrice + $productData->price - ($productData->price * $discountData->discount) / 100;
				 
				
			}else{
				
				$AccesoriesPrice=$productData->price;
				
			} 
			//echo $variationPrice ;exit;
			function addcrt($post,$productData,$AccesoriesPrice){
				
				/* $cartadd=Cart::add(	array(
					'id' =>$post['id'].$post['varaintToken'],
					'name' => $productData->productName, 
					'price' =>$variationPrice,
					'quantity' =>$post['qty'],
					'attributes' => array(
						'color' => $post['product_color'],
						'variation' => $post['varaintToken'],
						'qtystatus' =>$post['qtystatus'],
						'product' =>$productData->product_id,
						)
					)
				); */
				$cartadd=Cart::add(	array(
					'id' =>$post['id'].$productData->accessoriesToken,
					'name' => $productData->accessory_name, 
					'price' =>$AccesoriesPrice,
					'quantity' =>$post['qty'],
					'attributes' => array(
						'accessoriesToken'=> $productData->accessoryID, 
						)
					)
				);
				
			}
			if(Cart::isEmpty()){
				addcrt($post,$productData,$AccesoriesPrice);
			}else{
				$accessoryToken=base64_decode($post['accessoryToken']);
				
				$countar=0;
				$accessoriesAR=array();
				$poductnAR=array();
				$crtitmidAR='';
				$cartcountqty=0;
			 
				foreach($cartData as $item){
					$accessoriesAR[$countar]=$item['attributes']['accessoriesToken'];
					//$poductnAR[$countar]=$item['attributes']['product'];
					$crtitmidAR[$countar]=$item['id'];
					$countar++;
					$cartcountqty= $cartcountqty + $item['quantity'];
				} 
				//if(in_array($post['varaintToken'],$variatonAR,TRUE) && in_array($productData->accessoryID,$poductnAR,TRUE)){
					//print_r($productData->accessory_qty);
				if(in_array($productData->accessoryID,$accessoriesAR,TRUE)){
					  $key=array_search($accessoryToken,$accessoriesAR);
						$countqty=0;
					 /* foreach($productData as $product){
						// $countqty=$countqty + $product['accessory_qty'];
						 
					 }  */
					 if($countqty > $cartcountqty + $post['qty']){
						 
						$cartadd=Cart::update(	
							$crtitmidAR[$key],
							array(
							'quantity' =>$post['qty'],
							)
						); 
					 }
				}else{
					addcrt($post,$productData,$AccesoriesPrice);
				}
			}
			Session::flash('operationSucess','Added to cart Successfully !');
			 $cartitem=Cart::items();
        $cart=$cartitem->count();
		
		\Session::set('cartcount' , $cart);
        Session::save();
		   return Redirect::to('/dealer/viewCart')->with('cart',$cart);
        }else{
            return View::make('dealer/index');
        }
    }

	public function viewcart(){
        $sessionData=Session::get('dealerLog');
      if(isset($sessionData) && !empty($sessionData['dealerID'])){
		// $cartitem= Cart::clear();
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
			 
			//print_r(Cart::content());exit;
			 $cartitem=Cart::items();
			$cart=$cartitem->count();
			
			\Session::set('cartcount' , $cart);
			Session::save();
		   return view('dealer.cart.viewCart')->with('cart',$cart)->with('cartData',$cartData);
        }else{
            return View::make('dealer/index');
        }
	}

	public function removecartitem(Request $request){
		$post = $request->all();
        $sessionData=Session::get('dealerLog');
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			$cartRemove=Cart::remove($post['rowID']);
			Session::flash('operationSucess','Item removed Successfully !');
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
			 $cartitem=Cart::items();
			$cart=$cartitem->count();
			
			\Session::set('cartcount' , $cart);
			Session::save();
		   return Redirect::to('dealer/viewCart')->with('cart',$cart)->with('cartData',$cartData);
        }else{
            return View::make('dealer/index');
        }
	}

	public function updateCart(Request $request){
		 
		 $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			$post=$request->all();
			$cartitem=Cart::items();
			$cartData=$cartitem->toArray();
			$countar=0;
		//	print_r($post); exit;
			 
			
			foreach($cartData as $item){
				$itmoldqty[$countar]=$item['quantity'];
				$poductnAR[$countar]=$item['attributes']['accessoriesToken'];
				$crtitmidAR[$countar]=$item['id'];
				$countar++;
			}

			
			if(isset($post) && !empty($post)){
				$itm=0;
				foreach($post['cartToken'] as $k=>$v){
					//echo $v;
					if(in_array(base64_decode($v),$crtitmidAR,TRUE)){
						 $key=array_search(base64_decode($v),$crtitmidAR);
						 $oldqty=$itmoldqty[$key];
						 $newqty=$post['qty'][$itm];
						if($oldqty < $newqty){
							$finalqty=$newqty - $oldqty;
						}elseif($oldqty > $newqty){
							$finalqty = -($oldqty - $newqty);
						}else{
							$finalqty = 0;
						}
						 //echo $oldqty.'<br/>'.$newqty.'-------<br/>FINAL = '.$finalqty.'<hr/>';
						 $cartadd=Cart::update(	
							base64_decode($v),
							array(
							'quantity' =>$finalqty,
						));  
						$itm++;
					}
				}
				$i=0;
				foreach($cartData as $item){
					$productData=DB::table('product_accessories')->where('accessoryID','=',$item['attributes']['accessoriesToken'])->first();
			 
					$dealer= new Dealer();
					$dealerGroup=$dealer->where('id','=',$id)->first();
					$discountData=DB::table('group')->where('groupID','=',$dealerGroup->groupID)->first();
					$dicountAmount=$productData->price *  $discountData->discount / 100;
					$finalPrice=$productData->price - $dicountAmount;
					 
						$cartadd=Cart::update(	
							base64_decode($post['cartToken'][$i]),
							array(
							'price' =>$finalPrice,
						)); 
					$i++;
				
					
				}
			}
			
			$cart=$cartitem->count();
			//exit;
			 
		
		\Session::set('cartcount' , $cart);
        Session::save();
			Session::flash('operationSucess',' cart updated Successfully !');
		    return Redirect::to('dealer/viewCart')->with('cart',$cart)->with('cartData',$cartData);
        }else{
            return View::make('dealer/index');
        }
		
	}

	public function checkout(){
		 $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		 
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
			 $data = array();
			//print_r($cartData); exit;
            $data['customerName']= '';
            $data['companyName']= '';
            $data['firstname']= '';
            $data['lastname']= '';
            $data['address']= '';
            $data['city']= '';
            $data['state']= '';
            $data['brandNM']= '';
            $data['customer_country']= '';

            $data['billing_firstname']= '';
            $data['billing_lastname']= '';
            $data['billing_emailID']= '';
            $data['billing_address']= '';
            $data['billing_city']= '';
            $data['billing_state']= '';
            $data['billing_zipcode']= '';
            $data['billing_country']= '';

            $data['shipping_firstname']= '';
            $data['shipping_lastname']= '';
            $data['shipping_emailID']= '';
            $data['shipping_address']= '';
            $data['shipping_city']= '';
            $data['shipping_state']= '';
            $data['shipping_zipcode']= '';
            $data['shipping_country']= '';

            $data['country'] = DB::table('apps_countries')->lists('country_name','country_code');
            $data['country_A'] = $data['country'];


            $data['emailID']= '';
			 $userdata = DB::table('dealer')->where('id','=',$id)->first();
            if(!empty($userdata))
            {

                $data['customerName']= $userdata->first_name.'&nbsp;'.$userdata->last_name;
                $data['companyName']= $userdata->company_name;
                $data['emailID']= $userdata->emailID;

                $data['dealer_ContactID']= $userdata->dealer_ContactID;
                $data['firstname']= $userdata->first_name;
                $data['lastname']= $userdata->last_name;
                $data['address']= $userdata->address;
                $data['city']= $userdata->city;
                $data['state']= $userdata->state;
                $data['customer_country']= $userdata->country;

                $data['billing_firstname']= $userdata->billing_firstname;
                $data['billing_lastname']= $userdata->billing_lastname;
                $data['billing_emailID']= $userdata->billing_emailID;
                $data['billing_address']= $userdata->billing_address;
                $data['billing_city']= $userdata->billing_city;
                $data['billing_state']= $userdata->billing_state;
                $data['billing_zipcode']= $userdata->billing_zipcode;
                $data['billing_country']= $userdata->billing_country;

                $data['shipping_firstname']= $userdata->shipping_firstname;
                $data['shipping_lastname']= $userdata->shipping_lastname;
                $data['shipping_emailID']= $userdata->shipping_emailID;
                $data['shipping_address']= $userdata->shipping_address;
                $data['shipping_city']= $userdata->shipping_city;
                $data['shipping_state']= $userdata->shipping_state;
                $data['shipping_zipcode']= $userdata->shipping_zipcode;
                $data['shipping_country']= $userdata->shipping_country;

                $brandNm  = '';

                if(!empty($userdata->dealerAvatar)){
                    $cavatar='uploads/dealer/'.$userdata->dealerAvatar;
                } else{
                    $cavatar='assets/img/placeholder300x300.png';
                }
            }
			//print_r(Cart::content());exit;
			 
		
		\Session::set('cartcount' , $cart);
        Session::save();
		   if(Cart::isEmpty()){return View::make('dealer/cart/viewCart')->with('cart',$cart)->with('cartData',$cartData);}
		   return View::make('dealer.cart.checkout',$data)->with('cart',$cart)->with('cartData',$cartData);
        }else{ 
            return View::make('dealer/index');
        }
	}

	public function placeOrder(Request $request){
        $sessionData=Session::get('dealerLog');
		  $id = $sessionData['dealerID'];
		if(isset($sessionData) && !empty($sessionData['dealerID'])){
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
			$post=$request->all();
			//print_r($post);
			
			//exit;
		$post['created_at'] = Carbon::now()->toDateTimeString();
			if(isset($post) && !empty($post)){
				//print_r($post);exit;
				unset($post['_token']);
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
					$Ordertoken='';
				//print_r($cartData);exit; 
					for($j=0;$j < 4;$j++){
					    $Ordertoken .= getTokenProduct();
					}
					$accesoryOrderNumber=DB::table('accessory_order')->where('deleted_at','=',NULL)->orderBy('accessories_order_number','DESC')->first();
					
					$OrderNumber=0;
					if(!empty($accesoryOrderNumber)){
						$accesoryOrderNumberNew=$accesoryOrderNumber->accessories_order_number + 1;
						$OrderNumber=sprintf("%05s",$accesoryOrderNumberNew);
						
					}else{
						 $OrderNumber =sprintf("%05s", 1);;
					}
					
					
					$order_accesory=array(
						'accessories_order_token'=> $Ordertoken,
						'dealerID' =>$sessionData['dealerID'],
						'accessories_order_number' =>$OrderNumber,
						'orderStatus' =>'pending',
						'order_notes' =>$post['order_notes'],
						'created_at' =>$post['created_at']
					);
					$orderid=DB::table('accessory_order')->insertGetId($order_accesory);
$main_mailarray =array();
$child_mailarray =array();
				foreach($cartData as $item){
					for($j=0;$j < 4;$j++){
					    $Ordertoken .= getTokenProduct();
					}
					$productData=DB::table('product_accessories')->where('accessoryID','=',$item['attributes']['accessoriesToken'])->where('deleted_at','=',NULL)->first();
					 
					//print_r($variation);exit;
					 
						$dataArray=array();
						$orderDetail=array();
						 
						 
						//print_r($post);exit;
		
						if($productData->accessory_qty > 0){
							$dealerGroup=DB::table('dealer')->where('id','=',$id)->first();
							$discountData=DB::table('group')->where('groupID','=',$dealerGroup->groupID)->first();
								if(!empty($discountData->discount)){
									$discountper=$discountData->discount;
									 
								}else{$discountper='0';}
								
								$order_tranz=array(
									'accessories_order_tranz_token' => $Ordertoken,
									'accessoryID' =>$item['attributes']['accessoriesToken'],
									'accessory_order_ID' =>$orderid,
									'dealerID' =>$sessionData['dealerID'],
									'order_qty' =>$item['quantity'],
									'discount' =>$discountper,
									'orderStatus' =>'pending',
									'order_notes' =>$post['order_notes'],
									'price' =>$item['price'],
									'created_at' =>$post['created_at']
								);
								//$emails='nikhilpatel8000@gmail.com';
								$emails='lauren@superiorspas.co.uk';

								$accessoryData=DB::table('product_accessories')->where('accessoryID','=',$item['attributes']['accessoriesToken'])->first();
			
								$dealer=DB::table('dealer')->where('id','=',$sessionData['dealerID'])->first();
$child_mailarray =array();
								$child_mailarray[$accessoryData->accessoryID]['accessoryName']=$accessoryData->accessory_name ;
								$child_mailarray[$accessoryData->accessoryID]['order_qty']=$item['quantity'] ;
								$child_mailarray[$accessoryData->accessoryID]['discount']=$discountper ;
								$child_mailarray[$accessoryData->accessoryID]['order_notes']=$post['order_notes'] ;
								$child_mailarray[$accessoryData->accessoryID]['price']=$item['price'] ;
								$orderDetail['companyName']=$dealer->company_name ;
								$orderDetail['email']=$dealer->emailID ;
								array_push($main_mailarray,$child_mailarray);
  /* return view('email_templates.accessoryNewOrder')->with('main_mail',$main_mailarray)->with('orderDetail',$orderDetail);								
							exit;   */
								
								$orderTranzid=DB::table('accessory_order_tranz')->insertGetId($order_tranz);
								
								$getAccessoryStock=DB::table('product_accessories')->where('accessoryID','=',$item['attributes']['accessoriesToken'])->where('deleted_at','=',NULL)->first();
								
								$AccessoryStockArray=array(
									'accessory_qty'=>$getAccessoryStock->accessory_qty - $item['quantity'], 
									'updated_at'=>$post['created_at']
								);
								$updateAccessoryStock=DB::table('product_accessories')->where('accessoryID','=',$item['attributes']['accessoriesToken'])->update($AccessoryStockArray);
								if($updateAccessoryStock > 0){
									
								 Cart::clear();
								 Session::flash('operationSucess','Order placed Successfully !');
								}
								
							$OrderNumber='';
						}else{
							Session::flash('operationFaild','Sorry not enough stock!');
						}
					
				}
Mail::send('email_templates.accessoryNewOrder',['main_mail'=>$main_mailarray, 'orderDetail'=>$orderDetail], function($message)use ($emails)
								{
									$message->to($emails)->subject('New Accessory Order !');
								});		

			}else{return View::make('dealer/accessorylist')->with('cart',$cart)->with('cartData',$cartData);}
			\Session::set('cartcount' , $cart);
			Session::save();
		  return Redirect::to('dealer/accessorylist')->with('cart',$cart)->with('cartData',$cartData);
        }else{
            return View::make('dealer/index');
        }
	}
}
