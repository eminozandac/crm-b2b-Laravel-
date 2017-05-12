<?php

namespace App\Http\Controllers\dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use DB;
use View;
use Validator;
use Hash;
use Input;
use Mail;
use Form;
use Auth;
use File;
use Config;
use App\Dealer;
use App\Brand;
use App\Products;
use App\Variation;
use XeroLaravel;
use Carbon\Carbon;
use Cart;
use URL;
 

class DealerProductController extends Controller
{
    public function index()
    {
		\Session::forget('accessoriesPlaceorder');
        \Session::save();
        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $dealer  = new Dealer();
            $brand  = new Brand();
            $product  = new Products();
            $variavtion  = new Variation();

            $dealer_brandID_ar = array();
            $dealer_data = $dealer->where('id','=', $id)->first();
            $dealer_brandID =  $dealer_data->brandID;
            $dealer_brandID_ar = explode(',',$dealer_brandID);
//print_r($dealer_data->brandID); exit;
			$product_data =  DB::table('products')
				
                ->join('category', 'products.category_id', '=', 'category.id')
                ->join('brand', 'products.brand_id', '=', 'brand.id')
                ->where('products.deleted_at','=',NULL)
                ->where('products.visibility','=',1)
				->whereIn('products.brand_id',$dealer_brandID_ar)->get();
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			return view('dealer.product.productlist')->with('products',$product_data)->with('cart',$cart);
        }else{
            return Redirect::to('/');
        }
    }

    public  function  productDetail($id){
        $dealerID = 0;
        $sessionData=Session::get('dealerLog');
        $dealerID = $sessionData['dealerID'];
		
        if(isset($dealerID) && ($dealerID != 0))
        {
			$cartitem=Cart::items();
			$cart=$cartitem->count();
			$cartData=$cartitem->toArray();
			$product_data =  DB::table('products')
                ->join('category', 'products.category_id', '=', 'category.id')
                ->join('brand', 'products.brand_id', '=', 'brand.id')
                ->where('products.deleted_at','=',NULL)
                ->where('products.product_id','=',base64_decode($id))
                ->where('products.visibility','=',1)
                ->select('products.*','category.*', 'brand.*')->get(); 
				/* foreach($post as $k=>$v){
					$productadd = array_add($productadd, $k, $v);
				} */
			foreach($product_data  as $produc){
				//$product = array_add($product, $k, $v);
				$productData = $produc;
			}
		 
           return view('dealer.product.productdetail')->with('product',$productData)->with('cart',$cart);
        }else{
           return Redirect::to('/');
        }
    }
	public function productByCategory(Request $request){
		$post=$request->all();
		if(isset($post) && !empty($post)){
			//print_r($post); exit;
			//return Redirect::to('/dealer/product')->with('category_id',$post['category_id']);
			return view('dealer.product.productlist')->with('category_id',$post['category_id']);
		}else{
			 return Redirect::to('/dealer/product');
		}
	}
	public function getDelaerProducs(Request $request){
		$post=$request->all();
	//print_r($post);exit;
		if(isset($post) && !empty($post)){
			$sessionData=Session::get('dealerLog');
			$id = $sessionData['dealerID'];
			$getcategory=DB::table('dealer')->where('id',$id)->first();
			$category_ar =explode(',',$getcategory->categoryID);
			//	print_r($category_ar);
				$catidlist='';
				for($i=0;$i< count($category_ar);$i++){
					if($i==0){
						
						$catidlist .="'".$category_ar[$i]."'";
					}else{
						$catidlist .=",'".$category_ar[$i]."'";
					}
				} 
			if(isset($post['category_id']) && !empty($post['category_id'])){
				$qry = "SELECT products.*, variation.*, category.* FROM variation INNER JOIN products ON products.product_id=variation.product_id INNER JOIN category ON products.category_id=category.id WHERE variation.product_status != 'outofstock' AND products.category_id ='".$post['category_id']."' AND variation.productStock > '0' AND products.visibility='1' AND variation.deleted_at IS NULL AND products.deleted_at IS NULL";
				
			}else{
				
				$qry = "SELECT products.*, variation.*, category.* FROM variation INNER JOIN products ON products.product_id=variation.product_id INNER JOIN category ON products.category_id=category.id WHERE variation.product_status != 'outofstock' AND variation.productStock > '0' AND variation.deleted_at IS NULL  AND products.visibility='1'  AND  products.deleted_at IS NULL AND products.category_id IN (".$catidlist.") OR category.showforall = 1";
			}
			//echo  '<br/>'.$qry;exit;
			$datastoks=DB::select(DB::raw($qry));
			
				?>
				<table class="table table-striped table-bordered table-hover dataTables-examples" id="stock_table" style="width:100%;" >
								<thead>
									<tr>
										<th>Product Name</th>
										<th>Batch No.</th>
										<th>Category</th>
										<th>Product Status</th>
										<th>Price</th>
										<th>Color</th>
										<th>Qty</th>
										<th>Action</th>
										 
									</tr>
								</thead>
								<tbody>
				<?php
			if(!empty($datastoks)){
				$qtycount=0;
				foreach($datastoks as $stok){
					 $qtycount++;
					if($stok->product_status == 'instock' || $stok->product_status == 'inproduction' || $stok->product_status == 'onseaukarrival' ){
						if($stok->product_status == 'inproduction'){
							  $countOrder=0;
							$orderinsts=DB::table('inproduction_order')->where('product_id','=',$stok->product_id)->where('product_color','=',$stok->product_color)->get();
							foreach($orderinsts as $inPrdOrder){
								$countOrder = $countOrder + $inPrdOrder->orderqty;
							}
						}
				
						if($stok->productStock > 0){
						
						?>
						
					<tr>
						<td>
							<?php
							$productsName =DB::table('products')->where('product_id','=',$stok->product_id)->first();
							if($productsName->productName != ''){
								echo  $productsName->productName;
							}else{
								echo '---';
							}
							
							?>
						</td>
						<td>
							<?php 
							if($stok->batch != ''){
								echo $stok->batch;
							}else{
								echo '---';
							}
							?>
						</td>
						<td>
							<?php  
								 if(!empty($productsName->category_id)){
									$category=DB::table('category')->where('id','=',$productsName->category_id)->first();
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
							
							if($stok->product_status != ''){
								if($stok->product_status == 'instock'){
									if($stok->productStock > 0){
									echo '<label class="label label-info"> In Stock</label>';
										
									}else{
										echo '<label class="label label-danger"> Out of Stock</label>';
									}
								}elseif($stok->product_status == 'inproduction'){
									
									$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('updated_at','DESC')->first();
										if(!empty($getDateTranz)){
									 
											echo '<label class="label label-success"> In Production ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
									 
										}else{
											$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('variationTranzToken','DESC')->first();
											if(!empty($getDateTranzS)){
												
													echo '<label class="label label-success"> In Production ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												
											}else{
												if($getDateTranz->stockdate != '' && $getDateTranz->stockdate != '0000-00-00'){
													
													echo '<label class="label label-success"> In Production ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												} 
											}
										}
									 
										
									 
								}else if($stok->product_status == 'onseaukarrival'){
									 
								 
									
									$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('updated_at','DESC')->first();
										if(!empty($getDateTranz)){
									 
											echo '<label class="label label-success" style="background-color: #029dff;"> On Sea - UK Arrival ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
									 
										}else{
											$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('variationTranzToken','DESC')->first();
											if(!empty($getDateTranzS)){
												
													echo '<label class="label label-success" style="background-color: #029dff;"> On Sea - UK Arrival ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												
											}else{
												if($getDateTranz->stockdate != '' && $getDateTranz->stockdate != '0000-00-00'){
													
													echo '<label class="label label-success" style="background-color: #029dff;"> On Sea - UK Arrival ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												} 
											}
										}
									 
								}else{
									echo '<label class="label label-danger"> Out of Stock</label>';
								}
								// return $stok->product_status;
							}else{
								echo '---';
							} 

							?>
						</td>
						 
						<td>
							<?php
								if(!empty($productsName->real_price)){
									echo '&pound;'.$productsName->real_price;
								}else{
									echo '---';
								}
							?>
							
						</td>
						<td>
							<?php
							
							if($stok->product_color	 != ''){
								echo $stok->product_color	;
							}else{
								echo '---';
							}

							?>
						</td>
						
						<td>
							<?php
							 
								 if($stok->productStock > 0){
									 echo $stok->productStock;
								
								 }else{
									 echo '---';
									 
								 }
							 $action=URL::to('/dealer/dealerplaceorder');
							?>
						
						</td>
						<td>
							
							
							<a href="#" data-toggle="modal"  data-targets="#placeorders<?php echo $qtycount; ?>"  class="btn btn-primary btn-sm " onclick="showqtymodel('placeorder<?php echo $qtycount; ?>')"><i class="fa fa-shopping-cart">&nbsp;</i> Buy Now</a>
							
							<div class="modal inmodal fade" id="placeorder<?php echo $qtycount; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content animated fadeIn">

										<form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
											<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
											<input type="hidden" name="productToken" value="<?php echo  base64_encode($stok->product_id) ;?>"/>
											<input type="hidden" name="varaintToken" id="varaintToken_<?php echo $qtycount; ?>" value="<?php echo base64_encode($stok->variationID); ?>"/>
											<input type="hidden" name="qtystatus" id="qtystatus_<?php echo $qtycount; ?>" value="<?php echo $stok->product_status; ?>"/>
											
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
												<h4 class="modal-title">Buy Now </h4>
											</div>
											<div class="modal-body col-md-12" style=" max-height: 350px;overflow-y: scroll;">
												<label class="col-md-2">Qty</label>
												<div class="col-md-4" id="touchspin1qtydiv_<?php echo $qtycount; ?>">
													<input class="touchspin_<?php echo $qtycount; ?>" id="touchspin1qty_<?php echo $qtycount; ?>" type="text" value="1" name="qty" min="1" max="<?php echo $stok->productStock;?>" maxlength="9" />
												</div>
												
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
												<input type="submit" class="btn btn-primary" value="Buy now" />
											</div>
										</form>
									</div>
								</div>
							</div>
						</td>
					 
						 
					</tr>
					
							<?php
						}
					
					}
				}
			}else{
				echo '<td colspan="9">No Data Found</td>';
			}
				?>
				
				</tbody>
							</table>
							 
				<?php
			
		}
		
	}
}
