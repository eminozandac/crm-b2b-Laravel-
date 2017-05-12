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
use App\Dealer;
use Datatables;
use URL;
 

class NewStockController extends Controller
{
	public function index(){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			return view('admin.product.manageStock');
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
	public function factoryStockManage()
    {
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
				$products= new Products ();
			$productData=$products->all();
			return view('admin.product.factoryStockManage')->with('productData',$productData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function updatestock(Request $request){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			
			// print_r($post);  exit;
			$products = new Products();
			$dealer = new Dealer();
			if(isset($post['batch']) && !empty($post['batch'])){
				
				$oldstock=DB::table('variation')->where('batch','=',$post['batch'])->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->first();
				//print_r($oldstock); exit;
				if(isset($post['stockdate']) && !empty($post['stockdate'])){
					$stockdate=date('Y-m-d', strtotime($post['stockdate']));
				}else{
					if(!empty($oldstock->stockdate)){
							
						$stockdate=$oldstock->stockdate;
					}else{
						$stockdate=NULL;
					}
				}
				 //print_r($oldstock); exit;
				
				$oldproductStock=$oldstock->productStock;
				$oldstatus=$oldstock->product_status;
				if(isset($post['dealerID']) && !empty($post['dealerID'])){
					$newstatus=$oldstock->product_status;
				}else{
					$newstatus=$post['product_stausss'];
				}
				$newproductStock=0;
				$newinstockdata=0;
				 
				if($post['product_stausss']=='instock'){
					
					/** In Production to In Stock**/					
					$productData=$products->where('product_id','=',$post['product_name'])->first();
					if(isset($post['dealerID']) && !empty($post['dealerID'])){
						$dCount=0;
						foreach($post['dealerID'] as $k=>$v){

							$oldinproductionorder=DB::table('inproduction_order')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->first();
							$userorder=DB::table('product_order')->where('qtystatus','=','inproduction')->where('dealerID','=',$v)->first();
							$users=$dealer->where('id','=',$v)->first();
							$emails=$users->emailID;
							$data_user_stocck_update =array(
								'productName' => $productData->productName,
								'color' =>  $post['product_color'],
								'email' => $emails,
								'notes' =>$post['mailnotes'],
                                "loginUrl" => URL::to('/'),
							);
							//exit;
							Mail::send('email_templates.stockUpdate',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
							{
								$message->to($emails)->subject('Stock Update alert!');
							});
							$maildstatus=array(
								'mailstatus'=> '1'
							);
							$inproductionorder=array(
								'orderqty'=>$oldinproductionorder->orderqty - $userorder->qty 
							);
							//print_r($oldinproductionorder->inProductionOrder);
							 if($inproductionorder['orderqty'] > 0){}else{$inproductionorder['orderqty']=0;}
							// echo $userorder->qty .'<br/>';
							$updststus=DB::table('product_order')->where('dealerID','=',$v)->where('variationID','=',$oldstock->variationID)->update($maildstatus);
							 
							 $updateinproductionorderqty=DB::table('inproduction_order')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($inproductionorder);
						}
					}else{
						
						
						$getinproductionstock=DB::table('variation')->where('batch','=',$post['batch'])->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->where('product_status','=','instock')->first();
						if(!empty($getinproductionstock)){
							$newinstockdata= $getinproductionstock->productStock + $post['productStock'];
						}else{
							$newinstockdata = $post['productStock'];
						}
						if($oldproductStock >= $post['productStock']){
							$newproductStock= $oldproductStock - $post['productStock'];
							
						}else{
							$newproductStock=0;
						}
								
						 
						 
						$updtstock=array(
						 
							'productStock'=>$newproductStock,
							'stockdate'=>$stockdate,
							'updated_at'=>Carbon::now()->toDateTimeString()
						);
						$updatestocknewdata=array(
						 
							'productStock'=>$newinstockdata,
							'stockdate'=>$stockdate,
							'updated_at'=>Carbon::now()->toDateTimeString()
						);
						//print_r($newproductStock); exit;
						 
						if(!empty($getinproductionstock)){
						
							
							$updatestock=DB::table('variation')->where('variationID','=',$getinproductionstock->variationID)->where('product_status','=','instock')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($updatestocknewdata);
						
							$getbatch=DB::table('variation')->where('product_status','=','inproduction')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->first();
	 
							$updatestocknew=DB::table('variation')->where('variationID','=',$getbatch->variationID)->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($updtstock);
						}else{
							
							 
							$newstockdataddarry=array(
								'product_status'=>'instock',
								'stockdate'=>$stockdate,
								'product_id'=>$post['product_name'],
								'productStock'=>$post['productStock'],
								'batch'=>$post['batch'],
								'product_color'=>$post['product_color'],
								'model'=>$oldstock->model,
								'sku'=>$oldstock->sku,
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							$updatestock=DB::table('variation')->where('batch','=',$post['batch'])->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($updtstock);
							$addnewvariation=DB::table('variation')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->insert($newstockdataddarry);
						}
						if($updatestock >  0 ){
							Session::flash('operationSucess','Stock Updated Successfully !');
						}
					}	
						/*****************************************************/
				}else{
					/** In Stock to In Production**/
					$getinproductionstock=DB::table('variation')->where('batch','=',$post['batch'])->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->where('product_status','=','inproduction')->first();
					if(!empty($getinproductionstock)){
						$newinstockdata= $getinproductionstock->productStock - $post['productStock'];
					}else{
						$newinstockdata = $post['productStock'];
					}
					//print_r($oldproductStock);exit;
					
					
					$newproductStock= $oldproductStock + $post['productStock'];
						
					
					$updtstock=array(
						 
						'productStock'=>$newproductStock,
						'stockdate'=>$stockdate,
						'updated_at'=>Carbon::now()->toDateTimeString()
					);
					$updatestocknewdata=array(
						 
						'productStock'=>$newinstockdata,
						'stockdate'=>$stockdate,
						'updated_at'=>Carbon::now()->toDateTimeString()
					);
					
					//print_r($updatestocknewdata);exit;
					if(!empty($getinproductionstock)){
					// print_r($updatestocknewdata);exit;
					
						$updatestock=DB::table('variation')->where('variationID','=',$getinproductionstock->variationID)->where('product_status','=','inproduction')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($updtstock);
						
						$getbatch=DB::table('variation')->where('product_status','=','instock')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->first();
 
						$updatestocknew=DB::table('variation')->where('variationID','=',$getbatch->variationID)->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($updatestocknewdata);
						
					}else{
						 
						 
						$newstockdataddarry=array(
							'product_status'=>'instock',
							'stockdate'=>$stockdate,
							'product_id'=>$post['product_name'],
							'productStock'=>$post['productStock'],
							'batch'=>$post['batch'],
							'product_color'=>$post['product_color'],
							'model'=>$oldstock->model,
							'real_price'=>$oldstock->real_price,
							'sale_price'=>$oldstock->sale_price,
							'sku'=>$oldstock->sku,
							'created_at'=>Carbon::now()->toDateTimeString()
						);
						$updatestock=DB::table('variation')->where('batch','=',$getinproductionstock->batch)->where('product_status','=','inproduction')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($updtstock);
						$addnewvariation=DB::table('variation')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->insert($newstockdataddarry);
					}
					if($updatestock >  0 ){
						Session::flash('operationSucess','Stock Updated Successfully !');
					}
				}
				
				
				
			}else{
				 Session::flash('operationFaild','Some thing Went wrong!');
			}
			
			return Redirect::to('admin/stockManage');
		}else{
			 return Redirect::to('/');
		}
    }

	public function stockproductdatalist(Request $request)
    {
		$post=$request->all();
		//print_r($post); exit;
		
        $variation = new Variation;
		
		$data = Variation::select('*')
		->where('product_staus','!=','outofstock')
		->where('deleted_at','=',NULL)
		->orWhere('inStock','!=','0')
		->orWhere('inProduction','!=','0')
		->orderBy('variationID','asc');
		
        return Datatables::of($data)

			->filter(function($query){
				if(Input::get('batch')){
					$query ->where(DB::raw('batch') , "=" , Input::get('batch'));
				}
				if(Input::get('product_staus')){
					$query ->where(DB::raw('product_staus') , "=" , Input::get('product_staus'));
				}
				if(Input::get('product_color')){
					$query ->where(DB::raw('product_color') , "=" , Input::get('product_color'));
				}
			})

            ->addColumn('Product Name', function ($data) {
                if($data->product_id != ''){
                    $products = new Products();
					$productsName = $products->where('product_id','=',$data->product_id)->first();
					if($productsName->productName != ''){
						return $productsName->productName;
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })

            ->addColumn('Batch No', function ($data) {
				if($data->product_id != ''){
                    $products = new Products();
					$productsName=$products->where('product_id','=',$data->product_id)->first();
					if($productsName->batch != ''){
						return $productsName->batch;
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })

			->addColumn('Color', function ($data) {
                if($data->product_color	 != ''){
                    return $data->product_color	;
                }else{
                    return '---';
                }
            })
			
			->addColumn('In Stock', function ($data) {
                if($data->inStock != '' && $data->inStock != 0){
					return $data->inStock;
                }else{
                    return '---';
                }
            })
			
			->addColumn('In Production', function ($data) {
                if($data->inProduction != '' && $data->inProduction !=0){
					$orderinst=DB::table('product_order')->where('product_id','=',$data->product_id)->where('qtystatus','=','inproduction')->first();
					if(!empty($orderinst) && $data->inProductionOrder > 0){
						return  $data->inProduction.' ('.$data->inProductionOrder.')';
					}else{
						return $data->inProduction;
					}
                }else{
                    return '---';
                }
            })
			
			->addColumn('In Stock Date', function ($data) {
                if($data->inProduction != 0){
					if($data->stockdate != '' && $data->stockdate != '0000-00-00'){
						return $data->stockdate;
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })

            ->make(true);
    }

	public function getproducts(Request $request){
		$post=$request->all();
		if(isset($post['batch']) && !empty($post['batch'])){
			$products = new Products();
			$variation = new Variation;
			$variationsData=$variation->where('batch','=',$post['batch'])->where('deleted_at','=',NULL)->groupBy('product_id')->get();
			if(!empty($variationsData)){
					echo '<option value="">Select Product</option>';
				foreach($variationsData as $variations){
					$items = $products->where('product_id','=',$variations->product_id)->where('deleted_at','=',NULL)->first(); 
					 
						echo '<option value="'.$items->product_id.'">'.$items->productName.'</option>';
					 
				}
			}
		}
	}
	public function getproductscolor(Request $request){
		$post=$request->all();
		if(isset($post['product_name']) && !empty($post['product_name'])){
			 $variation = new Variation;
			$items = $variation->where('product_id','=',$post['product_name'])->where('deleted_at','=',NULL)->groupBy('product_color')->get(); 
		 
			echo '<option value="">Select color</option>';
			$variationColor=array(
				'0' => 'none',
				'1' => 'Tuscan Sun',
				'2' => 'Pearl White',
				'3' => 'Sterling Silver',
				'4' => 'Tranquility',
				'5' => 'Storm Clouds',
				'6' => 'Cinnabar',
			);
			$variationColorThumb=array(
				'0' => 'none',
				'1' => 'tuscan',
				'2' => 'pearl',
				'3' => 'cameo',
				'4' => 'tranq',
				'5' => 'strom',
				'6' => 'cinnabar',
				 
			);
			$color=count($variationColor);
					$inProduction=0;
					$inStock=0;
					//print_r($items);exit;
			foreach($items as $item){
				
					
					
						$getinstoks=DB::table('variation')->where('product_id','=',$post['product_name'])->where('product_color','=',$item->product_color)->where('product_status','=','instock')->get();
						foreach($getinstoks as $getinstok){
							$inStock = + $getinstok->productStock;
						}
					 
					
					$getinProductions=DB::table('variation')->where('product_id','=',$post['product_name'])->where('product_color','=',$item->product_color)->where('product_status','=','inproduction')->get();
						foreach($getinProductions as $getinProduction){
							$inProduction = + $getinProduction->productStock;
						}		
					
					
					 
					  $countOrder=0;
					$orderinsts=DB::table('inproduction_order')->where('product_id','=',$post['product_name'])->where('product_color','=',$item->product_color)->get();
					foreach($orderinsts as $inPrdOrder){
						$countOrder = $countOrder + $inPrdOrder->orderqty;
					}
				  
					
					echo '<option data-totlstk="'.$item->productStock.'" data-variation="'.$item->variationID.'" data-instk="'.$inStock.'" data-inprdorder="'.$countOrder.'" data-inprd="'.$inProduction.'" value="'.$item->product_color.'">'.$item->product_color.'</option>';
					/* for($i=0;$i<$color;$i++){
						if($variationColor[$i]==$item->product_color){
							if($variationColor[$i]=='none'){
								echo '<option  value="none">None</option>';
							}else{
								$thumb="assets/img/".$variationColorThumb[$i].".jpg";
								echo'<option  value="'.$variationColor[$i].'" data-thumbnail="'.URL::to($thumb).'">'.$variationColor[$i].'</option>';
							}
						}
					} */
				
				
			}
		}
	}
	 public function getproductsorder(Request $request){
		$post=$request->all();
		if(isset($post['variation']) && !empty($post['variation']))
		{ 
			$dealerlist=array();
			$dealersOrders=DB::table('product_order')->where('mailstatus','=',0)->where('qty','>',0)->where('variationID','=',$post['variation'])->get();
			 
			foreach($dealersOrders as $dealersOrder)
			{	
				$dealers=DB::table('dealer')->where('id','=',$dealersOrder->dealerID)->distinct()->get();
				
				foreach($dealers as $dealer){
					$dealerlist[$dealer->id]=$dealer->first_name.'&nbsp;'.$dealer->last_name;
				}
			}
			
			foreach($dealerlist as $k=>$v)
			{
				echo '<option value="'.$k.'">'.$v.'</option>';
			}
			//print_r($dealerlist);
		} 
	} 
	public function inProductionStockManage(){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			return view('admin.product.inProductionStockManage');
		}else{
			return Redirect::to('/');
		}
    }
	public function updateInProductionStock(Request $request){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
		$post=$request->all();
		//print_r($post); exit;
		
			if(!empty($post['varationToken']) && isset($post['qtytype']) && !empty($post['qtytype'])){
				if($post['qtytype']=='inproductiondate'){$post['qtytype']='inproduction';}
				$qtytype=$post['qtytype'];
				for($i=0;$i<count($post['varationToken']);$i++){
					
					if(!empty($post['orderNoteTokenString'][$i]) && !empty($post['varationToken'][$i])&& $post['orderNoteTokenString'][$i]!='0')
					{
						$getOrderID=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'][$i])->first();
						$getInPrdOrders=DB::table('inproduction_order')->where('orderID','=',$getOrderID->orderID)->first();
						if($getOrderID->specialOrderID > 0 ){
							if($qtytype=='onseaukarrival') {
								$orderTranzArray=array(
									'mailstatus'=> '0',
									'qtystatus'=>$post['qtytype'],
									'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
							}else{
								$orderTranzArray=array(
									'mailstatus'=> '1',
									'qtystatus'=>$post['qtytype'],
									'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
							}
								$updateorderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'][$i])->update($orderTranzArray);	
						}else{
						
							if($qtytype=='onseaukarrival' || $qtytype=='instock' || $qtytype=='factorystock') {
								
								//echo $post['orderNoteTokenString'][$i];
								// print_r($post);exit;
								if(!empty($getInPrdOrders)){
									
									if($getInPrdOrders->orderqty > 0){
										$newInPrdOrderArray=array(
											'orderqty'=>$getInPrdOrders->orderqty - 1,
											'updated_at'=>Carbon::now()->toDateTimeString()
										);
									}else{
									
										$newInPrdOrderArray=array(
											'orderqty'=>0,
											'updated_at'=>Carbon::now()->toDateTimeString()
										);
									
									}
									$updateinproductionorderqty=DB::table('inproduction_order')->where('inproduction_orderID','=',$getInPrdOrders->inproduction_orderID)->update($newInPrdOrderArray);
									
								}
								//print_r($getOrderID);exit;
								$users=DB::table('dealer')->where('id','=',$getOrderID->dealerID)->first();
								//echo $users->first_name; exit;
								$emails=$users->emailID;
								$productData=DB::table('products')->where('product_id','=',$getOrderID->product_id)->first();
								
								$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
								$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
								$data_user_stocck_update =array(
									'productName' => $productData->productName,
									'color' =>  $getOrderID->product_color,
									'dealername' =>  $users->first_name,
									'categoryName' =>$categoryData->categoryName,
									'brandName' =>   $brandData->brandName,
									'batch' =>  $getOrderID->batch,
									'customer_name' =>$getOrderID->customer_name ,
									'orderDate' =>  date('d-m-Y',strtotime($getOrderID->created_at)),
									'orderStatus' =>  $getOrderID->orderStatus,
									'qtytype' => $post['qtytype'],
									'email' => $emails
									
								);
								
								
								if($post['qtytype']=='instock'){
									 $orderTranzArray=array(
										'mailstatus'=> '1',
										'qtystatus'=>'instock',
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
										 
									);
									
									Mail::send('email_templates.stockUpdate',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
									{
										$message->to($emails)->subject('Stock Update alert!');
									});
									
								}else{
									 
									/*  Mail::send('email_templates.stockUpdate',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
									{
										$message->to($emails)->subject('Stock Update alert!');
									}); */
									 $orderTranzArray=array(
										'mailstatus'=> '0',
										'qtystatus'=>$post['qtytype'],
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
									
									if( $post['qtytype']=='onseaukarrival' && isset($post['stockdate']) && !empty($post['stockdate'])){
										
										$orderTranzArray['stockdate']= date('Y-m-d',strtotime($post['stockdate']));
										 
									 } 
									  
									  
								}
								$updateorderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'][$i])->update($orderTranzArray);
								
								
							}else{
								//print_r($post);exit;
								  $orderTranzArray=array(
										 
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
								if($post['orderNoteTokenString'][$i] != '0'){
							//print_r($post['orderNoteTokenString'][$i]); 
									
									$updateorderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'][$i])->update($orderTranzArray);	
									
								}else{
									
									$updateorderTranz=DB::table('variation_tranz')->where('variationTranzToken','=',$post['variationTokenString'][$i])->update($orderTranzArray);	
								}
							//exit;
							}

						}
					}else{
						if(!empty($post['orderNoteTokenString'][$i]) && $post['orderNoteTokenString'][$i]!='0'){
							$getOrderID=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'][$i])->first();
							//print_r($post);exit;
							//print_r($getOrderID->specialOrderID); exit;
							if($getOrderID->specialOrderID > 0 ){
								if($post['qtytype']=='onseaukarrival') {
									$orderTranzArray=array(
										 
										'qtystatus'=>$post['qtytype'],
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
								}else if($post['qtytype']=='instock'){
									$orderTranzArray=array(
										'mailstatus'=> '1',
										'qtystatus'=>$post['qtytype'],
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
								}else if($post['qtytype']=='factorystock'){
									$orderTranzArray=array(
										'qtystatus'=>$post['qtytype'],
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
								}else{
									$orderTranzArray=array(
										 
										'qtystatus'=>$post['qtytype'],
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
								}
								//print_r($orderTranzArray); exit;
								$updateorderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'][$i])->update($orderTranzArray);	
							}
						}else{
							//print_r($post);exit;
							if($qtytype=='onseaukarrival' || $qtytype=='instock' || $qtytype=='factorystock') {
								$getOldVariation=DB::table('variation')->where('variationID','=',$post['varationToken'][$i])->first();
							
								if(!empty($getSameVariationInStock)){
									$getSameVariationInStock=DB::table('variation')
									->where('batch','=',$getOldVariation->batch)
									->where('product_id','=',$getOldVariation->product_id)
									->where('product_color','=',$getOldVariation->product_color)
									->where('product_status','=',$post['qtytype'])
									->where('deleted_at','=',NULL)
									->first();
							 
									
									$updateInStockArray=array(
										'productStock'=>$getSameVariationInStock->productStock + 1,
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
									
									if($getOldVariation->productStock > 0){
										
										$updateInProductionArray=array(
											'productStock'=>$getOldVariation->productStock - 1,
											'updated_at'=>Carbon::now()->toDateTimeString()
										);
										
									}else{
										
										$updateInProductionArray=array(
											'productStock'=>0,
											'updated_at'=>Carbon::now()->toDateTimeString()
										);
										
									}
				$getvartranz=DB::table('variation_tranz')->where('variationID','=',$post['varationToken'][$i])->orderBy('variationTranzToken','DESC')->first();
									$lastTranzTockenArray=explode('_',$getvartranz->variationTranzToken);
										$lastRecord=$lastTranzTockenArray[2];
										$lastRecord= $lastRecord + 1; 
										//echo exit;
										$variationTranzToken=$getSameVariationInStock->variationToken .'_'.$getSameVariationInStock->variationID.'_'.$lastRecord;
										
											$varTranzArray=array(
												'variationID'=>$getSameVariationInStock->variationID,
												'variationTranzToken'=>$variationTranzToken,
												'product_id'=>$getOldVariation->product_id,
												'product_status'=>$getOldVariation->product_status,
												'qty'=>1,
												'stockdate'=>$getOldVariation->stockdate,
												'created_at'=>Carbon::now()->toDateTimeString()
											);
											if( $post['qtytype']=='onseaukarrival' && isset($post['stockdate']) && !empty($post['stockdate'])){
										
												$varTranzArray['stockdate']= date('Y-m-d',strtotime($post['stockdate']));
												 
											 } 
										//print_r($varTranzArray); echo '<br/>';
										$addVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
									
									
									//exit;
									$updateInProduction=DB::table('variation')->where('variationID','=',$post['varationToken'][$i])->update($updateInProductionArray);
									
									$updateInStock=DB::table('variation')->where('variationID','=',$getSameVariationInStock->variationID)->update($updateInStockArray);
									
								}else{
									$variationToken='';
									for($j=0;$j < 4;$j++){
										$variationToken .= $this->getTokenProduct();
									}
									
									$updateInStockArray=array(
										'product_id'=> $getOldVariation->product_id,
										'product_status'=> $post['qtytype'],
										'productStock'=> 1,
										'variationToken'=> $variationToken,
										'batch'=> $getOldVariation->batch,
										'product_color'=> $getOldVariation->product_color,
										'model'=> $getOldVariation->model,
										'sku'=> $getOldVariation->sku,
										'created_at'=>Carbon::now()->toDateTimeString()
									);
									
									if($getOldVariation->productStock > 0){
										
										$updateInProductionArray=array(
											'productStock'=>$getOldVariation->productStock - 1,
											'updated_at'=>Carbon::now()->toDateTimeString()
										);
										
									}else{
										
										$updateInProductionArray=array(
											'productStock'=>0,
											'updated_at'=>Carbon::now()->toDateTimeString()
										);
										
									}
									
									$updateInProduction=DB::table('variation')->where('variationID','=',$post['varationToken'][$i])->update($updateInProductionArray);
									
									$updateInStock=DB::table('variation')->insertGetId($updateInStockArray);
									
									
										$varTranzArray=array(
											'variationID'=>$updateInStock,
											'variationTranzToken'=>$variationToken.'_'.$updateInStock.'_'.$i,
											'product_id'=>$getOldVariation->product_id,
											'product_status'=>$post['qtytype'],
											'qty'=>1,
											'stockdate'=>$getOldVariation->stockdate,
											'created_at'=>Carbon::now()->toDateTimeString()
										);
										if( $post['qtytype']=='onseaukarrival' && isset($post['stockdate']) && !empty($post['stockdate'])){
									
											$varTranzArray['stockdate']= date('Y-m-d',strtotime($post['stockdate']));
											 
										 } 
										//print_r($varTranzArray); echo '<br/>';
										$addVarTranz=DB::table('variation_tranz')->insert($varTranzArray);
								}
								$getDateTranzupdated_at=DB::table('variation_tranz')->where('variationID','=',$post['varationToken'][$i])->orderBy('updated_at','DESC')->first();
								if(!empty($getDateTranzupdated_at)){
									 
									$DeleteTranz=DB::table('variation_tranz')->where('variation_tranzID','=',$getDateTranzupdated_at->variation_tranzID)->delete();
									
								}else{
									
									$getDateTranzS=DB::table('variation_tranz')->where('variationID','=',$post['varationToken'][$i])->orderBy('variationTranzToken','DESC')->first();
									if(!empty($getDateTranzS)){
										
										 
										$DeleteTranz=DB::table('variation_tranz')->where('variation_tranzID','=',$getDateTranzS->variation_tranzID)->delete();
										
									}else{
										 
									}
								}
							}else{
								 $orderTranzArray=array(
										 
										'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
										'updated_at'=>Carbon::now()->toDateTimeString()
									);
								if(!empty($post['orderNoteTokenString']) && $post['orderNoteTokenString'][$i] != '0'){
									// print_r($orderTranzArray); exit; 
									$updateorderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'][$i])->update($orderTranzArray);	
									
								}else{
									// print_r($post); exit;
									 //print_r($orderTranzArray); exit;
							 
								
								$updateorderTranz=DB::table('variation_tranz')->where('variationTranzToken','=',$post['variationTokenString'][$i])->update($orderTranzArray);	
								}
							}
						}
					}
 
					 
				}
				Session::flash('operationSucess','Stock Updated Successfully !');
			}else{
				
				Session::flash('operationFaild','Select at list one product');
			}
			if(isset($post['page']) && $post['page']=='arrival'){
				
				return Redirect::to('admin/inseaarrivalukstockmanage');
			}elseif(isset($post['page']) && $post['page']=='factorystock'){
				return Redirect::to('admin/factorystockmanage');
				
			}else{
				
				return Redirect::to('admin/inproductionstockmanage');
			}
		 
		}else{
			return Redirect::to('/');
		}
    }
	public function updateProductionDate(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post= $request->all();
			//print_r($post);exit;
			
			if(isset($post) && !empty($post['stockdate'])){
				
				if(isset($post['orderNoteTokenString']) && !empty($post['orderNoteTokenString'])){
					
					$updateDateArray= array(
						'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
						'updated_at'=> Carbon::now()->toDateTimeString()
					);
					
					$updateDate=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderNoteTokenString'])->update($updateDateArray);
					 
					echo $updateDate;
					
				}else{
					
					$updateDateArray= array(
						'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
						'updated_at'=> Carbon::now()->toDateTimeString()
					);
					
					$updateDate=DB::table('variation_tranz')->where('variationTranzToken','=',$post['inPrdToken'])->update($updateDateArray);
					 
					echo $updateDate;
					
				}
				
			}
		}else{
			return Redirect::to('/');
		}
	}
	public function inSeaArrivalUKStockManage(){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			return view('admin.product.inSeaArrivalUKStockManage');
		}else{
			return Redirect::to('/');
		}
    }
	public function batchWiseStockManage(){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			// echo'call';exit;
			return view('admin.product.batchWiseStockManage');
		}else{
			return Redirect::to('/');
		}
    }
}