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
use App\Dealer;
use XeroLaravel;
use Cart;
use Datatables;
use URL;
use Variation;
use App\SpecialOrder;
 

class ManageSpecialOrderController extends Controller
{

    public function index()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            return View::make('admin/specialorder/specialorderList');
        }else{
            return View::make('/');
        }
    }

    public function  dataSpecialOrder()
    {
        $specialOrder = new SpecialOrder;

        $data = SpecialOrder::select('*')->orderBy('id','asc');

        $no = 0;
        return Datatables::of($data, $no)
		
			->addColumn('Select', function ($data) {
                if($data->id != '')
                {
					$html = '';
					$html = "<div class=\"i-checkss\"><label> <input type=\"checkbox\" class=\"selectorder_$data->id\" name=\"orderQty[]\"  value=\"$data->id\"> </label></div>";
                    return $html;
                }else{
                    return '---';
                }
            })

            ->addColumn('Company Name', function ($data) {
                if($data->company_name != '')
                {
                    return $data->company_name;
                }else{
                    return '---';
                }
            })

            ->addColumn('Customer Name', function ($data) {
                if($data->dealerID != '')
                {
                    $result_data = DB::table('dealer')->where('id','=',$data->dealerID)->first();
                    if(!empty($result_data)){
                        return $result_data->first_name.' '.$result_data->last_name;
                    }else{
                        return '---';
                    }
                }else{
                    return '---';
                }
            })

            ->addColumn('Customer EmailID', function ($data) {
                if($data->dealerID != '')
                {
                    $result_data = DB::table('dealer')->where('id','=',$data->dealerID)->first();
                    if(!empty($result_data)){
                        return $result_data->emailID;
                    }else{
                        return '---';
                    }
                }else{
                    return '---';
                }
            })

            ->addColumn('Product', function ($data) {
                if($data->product_id != '')
                {
                    $result_data = DB::table('products')->where('product_id','=',$data->product_id)->first();
                    if(!empty($result_data)){
                        return $result_data->productName;
                    }else{
                        return '---';
                    }
                }else{
                    return '---';
                }
            })


             ->addColumn('Status', function ($data) {
                if($data->orderstatus != ''){
					if($data->orderstatus == 'pending'){
						return '<label class="label label-warning" style="text-transform:capitalize;">Pending</label>';
						
					}else if($data->orderstatus=='cancelled') {
						return '<label class="label label-danger" style="text-transform:capitalize;">Cancelled</label>';
						
					}else if($data->orderstatus=='complete'){
						return '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
						
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })
			
			->addColumn('Color', function ($data) {
                if($data->product_color != '')
                {
					return $data->product_color;
                     
                }else{
                    return '---';
                }
            })

            ->addColumn('Date', function ($data) {
                if($data->today_date != ''){
                    return $data->today_date;
                }else{
                    return '---';
                }
            })
			
			 ->addColumn('Action', function ($data) {
                $html = '';
                $deleted = $data->id;
				$uniqueQtyNumber = $data->special_orderID.'_'.$data->id;
				$notes=DB::table('admin_specialorder_notes')->where('orderTokenString','=',$uniqueQtyNumber)->where('sporderID','=',$data->id)->where('product_id','=',$data->product_id)->first();
				$result_data = DB::table('products')->where('product_id','=',$data->product_id)->first();
				 $action=URL::to('/admin/addspecialordernotes/');
				//echo $uniqueQtyNumber.'<br/>';
				 if(!empty($notes))
				 {
					$dataold='<p><strong>Name :</strong>'.$notes->name.'</p>
					<p><strong>Description :</strong>'.$notes->description.'</p>';
				 }else{
					 $dataold='';
				 }
                $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('$deleted')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash-o\"></i></a>";
			$deatailurl = URL::to('admin/specialorderdetail', base64_encode($deleted));
                $html.= '<a href="'.$deatailurl.'"  data-toggle="tooltip" title="View order" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>';
				 
					$html.= '<a href="javascript:void(0)"  data-toggle="modal" data-target="#notes'.$data->id.'"  title="Edit" class="btn btn-xs btn-default"><i class="fa fa-file-text-o"></i></a>

					<div class="modal inmodal" id="notes'.$data->id.'" tabindex="-1" role="dialog"  aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content animated fadeIn">

								<form action="'. $action.'" method="POST" enctype="multipart/form-data" class="products" id="">
									<input type="hidden" name="_token" value="'.csrf_token().'"/>
									<input type="hidden" name="productToken" value="'.base64_encode($data->product_id).'"/>
									<input type="hidden" name="orderToken" value="'.base64_encode($data->id).'"/>
									<input type="hidden" name="orderTokenString" value="'.$uniqueQtyNumber.'"/>
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<h4 class="modal-title">'.$result_data->productName.'</h4>
									</div>
									<div class="modal-body col-md-12">
										<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
											<p>'.$dataold.'</p><hr/>
											<div class="form-group" style="width: 100%;">
											<label class="control-label">Name:</label><br/>
											<input type="text" style="width:100%" required name="name" class="form-control" placeholder="Name" ></div><br/><br/>
										</div>
										<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
											<div class="form-group" style="width: 100%;">
											<label class="control-label">Notes:</label><br/>
											<textarea style="width:100%"  required name="description" class="form-control" placehoder="Notes"></textarea></div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
										<input type="submit" class="btn btn-primary" value="Save changes" />
									</div>
								</form>
							</div>
						</div>
					</div>';
                return $html;
            })
            ->make(true);
            
    }
	public function specialOrderDelete(Request $request){
		$post=$request->all();
		//print_r($post);
		$deletespecilorder=DB::table('special_order')->where('id','=',$post['special_orderID'])->delete();
	}
	public function addSpecialOrdernotes(Request $request){
		$post=$request->all();
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){

			if(isset($post) && !empty($post)){
				 
				//print_r($post);exit;
				$chek=DB::table('admin_specialorder_notes')->where('sporderID','=',base64_decode($post['orderToken']))->where('product_id','=',base64_decode($post['productToken']))->where('orderTokenString','=',$post['orderTokenString'])->first();
				if(!empty($chek)){
					$notesdata=array(
						'product_id'=>base64_decode($post['productToken']),
						'sporderID'=>base64_decode($post['orderToken']),
						'name'=>$post['name'],
						'description'=>$post['description'],
						'updated_at'=>Carbon::now()->toDateTimeString(),
					);
					$addnotes=DB::table('admin_specialorder_notes')->where('sporderID','=',base64_decode($post['orderToken']))->where('product_id','=',base64_decode($post['productToken']))->where('orderTokenString','=',$post['orderTokenString'])->update($notesdata);
				}else{
					$notesdata=array(
						'product_id'=>base64_decode($post['productToken']),
						'sporderID'=>base64_decode($post['orderToken']),
						'orderTokenString'=>$post['orderTokenString'],
						'name'=>$post['name'],
						'description'=>$post['description'],
						'created_at'=>Carbon::now()->toDateTimeString(),
					);
					$addnotes=DB::table('admin_specialorder_notes')->insert($notesdata);
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
			return Redirect::to('admin/specialorderslist')->with('sessionData',$sessionData);
			 
        }else{
           return Redirect::to('/');
        }
		
	}
	public function specialOrderDetail($orderID){
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){
			 return view('admin/specialorder/specialorderDetail')->with('orderID',$orderID);
		}else{
           return Redirect::to('/');
        }
	}
	public function getProductsColorEditSpeacilOrder(Request $request){
		$post=$request->all();
		//print_r($post); exit;
		if(isset($post['product_name']) && !empty($post['product_name'])){
			// $variation = new Variation;
			 $product_id=base64_decode($post['product_name']);
			$items = DB::table('variation')->where('product_id','=', $product_id)->where('deleted_at','=',NULL)->groupBy('product_color')->get(); 
		 
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
							
						echo '<option  value="'.$item->product_color.'">'.$item->product_color.'</option>';
						 
					}else{
						echo '<option value="">No Data Found</option>';
					}
				}
			}else{
				echo '<option value="">No Data Found</option>';
			}
		}
	}
	public function adminUpdateSpecialOrder(Request $request){
		$sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
		if(isset($id ) && !empty( $id )){
			$post=$request->all();
		/* $attribute=implode(",",$post['attributeid']);
			print_r($attribute); exit;  */
			$orderID=base64_decode($post['orderToken']);
			$product_id=$post['product_name'];
			
			if(!empty($post['inproduction_date'])){
				$inproduction_date=date('Y-m-d',strtotime($post['inproduction_date']));
			}else{
				$inproduction_date=NULL;
			}
			$specialOrderData=DB::table('special_order')->where('id','=',$orderID)->first();
				if($specialOrderData->finance==1){
					$orderStatus="finance new";
					//$orderStatus="pending";
					$finance=1;
				}else{
					$orderStatus="pending";
					$finance=0;
					
				}
			$getVariation=DB::table('variation')->where('product_id','=',$product_id)->where('product_color','=',$post['product_color'])->first();
			if(isset($post['attributeid']) && !empty($post['attributeid'])){
				
				$attribute=implode(",",$post['attributeid']);
			}else{
				$attribute='';
			}
			if(!empty($getVariation)){
				$variationID=$getVariation->variationID;
			}else{
				$variationID=0;
			}
			if(!empty($inproduction_date) && !empty($post['productbatch'])){
				$is_noramlOrder=1;
			}else{
				$is_noramlOrder=0;
			}
			$updateArray=array(
				'product_id'=>$product_id,
				'product_color'=>$post['product_color'],
				'product_side_color' =>$post['product_side_color'],
				'orderStatus'=>$orderStatus,
				'variationID'=>$variationID,
				'is_noramlOrder'=>$is_noramlOrder,
				'qtystatus'=>$post['qtystatus'],
				'attribute'=>$attribute,
				'inproduction_date'=>$inproduction_date,
				'productbatch'=>$post['productbatch'],
				'updated_at'=>Carbon::now()->toDateTimeString()
			);
			
			$updateorder=DB::table('special_order')->where('id','=',$orderID)->update($updateArray);
			
			if(!empty($inproduction_date) && !empty($post['productbatch'])){
				
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
				 $OrderNumber=$specialOrderData->OrderNumber;
						$order=array(
							'OrderNumber' => $OrderNumber,
							'product_id' =>$product_id,
							'qtystatus' =>$post['qtystatus'],
							'dealerID' =>$specialOrderData->dealerID,
							'variationID' =>$variationID,
							'specialOrderID' =>$specialOrderData->id,
							'finance'=>$finance,
							'product_color' =>$post['product_color'],
							'product_side_color' =>$post['product_side_color'],
							'batch' =>$post['productbatch'],
							'qty' =>1,
							'orderQty' =>1,
							'created_at' =>Carbon::now()->toDateTimeString()
						);
						 
						//print_r($order);exit;
						$normalorderid=DB::table('product_order')->insertGetId($order);
						
				
						$orderTran=array(
							'orderNoteTokenString' => $OrderNumber.'_'.$normalorderid.'_1',
							'product_id' =>$product_id,
							'orderID' =>$normalorderid,
							'qtystatus' =>$post['qtystatus'],
							'orderStatus' =>$orderStatus,
							'finance'=>$finance,
							'specialOrderID' =>$specialOrderData->id,
							'dealerID' =>$specialOrderData->dealerID,
							'stockdate'=>$inproduction_date,
							'variationID' =>$variationID,
							'product_color' =>$post['product_color'],
							'product_side_color' =>$post['product_side_color'],
							'batch' =>$post['productbatch'],
							'qty' =>1,
							'created_at' =>Carbon::now()->toDateTimeString()
						);
						//print_r($orderTran);echo '<br/>'; exit;
						$orderTransaction=DB::table('order_transaction')->insert($orderTran);
						$orderAdminNotes=DB::table('admin_order_notes')->where('orderTokenString','=',$OrderNumber)->get();
						foreach($orderAdminNotes as $orderAdminNote){
							$orderTokenStringCountArray= explode('_',$orderAdminNote->orderTokenStringCount);
							$orderTokenStringCount=$orderTokenStringCountArray[0].'_'.$normalorderid.'_1'.$orderTokenStringCountArray[1];
							$orderTokenStringCountUpdateArray=array(
								'orderTokenStringCount'=>$orderTokenStringCount
							);
							$orderAdminNotes=DB::table('admin_order_notes')->where('orderTokenString','=',$OrderNumber)->update($orderTokenStringCountUpdateArray);
						}
						 
						$adminNotesArray=array(
							'orderID'=>$normalorderid,
							'orderTokenString'=>$OrderNumber.'_'.$normalorderid.'_1'
							 
						);
						$orderAdminNotes=DB::table('admin_order_notes')->where('orderTokenString','=',$OrderNumber)->update($adminNotesArray);
						
			}
				
			
			if($updateorder > 0){
					Session::flash('operationSucess','Order Updated Successfully !');
				}else{
					Session::flash('operationFaild','Some thing went wrong ! ');
				}
           return Redirect::to('admin/specialorderslist');
		}else{
           return Redirect::to('/');
        }
	} 
	
}
