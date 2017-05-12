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
use App\Dealer;
use XeroLaravel;
use App\Products;
use App\Variation;
use App\Discount;
use App\Order;
use App\OrderDetails;
use App\AdminOrderNotes;
use Datatables;
use URL;
use DateTime;


class OrderController extends Controller
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
	public function orderList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.order.orderList')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function invoiceList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.order.invoiceList')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function createInvoice(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.order.createInvoice')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function orderDeliveryRotaList(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.order.orderListRota')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function createServiceInvoice(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.order.createServiceInvoice')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function orderDetials($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$orderidarray= explode('&', base64_decode($id));
			
			/* Update accessory id to json start*/
				/* 	$getallOrder=DB::table('order_transaction')->get();
					foreach($getallOrder as $geteachOrder){
						$oldAccessoryArray=array();
						$oldAccessoryArray=explode(',',$geteachOrder->accessoryID);
							$updayejsonarray=array();
							//print_r($oldAccessoryArray);
							$newAccessoryArray=array();
						 for($ik=0;$ik<count($oldAccessoryArray);$ik++){
								$newAccessoryArray[$oldAccessoryArray[$ik]] = '1';
							}
							$newAccessoryArray = json_encode($newAccessoryArray); 
							if(!empty($geteachOrder->accessoryID)){
								
								 $newAccessoryArrayUpade=array(
									'accessoryID'=>$newAccessoryArray
								);
								$updateorder=DB::table('order_transaction')->where('order_transactionID','=',$geteachOrder->order_transactionID)->update($newAccessoryArrayUpade); 
							//print_r($newAccessoryArray);
							//echo '<hr/>';
							}
					} */
					//exit;
					
					/* Update accessory id to json End*/
					
				//exit;	
			return view('admin.order.orderDetials')->with('sessionData',$sessionData)->with('orderID',$orderidarray)->with('pagedata',$id);
		}else{
			 return Redirect::to('/');
		}
    }
	public function orderPrint($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$orderidarray= explode('&', base64_decode($id));
			return view('admin.order.orderPrint')->with('sessionData',$sessionData)->with('orderID',$orderidarray);
		}else{
			 return Redirect::to('/');
		}
    }
	public function adminorderlistPending()
	{
		/*  $data=DB::table('order_transaction')
		->where('orderStatus','=','pending')
		->where('deleted_at','=',NULL)
		->orderBy('order_transactionID','desc'); */
		 
		 
		 $data=DB::table('order_transaction')
		->join('dealer', 'order_transaction.dealerID', '=', 'dealer.id')
		->join('products', 'order_transaction.product_id', '=', 'products.product_id')
		->join('category', 'products.category_id', '=', 'category.id')
		->where('order_transaction.deleted_at','=',NULL)
		->where('order_transaction.orderStatus','!=','complete')
		->where('order_transaction.finance','=','0')
		->orderBy('order_transaction.order_transactionID','desc')
		->select('order_transaction.orderNoteTokenString','order_transaction.dealerID','order_transaction.batch','order_transaction.product_id','order_transaction.specialOrderID','order_transaction.mailstatus','order_transaction.order_transactionID','order_transaction.accessoryID',
		 'order_transaction.product_color','order_transaction.stockdate','order_transaction.qtystatus','order_transaction.created_at','order_transaction.delivery_date','order_transaction.product_side_color','order_transaction.qty','order_transaction.orderStatus','order_transaction.customer_name','order_transaction.order_notes_descriptions','order_transaction.orderID',
		  'dealer.company_name','products.productName','category.categoryName'); 
		
		 
 
		$no = 0;
        return Datatables::of($data, $no)
			 
            ->addColumn('#', function ($data) {
				  $uniqueQtyNumber = $data->orderNoteTokenString;
                
                    return "<input type=\"checkbox\" name=\"OrderToken[]\" id=\"OrderToken$uniqueQtyNumber\"  value=\"$uniqueQtyNumber\"
					data-prdtoken=\"$data->product_id\" data-dealerToken=\"$data->dealerID\" onclick=\"orderforbooked('OrderToken$uniqueQtyNumber')\"
					data-colortoken=\"$data->product_color\" class=\"OrderToken\">";
                
            })
			
			->addColumn('Company Name', function ($data) {
				//$dealer=DB::table('dealer')->where('id','=',$data->dealerID)->first();
                if($data->company_name != ''){
					if($data->specialOrderID > 0){
						 return $data->company_name.'<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
					}else{
						
						return $data->company_name;
					}
                }else{
                    return '---';
                }
            })
			 
            ->addColumn('Product Name', function ($data) {
				//$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($data->productName != ''){
                    return $data->productName;
                }else{
                    return '---';
                }
                
            })
			
			->addColumn('Batch', function ($data) {
				if($data->batch != ''){
                    return $data->batch;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Category', function ($data) {
				//$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($data->categoryName != ''){
                   // $category=DB::table('category')->where('id','=',$data->category_id)->first();
					if(!empty($data->categoryName)){
						return $data->categoryName;
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })
			
			->addColumn('Color', function ($data) {
				if(!empty($data->product_color)){
					if(!empty($data->product_side_color)){
						$pathcolorpanel=URL::to('colorDataJson/panelcolor.json');
						$coloesidejson= file_get_contents($pathcolorpanel);
						$coloesidejson = @json_decode($coloesidejson,true);
						return $data->product_color  .'( with '.$coloesidejson[$data->product_side_color].' sides)';
					}else{
						
						return $data->product_color;
					}
						 
				}else{
					return '---';
				}
            })
			
			->addColumn('Order type', function ($data) {
				if(!empty($data->qtystatus)){
						//return $data->qtystatus;
						$uniqueQtyNumber = $data->orderNoteTokenString;
							if($data->qtystatus =='instock'){
								if(!empty($data->stockdate)){
									$stockDate=date('d-m-Y',strtotime($data->stockdate));
									return "<small class=\"label label-info\"> In Stock ($stockDate)</small><a href=\"#\" 
									onclick=\"editdate('$uniqueQtyNumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Edit Date</a>";
									
								}else{
									return "<small class=\"label label-info\"> In Stock</small><a href=\"#\" 
									onclick=\"editdate('$uniqueQtyNumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Edit Date</a>";
									
								}
							}else{
								if($data->mailstatus==0){
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
											return '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}
										
									}else{
										if($data->specialOrderID > 0){
											$getSpeacialOrders=DB::table('special_order')->where('id','=',$data->specialOrderID)->first();
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
										}else{
											if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
											}else{
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}
										}
										
									}
								}else{
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock ('.date('d-m-Y',strtotime($data->stockdate)).')</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}else{
										if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
											return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}
								}
							}
			 
					}else{
						return '---';
					}
            })
			
			->addColumn('Order Date', function ($data) {
				if(!empty($data->created_at)){
						return date('d-m-Y',strtotime($data->created_at));
					}else{
						return '---';
					}
            })
			
			->addColumn('Delivery Date', function ($data) {
				if(!empty($data->delivery_date)){
					if(!empty($data->orderStatus) && $data->orderStatus=="pending"){
						return '---';
					}else{
						return date('d-m-Y',strtotime($data->delivery_date));
						
					}
				}else{
					return '---';
				}
            })
			
			->addColumn('Qty', function ($data) {
				if(!empty($data->qty)){
						return $data->qty;
					}else{
						return '---';
					}
            })
			
			->addColumn('Status', function ($data) {
				if(!empty($data->orderStatus) && $data->orderStatus=="pending"){
					return '<label class="label label-warning" style="text-transform:capitalize;">'.$data->orderStatus.'</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="booked in for delivery"){
				 
					return '<label class="label label-warning" style="text-transform:capitalize;background-color:#F7609E;">booked in for delivery</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="invoiced"){
				 
					return '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="paid"){
				 
					return '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="collection"){
				 
					return '<label class="label label-primary" style="text-transform:capitalize;"> Collection</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="complete"){
				 
					return '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
				}else{
					return '---';
				}
            })
			
			->addColumn('Order Accessory', function ($data) {
				if(!empty($data->accessoryID)){
					 $acessorydata = json_encode($data->accessoryID,true);
					$datas = json_decode($acessorydata,true);
					$acessorydata = json_decode($datas,true);
					$number=0;
					$htmlData='';
					$htmlData .='
						<table  border="1" style="width:100%">
							<tr>
								<th style="font-size: 11px;">Accessory</th>
								<th style="font-size: 11px;">Qty</th>
							</tr>
							
						 
					';
						foreach($acessorydata as $k=>$v){
						//echo $acessory['accessory_qty'];
						$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$k)->first();
						$htmlData.='<tr>
								<td style="font-size: 11px;">'.$acessoryName->accessory_name .'</td>
								<td style="font-size: 11px;">'.$v.'</td>
							</tr>';
						}
						$htmlData .='</table>';
						return '<div class="accessoryData"><i style="padding: 5px; background: #18A689;color: #fff;   border-radius: 100%;" class="fa fa-check" aria-hidden="true"  data-toggle="tooltip" data-placement="bottom" title=" "></i>
						<div class="popover fade bottom in" role="tooltip" id="popover264416" style=""><div class="arrow"></div><h3 class="popover-title" style="font-weight: bold;
    text-align: center;">'.$data->productName .'</h3><div class="popover-content"> '.$htmlData.'</div></div>
						</div>';
						
					}else{
						return '---';
					} 
					 
            })
			
			->addColumn('Customer Name', function ($data) {
				if(!empty($data->customer_name)){
						return $data->customer_name;
					}else{
						return "<a href=\"#\" onclick=\"customername('$data->orderNoteTokenString')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Add Name</a>";
					}
            })
			
			->addColumn('Notes', function ($data) {
				if(!empty($data->order_notes_descriptions)){
						return $data->order_notes_descriptions;
					}else{
						return '---';
					}
            })

            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/edit-group', $data->order_transactionID);
				$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
				$productname= $productName->productName;
				$deatailurl = URL::to('admin/orderDetials', base64_encode($data->orderID.'&'.$data->orderNoteTokenString.'&pending'));
				$printurl = URL::to('admin/orderPrint', base64_encode($data->orderID.'&'.$data->orderNoteTokenString.'&pending'));
                $html = '';
				$uniqueQtyNumber = $data->orderNoteTokenString;
				if(!empty($data->orderStatus) && $data->orderStatus=="pending" || $data->orderStatus=="booked in for delivery" || $data->orderStatus=="collection" ){
					$html.= "<a href=\"javascript:void(0)\"  onclick=\"addnotes('$uniqueQtyNumber','$productname')\"  title=\"add notes\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-text-o\"></i></a>";
					
					$html.="<a href=\"$deatailurl\"  data-toggle=\"tooltip\ title=\"Edit order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil-square\"></i></a> ";
				}else{
					$html.="<a href=\"$deatailurl\"  data-toggle=\"tooltip\ title=\"Edit order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-eye\"></i></a> ";
				}
					$html.="  <a href=\"$printurl\"  data-toggle=\"tooltip\" target=\"_blank\" title=\"Print Order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-print\"></i></a>";
				if(!empty($data->orderStatus) && $data->orderStatus=="pending" || $data->orderStatus=="booked in for delivery" || $data->orderStatus=="collection" ){
					$html.="<a href=\"javascript:void(0);\" data-toggle=\"tooltip\" title=\"Delete\"  onclick=\"removedata('$data->orderID','$data->orderNoteTokenString')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";
				}
                return $html;
            })
            ->make(true);
		
	}
	public function adminOrderListRota()
	{
		/*  $data=DB::table('order_transaction')
		->where('orderStatus','=','pending')
		->where('deleted_at','=',NULL)
		->orderBy('order_transactionID','desc'); */
		 
		 
		/*  $data=DB::table('order_transaction')
		->join('dealer', 'order_transaction.dealerID', '=', 'dealer.id')
		->join('products', 'order_transaction.product_id', '=', 'products.product_id')
		->join('category', 'products.category_id', '=', 'category.id')
		->join('rota', 'order_transaction.order_transactionID', '=', 'rota.order_transactionID')
		->where('order_transaction.deleted_at','=',NULL)
		->where('order_transaction.orderStatus','!=','complete')
		->where('order_transaction.finance','=','0')
		->where('order_transaction.rota','=','1')
		->orderBy('order_transaction.order_transactionID','desc')
		->select('order_transaction.orderNoteTokenString','order_transaction.dealerID','order_transaction.batch','order_transaction.product_id','order_transaction.specialOrderID','order_transaction.mailstatus','order_transaction.order_transactionID','order_transaction.accessoryID',
		 'order_transaction.product_color','order_transaction.stockdate','order_transaction.qtystatus','order_transaction.created_at','order_transaction.delivery_date','order_transaction.qty','order_transaction.orderStatus','order_transaction.customer_name','order_transaction.order_notes_descriptions','order_transaction.orderID',
		  'dealer.company_name','products.productName','category.categoryName'
		  ,'rota.rotaID');  */
		
 
 
		$no = 0;
        return Datatables::of($data, $no)
			 
            ->addColumn('#', function ($data) {
				  $uniqueQtyNumber = $data->orderNoteTokenString;
                
                    return "<input type=\"checkbox\" name=\"OrderToken[]\" id=\"OrderToken$uniqueQtyNumber\"  value=\"$uniqueQtyNumber\"
					data-prdtoken=\"$data->product_id\" data-dealerToken=\"$data->dealerID\" onclick=\"orderforbooked('OrderToken$uniqueQtyNumber')\"
					data-colortoken=\"$data->product_color\" class=\"OrderToken\">";
                
            })
			
			->addColumn('Company Name', function ($data) {
				//$dealer=DB::table('dealer')->where('id','=',$data->dealerID)->first();
                if($data->company_name != ''){
					if($data->specialOrderID > 0){
						 return $data->company_name.'<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
					}else{
						
						return $data->company_name;
					}
                }else{
                    return '---';
                }
            })
			 
            ->addColumn('Product Name', function ($data) {
				//$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($data->productName != ''){
                    return $data->productName;
                }else{
                    return '---';
                }
                
            })
			
			->addColumn('Batch', function ($data) {
				if($data->batch != ''){
                    return $data->batch;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Category', function ($data) {
				//$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($data->categoryName != ''){
                   // $category=DB::table('category')->where('id','=',$data->category_id)->first();
					if(!empty($data->categoryName)){
						return $data->categoryName;
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })
			
			->addColumn('Color', function ($data) {
				if(!empty($data->product_color)){
						return $data->product_color;
					}else{
						return '---';
					}
            })
			
			->addColumn('Order type', function ($data) {
				if(!empty($data->qtystatus)){
						//return $data->qtystatus;
						$uniqueQtyNumber = $data->orderNoteTokenString;
							if($data->qtystatus =='instock'){
								if(!empty($data->stockdate)){
									$stockDate=date('d-m-Y',strtotime($data->stockdate));
									return "<small class=\"label label-info\"> In Stock ($stockDate)</small><a href=\"#\" 
									onclick=\"editdate('$uniqueQtyNumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Edit Date</a>";
									
								}else{
									return "<small class=\"label label-info\"> In Stock</small><a href=\"#\" 
									onclick=\"editdate('$uniqueQtyNumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Edit Date</a>";
									
								}
							}else{
								if($data->mailstatus==0){
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
											return '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}
										
									}else{
										if($data->specialOrderID > 0){
											$getSpeacialOrders=DB::table('special_order')->where('id','=',$data->specialOrderID)->first();
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
										}else{
											if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
											}else{
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}
										}
										
									}
								}else{
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock ('.date('d-m-Y',strtotime($data->stockdate)).')</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}else{
										if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
											return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}
								}
							}
			 
					}else{
						return '---';
					}
            })
			
			->addColumn('Order Date', function ($data) {
				if(!empty($data->created_at)){
						return date('d-m-Y',strtotime($data->created_at));
					}else{
						return '---';
					}
            })
			
			->addColumn('Delivery Date', function ($data) {
				if(!empty($data->delivery_date)){
						return date('d-m-Y',strtotime($data->delivery_date));
					}else{
						return '---';
					}
            })
			
			->addColumn('Qty', function ($data) {
				if(!empty($data->qty)){
						return $data->qty;
					}else{
						return '---';
					}
            })
			
			->addColumn('Status', function ($data) {
				if(!empty($data->orderStatus) && $data->orderStatus=="pending"){
					return '<label class="label label-warning" style="text-transform:capitalize;">'.$data->orderStatus.'</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="booked in for delivery"){
				 
					return '<label class="label label-warning" style="text-transform:capitalize;background-color:#F7609E;">booked in for delivery</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="invoiced"){
				 
					return '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="paid"){
				 
					return '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="collection"){
				 
					return '<label class="label label-primary" style="text-transform:capitalize;"> Collection</label>';
				}else if(!empty($data->orderStatus) && $data->orderStatus=="complete"){
				 
					return '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
				}else{
					return '---';
				}
            })
			
			->addColumn('Order Accessory', function ($data) {
				if(!empty($data->accessoryID)){
					 $acessorydata = json_encode($data->accessoryID,true);
					$datas = json_decode($acessorydata,true);
					$acessorydata = json_decode($datas,true);
					$number=0;
					$htmlData='';
					$htmlData .='
						<table  border="1" style="width:100%">
							<tr>
								<th style="font-size: 11px;">Accessory</th>
								<th style="font-size: 11px;">Qty</th>
							</tr>
							
						 
					';
						foreach($acessorydata as $k=>$v){
						//echo $acessory['accessory_qty'];
						$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$k)->first();
						$htmlData.='<tr>
								<td style="font-size: 11px;">'.$acessoryName->accessory_name .'</td>
								<td style="font-size: 11px;">'.$v.'</td>
							</tr>';
						}
						$htmlData .='</table>';
						return '<div class="accessoryData"><i style="padding: 5px;    background: #18A689;color: #fff;   border-radius: 100%;" class="fa fa-check" aria-hidden="true"  data-toggle="tooltip" data-placement="bottom" title=" "></i>
						<div class="popover fade bottom in" role="tooltip" id="popover264416" style=""><div class="arrow"></div><h3 class="popover-title" style="font-weight: bold;
						text-align: center;">'.$data->productName .'</h3><div class="popover-content"> '.$htmlData.'</div></div>
						</div>';
						
					}else{
						return '---';
					} 
					 
            })
			
			->addColumn('Customer Name', function ($data) {
				if(!empty($data->customer_name)){
						return $data->customer_name;
					}else{
						return "<a href=\"#\" onclick=\"customername('$data->orderNoteTokenString')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Add Name</a>";
					}
            })
			
			->addColumn('Notes', function ($data) {
				if(!empty($data->order_notes_descriptions)){
						return $data->order_notes_descriptions;
					}else{
						return '---';
					}
            })

            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/edit-group', $data->order_transactionID);
				$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
				$productname= $productName->productName;
				 $deatailurl = URL::to('admin/orderDetials', base64_encode($data->orderID.'&'.$data->orderNoteTokenString.'&pending'));
				 $printurl = URL::to('admin/orderPrint', base64_encode($data->orderID.'&'.$data->orderNoteTokenString.'&pending'));
                $html = '';
				$uniqueQtyNumber = $data->orderNoteTokenString;
				if(!empty($data->orderStatus) && $data->orderStatus=="pending" || $data->orderStatus=="booked in for delivery" || $data->orderStatus=="collection" ){
					$html.= "<a href=\"javascript:void(0)\"  onclick=\"addnotes('$uniqueQtyNumber','$productname')\"  title=\"add notes\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-text-o\"></i></a>";
					$html.=" <a href=\"$deatailurl\"  data-toggle=\"tooltip\ title=\"Edit order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil-square\"></i></a>  <a href=\"$printurl\"  data-toggle=\"tooltip\" target=\"_blank\" title=\"Print Order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-print\"></i></a>";
					$html.="<a href=\"javascript:void(0);\" data-toggle=\"tooltip\" title=\"Delete\"  onclick=\"removedata('$data->orderID','$data->orderNoteTokenString')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";
				}
                return $html;
            })
            ->make(true);
		
	}
	
	public function adminOrderlistBooked()
	{
		//$data=DB::table('order_transaction')->where('deleted_at','=',NULL)->where('orderStatus','=','booked in for delivery')->orderBy('order_transactionID','desc');
		
		 $data=DB::table('order_transaction')
		->join('dealer', 'order_transaction.dealerID', '=', 'dealer.id')
		->join('products', 'order_transaction.product_id', '=', 'products.product_id')
		->join('category', 'products.category_id', '=', 'category.id')
		->where('order_transaction.orderStatus','=','booked in for delivery')
		->where('order_transaction.deleted_at','=',NULL)
		->orderBy('order_transaction.order_transactionID','desc')
		->select('order_transaction.orderNoteTokenString','order_transaction.dealerID','order_transaction.batch','order_transaction.product_id','order_transaction.specialOrderID','order_transaction.mailstatus','order_transaction.order_transactionID',
		  'order_transaction.product_color','order_transaction.stockdate','order_transaction.qtystatus','order_transaction.created_at','order_transaction.delivery_date','order_transaction.qty','order_transaction.orderStatus','order_transaction.accessoryID','order_transaction.customer_name','order_transaction.order_notes_descriptions','order_transaction.orderID',
		  'dealer.company_name','products.productName','category.categoryName'); 
		  
		$no = 0;
        return Datatables::of($data, $no)

            ->addColumn('#', function ($data) {
				  $uniqueQtyNumber = $data->orderNoteTokenString;
                
					return "<input id=\"checkbox_$data->orderNoteTokenString\" type=\"checkbox\" class=\"selectorder disableclass\" name=\"orderID[]\" data-tranztoken=\"$data->orderNoteTokenString\" data-qtystatus=\"$data->qtystatus\" data-dealerToken=\"$data->dealerID\" value=\"$data->orderID\" onclick=\"invoicegen('checkbox_$data->orderNoteTokenString')\" />";
                
            })
			
			->addColumn('Company Name', function ($data) {
				$dealer=DB::table('dealer')->where('id','=',$data->dealerID)->first();
                if($dealer->company_name != ''){
					if($data->specialOrderID > 0){
						 return $dealer->company_name.'<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
					}else{
						
						return $dealer->company_name;
					}
                }else{
                    return '---';
                }
            })
			
            ->addColumn('Product Name', function ($data) {
				$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($productName->productName != ''){
                    return $productName->productName;
                }else{
                    return '---';
                }
                
            })
			
			->addColumn('Batch', function ($data) {
				if($data->batch != ''){
                    return $data->batch;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Category', function ($data) {
				$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($productName->category_id != ''){
                    $category=DB::table('category')->where('id','=',$productName->category_id)->first();
					if(!empty($category->categoryName)){
						return $category->categoryName;
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })
			
			->addColumn('Color', function ($data) {
				if(!empty($data->product_color)){
						return $data->product_color;
					}else{
						return '---';
					}
            })
			
			->addColumn('Order type', function ($data) {
				if(!empty($data->qtystatus)){
						//return $data->qtystatus;
						$uniqueQtyNumber = $data->orderNoteTokenString;
							if($data->qtystatus =='instock'){
								if(!empty($data->stockdate)){
									$stockDate=date('d-m-Y',strtotime($data->stockdate));
									return "<small class=\"label label-info\"> In Stock ($stockDate)</small><a href=\"#\" 
									onclick=\"editdate('$uniqueQtyNumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Edit Date</a>";
									
								}else{
									return "<small class=\"label label-info\"> In Stock</small><a href=\"#\" 
									onclick=\"editdate('$uniqueQtyNumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Edit Date</a>";
									
								}
							}else{
								if($data->mailstatus==0){
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
											return '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}
										
									}else{
										if($data->specialOrderID > 0){
											$getSpeacialOrders=DB::table('special_order')->where('id','=',$data->specialOrderID)->first();
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
										}else{
											if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
											}else{
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}
										}
										
									}
								}else{
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock ('.date('d-m-Y',strtotime($data->stockdate)).')</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}else{
										if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}
								}
							}
			 
					}else{
						return '---';
					}
            })
			
			->addColumn('Order Date', function ($data) {
				if(!empty($data->created_at)){
						return date('d-m-Y',strtotime($data->created_at));
					}else{
						return '---';
					}
            })
			
			->addColumn('Delivery Date', function ($data) {
				if(!empty($data->delivery_date)){
						return date('d-m-Y',strtotime($data->delivery_date));
					}else{
						return '---';
					}
            })
			
			->addColumn('Qty', function ($data) {
				if(!empty($data->qty)){
						return $data->qty;
					}else{
						return '---';
					}
            })
			
			->addColumn('Status', function ($data) {
				if(!empty($data->orderStatus)){
						return '<label class="label label-warning" style="text-transform:capitalize;background-color:#F7609E;">'.$data->orderStatus.'</label>';
					}else{
						return '---';
					}
            })
			
			->addColumn('Order Accessory', function ($data) {
				if(!empty($data->accessoryID)){
						return '<i style="padding: 5px;    background: #18A689;color: #fff;    border-radius: 100%;" class="fa fa-check" aria-hidden="true"></i>';
					}else{
						return '---';
					} 
					 
            })
			
			->addColumn('Customer Name', function ($data) {
				if(!empty($data->customer_name)){
						return $data->customer_name;
						 
					}else{
						return "<a href=\"#\" onclick=\"customername('$data->orderNoteTokenString')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-plus-square\">&nbsp;</i>Add Name</a>";
					}
            })
			
			->addColumn('Notes', function ($data) {
				if(!empty($data->order_notes_descriptions)){
						return $data->order_notes_descriptions;
					}else{
						return '---';
					}
            })

            ->addColumn('Action', function ($data) {
                $url = URL::to('admin/edit-group', $data->order_transactionID);
				$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
				$productname= $productName->productName;
				$deatailurl = URL::to('admin/orderDetials', base64_encode($data->orderID.'&'.$data->orderNoteTokenString.'&pending'));
				$printurl = URL::to('admin/orderPrint', base64_encode($data->orderID.'&'.$data->orderNoteTokenString.'&pending'));
                $html = '';
			$uniqueQtyNumber = $data->orderNoteTokenString;
                $html.= "<a href=\"javascript:void(0)\"  onclick=\"addnotes('$uniqueQtyNumber','$productname')\"  title=\"add notes\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-text-o\"></i></a>";
				$html.=" <a href=\"$deatailurl\"  data-toggle=\"tooltip\ title=\"Edit order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil-square\"></i></a>  <a href=\"$printurl\"  data-toggle=\"tooltip\" target=\"_blank\" title=\"Print Order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-print\"></i></a>";
				$html.="<a href=\"javascript:void(0);\" data-toggle=\"tooltip\" title=\"Delete\"  onclick=\"removedata('$data->orderID','$data->orderNoteTokenString')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";
                return $html;
            })
			
            ->make(true);
		
	}
	public function adminOrderListCompleteOrderSingle()
	{
		//$data=DB::table('order_transaction')->where('deleted_at','=',NULL)->where('orderStatus','=','booked in for delivery')->orderBy('order_transactionID','desc');
		
		 $data=DB::table('order_transaction')
		->join('dealer', 'order_transaction.dealerID', '=', 'dealer.id')
		->join('products', 'order_transaction.product_id', '=', 'products.product_id')
		->join('category', 'products.category_id', '=', 'category.id')
		->where('order_transaction.orderStatus','=','complete')
		->where('order_transaction.deleted_at','=',NULL)
		->orderBy('order_transaction.order_transactionID','desc')
		->select('order_transaction.orderNoteTokenString','order_transaction.dealerID','order_transaction.batch','order_transaction.product_id','order_transaction.specialOrderID','order_transaction.mailstatus','order_transaction.order_transactionID',
		  'order_transaction.product_color','order_transaction.stockdate','order_transaction.qtystatus','order_transaction.created_at','order_transaction.delivery_date','order_transaction.qty','order_transaction.orderStatus','order_transaction.accessoryID','order_transaction.product_side_color','order_transaction.customer_name','order_transaction.order_notes_descriptions','order_transaction.orderID',
		  'dealer.company_name','products.productName','category.categoryName'); 
		  
		$no = 0;
        return Datatables::of($data, $no)

           
			->addColumn('Company Name', function ($data) {
				$dealer=DB::table('dealer')->where('id','=',$data->dealerID)->first();
                if($dealer->company_name != ''){
					if($data->specialOrderID > 0){
						 return $dealer->company_name.'<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
					}else{
						
						return $dealer->company_name;
					}
                }else{
                    return '---';
                }
            })
			
            ->addColumn('Product Name', function ($data) {
				$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($productName->productName != ''){
                    return $productName->productName;
                }else{
                    return '---';
                }
                
            })
			
			->addColumn('Batch', function ($data) {
				if($data->batch != ''){
                    return $data->batch;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Category', function ($data) {
				$productName=DB::table('products')->where('product_id','=',$data->product_id)->first();
                if($productName->category_id != ''){
                    $category=DB::table('category')->where('id','=',$productName->category_id)->first();
					if(!empty($category->categoryName)){
						return $category->categoryName;
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })
			
			->addColumn('Color', function ($data) {
				if(!empty($data->product_color)){
					if(!empty($data->product_side_color)){
						$pathcolorpanel=URL::to('colorDataJson/panelcolor.json');
						$coloesidejson= file_get_contents($pathcolorpanel);
						$coloesidejson = @json_decode($coloesidejson,true);
						return $data->product_color .'( with '.$coloesidejson[$data->product_side_color].' sides)';
					}else{
						
						return $data->product_color;
					}
						 
				}else{
					return '---';
				}
            })
			
			->addColumn('Order type', function ($data) {
				if(!empty($data->qtystatus)){
						//return $data->qtystatus;
						$uniqueQtyNumber = $data->orderNoteTokenString;
							if($data->qtystatus =='instock'){
								if(!empty($data->stockdate)){
									$stockDate=date('d-m-Y',strtotime($data->stockdate));
									return "<small class=\"label label-info\"> In Stock ($stockDate)</small> ";
									
								}else{
									return "<small class=\"label label-info\"> In Stock</small> ";
									
								}
							}else{
								if($data->mailstatus==0){
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
											return '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}
										
									}else{
										if($data->specialOrderID > 0){
											$getSpeacialOrders=DB::table('special_order')->where('id','=',$data->specialOrderID)->first();
											return '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
										}else{
											if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
											}else{
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
											}
										}
										
									}
								}else{
									if(!empty($data->stockdate)){
										if($data->qtystatus== 'onseaukarrival'){
											return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
											return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
											return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock ('.date('d-m-Y',strtotime($data->stockdate)).')</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}else{
										if($data->qtystatus== 'onseaukarrival'){
												return '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'inproduction'){
												return '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($data->stockdate)).') </small>';
										}elseif($data->qtystatus== 'factorystock'){
												return '<label class="label label-primary"> FactoryStock</label>';
										}else{
											
										return '<small class="label label-info"> In Stock</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
										}
										
									}
								}
							}
			 
					}else{
						return '---';
					}
            })
			
			->addColumn('Order Date', function ($data) {
				if(!empty($data->created_at)){
						return date('d-m-Y',strtotime($data->created_at));
					}else{
						return '---';
					}
            })
			
			->addColumn('Delivery Date', function ($data) {
				if(!empty($data->delivery_date)){
						return date('d-m-Y',strtotime($data->delivery_date));
					}else{
						return '---';
					}
            })
			
			->addColumn('Qty', function ($data) {
				if(!empty($data->qty)){
						return $data->qty;
					}else{
						return '---';
					}
            })
			
			->addColumn('Status', function ($data) {
				if(!empty($data->orderStatus)){
						return '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
					}else{
						return '---';
					}
            })
			
			->addColumn('Order Accessory', function ($data) {
				if(!empty($data->accessoryID)){
						return '<i style="padding: 5px;    background: #18A689;color: #fff;    border-radius: 100%;" class="fa fa-check" aria-hidden="true"></i>';
					}else{
						return '---';
					} 
					 
            })
			
			->addColumn('Customer Name', function ($data) {
				if(!empty($data->customer_name)){
						return $data->customer_name;
						 
					}else{
						return "--";
					}
            })
			
			->addColumn('Notes', function ($data) {
				if(!empty($data->order_notes_descriptions)){
						return $data->order_notes_descriptions;
					}else{
						return '---';
					}
            })

           
			
            ->make(true);
		
	}
	public function adminOrderListInvoiced(){
 
		//$data=DB::table('order_invoice')->where('invoice_status','=','invoiced');
		$data=DB::table('order_invoice')
		->join('dealer', 'order_invoice.dealerID', '=', 'dealer.id')
		->where('order_invoice.invoice_status','!=','complete')->select('order_invoice.created_at',
		'order_invoice.order_invoice_ID',
		'order_invoice.orderID',
		'order_invoice.invoiceNumber',
		'order_invoice.serviceInvoice',
		'order_invoice.invoiceTitle',
		'order_invoice.invoice_status',
		'order_invoice.invoicepdf',
		'order_invoice.orderNoteTokenString',
		'dealer.company_name');
		$no = 0;
        return Datatables::of($data, $no)

            ->addColumn('#', function ($data) {
				 $orderid=base64_encode($data->order_invoice_ID);
					return "<input type=\"checkbox\" name=\"invoiceToken[]\" class=\"form-control\" value=\"$orderid\"/> ";
                
            })
			
			->addColumn('Invoice Number', function ($data) {
				if($data->invoiceNumber != ''){
					$invoiceediturl = URL::to('admin/adminorderinvoiceedit', base64_encode($data->order_invoice_ID).'&invoice');
                   
					return '<a href="'.$invoiceediturl.'" data-toggle="tooltip" title="View order">'.$data->invoiceNumber.'</a>';
                }else{
                    return '---';
                }
            })
			->addColumn('Invoice Title', function ($data) {
				if($data->invoiceTitle != ''){
					 
                   
					return $data->invoiceTitle ;
                }else{
                    return '---';
                }
            })
			
			->addColumn('Invoice Date', function ($data) {
				 
                if($data->created_at != ''){
                    return date('d-m-Y',strtotime($data->created_at));
                }else{
                    return '---';
                }
            })

            ->addColumn('Delivery Date', function ($data) {
				$orderID=explode(",",$data->orderNoteTokenString);
				if(!empty($data->orderNoteTokenString)){
					 $getDate=DB::table('order_transaction')->where('orderNoteTokenString','=',$orderID[0])->first();
					if($getDate->delivery_date != ''){
						return date('d-m-Y',strtotime($getDate->delivery_date));
					}else{
						return '---';
					} 
				}else{
					return '---';
				}
        
                
            })
			->addColumn('Company Name', function ($data) {
				//$dealer=DB::table('dealer')->where('id','=',$data->dealerID)->first();
                if($data->company_name != ''){
                    return $data->company_name;
                }else{
                    return '---';
                }
            })
			->addColumn('Status', function ($data) {
				if($data->invoice_status=='invoiced'){
					return '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
				}elseif($data->invoice_status=='paid'){
					return '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
				}elseif($data->invoice_status=='complete'){
					return '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
				}else{
					return '--';
				}
                
            })
			

            ->addColumn('Action', function ($data) {
                $html = '';
				$orderinvoiceid=base64_encode($data->order_invoice_ID);
				$invoicenumber=$data->invoiceNumber;
				$invoiceediturl=URL::to('admin/adminorderinvoiceedit', base64_encode($data->order_invoice_ID).'&invoice');
					if($data->serviceInvoice!='1'){
				$html.="<a href=\"#\" title=\"View\"  onclick=\"showinvoicedorders('$orderinvoiceid','$invoicenumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-eye\"></i></a>";
					}
				if($data->invoice_status!='complete'){
					 $html.="<a href=\"$invoiceediturl\" data-toggle=\"tooltip\" title=\"Edit order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil-square\"></i></a>";
				}
				if(!empty($data->invoicepdf)){
					$path=URL::to('uploads/invoicepdf/'.$data->invoicepdf);
					if (file_exists('uploads/invoicepdf/'.$data->invoicepdf))
						{
							//echo "Yup. It exists.";
						$html.="<a href=\"$path\" target=\"_blank\" title=\"View Invoice\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i></a>";
						}
				}
				if($data->invoice_status!='complete'){
					$html.="<a href=\"javascript:void(0);\" data-toggle=\"tooltip\" title=\"Delete\"  onclick=\"removedatainvoice('$data->order_invoice_ID')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";
				}
                return $html;
            })
            ->make(true);
	}
	public function adminOrderListInvoicedPopup(){
		$datainv=Input::all();
		$dataInvoice=DB::table('order_invoice')->where('order_invoice_ID','=',base64_decode($datainv['valID']))->first();
		//$data=DB::table('order_transaction')->where('deleted_at','=',NULL)->where('orderStatus','=',base64_decode($datainv['valID']))->orderBy('order_transactionID','desc');
		$no = 0;
		//print_r($dataInvoice->orderNoteTokenString);
		$ordertranz=explode(",",$dataInvoice->orderNoteTokenString)	;
		for($i=0;$i<count($ordertranz);$i++){
			$order=DB::table('order_transaction')->where('deleted_at','=',NULL)->where('orderNoteTokenString','=',$ordertranz[$i])->first();
			if(!empty($order)){
				?>
					<tr>
						<td>
							<?php 
							if(!empty($order->dealerID)){
								
								$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
								$name='';
								if(!empty($delaername->company_name)){
									$name= $delaername->company_name;
								}
								echo $name;
							}
							?>
						</td>
						<td>
						<?php 
							$getProductData=DB::table('products')->where('product_id','=', $order->product_id)->first();
							if(!empty($getProductData->productName)){
								echo $getProductData->productName;
								if($order->specialOrderID > 0){
									echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
								}
							}
						?>
						</td>
						<td>
							<?php 
								if(!empty($order->batch)){
									echo $order->batch;
								}
							?>
						</td>
						<td>
							<?php 
								$getCategoryData=DB::table('category')->where('id','=', $getProductData->category_id)->first();
								if(!empty($getCategoryData->categoryName)){
									echo $getCategoryData->categoryName;
								}
							?>
						</td>
						
						<td>
						<?php
						//echo $order->product_id;
							if(!empty($order->qtystatus)){
								$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
								if($order->qtystatus =='instock'){
									echo '<small class="label label-info"> In Stock</small>';
								}else{
									 
									if($order->mailstatus==0){
										if(!empty($date->stockdat)){
											if($order->qtystatus== 'onseaukarrival'){
												echo '<small class="label label-success"   style="background-color: #029dff;"> On Sea - UK Arrival('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
											}else{
												echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
											 
											}
											
										}else{
											if($order->specialOrderID > 0){
												$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
												if($order->qtystatus== 'onseaukarrival'){
													echo '<small class="label label-success"   style="background-color: #029dff;"> On Sea - UK Arrival('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
												}else if($order->qtystatus== 'factorystock'){
													echo '<label class="label label-primary"> FactoryStock</label>';
												}else{
													echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
												 
												}
											 
											}else{
												
												if($order->qtystatus== 'onseaukarrival'){
													echo '<small class="label label-success"   style="background-color: #029dff;"> On Sea - UK Arrival('.date('d-m-Y',strtotime($order->stockdate)).' ) </small>';
												}else if($order->qtystatus== 'factorystock'){
													echo '<label class="label label-primary"> FactoryStock</label>';
												}else{
													echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($order->stockdate)).' ) </small>';
												 
												}
											}
										}
									}else{
										echo '<small class="label label-info"> In Stock</small>';
									}
								}
							} 
							
						?>
						</td>
						<td>
							<?php
									if(!empty($order->product_color)){
										if(!empty($order->product_side_color)){
											$pathcolorpanel=URL::to('colorDataJson/panelcolor.json');
											$coloesidejson= file_get_contents($pathcolorpanel);
											$coloesidejson = @json_decode($coloesidejson,true);
											echo $order->product_color .'( with '.$coloesidejson[$order->product_side_color].' sides)';
										}else{
											
											echo $order->product_color;
										}
									}
								 
							?>
						</td>
						<td>1</td>
						<td>
							<?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at));}?>
						</td>
					
						<td>
							<?php
								if(!empty($order->delivery_date)){
									
									echo date('d-m-Y',strtotime($order->delivery_date));
								}else{
									echo '---';
								}
								?>
						</td>
						<td>
							<?php
							$orderNoteTokenString=$order->orderNoteTokenString;
							//echo $orderNoteTokenString;
							//$getOrderNotes=DB::table('order_notes')->where('orderNoteTokenString','=',$orderNoteTokenString)->first();
								if(!empty($order->customer_name)){
									echo $order->customer_name;
								}else{
									echo '---';
								}
							?>
						</td>
						<td>
							<?php 
								if(!empty($order->order_notes_descriptions)){
									echo $order->order_notes_descriptions;
								}else{
									echo '---';
								}
							?>
						</td>
						
						<td>
						<?php 
							if($order->orderStatus=='invoiced'){
								echo '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
							}else if($order->orderStatus=='paid'){
								echo '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
							}else{
								 if($order->orderStatus=='complete'){
									 echo '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
								 }
							}
							
							?>
						</td>
						 
					</tr>
					<?php
			}
		}
		
	}
	public function adminOrderListPaid(){
		//$data=DB::table('order_invoice')->where('invoice_status','=','paid');
		$data=DB::table('order_invoice')->where('order_invoice.invoice_status','=','paid')
		->join('dealer', 'order_invoice.dealerID', '=', 'dealer.id');
		$no = 0;
        return Datatables::of($data, $no)

            ->addColumn('#', function ($data) {
				 $orderid=base64_encode($data->order_invoice_ID);
					return "<input type=\"checkbox\" name=\"invoiceToken[]\" class=\"form-control\" value=\"$orderid\"/> ";
                
            })
			
			->addColumn('Invoice Number', function ($data) {
				if($data->invoiceNumber != ''){
					$invoiceediturl = URL::to('admin/adminorderinvoiceedit', base64_encode($data->order_invoice_ID).'&invoice');
                   
					return '<a href="'.$invoiceediturl.'" data-toggle="tooltip" title="View order">'.$data->invoiceNumber.'</a>';
                }else{
                    return '---';
                }
            })
			
			->addColumn('Invoice Date', function ($data) {
				 
                if($data->created_at != ''){
                    return date('d-m-Y',strtotime($data->created_at));
                }else{
                    return '---';
                }
            })

            ->addColumn('Delivery Date', function ($data) {
				if($data->delivery_date != ''){
                    return date('d-m-Y',strtotime($data->delivery_date));
                }else{
                    return '---';
                }
                
            })
			->addColumn('Company Name', function ($data) {
				//$dealer=DB::table('dealer')->where('id','=',$data->dealerID)->first();
                if($data->company_name != ''){
                    return $data->company_name;
                }else{
                    return '---';
                }
            })
			->addColumn('Status', function ($data) {
				 
                return '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
                
            })
			

            ->addColumn('Action', function ($data) {
                $html = '';
				$orderinvoiceid=base64_encode($data->order_invoice_ID);
				$invoicenumber=$data->invoiceNumber;
				$invoiceediturl=URL::to('admin/adminorderinvoiceedit', base64_encode($data->order_invoice_ID).'&invoice');
				$html.="<a href=\"#\" title=\"View\"  onclick=\"showinvoicedorders('$orderinvoiceid','$invoicenumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-eye\"></i></a>";
                  $html.="<a href=\"$invoiceediturl\" data-toggle=\"tooltip\" title=\"Edit order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil-square\"></i></a>";
				
				if(!empty($data->invoicepdf)){
					$path=URL::to('uploads/invoicepdf/'.$data->invoicepdf);
					if (file_exists('uploads/invoicepdf/'.$data->invoicepdf))
						{
							//echo "Yup. It exists.";
						$html.="<a href=\"$path\" target=\"_blank\" title=\"View Invoice\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i></a>";
						}
				}
				
				$html.="<a href=\"javascript:void(0);\" data-toggle=\"tooltip\" title=\"Delete\"  onclick=\"removedatainvoice('$data->order_invoice_ID')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";
                return $html;
            })
            ->make(true);
	}
	public function adminOrderListComplete(){
		// $data=DB::table('order_invoice')->where('invoice_status','=','complete');
			$data=DB::table('order_invoice')
		->join('dealer', 'order_invoice.dealerID', '=', 'dealer.id')
		->where('order_invoice.invoice_status','=','complete')->select('order_invoice.created_at',
		'order_invoice.order_invoice_ID',
		'order_invoice.invoiceNumber',
		'order_invoice.orderID',
		 
		'order_invoice.serviceInvoice',
		'order_invoice.invoiceTitle',
		'order_invoice.invoice_status',
		'order_invoice.invoicepdf',
		'dealer.company_name');
		// $data=DB::table('order_invoice')->where('order_invoice.invoice_status','=','complete')->join('dealer', 'order_invoice.dealerID', '=', 'dealer.id');
		$no = 0;
         return Datatables::of($data, $no)

			
			->addColumn('Invoice Number', function ($data) {
				if($data->invoiceNumber != ''){
					$invoiceediturl = URL::to('admin/adminorderinvoiceedit', base64_encode($data->order_invoice_ID).'&invoice');
                   
					return '<a href="'.$invoiceediturl.'" data-toggle="tooltip" title="View order">'.$data->invoiceNumber.'</a>';
                }else{
                    return '---';
                }
            })
			->addColumn('Invoice Title', function ($data) {
				if($data->invoiceTitle != ''){
					 
                   
					return $data->invoiceTitle ;
                }else{
                    return '---';
                }
            })
			->addColumn('Invoice Date', function ($data) {
				 
                if($data->created_at != ''){
                    return date('d-m-Y',strtotime($data->created_at));
                }else{
                    return '---';
                }
            })

            ->addColumn('Delivery Date', function ($data) {
				$orderID=explode(",",$data->orderID);
				if(!empty($data->orderID)){
					 $getDate=DB::table('order_transaction')->where('orderID','=',$orderID[0])->first();
					if($getDate->delivery_date != ''){
						return date('d-m-Y',strtotime($getDate->delivery_date));
					}else{
						return '---';
					} 
				}else{
					return '---';
				}
                
            })
			->addColumn('Company Name', function ($data) {
				//$dealer=DB::table('dealer')->where('id','=',$data->dealerID)->first();
                if($data->company_name != ''){
                    return $data->company_name;
                }else{
                    return '---';
                }
            })
			->addColumn('Status', function ($data) {
				 
                return '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
                
            })
			

            ->addColumn('Action', function ($data) {
                $html = '';
				$orderinvoiceid=base64_encode($data->order_invoice_ID);
				$invoicenumber=$data->invoiceNumber;
				$invoiceediturl=URL::to('admin/adminorderinvoiceedit', base64_encode($data->order_invoice_ID).'&invoice');
				$html.="<a href=\"#\" title=\"View\"  onclick=\"showinvoicedorders('$orderinvoiceid','$invoicenumber')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-eye\"></i></a>";
                  $html.="<a href=\"$invoiceediturl\" data-toggle=\"tooltip\" title=\"Edit order\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil-square\"></i></a>";
				
				if(!empty($data->invoicepdf)){
					$path=URL::to('uploads/invoicepdf/'.$data->invoicepdf);
					if (file_exists('uploads/invoicepdf/'.$data->invoicepdf))
						{
							//echo "Yup. It exists.";
						$html.="<a href=\"$path\" target=\"_blank\" title=\"View Invoice\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i></a>";
						}
				}
				
			 
                return $html;
            })
            ->make(true);
	}
	public function admineditorder($orderId){
         $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
	 
		if(isset($id ) && !empty( $id ) )
        {
			$order = new Order();
			$cartData=$order->where('orderID','=',base64_decode($orderId))->get();
			//print_r($cartData); 
			 return view('admin.order.adminEditOrder')->with('cartData',$cartData);
		//   return Redirect::to('/admin/productdetail/'.$post['productToken'])->with('cart',$cart);
        }else{
            return Redirect::to('/');
        }
	}
	public function orderNotesListAdmin(Request $request){
		$post=$request->all();
		 $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($post) && !empty($post)){
			//print_r($post);exit;
			$uniqueQtyNumber=$post['orderdata'];
			$getTran=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderdata'])->where('deleted_at','=',NULL)->first();
			$notes=DB::table('admin_order_notes')->where('orderTokenString','=',$post['orderdata'])->orderBy('admin_order_notesID','desc')->get();
			$delaerName=DB::table('dealer')->where('id','=',$getTran->dealerID)->first();
			//print_r($getTran);
			//exit; 
			?>
			
				<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
					<input type="hidden" name="productToken" value="<?php echo  base64_encode($getTran->product_id) ;?>"/>
					<?php 
					$sender="";
					
					 if($sessionData['role']=='admin'){
						$sender='admin';
					}else{
						if($sessionData['role']=='staff' && isset($sessionData['first_name']) && !empty($sessionData['first_name'])){
							$sender=$sessionData['first_name'].' (Staff)';
						}
					} 
					//echo $sender;
						?>
					<input type="hidden" name="sender" value="<?php echo $sender; ?>"/>
					<input type="hidden" name="dealerID" value="<?php echo $getTran->dealerID; ?>"/>
					<input type="hidden" name="opentab" value="pending"/>
					<input type="hidden" name="orderToken" value="<?php echo  base64_encode($getTran->orderID); ?>"/>
					<input type="hidden" name="orderTokenString" value="<?php echo $uniqueQtyNumber; ?>"/>
					
				</div> 
				<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
					<div class="form-group" style="width: 100%;">
					<label class="control-label">Notes:</label><br/>
					<textarea style="width:100%"  required name="description" class="form-control" placehoder="Notes"></textarea></div>
					<div class="form-group" style="width: 100%;">
					<label class="control-label">Send Mail:</label><br/>
					<input type="checkbox" name="sendMail" value="1"></div>
				</div>
				<div class="clearfix"></div>
				<hr/>
				<?php
				foreach($notes as $note){
					if(!empty($note->name) || !empty($note->description)){
						 $today = date('Y-m-d H:i:s');
							$a = new DateTime($today);
							$b = new DateTime($note->created_at);
							$difference_time = $a->diff($b);

							$time_text = '';
							if(($difference_time->format("%d") != 0)){
								$time_text.= $difference_time->format("%d").'days';
								$time_text.= ' ';

							}else if(($difference_time->format("%d") == 0) && ($difference_time->format("%h") != 0)){
							   $time_text.= $difference_time->format("%h").'h';
								$time_text.= ' ';
							}
							$time_text.= $difference_time->format("%i").'m ago';
							/* if($note->sender=='admin'){
								$sender='<small class="label label-info"> You</small>&nbsp;Admin';
							}else{
								$sender='<small class="label label-success"> Dealer</small>&nbsp;&nbsp;'.$delaerName->first_name.'&nbsp;'.$delaerName->last_name;
							} */
							$sender='<small class="label label-success">'.$note->sender.'</small>';
					   echo'<h3>'.$sender.'<small class="pull-right text-navy">'.$time_text.'</small></h3>';
					   echo '<p>'.$note->description.'</p>
					   <small class="text-muted">
							Time : '. date('H:i A',strtotime($note->created_at)) .' - '. date('Y-m-d',strtotime($note->created_at)).'
						</small>
					   <hr/>';
					   
					}else{
					   $notestitle='No Notes Available';
					   $datanotes ='<i class="fa fa-frown-o" aria-hidden="true" style="font-size:85px;color:#ccc;"></i>';
				   }
				}
			 
		}
		
	}
	public function adminupdateorder(Request $request){
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		 
		$post=$request->all();
		$orderID=base64_decode($post['orderToken']);
		
		if(isset($id ) && !empty( $id ) &&  isset($post) && !empty($post))
        {	
	
			//print_r($post);
			//echo base64_decode($post['orderTokenID']);
			if($post['orderStatus']=='finance declined'){
				$order = new Order();
				$variation = new Variation();
				$orderID=base64_decode($post['orderToken']);
				$order_transactionID=base64_decode($post['orderTokenID']);
				$product_id=base64_decode($post['product_name']);
				$variationID=$post['ProductOrderTokenIDVar'];
				$product_color=$post['product_color'];
				$batch=$post['productbatch'];
				$orderStatus=$post['orderStatus'];
				$productStatus=$post['productStatus'];
				
				$oldData=DB::table('order_transaction')->where('orderID','=',$orderID)->where('orderNoteTokenString','=', $order_transactionID)->first();
				$oldstck=$variation->where('variationID','=', $oldData->variationID)->first();
				$oldstockUpdateArray=array(
					'productStock'=>$oldstck->productStock + 1,
					'updated_at'=>Carbon::now()->toDateTimeString()
				);
				$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$oldstck->variationID)->orderBy('variationTranzToken','DESC')->first();
				
				if(!empty($getVariationTranz)){
					$lastTranzTockenArray=explode('_',$getVariationTranz->variationTranzToken);
				 
					$lastRecord=$lastTranzTockenArray[2];
					$lastRecord= $lastRecord + 1; 
					$variationTranzToken=$oldstck->variationToken .'_'.$oldstck->variationID.'_'.$lastRecord;
					$tranzUpdateArray=array(
						'variationID'=>$oldstck->variationID,
						'variationTranzToken'=>$variationTranzToken,
						'product_id'=>$oldstck->product_id,
						'product_status'=>$oldstck->product_status,
						'qty'=>1,
						'stockdate'=>$oldData->stockdate,
						'created_at'=>Carbon::now()->toDateTimeString()
					);
					$addVarTranz=DB::table('variation_tranz')->insert($tranzUpdateArray);
				}else{
					 
					$variationTranzToken=$oldstck->variationToken .'_'.$oldstck->variationID.'_1';
					$tranzUpdateArray=array(
						'variationID'=>$oldstck->variationID,
						'variationTranzToken'=>$variationTranzToken,
						'product_id'=>$oldstck->product_id,
						'product_status'=>$oldstck->product_status,
						'qty'=>1,
						'stockdate'=>$oldData->stockdate,
						'created_at'=>Carbon::now()->toDateTimeString()
					);
					$addVarTranz=DB::table('variation_tranz')->insert($tranzUpdateArray);
				}
				$updateOrderArray=array(
					'orderStatus'=>'Finance Declined',
					'updated_at'=>Carbon::now()->toDateTimeString()
				);
				$oldstockUpdat=$variation->where('variationID','=',$oldData->variationID)->update($oldstockUpdateArray);				
				Session::flash('operationSucess','Order Updated Successfully !');
				$updateorder=DB::table('order_transaction')->where('orderID','=',$orderID)->where('orderNoteTokenString','=',$order_transactionID)->update($updateOrderArray);
				return Redirect::to('admin/financeorder/');
			exit;
				 
				 
			}
			
			
			/********************************************************************************************************/
			if($post['orderStatus']=='invoiced'){
				if($post['productStatus']=='onseaukarrival' || $post['productStatus']=='inproduction' || $post['productStatus']=='factorystock'){
					
					if(isset($post['opentab'])){
						
						Session::flash('opentab',$post['opentab']);
					}
					 
						Session::flash('operationFaild','only in stock order items are allowed for invoice');  
					
					if(isset($post['ordertype']) && !empty($post['ordertype'])){
						
						return Redirect::to('admin/financeorder/');
					}else{
						
						if($post['orderStatus']=='invoiced'){
							return Redirect::to('admin/orderList/');
						}else{
							
							 return Redirect::to('admin/orderDetials/'.$post['pagedata']);
						  
						}
					}
				}
				 
			}
			/* if($post['productStatus']=='onseaukarrival' || $post['productStatus']=='factorystock' || $post['productStatus']=='inproduction' && $post['orderStatus']=='invoiced'){
				if(isset($post['opentab'])){
						
						Session::flash('opentab',$post['opentab']);
					}
					 
						Session::flash('operationFaild','only in stock order items are allowed for invoice');  
					
					if(isset($post['ordertype']) && !empty($post['ordertype'])){
						
						return Redirect::to('admin/financeorder/');
					}else{
						if($post['orderStatus']=='invoiced'){
							return Redirect::to('admin/orderList/');
						}else{
							
							 return Redirect::to('admin/orderDetials/'.$post['pagedata']);
						  
						}
					}
			} */else{
				 $order = new Order();
				 $variation = new Variation();
				 
				 $orderID=base64_decode($post['orderToken']);
				 $order_transactionID=base64_decode($post['orderTokenID']);
				 $product_id=base64_decode($post['product_name']);
				 $variationID=$post['ProductOrderTokenIDVar'];
				 $product_color=$post['product_color'];
				 $batch=$post['productbatch'];
				 $orderStatus=$post['orderStatus'];
				 $productStatus=$post['productStatus'];
					
					
				$oldData=DB::table('order_transaction')->where('orderID','=',$orderID)->where('orderNoteTokenString','=', $order_transactionID)->first();
				//print_r($oldData);exit;
				
				/*********************Order Accessory***********************/ 
				if(isset($post['accessory_name']) && !empty($post['accessory_name']) || !empty($post['accessory_nameeedit'])){
					
					/* for($i=0;$i<count($post['accessory_name']);$i++){
						 $post['accessory_name'][$i]=base64_decode($post['accessory_name'][$i]);
					}
					if(!empty($oldData->accessoryID)){
						//$accessoryIDs=$getOrderdata->accessoryID .','.base64_decode($post['accessory_name']);	
						$oldAccessoryArray=explode(',',$oldData->accessoryID);
						$mergeArray=array_merge( $post['accessory_name'],$oldAccessoryArray);
						$newAccessoryArray=implode(',',array_unique($mergeArray));
						//print_r($newAccessoryArray);exit;
						
					}else{
						$newAccessoryArray=implode(',',$post['accessory_name']);
					} */
					if(!empty($oldData->accessoryID)){
						$datacount=0;
							$newAccessoryArray=array();
						if(!empty($post['accessory_nameeedit']) && !empty($post['qtyedit'])){
							$datacount=count($post['accessory_nameeedit']);
							for($i=0;$i<$datacount;$i++){
								$newAccessoryArray[base64_decode($post['accessory_nameeedit'][$i])] = $post['qtyedit'][$i];
							}
							
						}
						if(!empty($post['accessory_name']) && !empty($post['qty'])){
							 if (array_key_exists(base64_decode($post['accessory_name']),$newAccessoryArray)){
									Session::flash('operationFaild','item already in order'); 
									if($post['orderStatus']=='invoiced'){
										return Redirect::to('admin/orderList/');
									}else{
										
									 return Redirect::to('admin/orderDetials/'.base64_encode($orderID.'&'.$order_transactionID));
									}
								 
								 exit;
							 }else{
								 
								$newAccessoryArray[base64_decode($post['accessory_name'])] = $post['qty'];
							 }
						}
						 $newAccessoryArray = json_encode($newAccessoryArray); 
					}else{
						//$newAccessoryArray=implode(',',$post['accessory_name']);
						$newAccessoryArray = array();
						$newAccessoryArray[base64_decode($post['accessory_name'])]=$post['qty'];
						 
						 $newAccessoryArray = json_encode($newAccessoryArray);
						 //echo $newAccessoryArray;
					}
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
					//print_r($UpdateorderArray); exit;
					$oldDataUpdate=DB::table('order_transaction')->where('orderID','=',$orderID)->where('orderNoteTokenString','=', $order_transactionID)->update($UpdateorderArray);
				}
				 
				/*********************end Order Accessory***********************/  
				 
				if(isset($post['delivery_date']) && !empty($post['delivery_date'])){
					$deliveryDate=date('Y-m-d',strtotime($post['delivery_date']));
					if(empty($oldData->orderStatus) && empty($oldData->delivery_date)){
						$orderStatus='booked in for delivery';
					}else{
						$orderStatus=$post['orderStatus'];
					}
				}else{
					$deliveryDate=NULL;
					$orderStatus=$post['orderStatus'];
				}
					 
					$oldstck=$variation->where('variationID','=', $oldData->variationID)->first();
				//print_r($oldstck);exit;
					 
					if(!empty($oldData) && $oldData->specialOrderID==0){
						if($oldData->product_id==$product_id && $oldData->variationID==$variationID && $oldData->product_color==$product_color && $oldData->batch==$batch && $oldData->qtystatus==$productStatus)
						{
					
							$updateOrderArray=array(
								'orderStatus'=>$orderStatus,
								'delivery_date'=>$deliveryDate,
								'updated_at'=>Carbon::now()->toDateTimeString()
							
							);
						
						}else{
						 
							$oldstockUpdateArray=array(
								'productStock'=>$oldstck->productStock + 1,
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							
							$orderProductUpdateArry=array(
								'product_id'=>$product_id,
								'variationID'=>$variationID,
								'batch'=>$post['productbatch'],
								'product_color'=>$post['product_color'],
								'qtystatus'=>$post['productStatus'],
								'orderStatus'=>$orderStatus,
								'delivery_date'=>$deliveryDate,
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							//print_r($orderProductUpdateArry);
							//exit;
						//	$orderProductUpdate=DB::table('product_order')->where('orderID','=',$orderID)->update($orderProductUpdateArry);
							
							$getVariationTranz=DB::table('variation_tranz')->where('variationID','=',$oldstck->variationID)->orderBy('variationTranzToken','DESC')->first();
						//	print_r($getVariationTranz);exit;	
							if(!empty($getVariationTranz)){
								$lastTranzTockenArray=explode('_',$getVariationTranz->variationTranzToken);
							 
								$lastRecord=$lastTranzTockenArray[2];
									$lastRecord= $lastRecord + 1; 
									$variationTranzToken=$oldstck->variationToken .'_'.$oldstck->variationID.'_'.$lastRecord;
									$tranzUpdateArray=array(
										'variationID'=>$oldstck->variationID,
										'variationTranzToken'=>$variationTranzToken,
										'product_id'=>$oldstck->product_id,
										'product_status'=>$oldstck->product_status,
										'qty'=>1,
										'stockdate'=>$oldData->stockdate,
										'created_at'=>Carbon::now()->toDateTimeString()
									);
							
									$addVarTranz=DB::table('variation_tranz')->insert($tranzUpdateArray);
							}else{
								 
									$variationTranzToken=$oldstck->variationToken .'_'.$oldstck->variationID.'_1';
									$tranzUpdateArray=array(
										'variationID'=>$oldstck->variationID,
										'variationTranzToken'=>$variationTranzToken,
										'product_id'=>$oldstck->product_id,
										'product_status'=>$oldstck->product_status,
										'qty'=>1,
										'stockdate'=>$oldData->stockdate,
										'created_at'=>Carbon::now()->toDateTimeString()
									);
									$addVarTranz=DB::table('variation_tranz')->insert($tranzUpdateArray);
							}
							
							$oldstockUpdat=$variation->where('variationID','=',$oldData->variationID)->update($oldstockUpdateArray);
							
							$newstokData=$variation->where('product_id','=',$product_id)->where('product_color','=',$product_color)->where('batch','=',$batch)->where('product_status','=',$productStatus)->first();
							
							
							//print_r($newstokData);echo'<br/>';
						
							if(!empty($newstokData->productStock) && $newstokData->productStock > 0){
								
								$newstokDataArray=array(
									'productStock'=>$newstokData->productStock - 1,
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
								//print_r($newstokDataArray);
								$newstokDataUpdate=$variation->where('variationID','=', $newstokData->variationID)->update($newstokDataArray);
								
								$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $newstokData->variationID)->orderBy('updated_at','DESC')->first();
									 
											//print_r($getDateTranz); exit;
										if(!empty($getDateTranz)){
											$DeleteTranz=DB::table('variation_tranz')->where('variation_tranzID','=',$getDateTranz->variation_tranzID)->delete();
										}else{
											$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $newstokData->variationID)->orderBy('	variationTranzToken','DESC')->first();
											//print_r($getDateTranzS); exit;
											if(!empty($getDateTranzS)){
												
												$DeleteTranz=DB::table('variation_tranz')->where('variation_tranzID','=',$getDateTranzS->variation_tranzID)->delete();
												
											}else{
												 
											}
										}							
								
							//print_r($newstokDataArray); exit;
								
								$updateOrderArray=array(
									'product_id'=>$newstokData->product_id,
									'variationID'=>$newstokData->variationID,
									'product_color'=>$newstokData->product_color,
									'delivery_date'=>$deliveryDate,
									'orderStatus'=>$orderStatus,
									'qtystatus'=>$newstokData->product_status,
									'batch'=>$newstokData->batch,
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
								if($productStatus=='inproduction'){
									$inProductionOrder=DB::table('inproduction_order')->where('orderID','=',$orderID)->where('product_color','=',$newstokData->product_color)->where('product_id','=',$product_id)->first();
									
									if(!empty($inProductionOrder)){
										$inProOrder=array(
											'product_color'=>$newstokData->product_color,
											'product_id'=>$product_id,
											'orderID'=>$orderID,
											'variationID' =>$newstokData->variationID,
											'orderqty'=>$inProductionOrder->orderqty + 1,
											'updated_at'=>Carbon::now()->toDateTimeString()
										);
										//print_r($inProOrder);
										
										$inProOrderadd=DB::table('inproduction_order')->where('product_color','=',$newstokData->product_color)->where('product_id','=',$product_id)->where('inproduction_orderID','=',$inProductionOrder->inproduction_orderID)->update($inProOrder);
										
									}else{
										$inProOrder=array(
											'product_color'=>$newstokData->product_color,
											'product_id'=>$product_id,
											'orderID'=>$orderID,
											'variationID' =>$newstokData->variationID,
											'orderqty'=> 1,
											'created_at'=>Carbon::now()->toDateTimeString()
										);
										//print_r($inProOrder);
										$inProOrderadd=DB::table('inproduction_order')->insertGetId($inProOrder);
										
										 
										
									}
									//exit;
									
									
								}else{
									
									$inProductionOrder=DB::table('inproduction_order')->where('orderID','=',$orderID)->first();
									if(!empty($inProductionOrder)){
										if($inProductionOrder->orderqty >= 1){
											$inProOrder=array(
												'orderqty'=>$inProductionOrder->orderqty - 1,
												'updated_at'=>Carbon::now()->toDateTimeString()
											);
											
											$inProOrderadd=DB::table('inproduction_order')->where('product_color','=',$newstokData->product_color)->where('product_id','=',$product_id)->where('inproduction_orderID','=',$inProductionOrder->inproduction_orderID)->update($inProOrder);
											
											if($inProOrder['orderqty'] ==0){
												
											$inProductionOrder=DB::table('inproduction_order')->where('orderID','=',$orderID)->where('product_color','=',$newstokData->product_color)->where('product_id','=',$product_id)->delete();
											
											}
										}else{
											
										 $inProductionOrder=DB::table('inproduction_order')->where('orderID','=',$orderID)->where('product_color','=',$newstokData->product_color)->where('product_id','=',$product_id)->delete();
										}
										
										 
									}
								}
								
								 
							}else{
								Session::flash('operationFaild','Not enough Stock');  
								if($post['orderStatus']=='invoiced'){
									return Redirect::to('admin/orderList/');
								}else{
									
								 return Redirect::to('admin/orderDetials/'.base64_encode($orderID.'&'.$order_transactionID));
								}
								  
								 exit;
							}
						
						}
					}else{
						
						$updateOrderArray=array(
							'product_id'=>$product_id,
							'variationID'=>0,
							'product_color'=>$product_color,
							'delivery_date'=>$deliveryDate,
							'orderStatus'=>$orderStatus,
							'qtystatus'=>$oldData->qtystatus,
							'batch'=>$batch,
							'updated_at'=>Carbon::now()->toDateTimeString()
						);
						
					}
					//echo $order_transactionID.'<br/>';
					//print_r($updateOrderArray);
					//exit;
					$orders=DB::table('order_transaction')->where('orderID','=',$orderID)->first();
					if($orderStatus == 'invoiced'){
						$orderData=DB::table('product_order')->where('orderID','=',$orderID)->first();
						
						$invoiceData=DB::table('order_invoice')->where('dealerID','=',$orders->dealerID)->orderBy('order_invoice_ID','desc')->first();
						
						$dealerData=DB::table('dealer')->where('id','=',$orders->dealerID)->first();
						//$orderNoteTokenString=implode(",",$post['orderTranzToken']);
						
						if(!empty($invoiceData)){
							
							$newOrderNumber=substr($invoiceData->invoiceNumber, -4);
							$orderNumber=$dealerData->invoicePrefix .sprintf("%04s", $newOrderNumber + 1);
							
							$invoceArry=array(
								'invoiceNumber'=>$orderNumber,
								'orderID'=>$orderID,
								'orderNoteTokenString'=>$order_transactionID,
								'qty'=>1,
								'invoice_status'=>'invoiced',
								'dealerID'=>$oldData->dealerID,
								'created_at'=>Carbon::now()->toDateTimeString()
							);
						}else{
							$orderNumber=$dealerData->invoicePrefix .sprintf("%04s", 1);
							
							//echo $orderNoteTokenString;
							$invoceArry=array(
								'invoiceNumber'=>$orderNumber,
								'orderID'=>$orderID,
								'orderNoteTokenString'=>$order_transactionID,
								'qty'=>1,
								'invoice_status'=>'invoiced',
								'dealerID'=>$oldData->dealerID,
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							
						}
						$addinvoice=DB::table('order_invoice')->insert($invoceArry);
					}
					
				$updateorder=DB::table('order_transaction')->where('orderID','=',$orderID)->where('orderNoteTokenString','=',$order_transactionID)->update($updateOrderArray);	
				
				$users=DB::table('dealer')->where('id','=',$oldData->dealerID)->first();
				//echo $users->first_name; exit;
				$emails=$users->emailID;
				$productData=DB::table('products')->where('product_id','=',$oldData->product_id)->first();
				
				$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
				$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
				$data_user_stocck_update =array(
					'productName' => $productData->productName,
					'color' =>  $oldData->product_color,
					'dealername' =>  $users->first_name,
					'categoryName' =>$categoryData->categoryName,
					'brandName' =>   $brandData->brandName,
					'batch' =>  $oldData->batch,
					'orderDate' =>  date('d-m-Y',strtotime($oldData->created_at)),
					'orderStatus' =>  $post['orderStatus'],
					'customer_name' =>$orders->customer_name ,
					'qtytype' => $post['productStatus'],
					'email' => $emails
					
				);
				if(isset($post['delivery_date']) && !empty($post['delivery_date'])){
					$data_user_stocck_update['deliveryDate'] =  date('d-m-Y',strtotime($post['delivery_date']));
				}
				if($orderStatus == 'invoiced'){
					$data_user_stocck_update['invoiceNumber'] =  $orderNumber;
				}
				if($post['productStatus'] == 'instock' || $post['orderStatus'] == 'invoiced'){
					
					Mail::send('email_templates.stockUpdate',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
					{
						$message->to($emails)->subject('Stock Update alert!');
					});
				}
				//exit;
				if(isset($post['opentab'])){
					
					Session::flash('opentab',$post['opentab']);
				}
				if($updateorder > 0 || $oldDataUpdate > 0){
					Session::flash('operationSucess','Order Updated Successfully !');
					 
				}else{
					Session::flash('operationFaild','Some thing Went wrong');  
				}
				if(isset($post['ordertype']) && !empty($post['ordertype'])){
					
					return Redirect::to('admin/financeorder/');
				}else{
					if($post['orderStatus']=='invoiced'){
						return Redirect::to('admin/orderList/');
					}else{
						
					 return Redirect::to('admin/orderDetials/'.base64_encode($orderID.'&'.$order_transactionID));
					}
				}
			}
        }else{
            return Redirect::to('/');
        }
	}	
	public function bookedForDeliveryOrder(Request $request){
		$sessionData=Session::get('adminLog');
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		$post=$request->all();
		if(isset($id ) && !empty( $id ))
        {
			//print_r($post);exit;
			if(isset($post) && !empty($post) && !empty($post['OrderToken'])){
				if(isset($post['delivery_date']) && !empty($post['delivery_date'])){
					$delivery_date=date('Y-m-d',strtotime($post['delivery_date']));
				}else{
					$delivery_date=NULL;
				}
				$rota=0;
				if(isset($post['rota']) && !empty($post['rota'])){
					$rota=1;
				}else{
					$rota=0;
				}
				for($i=0;$i< count($post['OrderToken']) ;$i++){
					//echo $post['OrderToken'][$i].'<br/>';
					$ordertransactionData=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['OrderToken'][$i])->first();
					if($ordertransactionData->orderStatus =='invoiced'){
						$updateArray=array(
							'orderStatus'=>'invoiced',
							'delivery_date'=>$delivery_date,
							'rota'=>$rota,
							'updated_at'=>Carbon::now()->toDateTimeString()
						);
						 
						 
						//exit;
					}else{
						
						$updateArray=array(
							'orderStatus'=>$post['orderStatus'],
							'delivery_date'=>$delivery_date,
							'rota'=>$rota,
							'updated_at'=>Carbon::now()->toDateTimeString()
						);
					}
					$DealerData=DB::table('dealer')->where('id','=',$ordertransactionData->dealerID)->first();
					if(!empty($DealerData)){
						$customerDetail=$DealerData->first_name .'&nbsp;'.$DealerData->last_name .',' .	$DealerData->phone;
					}
					$updatedata=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['OrderToken'][$i])->update($updateArray);
					/* if($rota == 1){
						
						$rotaData=array(
							'order_transactionID'=>$ordertransactionData->order_transactionID,
							'customerDetail'=>$customerDetail,
							'postcode'=>$ordertransactionData->address,
							'rotaStatus'=>'pending',
							'createdDate'=> $delivery_date,
							'created_at'=> Carbon::now()->toDateTimeString(),
						);  
						$updatedataRota=DB::table('rota')->insert($rotaData);
					} */
				}
				///print_r($ordertransactionData);exit;
				//if($updatedata > 0 || $updatedataRota > 0){
				if($updatedata > 0 ){
					Session::flash('operationSucess','Order Updated Successfully !');
					 
				}else{
					Session::flash('operationFaild','Some thing Went wrong');  
				}
			}else{
				Session::flash('operationFaild','Some thing Went wrong');  
			}
			return Redirect::to('admin/orderList/');
		}else{
            return Redirect::to('/');
        }
		
		
	}
	public function admincancelorder($orderId){
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
	 
		 
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
			return Redirect::to('admin/orderList');
        }else{
            return Redirect::to('/');
        }
	}
	public function adminorderinvoice($orderId){
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
	 
		 
		if(isset($id ) && !empty( $id ) &&  isset($orderId) && !empty($orderId))
        {	
			$order = new Order();
			$orderData=$order->where('orderID','=',base64_decode($orderId))->first();
			//print_r($cartData); 
			 return view('admin.order.orderInvoice')->with('orderData',$orderData);	 	 
			 
        }else{
            return Redirect::to('/');
        }
	}
	public function getAllInvoice(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		//print_r($post);// exit;
		if(isset($id ) && !empty( $id )){
			if(isset($post['dealer']) && !empty($post['dealer'])){
				$getTranzs=DB::table('order_transaction')
				->where('deleted_at','=',NULL)
				->where('dealerID','=',base64_decode($post['dealer']))
				->where('finance','=','0')
				->where('orderStatus','!=','complete')
				->where('orderStatus','!=','invoiced')
				->where('orderStatus','!=','paid')
				->get();
				
				//print_r($getorder);
				$i=0;
				$num=0;
				if(!empty($getTranzs)){
					foreach($getTranzs as $getTran){
						$i++;
						$num++;
						$uniqueQtyNumber = $getTran->orderNoteTokenString;
					 
				?>
					<tr>
						<td align="center">
							<?php
							 if($getTran->orderStatus=="invoiced" || $getTran->orderStatus=="paid" || $getTran->orderStatus=="complete"){
								?>
								<div class="i-checkss"><label>
	<input disabled  id="checkbox_<?php echo $getTran->orderNoteTokenString ?>" type="checkbox" class="selectorder disableclass" name="orderID[]" data-tranztoken="<?php echo $getTran->orderNoteTokenString; ?>" data-qtystatus="<?php echo $getTran->qtystatus; ?>" data-dealerToken="<?php echo $getTran->dealerID; ?>" value="<?php echo $getTran->orderID; ?>" onclick="incoicegen('checkbox_<?php echo $getTran->orderNoteTokenString ?>')" /> <i></i> </label></div>
								<?php								
								
								}else{
									?>
									<div class="i-checkss"><label>
	<input id="checkbox_<?php echo $getTran->orderNoteTokenString ?>" type="checkbox" class="selectorder disableclass" name="orderID[]" data-tranztoken="<?php echo $getTran->orderNoteTokenString; ?>" data-qtystatus="<?php echo $getTran->qtystatus; ?>" data-dealerToken="<?php echo $getTran->dealerID; ?>" value="<?php echo $getTran->orderID; ?>" onclick="incoicegen('checkbox_<?php echo $getTran->orderNoteTokenString ?>')" /> <i></i> </label></div>
									<?php
								}
							?>
						 
						 
							 
							 
						</td>
						<td>
							<?php 
							if(!empty($getTran->dealerID)){
								
								$delaername=DB::table('dealer')->where('id','=',$getTran->dealerID)->first();
								 $name='';
								if(!empty($delaername->company_name)){
									$name= $delaername->company_name ;
								}
								echo $name; 
							}
							?>
						</td>
						<td>
							<?php
								if(!empty($getTran->product_id)){
									$productName=DB::table('products')->where('product_id','=',$getTran->product_id)->where('deleted_at','=',NULL)->first();
									if(!empty($productName->productName)){
										echo $productName->	productName;
										if($getTran->specialOrderID > 0){
											echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
										}
									}
								}
							?>
						</td>
						
						<td>
							<?php 
								if(!empty($getTran->batch)){
									
									echo $getTran->batch;
									
								}else{
									echo '---';
								}
							
							?>
						</td>
						<td>
							<?php 
								 if(!empty($productName->category_id)){
									$category=DB::table('category')->where('id','=',$productName->category_id)->first();
									if(!empty($category->categoryName)){
										echo $category->categoryName;
									}else{
										echo '---';
									}
								}else{
									echo '---';
								}
							
							?>
						</td>
						<td>
							<?php
								if(!empty($getTran->variationID)){
									$color=DB::table('variation')->where('variationID','=',$getTran->variationID)->where('deleted_at','=',NULL)->first();
									if(!empty($color->product_color)){
										echo $color->	product_color;
									}
								}else{
											echo $getTran->product_color;
										}
							?>
						</td>
						<td>
							<?php
							//echo $order->product_id;
								 if(!empty($getTran->qtystatus)){
								//	echo $getTran->qtystatus; 
									if($getTran->qtystatus =='instock'){
										if(!empty($getTran->stockdate)){
											echo '<small class="label label-info"> In Stock ('.date('d-m-Y',strtotime($getTran->stockdate)).')</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
											
										}else{
											echo '<small class="label label-info"> In Stock</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
											
										}
									}else{
										if($getTran->mailstatus==0){
											if(!empty($getTran->stockdate)){
												if($getTran->qtystatus== 'onseaukarrival'){
													echo '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
												}else{
													echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
												}
												
											}else{
												if($getTran->specialOrderID > 0){
													$getSpeacialOrders=DB::table('special_order')->where('id','=',$getTran->specialOrderID)->first();
													echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
												}else{
													if($getTran->qtystatus== 'onseaukarrival'){
														echo '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
													}else{
														echo '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
													}
												}
												
											}
										}else{
											if(!empty($getTran->stockdate)){
												if($getTran->qtystatus== 'onseaukarrival'){
														echo '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
												}elseif($getTran->qtystatus== 'inproduction'){
														echo '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
												}else{
													
												echo '<small class="label label-info"> In Stock ('.date('d-m-Y',strtotime($getTran->stockdate)).')</small><a href="#" data-toggle="modal" data-target="#productiondate'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>';
												}
												
											}else{
												if($getTran->qtystatus== 'onseaukarrival'){
														echo '<small   style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
												}elseif($getTran->qtystatus== 'inproduction'){
														echo '<small  class="label label-success">In Production ('.date('d-m-Y',strtotime($getTran->stockdate)).') </small>';
												}else{
													
												echo '<small class="label label-info"> In Stock</small>';
												}
												
											}
										}
									}
								} 
								
							?>

						</td>
						<td>
							<?php if(!empty($getTran->created_at)){echo date('d-m-Y',strtotime($getTran->created_at));}?>
						</td>
						
						<td>
							<?php
								if(!empty($getTran->delivery_date)){
									
									echo date('d-m-Y',strtotime($getTran->delivery_date));
								}else{
									echo '---';
								}
							?>
						</td>
						
						<td>1</td>
						 
						<td>
							<?php
								if(!empty($getTran->orderStatus) && $getTran->orderStatus=="pending"){
									echo '<label class="label label-warning" style="text-transform:capitalize;">'.$getTran->orderStatus.'</label>';
								}else if(!empty($getTran->orderStatus) && $getTran->orderStatus=="booked in for delivery"){
								 
									echo '<label class="label label-warning" style="text-transform:capitalize;background-color:#F7609E;">booked in for delivery</label>';
								}else if(!empty($getTran->orderStatus) && $getTran->orderStatus=="invoiced"){
								 
									echo '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
								}else if(!empty($getTran->orderStatus) && $getTran->orderStatus=="paid"){
								 
									echo '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
								}else if(!empty($getTran->orderStatus) && $getTran->orderStatus=="collection"){
								 
									echo '<label class="label label-primary" style="text-transform:capitalize;"> Collection</label>';
								}else if(!empty($getTran->orderStatus) && $getTran->orderStatus=="complete"){
								 
									echo '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
								}else{
									echo '--';
								}
							?>
						</td>
						<td>
							<?php
							if(!empty($getTran->accessoryID)){
								echo '<i style="padding: 5px;    background: #18A689;color: #fff;    border-radius: 100%;" class="fa fa-check" aria-hidden="true"></i>';
							}else{
								echo '---';
							} 
					 
							?>
						</td>
						
						
						<td>
							<?php
							$orderNoteTokenString=$getTran->orderNoteTokenString;
							 
								if(!empty($getTran->customer_name)){
									echo $getTran->customer_name;
								}else{
									echo '<a href="#" data-toggle="modal" data-target="#Customername'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Add Name</a>';
								}
								$addcutomeraction=URL::to('/admin/addadmincustomername/');
							?>
						</td>
						
						
						<td>
							<?php 
								if(!empty($getTran->order_notes_descriptions)){
									echo $getTran->order_notes_descriptions;
								}else{
									echo '---';
								}
							?>
						</td>
						 
					</tr>
				<?php	 
						
					
					}
				}else{
					echo '<tr><td colspan="14">No Order Found</td></tr>';
				}
			}else{
					echo '<tr><td colspan="14">No Order Found</td></tr>';
				}
		} 
	}
	public function generateServiceInvoiceData(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){
			//print_r($post);exit;
			if(isset($post['invoiceNumber']) && !empty($post['invoiceNumber']) && isset($post['dealer']) && !empty($post['dealer'])){
				$invoiceSameData=DB::table('order_invoice')->where('invoiceNumber','=',$post['invoiceNumber'])->first();
				if(!empty($invoiceSameData)){ 
				 
					Session::flash('operationFaild','Duplicate invoice number ');
					return Redirect::to('admin/createserviceinvoice');
					exit;
				}else{
					$invoiceArray=array(
						'invoiceNumber'=>$post['invoiceNumber'],
						'invoiceTitle'=>$post['invoiceTitle'],
						'serviceInvoice'=>1,
						'dealerID'=>base64_decode($post['dealer']),
						'invoice_status'=>'invoiced',
						'created_at'=>Carbon::now()->toDateTimeString()
					);

					$dealerData=DB::table('dealer')->where('id','=',base64_decode($post['dealer']))->first();
					if(!empty(Input::file('invoicepdf'))){
						$today=date('Y-m-d-H-i-s');
						$destinationPath = 'uploads/invoicepdf'; // upload path
					 
						$file = array('image' => Input::file('invoicepdf'));
						
						$extension = Input::file('invoicepdf')->getClientOriginalExtension(); // getting image extension
						$fileName = $dealerData->first_name .'_'.$today.'_'.$post['invoiceNumber'].'_'.rand(11111,99999).'.'.$extension; // renameing image
					   
						//print_r($post);exit;
						Input::file('invoicepdf')->move($destinationPath, $fileName); // uploading file to given path
						// sending back with message
						$invoiceArray['invoicepdf']=$fileName;
						$pathToFile= $destinationPath.'/'.$fileName ;
							
						
					}
					$invoice_array=array();
					$invoice_array['orderStatus'] = 'invoiced';
						$invoice_array['invoiceNumber'] = $post['invoiceNumber'];
						$invoice_array['dealername'] = $dealerData->first_name;
					 /* return view('email_templates.stockUpdateInvoice')->with('main_mail',$main_mailarray)->with('invoice_ar',$invoice_array);								
						exit;  */
						
						$emails=$dealerData->emailID;
						
						Mail::send('email_templates.stockUpdateInvoice',['invoice_ar'=>$invoice_array], function($message)use ($emails,$pathToFile)
							{
								$message->to($emails)->subject('Stock Update alert!');
								if(!empty($pathToFile)){
									
									$message->attach($pathToFile);
								}
							});
					
					$addinvoice=DB::table('order_invoice')->insert($invoiceArray);
					if($addinvoice > 0){
						Session::flash('operationSucess','Invoice created successfully !');
					}else{
						Session::flash('operationFaild','Some thing went wrong ! ');
						
					}
					 
					return Redirect::to('admin/invoicelist');
					exit;
				}
			}else{
				Session::flash('operationFaild','Some thing went wrong ');
				return Redirect::to('admin/createserviceinvoice');
				exit;
			}
		}else{
			 return Redirect::to('/');
		}
	}
	public function generateServiceInvoiceDataUpdate(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){
			//print_r($post);exit;
			if(isset($post['invoiceNumber']) && !empty($post['invoiceNumber']) && isset($post['dealer']) && !empty($post['dealer'])){
				$invoiceData=DB::table('order_invoice')->where('order_invoice_ID','=',base64_decode($post['invoiceToken']))->first();
				$invoiceSameData=DB::table('order_invoice')->where('invoiceNumber','=',$post['invoiceNumber'])->where('order_invoice_ID','!=',base64_decode($post['invoiceToken']))->first();
				if(!empty($invoiceSameData)){ 
				 
					Session::flash('operationFaild','Duplicate invoice number ');
					return Redirect::to('admin/invoicelist');
					exit;
				}else{
					$invoiceArray=array(
						'invoiceNumber'=>$post['invoiceNumber'],
						'invoiceTitle'=>$post['invoiceTitle'],
						'serviceInvoice'=>1,
						'dealerID'=>base64_decode($post['dealer']),
						'invoice_status'=>'invoiced',
						'created_at'=>Carbon::now()->toDateTimeString()
					);
					$dealerData=DB::table('dealer')->where('id','=',base64_decode($post['dealer']))->first();
					if(!empty(Input::file('invoicepdf'))){
						$today=date('Y-m-d-H-i-s');
						$destinationPath = 'uploads/invoicepdf'; // upload path
					 	if(!empty($invoiceData->invoicepdf)){
							File::delete($destinationPath.'/'.$invoiceData->invoicepdf);
						} 
						$file = array('image' => Input::file('invoicepdf'));
						
						$extension = Input::file('invoicepdf')->getClientOriginalExtension(); // getting image extension
						$fileName = $dealerData->first_name .'_'.$today.'_'.$post['invoiceNumber'].'_'.rand(11111,99999).'.'.$extension; // renameing image
					   
						//print_r($post);exit;
						Input::file('invoicepdf')->move($destinationPath, $fileName); // uploading file to given path
						// sending back with message
						$invoiceArray['invoicepdf']=$fileName;
						$pathToFile= $destinationPath.'/'.$fileName ;
							
						
					}
					
					$addinvoice=DB::table('order_invoice')->where('order_invoice_ID','=',base64_decode($post['invoiceToken']))->update($invoiceArray);
					if($addinvoice > 0){
						Session::flash('operationSucess','Invoice Updated successfully !');
					}else{
						Session::flash('operationFaild','Some thing went wrong ! ');
						
					}
					 
					return Redirect::to('admin/invoicelist');
					exit;
				}
			}else{
				Session::flash('operationFaild','Some thing went wrong ');
				return Redirect::to('admin/createserviceinvoice');
				exit;
			}
		}else{
			 return Redirect::to('/');
		}
	}
	public function generateInvoice(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		// print_r($post);
		// exit; 
		 
		if(isset($id ) && !empty( $id )){
			if(isset($post['orderID']) && !empty($post['orderID']) && isset($post['invoiceNumber']) && !empty($post['invoiceNumber'])){
				$firstValue = current($post['dealerToken']);
				//echo $firstValue; exit;
				 $deaerID=array();
				 $orderids=array();
				 if (in_array("onseaukarrival", $post['qtystatus']) || in_array("inproduction", $post['qtystatus']))
				 {
					Session::flash('opentab',$post['opentab']);
					Session::flash('operationFaild','select only in stock item order ! ');
					return Redirect::to('admin/createinvoice');
					exit; 
				 }else{
					 
				 }
				$invoiceSameData=DB::table('order_invoice')->where('invoiceNumber','=',$post['invoiceNumber'])->first();
				if(!empty($invoiceSameData)){ 
				Session::flash('opentab',$post['opentab']);
					Session::flash('operationFaild','Duplicate invoice number ');
					return Redirect::to('admin/createinvoice');
					exit; }
				
				$post['orderStatus']='invoiced';
				if(isset($post['type']) && $post['type']="Generate Invoice" && isset($post['orderStatus']) && !empty($post['orderStatus']) ){
					
					// print_r($orderids);echo'<br/>';
					
					//print_r($post);exit;
					$orderIDString=implode(',',$orderids);
					 
						$orderData=DB::table('product_order')->where('orderID','=',$post['orderToken'][0])->first();
					//	print_r($orderData); exit;
						
						$invoiceData=DB::table('order_invoice')->where('dealerID','=',$post['dealerToken'][0])->orderBy('order_invoice_ID','desc')->first();
							$dealerData=DB::table('dealer')->where('id','=',$orderData->dealerID)->first();
							//print_r($dealerData); exit;
							$orderNoteTokenString=implode(",",$post['orderTranzToken']);
							
							//echo $orderNoteTokenString;
							$invoiceArray=array(
								'invoiceNumber'=>$post['invoiceNumber'],
								'orderID'=>$orderIDString,
								'orderNoteTokenString'=>$orderNoteTokenString,
								'qty'=>count($post['orderID']),
								'invoice_status'=>$post['orderStatus'],
								'dealerID'=>$firstValue,
								'created_at'=>Carbon::now()->toDateTimeString()
							);
							//print_r($invoiceArray);exit;
							$pathToFile='';
							if(!empty(Input::file('invoicepdf'))){
							$today=date('Y-m-d-H-i-s');
								$destinationPath = 'uploads/invoicepdf'; // upload path
							 
								$file = array('image' => Input::file('invoicepdf'));
								
								$extension = Input::file('invoicepdf')->getClientOriginalExtension(); // getting image extension
								$fileName = $dealerData->first_name .'_'.$today.'_'.$post['invoiceNumber'].'_'.rand(11111,99999).'.'.$extension; // renameing image
							   
								//print_r($post);exit;
								Input::file('invoicepdf')->move($destinationPath, $fileName); // uploading file to given path
								// sending back with message
								$invoiceArray['invoicepdf']=$fileName;
								$pathToFile= $destinationPath.'/'.$fileName ;
									
								
							}	
						
					//	print_r($invoiceArray); exit;
							$order_tanzArray=array(
								'orderStatus'=>$post['orderStatus'],
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							$main_mailarray=array();
							$child_mailarray=array();
							$main_invoice_array = array();
							$invoice_array = array();
							for($i=0;$i<count($post['orderTranzToken']);$i++)
							{
								$order_tranzUpadte=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderTranzToken'][$i])->update($order_tanzArray);
								/***********/
								$child_mailarray = array();
								$invoice_array = array();
								
								$orderdata=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderTranzToken'][$i])->first();
								//print_r($orderdata); exit;
								$productData=DB::table('products')->where('product_id','=',$orderdata->product_id)->first();
								
								$child_mailarray[$orderdata->order_transactionID]['productName']=$productData->productName;
								
								$child_mailarray[$orderdata->order_transactionID]['product_color']=$orderdata->product_color;
								
								$child_mailarray[$orderdata->order_transactionID]['batch']=$orderdata->batch;
								$child_mailarray[$orderdata->order_transactionID]['orderDate']=date('d-m-Y',strtotime($orderdata->created_at));
								
								$child_mailarray[$orderdata->order_transactionID]['delivery_date']=date('d-m-Y',strtotime($orderdata->delivery_date));

								
								$child_mailarray[$orderdata->order_transactionID]['orderStatus']=$post['orderStatus'];
								$child_mailarray[$orderdata->order_transactionID]['invoiceNumber']=$post['invoiceNumber'];
								
								array_push($main_mailarray,$child_mailarray);				
							}
							
							$invoice_array['orderStatus'] = $post['orderStatus'];
							$invoice_array['invoiceNumber'] = $post['invoiceNumber'];
							$invoice_array['dealername'] = $dealerData->first_name;
						 /* return view('email_templates.stockUpdateInvoice')->with('main_mail',$main_mailarray)->with('invoice_ar',$invoice_array);								
							exit;  */
							
							$emails=$dealerData->emailID;
							
							Mail::send('email_templates.stockUpdateInvoice',['main_mail'=>$main_mailarray,'invoice_ar'=>$invoice_array], function($message)use ($emails,$pathToFile)
								{
									$message->to($emails)->subject('Stock Update alert!');
									if(!empty($pathToFile)){
										
										$message->attach($pathToFile);
									}
								});
								//print_r($invoiceArray);
								//exit;
							$addinvoice=DB::table('order_invoice')->insert($invoiceArray);
							
							if($addinvoice > 0){
								Session::flash('operationSucess','Invoice created successfully !');
							}else{
								Session::flash('operationFaild','Some thing went wrong ! ');
							}
						//print_r($invoiceArray);
						
				//	exit;
				}else{
					$orders=count($post['dealerToken']);
					if($orders > 2){
						Session::flash('operationFaild','Select Only two order at a time ! ');
						return Redirect::to('admin/invoicelist');
						exit;
					}else{
						
					}
				}
			}else{
				
				Session::flash('operationFaild','Please select order ! ');
			}
		}else{
			
			Session::flash('operationFaild','Some thing went wrong ! ');
			 return Redirect::to('/');
		}
		 
		return Redirect::to('admin/invoicelist')->with('sessionData',$sessionData);
		
		
	}
	public function paidInvoice(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		
	 	//print_r($post); exit; 
	
		if(isset($id ) && !empty( $id )){
			if(isset($post) && !empty($post)){
			 
				if(isset($post['invoiceToken']) && !empty($post['invoiceToken']) && $post['orderStatus']=='paid' || $post['orderStatus']=='complete' || $post['orderStatus']=='invoiced'){
					for($i=0;$i< count($post['invoiceToken']); $i++){
						//echo $post['invoiceToken'][$i].'<br/>';
						$getInvoiceData=DB::table('order_invoice')->where('order_invoice_ID','=',base64_decode($post['invoiceToken'][$i]))->first();
						if(!empty($getInvoiceData)){
							$ordertranz=explode(",",$getInvoiceData->orderNoteTokenString);
							for($j=0;$j< count($ordertranz);$j++){
								$orderTranzArray=array(
									'orderStatus'=>$post['orderStatus'],
									'updated_at'=>Carbon::now()->toDateTimeString()
								);
							$updateOrderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$ordertranz[$j])->update($orderTranzArray);
							 
							}
							$invoiceArray=array(
								'invoice_status'=>$post['orderStatus'],
								'updated_at'=>Carbon::now()->toDateTimeString()
							);
							//print_r($invoiceArray);
 
							$updateInvoice=DB::table('order_invoice')->where('order_invoice_ID','=',base64_decode($post['invoiceToken'][$i]))->update($invoiceArray);
						}
					}
					 if($updateInvoice > 0 || $updateOrderTranz > 0){
						Session::flash('operationSucess','Invoice Updated successfully !');
					}else{
						Session::flash('operationFaild','Some thing went wrong ! ');
					} 
				}else{
					Session::flash('operationFaild','Select at list one invoice ! ');
				}
				
			}else{
				Session::flash('operationFaild','Some thing went wrong ! ');
			}
		}else{
			
			Session::flash('operationFaild','Some thing went wrong ! ');
		 return Redirect::to('/');
		}
		//exit;
		Session::flash('opentab',$post['opentab']);
		return Redirect::to('admin/invoicelist')->with('sessionData',$sessionData);
		
	}
	public function emailtmp(){
		$main_mailarray=array();
		$child_mailarray=array();
						
	   $i='_1';
		
		$child_mailarray['15']['productName']='demo'.$i;
		
		$child_mailarray['15']['product_color']='red'.$i;
		
		$child_mailarray['15']['batch']='886692'.$i;
		$child_mailarray['15']['orderDate']='20-11-2016'.$i;
		
		$child_mailarray['15']['delivery_date']='30-12-2016'.$i;

		 $i='_2';
		
		$child_mailarray['16']['orderStatus']='invoiced'.$i;
		$child_mailarray['16']['invoiceNumber']='NP0005010'.$i;
		
		$child_mailarray['16']['productName']='demo'.$i;
		
		$child_mailarray['16']['product_color']='red'.$i;
		
		$child_mailarray['16']['batch']='886692'.$i;
		$child_mailarray['16']['orderDate']='20-11-2016'.$i;
		
		$child_mailarray['16']['delivery_date']='30-12-2016'.$i;

		 
		
		$child_mailarray['16']['orderStatus']='invoiced'.$i;
		$child_mailarray['16']['invoiceNumber']='NP0005010'.$i;
		
		array_push($main_mailarray,$child_mailarray);
	   			
		return view('email_templates.stockUpdateInvoice')->with('main_mail',$main_mailarray);	 	 
	}
	
	public function adminOrderInvoiceEdit($invoceid){
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){
				
			return view('admin.order.orderInvoiceEdit')->with('invoceid',$invoceid);	 	 
			 
        }else{
            return Redirect::to('/');
        }
	}
	
	public function adminOrderInvoiceUpdate(Request $request){
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){
			$post=$request->all();
		
			
			$invoiceID=base64_decode($post['invoiceToken']);
			 
			$invoicedata=DB::table('order_invoice')->where('order_invoice_ID','=',$invoiceID)->first();
			
			$invoiceArray=array(
				 
				'invoice_status'=>$post['orderStatus'],
				'updated_at'=>Carbon::now()->toDateTimeString()
			);
			
			//print_r($invoiceArray);exit;
			
			$ordertranz=explode(",",$invoicedata->orderNoteTokenString)	;
			$orderTranzData=DB::table('order_transaction')->where('orderNoteTokenString','=',$ordertranz[0])->first();
			$dealerData=DB::table('dealer')->where('id','=',$orderTranzData->dealerID)->first();
			$today=date('d-m-Y');
				//print_r($dealerData);exit;
				
			 if(!empty(Input::file('invoicepdf'))){
				 
				$destinationPath = 'uploads/invoicepdf'; // upload path
				if(!empty($invoicedata->invoicepdf)){
					File::delete($destinationPath.'/'.$invoicedata->invoicepdf);
				} 
				$file = array('image' => Input::file('invoicepdf'));
				
				$extension = Input::file('invoicepdf')->getClientOriginalExtension(); // getting image extension
				$fileName = $dealerData->first_name .'_'.$today.'_'.$invoicedata->invoiceNumber.'_'.rand(11111,99999).'.'.$extension; // renameing image
			   
				//print_r($post);exit;
				Input::file('invoicepdf')->move($destinationPath, $fileName); // uploading file to given path
				// sending back with message
				$invoiceArray['invoicepdf']=$fileName;
						   
					
				
			}
			$updateinvoice=DB::table('order_invoice')->where('order_invoice_ID','=',$invoiceID)->update($invoiceArray);
			
			$main_mailarray=array();
			$child_mailarray=array();
			$main_invoice_array = array();
			$invoice_array = array();
			
			//print_r($ordertranz);
			for($i=0;$i<count($ordertranz);$i++){
				$tranz=array(
					 
					'orderStatus'=>$post['orderStatus'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				);
				
				$updateTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$ordertranz[$i])->update($tranz);
				
				/***********/
				$child_mailarray = array();
				$invoice_array = array();
				
				$orderdata=DB::table('order_transaction')->where('orderNoteTokenString','=',$ordertranz[$i])->where('deleted_at','=',NULL)->first();
				
				 
				$productData=DB::table('products')->where('product_id','=',$orderdata->product_id)->first();
				
				$child_mailarray[$orderdata->order_transactionID]['productName']=$productData->productName;
				
				$child_mailarray[$orderdata->order_transactionID]['product_color']=$orderdata->product_color;
				
				$child_mailarray[$orderdata->order_transactionID]['batch']=$orderdata->batch;
				$child_mailarray[$orderdata->order_transactionID]['orderDate']=date('d-m-Y',strtotime($orderdata->created_at));
				
				$child_mailarray[$orderdata->order_transactionID]['delivery_date']=date('d-m-Y',strtotime($orderdata->delivery_date));

				 
				
				array_push($main_mailarray,$child_mailarray);	
				
				
			}
			$updateinvoiceData=DB::table('order_invoice')->where('order_invoice_ID','=',$invoiceID)->first();
			$invoice_array['orderStatus'] = $post['orderStatus'];
			$invoice_array['invoiceNumber'] = $updateinvoiceData->invoiceNumber;
			$invoice_array['dealername'] = $dealerData->first_name;
			
			$emails=$dealerData->emailID;
				
				
			Mail::send('email_templates.stockUpdateInvoice',['main_mail'=>$main_mailarray,'invoice_ar'=>$invoice_array], function($message)use ($emails)
				{
					$message->to($emails)->subject('Stock Update alert!');
				});
			
			
			if($updateinvoice > 0){
				Session::flash('operationSucess','Invoice created successfully !');
			}else{
				Session::flash('operationFaild','Some thing went wrong ! ');
			}
			Session::flash('opentab',$post['opentab']);
			return Redirect::to('admin/invoicelist');
		}else{
            return Redirect::to('/');
        }
	}
	public function deleteaminorder(Request $request){
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
	public function deleteAminInvoice(Request $request){
		$post=$request->all();
		$getInvoiceData=DB::table('order_invoice')->where('order_invoice_ID','=',$post['invoice'])->first();
		$destinationPath = 'uploads/invoicepdf'; // upload path
		if(!empty($getInvoiceData->invoicepdf)){
			File::delete($destinationPath.'/'.$getInvoiceData->invoicepdf);
		} 
		if($getInvoiceData->invoice_status='invoiced'){
			$invoiceStatus='booked in for delivery';
		}elseif($getInvoiceData->invoice_status='paid'){
			$invoiceStatus='invoiced';
			$orderinvArray=array(
				'invoice_status'=>'invoiced',
				'updated_at'=>Carbon::now()->toDateTimeString()
			);
			$InvoiceDataUpdate=DB::table('order_invoice')->where('order_invoice_ID','=',$post['invoice'])->update($orderinvArray);
		}else{
			$invoiceStatus='invoiced';
		}
		$orderTranzArray=array(
			'orderStatus'=>$invoiceStatus,
			'updated_at'=>Carbon::now()->toDateTimeString()
		);
		//print_r($orderTranzArray); exit;
		$NeworderNoteTokenString=explode(',',$getInvoiceData->orderNoteTokenString);
		for($i=0;$i<count($NeworderNoteTokenString);$i++){
			
			$updateOrderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$NeworderNoteTokenString[$i])->update($orderTranzArray);
			
		}
		if($getInvoiceData->invoice_status='invoiced'){
			$getInvoiceData=DB::table('order_invoice')->where('order_invoice_ID','=',$post['invoice'])->delete();
		}
	}
	public function deleteAminPaid(Request $request){
		$post=$request->all();
		//print_r($post);  
		$getInvoiceData=DB::table('order_invoice')->where('order_invoice_ID','=',$post['invoice'])->first();
		$destinationPath = 'uploads/invoicepdf'; // upload path
		if(!empty($getInvoiceData->invoicepdf)){
			File::delete($destinationPath.'/'.$getInvoiceData->invoicepdf);
		} 
		$orderTranzArray=array(
			'orderStatus'=>'invoiced',
			'updated_at'=>Carbon::now()->toDateTimeString()
		);
		 
		$updateOrderTranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$getInvoiceData->orderNoteTokenString)->update($orderTranzArray);
		
		
		$orderinvArray=array(
			'invoice_status'=>'invoiced',
			'updated_at'=>Carbon::now()->toDateTimeString()
		);
		
		 
		$InvoiceDataUpdate=DB::table('order_invoice')->where('order_invoice_ID','=',$post['invoice'])->update($orderinvArray);
	}
	public function addOrderNotes(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		//print_r($post);exit;
		//if(isset($id ) && !empty( $id )){
		//	print_r($post); exit;
			if(isset($post) && !empty($post)){
				 
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
				
				if(isset($post['sendMail']) && $post['sendMail']==1){
					
					$getOrderData=DB::table('product_order')->where('orderID','=',base64_decode($post['orderToken']))->first();
					
					$getOrderTranzData=DB::table('order_transaction')->where('orderID','=',base64_decode($post['orderToken']))->where('orderNoteTokenString','=',$post['orderTokenString'])->first();
					
					$users=DB::table('dealer')->where('id','=',$getOrderData->dealerID)->first();
					$productData=DB::table('products')->where('product_id','=',base64_decode($post['productToken']))->first();
					 
					$email=$users->emailID;
					$data_user_stocck_update =array(
						'productName' => $productData->productName,
						'email' => $email,
						'color' => $getOrderTranzData->product_color,
						'batch' => $getOrderTranzData->batch,
						'notes' => $post['description'],
                        'loginUrl' => URL::to('/'),
					);
					//print_r($data_user_stocck_update); exit;
					//exit;
					Mail::send('email_templates.orderNotes',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use($email)
					{
						$message->to($email)->subject('Order Notification !');
					});
				}
				if($addnotes > 0){
					Session::flash('operationSucess','Order Notes added Successfully !');
				}else{
					Session::flash('operationFaild','Some thing went wrong ! ');
				}
				
			}else{
				Session::flash('operationFaild','Please fill all details ! ');
			}
			Session::flash('opentab',$post['opentab']);
			
		 
			if(isset($post['ordertype']) && $post['ordertype']=='finance'){
				return Redirect::to('admin/financeorder')->with('sessionData',$sessionData);
			}elseif(isset($post['ordertype']) && $post['ordertype']=='special'){
				return Redirect::to('admin/specialorderslist')->with('sessionData',$sessionData);
			}elseif(isset($post['ordertype']) && $post['ordertype']=='staffdashboard'){
				return Redirect::to('staff/dashboard')->with('sessionData',$sessionData);
			}elseif(isset($post['ordertype']) && $post['ordertype']=='admindashboard'){
				return Redirect::to('admin/dashboard')->with('sessionData',$sessionData);
			}elseif(isset($post['ordertype']) && $post['ordertype']=='dealerdashboard'){
				return Redirect::to('dealer/dashboard')->with('sessionData',$sessionData);
			}else{
				
				return Redirect::to('admin/orderList')->with('sessionData',$sessionData);
			}
			 
			
			 
			 
      /*   }else{
           return Redirect::to('/');
        } */
		 
	}
	public function inproductionorder(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){
			$post=$request->all();
			//print_r($post); 
			 
			
			
			$productData=DB::table('products')->where('product_id','=',base64_decode($post['productToken']))->first();
			$oldinproductionorder=DB::table('inproduction_order')->where('orderID','=',base64_decode($post['orderToken']))->first();
			
			$OrderTransactionOld=DB::table('order_transaction')->where('order_transactionID','=',base64_decode($post['orderToken']))->first();
			//print_r($oldinproductionorder); exit;
			$userorder=DB::table('product_order')->where('orderID','=',base64_decode($post['orderToken']))->first();
			 
			$users=DB::table('dealer')->where('id','=',$userorder->dealerID)->first();
			$emails=$users->emailID;
			$data_user_stocck_update =array(
				'productName' => $productData->productName,
				'color' =>  $userorder->product_color	,
				'email' => $emails,
				'notes' =>$post['mailnotes'],
                'loginUrl' => URL::to('/'),
			);
			//exit;
			Mail::send('email_templates.stockUpdate',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
			{
				$message->to($emails)->subject('Stock Update alert!');
			});
			$maildstatus=array(
				'mailstatus'=>1,
				'qtystatus'=>'instock',
				'updated_at'=>Carbon::now()->toDateTimeString()
			);
			$updateinproductionorderqty=0;
			if($OrderTransactionOld->specialOrderID == 0){
				if($OrderTransactionOld->specialOrderID==0 && !empty($oldinproductionorder)){
					$inproductionorder=array(
						'orderqty'=>$oldinproductionorder->orderqty - $userorder->qty 
					);
					//print_r($oldinproductionorder->inProductionOrder);
					 if($inproductionorder['orderqty'] > 0){}else{$inproductionorder['orderqty']=0;}
					$updateinproductionorderqty=DB::table('inproduction_order')->where('orderID','=',base64_decode($post['orderToken']))->update($inproductionorder);
				}
			
			}
			$updststus=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderTokenString'])->update($maildstatus);
			if($updateinproductionorderqty >  0 || $updststus > 0){
				Session::flash('operationSucess','Notification sent Successfully !');
			}else{
				Session::flash('operationFaild','Some thing Went wrong!');
			}
			 return Redirect::to('/admin/orderList');
		}else{
           return Redirect::to('/');
        }
        
	}
	public function getProductsColorEditOrder(Request $request){
		$post=$request->all();
		//print_r($post); exit;
		if(isset($post['product_name']) && !empty($post['product_name'])){
			 $variation = new Variation;
			 $product_id=base64_decode($post['product_name']);
			$items = $variation->where('product_id','=', $product_id)->where('productStock','>','0')->where('deleted_at','=',NULL)->groupBy('product_color')->get(); 
		 
			//echo '<option value="">Select color</option>';
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
			if(!empty($items)){	
				 foreach($items as $item){
					if(!empty($item->product_color)){
						
						$getinstoks=DB::table('variation')->where('product_id','=',$product_id)->where('product_color','=',$item->product_color)->where('product_status','=','instock')->where('productStock','>','0')->get();
						
						foreach($getinstoks as $getinstok){
							$inStock = + $getinstok->productStock;
						}
							 
							
						$getinProductions=DB::table('variation')->where('product_id','=',$product_id)->where('product_color','=',$item->product_color)->where('product_status','=','inproduction')->where('productStock','>','0')->get();
						
						foreach($getinProductions as $getinProduction){
							$inProduction = + $getinProduction->productStock;
						}		
							
						$countOrder=0;
						$orderinsts=DB::table('inproduction_order')->where('product_id','=',$product_id)->where('product_color','=',$item->product_color)->get();
						
						foreach($orderinsts as $inPrdOrder){
							$countOrder = $countOrder + $inPrdOrder->orderqty;
						}
					
						if($post['selected_option']==$item->product_color){$selected='selected=selected';}else{$selected='';}
							
						//echo '<option data-totlstk="'.$item->productStock.'" data-variation="'.$item->variationID.'" data-instk="'.$inStock.'" data-inprdorder="'.$countOrder.'" '.$selected.' data-inprd="'.$inProduction.'" value="'.$item->product_color.'">'.$item->product_color.'</option>';
							
						/* for($i=0;$i<$color;$i++){
							if($variationColor[$i]==$item->product_color){
								if($variationColor[$i]=='none'){
									echo '<option  value="none">None</option>';
								}else{
									$thumb="assets/img/".$variationColorThumb[$i].".jpg";
									echo'<option  value="'.$variationColor[$i].'" data-thumbnail="'.URL::to($thumb).'">'.$variationColor[$i].'</option>';
								}
							}
						}*/
					} 
				} 
						$colorArray=array();
						foreach($items as $item){
							 
							array_push($colorArray,$item->product_color);
						}
						 
						if (in_array($post['selected_option'], $colorArray)){
							for($i=0;$i<count($colorArray);$i++){
								if($post['selected_option']==$colorArray[$i]){$selected='selected=selected';}else{$selected='';}
								echo '<option '.$selected.' value="'.$colorArray[$i].'">'.$colorArray[$i].'</option>';
							}
						}else{
							echo '<option selected=selected value="'.$post['selected_option'].'">'.$post['selected_option'].'</option>';
							for($i=0;$i<count($colorArray);$i++){
								 
								echo '<option value="'.$colorArray[$i].'">'.$colorArray[$i].'</option>';
							}
						}
						
			}else{
				echo '<option value="">No Data Found</option>';
			}
		}
	}
	public function getProductsStatusEditOrder(Request $request){
		$post=$request->all();
		if(isset($post['product_name']) && !empty($post['product_name'])){
			//print_r($post);
			$productID=base64_decode($post['product_name']);
			$color=$post['datacolor'];
			
			$getSataus=DB::table('variation')->where('product_id','=',$productID)->where('product_color','=',$color)->where('productStock','>','0')->where('deleted_at','=',NULL)->where('product_status','!=','outofstock')->where('deleted_at','=',NULL)->groupBy('product_status')->get();
			// print_r($getSataus);
			if(!empty($getSataus)){
				foreach($getSataus as $sataus){
					if($post['seletedstatus']==$sataus->product_status){$selected='selected=selected';}else{$selected='';}
					echo '<option '.$selected.' value="'.$sataus->product_status.'">'.$sataus->product_status.'</option>';
				}
			}else{
				//echo '<option value="">No Data Found</option>';
				echo '<option  selected="selected" value="'.$post['seletedstatus'].'">'.$post['seletedstatus'].'</option>';
			}
		}
	}
	public function getProductsBatchEditOrder(Request $request){
		$post=$request->all();
		if(isset($post['product_name']) && !empty($post['product_name'])){
			//print_r($post); exit;
			$productID=base64_decode($post['product_name']);
			$color=$post['datacolor'];
			$status=$post['datastatus'];
			
			$getBatch=DB::table('variation')->where('product_id','=',$productID)->where('product_status','=',$status)->where('product_color','=',$color)->where('productStock','>','0')->groupBy('batch')->where('deleted_at','=',NULL)->get();
			//print_r($getBatch);
			if(!empty($getBatch)){
			 
				foreach($getBatch as $batch){
					if($post['selectedbatch']==$batch->batch){$selected='selected=selected';}else{$selected='';}
					echo '<option '.$selected.' value="'.$batch->batch.'">'.$batch->batch.'</option>';
				} 
			}else{
				echo '<option selected="selected"  value="'.$post['selectedbatch'].'">'.$post['selectedbatch'].'</option>';
			}
		}
	}
	public function financeOrder(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.order.financeOrder')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
	} 
	public function financeOrderDetail($id){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$orderidarray= explode('&', base64_decode($id));
			 
			return view('admin.order.financeOrderDetail')->with('sessionData',$sessionData)->with('orderID',$orderidarray);
		}else{
			 return Redirect::to('/');
		}
    }
	public function addAdminCustomerName(Request $request){
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		 
		 
		$post=$request->all();
	//	print_r($post); exit;
		if(isset($id ) && !empty( $id ) &&  isset($post) && !empty($post))
        {	
			$updatCustomernameArray=array(
				'customer_name'=>$post['order_notes_title'],	
				'order_notes_descriptions'=>$post['order_notes_descriptions']
			);
			
			$updatCustomername=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['orderTokenString'])->update($updatCustomernameArray);
			if($updatCustomername > 0){
				Session::flash('operationSucess','Customer name Added Successfully !');
			}
			Session::flash('opentab',$post['opentab']);  
			if(isset($post['otdertypepage']) && $post['otdertypepage']=='finance'){
			 return Redirect::to('admin/financeorder');
			}else{
				 return Redirect::to('admin/orderList');
				
			}
		}else{
			return Redirect::to('/');
		}
	}
	public function swapOrder(Request $request){
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		$post=$request->all();
		//print_r($post); //exit;
		if(isset($id ) && !empty( $id )) {
			if( isset($post['OrderToken']) && !empty($post['OrderToken'])){			
				//print_r($post);exit;
				$orderCount = count($post['OrderToken']);
				if($orderCount > 2 || $orderCount < 2){
					if(isset($post['opentab'])){
							Session::flash('opentab',$post['opentab']);  
						}
					Session::flash('operationFaild','Please select only two order at a time');
					return Redirect::to('admin/orderList');
					exit;
				}else{
					$firstOrderData=DB::table('order_transaction')->where("orderNoteTokenString",'=',$post['OrderToken'][0])->first();
					$secondOrderData=DB::table('order_transaction')->where("orderNoteTokenString",'=',$post['OrderToken'][1])->first();
					if(!empty($firstOrderData->stockdate)){
						$fiststockdate=date('Y-m-d',strtotime($firstOrderData->stockdate));
					}else{
						$fiststockdate=NULL;
					}
					if(!empty($secondOrderData->stockdate)){
						$secondstockdate=date('Y-m-d',strtotime($secondOrderData->stockdate));
					}else{
						$secondstockdate=NULL;
					}
					if($firstOrderData->specialOrderID==0 && $secondOrderData->specialOrderID==0){
						
						if($post['prdtoken'][0] == $post['prdtoken'][1] && $post['colortoken'][0] == $post['colortoken'][1]){
							
							if($firstOrderData->qtystatus == $secondOrderData->qtystatus){
								
								$firstOrderDataUpdateArray=array(
									'batch'=>$secondOrderData->batch,
									'delivery_date'=>$secondOrderData->delivery_date,
									'stockdate'=>$secondstockdate,
									'updated_at'=>Carbon::now()->toDateTimeString(),
								);
								
								$secondOrderDataUpdateArray=array(
									'batch'=>$firstOrderData->batch,
									'delivery_date'=>$firstOrderData->delivery_date,
									'stockdate'=>$fiststockdate,
									'updated_at'=>Carbon::now()->toDateTimeString(),
								);
							}else{
								$firstOrderDataUpdateArray=array(
									'batch'=>$secondOrderData->batch,
									'delivery_date'=>$secondOrderData->delivery_date,
									'qtystatus'=>$secondOrderData->qtystatus,
									'stockdate'=>$secondstockdate,
									'updated_at'=>Carbon::now()->toDateTimeString(),
								);
								
								$secondOrderDataUpdateArray=array(
									'batch'=>$firstOrderData->batch,
									'qtystatus'=>$firstOrderData->qtystatus,
									'delivery_date'=>$firstOrderData->delivery_date,
									'stockdate'=>$fiststockdate,
									'updated_at'=>Carbon::now()->toDateTimeString(),
								);
							}
							//echo '<br/>';
							/* print_r($firstOrderDataUpdateArray);echo '<br/>';
							print_r($secondOrderDataUpdateArray);
							exit; */ 
							
							$firstOrderDataUpdate=DB::table('order_transaction')->where("orderNoteTokenString",'=',$post['OrderToken'][0])->update($firstOrderDataUpdateArray);
							
							//$firstOrderTblUpdate=DB::table('product_order')->where("orderID",'=',$firstOrderData->orderID)->update($firstOrderTblArray);
							
							$secondOrderDataUpdate=DB::table('order_transaction')->where("orderNoteTokenString",'=',$post['OrderToken'][1])->update($secondOrderDataUpdateArray);
							
							//$secondOrderTblUpdate=DB::table('product_order')->where("orderID",'=',$secondOrderData->orderID)->update($secondOrderTblArray);
							
							if($firstOrderDataUpdate >0 && $secondOrderDataUpdate > 0){
								
								Session::flash('operationSucess','Order Swapped Successfully !');
							}else{
								Session::flash('operationFaild','Some thing went wrong');
							}
							if(isset($post['opentab'])){
								Session::flash('opentab',$post['opentab']);  
							}
								
							return Redirect::to('admin/orderList');
						
						}else{
							if(isset($post['opentab'])){
								Session::flash('opentab',$post['opentab']);  
							}
							Session::flash('operationFaild','Please select same product or same color order at a time');
							return Redirect::to('admin/orderList');
							exit;
						}
					}else{
						Session::flash('operationFaild','special order cannot be swap!');
					}
				
				}
				if(isset($post['opentab'])){
							Session::flash('opentab',$post['opentab']);  
						}
				return Redirect::to('admin/orderList');
				
				
			}else{
				if(isset($post['opentab'])){
					Session::flash('opentab',$post['opentab']);  
				}
				Session::flash('operationFaild','Some thing went wrong');
				return Redirect::to('admin/orderList');
			}
				
			 
		}else{
			return Redirect::to('/');
		}
	}
	public function inStockOrderDate(Request $request){
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
	 
		$post=$request->all();
		//print_r($post); exit;
		if(isset($id ) && !empty( $id ))
        {	
			if(isset($post['stockdate']) && !empty($post['stockdate'])){
				$upddateArray=array(
					'stockdate'=> date('Y-m-d',strtotime($post['stockdate'])),
					'updated_at'=>Carbon::now()->toDateTimeString(),
				);
				$updatedata=DB::table('order_transaction')->where('orderNoteTokenString','=',$post['inStkToken'])->update($upddateArray);
				
				if($updatedata > 0){
					Session::flash('operationSucess','Date saved Successfully !');
				}else{
					Session::flash('operationFaild','Some thing went wrong');
				}
			} 
			return Redirect::to('admin/orderList');
        }else{
            return Redirect::to('/');
        }
	}
	public function completedOrders(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			
			return view('admin.order.completedOrders')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('admin/orderList');
		}
    }
	public function orderDeliveryRotaEdit(){
		
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			
			return view('admin.order.deliveryRotaEdit')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	
	public function orderDeliveryVehicle(){
		 
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			
			return view('admin.order.orderDeliveryVehicle')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
	public function deliveryRotaUpdate(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			$post=$request->all();
			//print_r($post);exit;
			if($post['driver']==$post['codriver']){
				Session::flash('operationFaild','Driver And Co-Drive Must be different');
				return Redirect::to('admin/orderdeliveryrotaedit/'.$post['rotatoken']);
			}else{
				 $updatearray=array(
					'driver'=>base64_decode($post['driver']),
					'codriver'=>base64_decode($post['codriver']),
					'model'=>$post['model'],
					'dropof'=>$post['dropof'],
					'comment'=>$post['additionalcomments'],
				); 
				$updateData=DB::table('rota')->where('rotaID',base64_decode($post['rotatoken']))->update($updatearray);
				if($updateData > 0){
					Session::flash('operationSucess','Date saved Successfully !');
				}else{
					Session::flash('operationFaild','Some thing went wrong');
				}
			}
			 return Redirect::to('admin/orderdeliveryrotalist/');
		}else{
			 return Redirect::to('/');
		}
    }
	public function deliveryVehicleData(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			$post=$request->all();
			//print_r($post);exit;
			if(!empty($post)){
				$array=array(
					'regNo'=>$post['regNo'],
					'loadCapacity'=>$post['loadCapacity'],
					'comments'=>$post['comments'],
					'created_at'=>Carbon::now()->toDateTimeString(),
				);
				$detailsData=DB::table('vehicle')->insert($array);
				if($detailsData > 0){
					Session::flash('operationSucess','Date saved Successfully !');
				}else{
					Session::flash('operationFaild','Some thing went wrong');
				}
				return Redirect::to('admin/orderdeliveryvehicle/');
			}else{
				Session::flash('operationFaild','Some Thing Went Wrong');
				  
			}
			 return Redirect::to('admin/orderdeliveryvehicle/');
		}else{
			 return Redirect::to('/');
		}
    }
	public function deliveryVehicleEditData($id){
		$id=$id;
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			
			return view('admin.order.deliveryVehicleEditData')->with('sessionData',$sessionData)->with('id',$id);
		}else{
			 return Redirect::to('/');
		}
    }
	public function deliveryVehicleDataSave(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			$post=$request->all();
			//print_r($post);exit;
			$updateArray=array(
				'regNo'=>$post['regNo'],
				'loadCapacity'=>$post['loadCapacity'],
				'comments'=>$post['comments'],
				'updated_at'=>Carbon::now()->toDateTimeString(),
			);
			$detailsData=DB::table('vehicle')->where('vehicleID','=',base64_decode($post['vhtoken']))->update($updateArray);			
			Session::flash('operationSucess','Date saved Successfully !');
			  return Redirect::to('/admin/orderdeliveryvehicle');
		}else{
			 return Redirect::to('/');  
		}
    }
	public function deleteVehicle(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])  || !empty($sessionData['staff_ID'])){
			$post=$request->all();
			//print_r($post);exit;
			$updateArray=array(
				'deleted_at'=>Carbon::now()->toDateTimeString(),
			);
			$detailsData=DB::table('vehicle')->where('vehicleID','=',$post['vehicle'])->update($updateArray);			
			 
		}else{
			  
		}
    }
	public function editableDataPostSave(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			$post=$request->all();
			//print_r($post);exit;
			if(isset($post['day']) && !empty($post['day'])){
				$session_date = array(
                    'day' => $post['day']
                    
                );
					\Session::set('listdate' , $session_date);
                    Session::save();
			}
			if(isset($post['status']) && !empty($post['status'])){
				
				$updateArray=array(
					'rotaStatus'=>$post['status'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				); 
				$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
				print_r($detailsData);
			}elseif(isset($post['driver']) && !empty($post['driver'])){
				
				$rotaData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->first();	
				
				$getSamedriver=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->where('codriver','=',base64_decode($post['driver']))->first();	
				
				if(!empty($getSamedriver)){
					Session::flash('operationFaild','Driver and Co-Drive Cannot be Same');
					echo '0';
				}else{
					
					$updateArray=array(
						'driver'=>base64_decode($post['driver']),
						'rotaStatus'=>'assign',
						'updated_at'=>Carbon::now()->toDateTimeString()
					); 
					
					$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);
					
					$empData=DB::table('employee')->where('id','=',base64_decode($post['driver']))->first();
					
					$getOrderTranz=DB::table('order_transaction')->where('order_transactionID','=',$rotaData->order_transactionID)->first();
					
					$dealertData=DB::table('dealer')->where('id','=',$getOrderTranz->dealerID)->first();
					
					$getProdudtData=DB::table('products')->where('product_id','=',$getOrderTranz->product_id)->first();
					
					$emails=$empData->emailID;
					$data_user_stocck_update =array(
						'assing' =>'Driver',
						'productName' =>$getProdudtData->productName ,
						'customerDestails' =>$rotaData->customerDetail ,
						'companyName' =>$dealertData->company_name ,
						'address' =>$rotaData->postcode,
					);
					//exit;
					Mail::send('email_templates.driverAssingMail',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
					{
						$message->to($emails)->subject('Delivery Alert !');
					});
					print_r($detailsData);
				}
			}elseif(isset($post['codriver']) && !empty($post['codriver'])){
				$rotaData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->first();
				
				$getSamedriver=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->where('driver','=',base64_decode($post['codriver']))->first();
				
				if(!empty($getSamedriver)){
					Session::flash('operationFaild','Driver and Co-Drive Cannot be Same');
					echo '0';
				}else{
					
					$updateArray=array(
						'codriver'=>base64_decode($post['codriver']),
						'updated_at'=>Carbon::now()->toDateTimeString()
					); 
					$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
					$empData=DB::table('employee')->where('id','=',base64_decode($post['codriver']))->first();
					
					$getOrderTranz=DB::table('order_transaction')->where('order_transactionID','=',$rotaData->order_transactionID)->first();
					
					$dealertData=DB::table('dealer')->where('id','=',$getOrderTranz->dealerID)->first();
					
					$getProdudtData=DB::table('products')->where('product_id','=',$getOrderTranz->product_id)->first();
					
					$emails=$empData->emailID;
					$data_user_stocck_update =array(
						'assing' =>'Co driver',
						'productName' =>$getProdudtData->productName ,
						'customerDestails' =>$rotaData->customerDetail ,
						'companyName' =>$dealertData->company_name ,
						'address' =>$rotaData->postcode,
					);
					//exit;
					Mail::send('email_templates.driverAssingMail',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
					{
						$message->to($emails)->subject('Delivery Alert !');
					});
					print_r($detailsData);
				}
			}elseif(isset($post['customerDetail']) && !empty($post['customerDetail'])){
				 
				$updateArray=array(
					'customerDetail'=>$post['customerDetail'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				); 
				$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
				print_r($detailsData);
				 
			}elseif(isset($post['postcode']) && !empty($post['postcode'])){
				 
				$updateArray=array(
					'postcode'=>$post['postcode'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				); 
				$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
				print_r($detailsData);
				 
			}elseif(isset($post['model']) && !empty($post['model'])){
				 
				$updateArray=array(
					'model'=>$post['model'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				); 
				$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
				print_r($detailsData);
				 
			}elseif(isset($post['dropof']) && !empty($post['dropof'])){
				 
				$updateArray=array(
					'dropof'=>$post['dropof'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				); 
				$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
				print_r($detailsData);
				 
			}elseif(isset($post['comment']) && !empty($post['comment'])){
				 
				$updateArray=array(
					'comment'=>$post['comment'],
					'updated_at'=>Carbon::now()->toDateTimeString()
				); 
				$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);	
				print_r($detailsData);
				 
			}elseif(isset($post['vehicle']) && !empty($post['vehicle'])){
				//	print_r($post['vehicle']);
				 if($post['vehicle']=='none') {
					$updateArray=array(
						'vehicleID'=>'',
						'updated_at'=>Carbon::now()->toDateTimeString()
					); 
					$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);
					print_r($detailsData);
				}else{
					$getvehiclaData=DB::table('vehicle')->where('vehicleID','=',base64_decode($post['vehicle']))->first();
					if(!empty($getvehiclaData)){
						$countassing=DB::table('rota')->where('vehicleID','=',base64_decode($post['vehicle']))->count();
						if($countassing < $getvehiclaData->loadCapacity){
							$updateArray=array(
								'vehicleID'=>base64_decode($post['vehicle']),
								'updated_at'=>Carbon::now()->toDateTimeString()
							); 
							$detailsData=DB::table('rota')->where('rotaID','=',base64_decode($post['rtoken']))->update($updateArray);
							print_r($detailsData);
						}else{
							Session::flash('operationFaild','This Vehicle have enough item');
						echo '0';
						}
						
					}else{
						Session::flash('operationFaild','something went wrong');
						echo '0';
					}
				} 
			}else{}
			
//print_r($detailsData);			
		}else{
			  
		}
    }
	public function editableDataPostGetData(Request $request){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			$post=$request->all();
			if(isset($post['todayDate']) && !empty($post['todayDate'])){
				//$date= date('Y-m-d',strtotime($post['todayDate']));
				 $i=5;
				 $getdate =date('Y-m-d',strtotime($post['todayDate']));
				$togetdate =date('Y-m-d', strtotime(date("Y-m-d", strtotime($getdate)) . " +".$i."days"));
				//echo $date ;exit;
			}else{
				$i=5;
				$getdate =date('Y-m-d');
				$togetdate =date('Y-m-d', strtotime(date("Y-m-d", strtotime($date)) . " +".$i."days"));
			}
			//print_r($date);exit;
			$dateWiseRota=DB::table('rota')->whereBetween('createdDate', [$getdate, $togetdate])->where('deleted_at','=',NULL)->groupBy('createdDate')->get();
			//print_r($dateWiseRota);
			foreach($dateWiseRota as $rotadate){
				$getRotaData=DB::table('rota')->where('deleted_at','=',NULL)->where('createdDate','=',$rotadate->createdDate)->get();
				echo'<tr><th colspan="14" style="text-align:center;background: #e7eaec;font-size: 20px;">'.date('d-m-Y',strtotime($rotadate->createdDate)).'</th></tr>';
				$n=0;
				foreach($getRotaData as $rota){
					$n++;
					$orderTranzData=DB::table('order_transaction')
					->where('order_transactionID','=',$rota->order_transactionID)
					->first();
						$satus='statid'.$n;
					if(!empty($rota->driver)){
						$employeeData=DB::table('employee')->where('id','=',$rota->driver)->first(); 
						$driver=$employeeData->first_name .'&nbsp;'.$employeeData->last_name  ;
					}else{
						$driver='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					if(!empty($rota->codriver)){
						$employeeData=DB::table('employee')->where('id','=',$rota->codriver)->first(); 
						$codriver=$employeeData->first_name .'&nbsp;'.$employeeData->last_name  ;
						 
					}else{
						$codriver='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					$productData=DB::table('products')
					->where('product_id','=',$orderTranzData->product_id)
					->first();
					if(!empty($productData->productName)){
						$productName=$productData->productName ;
					}else{
						$productName='--';
					}
					$delaerData=DB::table('dealer')->where('id','=',$orderTranzData->dealerID)->first();
					if(!empty($delaerData->company_name)){
						$company_name=$delaerData->company_name ;
					}else{
						$company_name='--';
					}
					
					if(!empty($orderTranzData->accessoryID)){
						$acessorydata = json_encode($orderTranzData->accessoryID,true);
						$datas = json_decode($acessorydata,true);
						$acessorydata = json_decode($datas,true);
						$number=0;
						$htmlData='';
						$htmlDataIcon='';
						$htmlData .='
							<table  border="1" style="width:100%">
								<tr>
									<th style="font-size: 11px;">Accessory</th>
									<th style="font-size: 11px;">Qty</th>
								</tr>
								
							 
						';
							foreach($acessorydata as $k=>$v){
							//echo $acessory['accessory_qty'];
							$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$k)->first();
							$htmlData.='<tr>
									<td style="font-size: 11px;">'.$acessoryName->accessory_name .'</td>
									<td style="font-size: 11px;">'.$v.'</td>
								</tr>';
							}
							$htmlData .='</table>';
							$htmlDataIcon= '<div class="accessoryData"><i style="padding: 5px;    background: #18A689;color: #fff;   border-radius: 100%;" class="fa fa-check" aria-hidden="true"  data-toggle="tooltip" data-placement="bottom" title=" "></i>
							<div class="popover fade bottom in" role="tooltip" id="popover264416" style=""><div class="arrow"></div><h3 class="popover-title" style="font-weight: bold;text-align: center;">'.$productData->productName .'</h3><div class="popover-content"> '.$htmlData.'</div></div>
							</div>';
							
						}else{
							$htmlDataIcon= '---';
						} 
						$url=URL::to('admin/orderdeliveryrotaedit', base64_encode($rota->rotaID));
						if($rota->rotaStatus=='pending'){
							$status='<label class="label label-warning" style="text-transform:capitalize;">pending</label>';
						}elseif($rota->rotaStatus=='assign'){
							$status='<label class="label label-primary" style="text-transform:capitalize;">assign</label>';
						}else{
							$status='<label class="label label-success" style="text-transform:capitalize;">complete</label>';
						}
						$url=URL::to('admin/editabledatapost');
					if(!empty($rota->customerDetail)){
						$customerDetail=$rota->customerDetail ;
					}else{
						$customerDetail='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					if(!empty($rota->vehicleID)){
						//$vehicleID=$rota->vehicleID ;
						$vehicledataGet=DB::table('vehicle')->where('vehicleID','=',$rota->vehicleID)->first(); 
						$vehicledata=$vehicledataGet->regNo ;
					}else{
						$vehicledata='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					if(!empty($rota->postcode)){
						$address=$rota->postcode ;
					}else{
						$address='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					if(!empty($rota->model)){
						$model=$rota->model ;
					}else{
						$model='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					if(!empty($rota->dropof)){
						$dropof=$rota->dropof ;
					}else{
						$dropof='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					if(!empty($rota->comment)){
						$comment=$rota->comment ;
					}else{
						$comment='<label class="btn btn-xs btn-default"><i class="fa fa-plus"></i></label>';
					}
					if(!empty($orderTranzData->orderStatus)){
						$orderStatus=''; 	
						if(!empty($orderTranzData->orderStatus) && $orderTranzData->orderStatus=="pending"){
							$orderStatus= '<label class="label label-warning" style="text-transform:capitalize;">'.$orderTranzData->orderStatus.'</label>';
						}else if(!empty($orderTranzData->orderStatus) && $orderTranzData->orderStatus=="booked in for delivery"){
						 
							$orderStatus= '<label class="label label-warning" style="text-transform:capitalize;background-color:#F7609E;">booked in for delivery</label>';
						}else if(!empty($orderTranzData->orderStatus) && $orderTranzData->orderStatus=="invoiced"){
						 
							$orderStatus= '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
						}else if(!empty($orderTranzData->orderStatus) && $orderTranzData->orderStatus=="paid"){
						 
							$orderStatus= '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
						}else if(!empty($orderTranzData->orderStatus) && $orderTranzData->orderStatus=="collection"){
						 
							$orderStatus= '<label class="label label-primary" style="text-transform:capitalize;"> Collection</label>';
						}else if(!empty($orderTranzData->orderStatus) && $orderTranzData->orderStatus=="complete"){
						 
							$orderStatus= '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
						}else{
							$orderStatus='---';
						}
			 
					}else{
						$orderStatus= '---';
					}
				 echo'
					<tr>
						<td>'.$company_name .'</td>
						<td>'.$productName.'</td>
						<td>'.$orderTranzData->batch .'</td>
						<td>'.$orderStatus.'</td>
						<td id="'.$satus.'"> 
							'.$status.'
						</td>
						<td>
							<a href="#" class="rotadriver" data-type="select" data-stat="'.$satus.'" data-pk="pending" data-day="'.$getdate.'"  data-rtoken="'.base64_encode($rota->rotaID).'"  data-title="Select Driver">'.$driver.'</a>
						</td>
						<td>
							<a href="#" class="rotacodriver" data-type="select" data-pk="pending" data-day="'.$getdate.'"  data-rtoken="'.base64_encode($rota->rotaID).'"  data-title="Select Driver">'.$codriver.'</a>
						</td>
						<td>
							<a href="#" class="rotavehicle" data-type="select" data-pk="pending" data-day="'.$getdate.'"  data-rtoken="'.base64_encode($rota->rotaID).'"  data-title="Select Vehicle">'.$vehicledata.'</a>
						</td>
						
						<td><a href="#" class="customerDetail" data-rtoken="'.base64_encode($rota->rotaID).'" data-day="'.$getdate.'"  data-type="textarea" data-pk="1">'.$customerDetail .'</a></td>
						
						<td><a href="#" class="postcode" data-rtoken="'.base64_encode($rota->rotaID).'"  data-day="'.$getdate.'"  data-type="textarea" data-pk="1">'.$address .'</a></td>
						
						<td><a href="#" class="model" data-rtoken="'.base64_encode($rota->rotaID).'"  data-day="'.$getdate.'"  data-type="textarea" data-pk="1">'.$model .'</a></td>
						 
						<td>'.$htmlDataIcon .'</td>
						
						<td><a href="#" class="dropof" data-rtoken="'.base64_encode($rota->rotaID).'"  data-day="'.$getdate.'" data-type="textarea" data-pk="1">'.$dropof .'</a></td>
					
					<td><a href="#" class="comment" data-rtoken="'.base64_encode($rota->rotaID).'" data-day="'.$getdate.'"  data-type="textarea" data-pk="1">'.$comment .'</a></td>
					 
						 
					</tr>';
				}
			}
		}
	}
	public function deliveryRotaMap(){
		 
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID']) || !empty($sessionData['staff_ID'])){
			
			return view('admin.order.deliveryRotaMap')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
	}
	
	public function mapGeoGoogleDataGet()
	{
		 
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])  || !empty($sessionData['staff_ID']))
		{
			$get=Input::all();
			//print_r($get);
			 if(isset($get['todayDate']) && !empty($get['todayDate']))
			 {
				//$date= date('Y-m-d',strtotime($get['todayDate']));
				// $i=5;
				 $getdate =date('Y-m-d',strtotime($get['todayDate']));
				 
				//echo $getdate ;exit;
			}else{
				$i=5;
				$getdate =date('Y-m-d');
				 
			}
			
			
			$mapdata = array();
			$n = 0;
			 
				$getRotaData=DB::table('rota')->where('deleted_at','=',NULL)->where('createdDate','=',$getdate)->get();
				// print_r($getRotaData);
				foreach($getRotaData as $rota)
				{
					//echo $rota->postcode;
				
					$address=$rota->postcode;
					//echo $address; 
					 if(!empty($address))
					 {
						//Formatted address
						$formattedAddr = str_replace(' ','+',$address);
						//Send request and receive json data by address
						$geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=false'); 
						$output = json_decode($geocodeFromAddr);
						//print_r($output);
						//Get latitude and longitute from json data
						$data['latitude']  = $output->results[0]->geometry->location->lat; 
						$data['longitude'] = $output->results[0]->geometry->location->lng;
						//Return latitude and longitude of the given address
						if(!empty($data)){
							//print_r( $data);
							//$encode=json_encode($data);
							//print_r($encode);
							$mapdata[$n]=$data;
							$n++;
						}else{
							print_r( 'nopoo');
						}
					}else{
						print_r(  'nopoo');
					} 
				}
			  
			$encode = json_encode($mapdata);
			print_r($encode);
			//return view('admin.order.deliveryRotaMap')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
	}
	public function pastPaidOrderAutoComplete(){
		$getInvoice=DB::table('order_invoice')->where('invoice_status','=','paid')->where('deleted_at','=',NULL)->get();
		//print_r($getInvoice);
		foreach($getInvoice as $invoice){
			$getTranzToken=explode(",",$invoice->orderNoteTokenString);
			for($i=0;$i<count($getTranzToken);$i++){
				$today=date('Y-m-d');
				$getOrderTransaction=DB::table('order_transaction')->where('delivery_date','<',$today)->where('orderNoteTokenString','=',$getTranzToken[$i])->where('deleted_at','=',NULL)->first();
				if(!empty($getOrderTransaction)){
					//print_r($getOrderTransaction);
					$updatetransactionArray=array(
						'orderStatus'=>'complete',
						'updated_at'=>Carbon::now()->toDateTimeString()
					);
					$updateinvoiceArray=array(
						'invoice_status'=>'complete',
						'updated_at'=>Carbon::now()->toDateTimeString()
					);
					/*  print_r($updatetransactionArray);
					echo '<br/>';
					//print_r($updateinvoiceArray);  */
					 $updatetranz=DB::table('order_transaction')->where('orderNoteTokenString','=',$getTranzToken[$i])->update($updatetransactionArray);

					$updateinvoice=DB::table('order_invoice')->where('order_invoice_ID','=',$invoice->order_invoice_ID)->update($updateinvoiceArray); 
				}
			}
		}
	} 
}
