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
use Form;
use Auth;
use File;
use Config;
use App\Dealer;
use XeroLaravel;
use Cart;
use Datatables;
use URL;
use App\SpecialOrder;
 

class SpecialOrderController extends Controller
{

    public function index()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            return View::make('dealer/specialorder/specialorderList');
        }else{
            return View::make('/');
        }
    }

    public function dataSpecialOrder()
    {
        $specialOrder = new SpecialOrder();

        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $data = SpecialOrder::select('*')->where('dealerID','=',$id)->orderBy('id','asc');

        $no = 0;
        return Datatables::of($data, $no)

            ->addColumn('Product', function ($data) {
                if($data->product_id != '')
                {
                    $result_data = DB::table('products')->where('product_id','=',$data->product_id)->first();
                    if(!empty($result_data)){
                        //return $result_data->productName;
						$popupdata = '<a htef="javascript:void(0)"  data-toggle="modal" data-target="#adminnotes'.$data->product_id.'" >'.$result_data->productName.'</a>';
						
						 $uniqueQtyNumber = $data->special_orderID.'_'.$data->id;
						   $notes=DB::table('admin_specialorder_notes')->where('orderTokenString','=',$uniqueQtyNumber)->first();
						   if(!empty($notes)){
							   $notestitle=$notes->name;
							   $datanotes=$notes->description;
						   }else{
							   $notestitle='No Notes Available';
							   $datanotes='<i class="fa fa-frown-o" aria-hidden="true" style="font-size:85px;color:#ccc;"></i>';
						   }
						 $popupdata .= '<div class="modal inmodal" id="adminnotes'.$data->product_id.'" tabindex="-1" role="dialog"  aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content animated fadeIn">

													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
														<h4 class="modal-title">'.$notestitle.'</h4>
													</div>
													<div class="modal-body col-md-12">
														<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
															<p style="text-align:center;">'.$datanotes.'</p> 
															
														</div>
														<div class="clearfix"></div>
													</div>
													<div class="modal-footer">
													 <div class="clearfix"></div>
													 <br/>
														<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
														
													</div>
												</form>
											</div>
										</div>
									</div>';
                        return  $popupdata;
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

            

            ->addColumn('Status', function ($data) {
                if($data->orderstatus != '')
				{
					if($data->orderstatus == "pending"){
						
						return '<label class="label label-warning" style="text-transform:capitalize;">Pending</label>';
						
					}else if($data->orderstatus == "cancelled") {
						
						return '<label class="label label-danger" style="text-transform:capitalize;">Cancelled</label>';
						
					}else if($data->orderstatus == "complete"){
						
						return '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
						
					}else{
						return '---';
					}
                }else{
                    return '---';
                }
            })

            ->addColumn('Action', function ($data) {
                $html = '';
                $deleted = $data->id;
                $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('$deleted')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash-o\"></i></a>";
					$deatailurl = URL::to('dealer/dealerspecialorderdetail', base64_encode($data->id));
						$html.= '<a href="'.$deatailurl.'"  data-toggle="tooltip" title="View order" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>';		
                return $html;
            })
            ->make(true);
    }

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
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
        }
        return $token;
    }

    public function addSpecialOrder()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $productUnique='';
            for($j=0;$j < 4;$j++){
                if($j!=3){$dash='-';}else{$dash='';}
                $productUnique .= $this->getTokenProduct().$dash;
            }
			$OrderNumber='';
				for($jn=0;$jn < 4;$jn++){
					$OrderNumber .= $this->getTokenProduct();
				}
            $data['specialorderID'] = $productUnique;
            $data['OrderNumber'] = $OrderNumber;
			$specialorderID= $productUnique;
            $data['company_name'] = '';
            $data['productID'] = '';
            $data['brandID'] = '';
           

            $result_product = DB::table('products')->where('deleted_at','=',NULL)->get();
            
            $data['result_products'] = $result_product;  

            return View::make('dealer/specialorder/index',$data);
        }else{
            return View::make('/');
        }
    }

    public function dataColor()
    {
        $data_post = Input::all();
        $productID =  $data_post['data_productID'];

        $result_color = DB::table('variation')
            ->where('variation.product_id','=',$productID)->where('variation.deleted_at','=',NULL)->groupBy('product_color')->lists('product_color','variationID');

        $html = '';
        if(!empty($result_color))
        {
            $html.= "<option value=''>Select</option>";
              foreach($result_color as $key => $value){
                 $html.= "<option value='$key'>";
                 $html.= $value;
                 $html.= "</option>";
              }
        }else{
            $html.= "<option value=''>Not Data Found</option>";
        }
        echo $html;
    }

	public function attributeData(){
		$data_post = Input::all();
        $productID =  $data_post['data_productID'];
		$getarrtibutes=DB::table('product_attribute')->where('product_id','=',$productID)->get();
		$first=0;
		$attrdiv='';
		if(!empty($getarrtibutes)){
			
			foreach($getarrtibutes as $attr){
				$attrname=DB::table('attribute')->where('attributeID','=',$attr->attributeID)->first();
				$first++;
				if($first == 1){
					$selected="";
				}else{
					$selected="";
				}
				$attrdiv .='<div class="i-checks"><label> <input type="checkbox" '.$selected.' name="attributeid[]" value="'.$attrname->attributeID.'" class="form-control" > <i></i>'.$attrname->attributeName.'</label></div>';
			}
			$attrdiv .='</div><div class="clearfix"></div> ';
			echo $attrdiv;
		}else{
			echo 'No Arributes Found';
		}
	}

    public function saveData()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$delerData=DB::table('dealer')->where('id','=',$id)->first();
        if(isset($id) && ($id != 0))
        {
            $data_post = Input::all();
		//	print_r($data_post);exit;
			$finance=0;
		if(isset($data_post['finance']) && $data_post['finance']==1){
				$orderStatus="finance new";
				$finance=1;
			}else{
				$orderStatus="pending";
				$finance=0;
				
			}
            if(!empty($data_post)){
					$attributes='';
				if(!empty($data_post['attributeid'])){
					for($i=0;$i<count( $data_post['attributeid']);$i++){
						$getAttribute=DB::table('attribute')->where('attributeID','=',$data_post['attributeid'][$i])->first();
						//$attributes .=$getAttribute->attributeName	.',';
					}
					$data_post['attribute'] = implode(',',$data_post['attributeid']);
					 $attributes = implode(',',$data_post['attributeid']);
					
				 }else{
					 $data_post['attribute']='';
				 }
					$data_post['today_date'] = date('Y-m-d');
				
				//print_r($data_post['attribute']);exit;
				unset($data_post['_token']);
			 
				unset($data_post['attributeid']);
				unset($data_post['finance']);
				
				/* if(isset($data_post['product_side_color']) && !empty($data_post['product_side_color'])){
					
					$data_post['product_color']=$data_post['product_color'].' whith '.$data_post['product_side_color'].' Sides';
				} */
				$data_post['dealerID'] = $id;
				$data_post['orderstatus'] = $orderStatus;
				$data_post['finance'] = $finance;
				$data_post['color']=$data_post['product_color'];
				$data_post['address']=$delerData->address;
				if ($data_post['special_orderID'] != '') {
					$emails='lauren@superiorspas.co.uk';
					//$emails='nikhilpatel8000@gmail.com';
					if(isset($data_post['finance']) && !empty($data_post['finance'])){
						$data_post['isfinance']='finance';
					}else{
						$data_post['isfinance']='';
					}
					$users=DB::table('dealer')->where('id','=',$id)->first();
					$productData=DB::table('products')->where('product_id','=',$data_post['product_id'])->first();
					$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
					$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
					
					$data_user_stocck_update =array(
						'productName' => $productData->productName,
						'color' =>  $data_post['product_color'],
						'company' => $users->company_name,
						'dealername' =>  $users->first_name,
						'categoryName' =>$categoryData->categoryName,
						'brandName' =>   $brandData->brandName,
						'attributes' =>   $attributes,
						'orderDate' =>  date('d-m-Y'),
						'email' => $users->emailID,
						'finance' =>  $data_post['isfinance'],
						'comments' => $data_post['comments']
						 
						 
					); 
					 Mail::send('email_templates.specialOrderMail',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
					{
						$message->to($emails)->subject('New Special order Received !');
					}); 
					/* print_r($data_post);
					exit; */
					SpecialOrder::updateOrCreate(array('special_orderID' => $data_post['special_orderID']), $data_post);
					Session::flash('operationSucess', 'Successfully Add Your Special Order');
					return Redirect::to('dealer/specialorders');
					exit;
				} else {
					Session::flash('operationFaild', 'Something is Wrong');
					return Redirect::to('dealer/specialorders');
					exit;
				}
			}else{
				Session::flash('operationFaild', 'Something went wrong !');
				return Redirect::to('dealer/specialorderadd');
				exit;
			}
				return Redirect::to('dealer/specialorders');
					exit;			
        }else{
            return View::make('/');
        }
    }

    public function deleteData(Request $request)
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {	$post=$request->all();
            // print_r($post);
			$delete=DB::table('special_order')->where('id','=',$post['special_orderID'])->delete();
        }else{
            return View::make('/');
        }
    }
	public function dealerSpecialOrderDetail($orderID)
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {	 
			return view('dealer/specialorder/dealerSpecialorderDetail')->with('orderID',$orderID);
        }else{
            return View::make('/');
        }
    }
}
