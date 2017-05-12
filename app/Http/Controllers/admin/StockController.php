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
 

class StockController extends Controller
{
	public function index(){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			 
			return view('admin.manageStock');
		}else{
			return Redirect::to('/');
		}
    }
	public function updatestock(Request $request){
        $sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			$post=$request->all();
			print_r($post); exit;
			$products = new Products();
			$dealer = new Dealer();
			if(isset($post['batch']) && !empty($post['batch'])){
				if(isset($post['stockdate']) && !empty($post['stockdate'])){
					$stockdate=date('Y-m-d', strtotime($post['stockdate']));
				}else{
					$stockdate='0000-00-00';
				}
				$oldstock=DB::table('variation')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->first();
				//print_r($oldstock); exit;
				$oldinstock=$oldstock->inStock;
				$oldinproduction=$oldstock->inProduction;
				$oldstatus=$oldstock->product_staus;
				$newstatus=$post['product_stausss'];
				$newinproduction=0;
				$newinstock=0;
				if($post['product_stausss']=='instock'){
					/** In Stock to In Production**/
					if($oldinproduction !=0){
						if($oldinproduction > $post['productStock']){
							$newinproduction= $oldinproduction - $post['productStock'];
							$newinstock= $oldinstock + $post['productStock'];
						}else{
							$newinproduction= 0;
							$newinstock= $oldinstock + $post['productStock'];
						}
					}else{
						$newinproduction= 0;
						$newinstock= $post['productStock'];
					}
					$productData=$products->where('product_id','=',$post['product_name'])->first();
					$userids=explode(",",$productData->dealerID);
						 
					foreach($userids as $k=>$v){
						
						$users=$dealer->where('id','=',$v)->first();
						$emails=$users->emailID;
						$data_user_stocck_update =array(
							'productName' => $productData->productName,
							'color' =>  $post['product_color'],
							'email' => $emails,
                            'loginUrl' => URL::to('/'),
						);
						Mail::send('email_templates.stockUpdate',['data_user_stocck_update'=>$data_user_stocck_update], function($message)use ($emails)
						{
							$message->to($emails)->subject('Stock Update alert!');
						});
					}
					 
				}else{
					/** In Production to In Stock**/
					if($oldinstock != 0){
						if($oldinstock > $post['productStock']){
							$newinstock= $oldinstock - $post['productStock'];
							$newinproduction=  $oldinproduction + $post['productStock'];
						}else{
							$newinstock= 0;
							$newinproduction=  $oldinproduction + $post['productStock'];
						}
					}else{
						$newinstock= 0;
						$newinproduction=$oldinproduction;
					}
				}
				
				$updtstock=array(
					'product_staus'=>$newstatus,
					'inStock'=>$newinstock,
					'inProduction'=>$newinproduction,
					'stockdate'=>$stockdate,
					'updated_at'=>Carbon::now()->toDateTimeString()
				);
				//print_r($updtstock);exit;
				$updatestock=DB::table('variation')->where('product_color','=',$post['product_color'])->where('product_id','=',$post['product_name'])->update($updtstock);
				if($updatestock >  0){
					Session::flash('operationSucess','Stock Updated Successfully !');
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
		$data = Variation::select('*')->where('product_staus','!=','outofstock')->where('deleted_at','=',NULL)->orderBy('variationID','asc');
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
					$productsName=$products->where('product_id','=',$data->product_id)->first();
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
                if($data->inStock != ''){
					return $data->inStock;
                }else{
                    return '---';
                }
            })
			
			->addColumn('In Production', function ($data) {
                if($data->inProduction != ''){
					$orderinst=DB::table('product_order')->where('product_id','=',$productData->product_id)->where('qtystatus','=','inproduction')->first();
					if(!empty($orderinst)){
						return  $data->inProduction.'('.$orderinst->qty.')';
						
					}else{
						
						return '('.$orderinst->qty.')';
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
			$items = $products->where('batch','=',$post['batch'])->where('deleted_at','=',NULL)->get(); 
			echo '<option value="">Select Product</option>';
			foreach($items as $item){
				echo '<option value="'.$item->product_id.'">'.$item->productName.'</option>';
			}
		}
	}
	public function getproductscolor(Request $request){
		$post=$request->all();
		/*if(isset($post['product_name']) && !empty($post['product_name'])){
			 $variation = new Variation;
			$items = $variation->where('product_id','=',$post['product_name'])->where('deleted_at','=',NULL)->get(); 
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
			foreach($items as $item){
				echo '<option data-totlstk="'.$item->productStock.'" data-variation="'.$item->variationID.'" data-instk="'.$item->inStock.'" data-inprd="'.$item->inProduction.'" value="'.$item->product_color.'">'.$item->product_color.'</option>';
				/* for($i=0;$i<$color;$i++){
					if($variationColor[$i]==$item->product_color){
						if($variationColor[$i]=='none'){
							echo '<option  value="none">None</option>';
						}else{
							$thumb="assets/img/".$variationColorThumb[$i].".jpg";
							echo'<option  value="'.$variationColor[$i].'" data-thumbnail="'.URL::to($thumb).'">'.$variationColor[$i].'</option>';
						}
					}
				} 
			}
		}*/
	}
	/* public function getproductsorder(Request $request){
		$post=$request->all();
		if(isset($post['batch']) && !empty($post['batch'])){
			$products = new Products();
			$items = $products->where('batch','=',$post['batch'])->where('deleted_at','=',NULL)->get(); 
			foreach($items as $item){
				$dealersOrder=DB::table('product_order')->where('product_id','=',$item->product_id)->get();
				foreach($dealersOrder as $order){
					
				$dealers=DB::table('product_order')->where('id','=',$order->dealerID)->first();

				echo '<option value="">Select Product</option>';
				echo '<option value="'.$dealers->id.'">'.$dealers->first_name.'&nbsp;'.$dealers->first_name.'</option>';
				}
			}
		}
	} */
}