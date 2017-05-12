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
use Cart;
use Auth;
use File;
use App\Brand;
use App\Category;
use App\Products;
use App\Variation;
use Datatables;
use URL;
 

class DealerAccessoryController extends Controller
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
	
	public function accessoryList(){
		$id = 0;
		$cartitem=Cart::items();
        $cart=$cartitem->count();
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$getCats=DB::table('accessory_category')->where('parent_id','=','0')->where('deleted_at','=',NULL)->get();
		\Session::set('cartcount' , $cart);
        Session::save();
		if(isset($id) && !empty($id)){

			return view('dealer.accessory.dealerAccessoryList')->with('sessionData',$sessionData)->with('getCats',$getCats)->with('cart',$cart);
			
			
		}else{
			return Redirect::to('/');
		}
	}
	public function accessorySubList($catID){
		$id = 0;
		$cartitem=Cart::items();
        $cart=$cartitem->count();
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		$parent_id= base64_decode($catID);
		$getSubCats=DB::table('accessory_category')->where('parent_id','=',$parent_id)->where('deleted_at','=',NULL)->get();
		 
		
	 $getAccessoryData=DB::table('product_accessories')->where('category_id','=',$parent_id)->where('deleted_at','=',NULL)->get();
		
		 
	\Session::set('cartcount' , $cart);
        Session::save();
		if(isset($id) && !empty($id)){

			return view('dealer.accessory.dealerAccessoryList')->with('sessionData',$sessionData)->with('getSubCats',$getSubCats)->with('getAccessoryData',$getAccessoryData)->with('cart',$cart);
			
			
		}else{
			return Redirect::to('/');
		}
	}
	public function accessoryItemList($catID){
		$cartitem=Cart::items();
        $cart=$cartitem->count();
		
		$id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		
		\Session::set('cartcount' , $cart);
        Session::save();
		if(isset($id) && !empty($id)){
			return view('dealer.accessory.dealerAccessoryItemList')->with('catID',$catID)->with('cart',$cart);
			
			
		}else{
			return Redirect::to('/');
		}
	}
	public function dealerAccessoryOrder(){
		$cartitem=Cart::items();
        $cart=$cartitem->count();
		
		$id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		
		\Session::set('cartcount' , $cart);
        Session::save();
		if(isset($id) && !empty($id)){
			return view('dealer.accessory.accessoryOrderList')->with('cart',$cart);
			
			
		}else{
			return Redirect::to('/');
		}
	}
	public function dealerAccessoryOrderDetail($accesotyOrderID){
		$cartitem=Cart::items();
        $cart=$cartitem->count();
		
		$id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
		
		\Session::set('cartcount' , $cart);
        Session::save();
		$accesotyOrderID=base64_decode($accesotyOrderID);
		
		if(isset($id) && !empty($id)){
			return view('dealer.accessory.accessoryorderDetials')->with('accesotyOrderID',$accesotyOrderID)->with('cart',$cart);
			
			
		}else{
			return Redirect::to('/');
		}
	}
	public function accessoryFilter(Request $request){
		$post= $request->all();
		//print_r($post);
		
		$accessory=DB::table('product_accessories')->where('accessoriesToken','=',$post['accessoryData'])->where('deleted_at','=',NULL)->first();
		$catName=DB::table('accessory_category')->where('id','=',$accessory->category_id)->where('deleted_at','=',NULL)->first();
		$num=1;
		//print_r($getallAccessory);
		?>
		<div class="col-md-3">
				<div class="ibox">
					<div class="ibox-content product-box">

						<div class="product-imitation" style="min-height: 185px;">
							 <?php 
							if(!empty($accessory->accessory_image)){
								$cavatar=URL::to('uploads/accessories/'.$accessory->accessory_image);
							} else{
								$cavatar=URL::to('assets/img/placeholder300x300.png');
							}
							
							?>
							 <img alt="image" class="img-circles" src="<?php echo $cavatar; ?>" style="width: auto;max-width: 100%;">
						</div>
						<div class="product-desc">
							<span class="product-price">
                                   &pound;<?php echo $accessory->price; ?>
                                </span>
							<small class="text-muted"><?php echo $catName->categoryName; ?></small><br/>
							<small class="text-muted"><strong>SKU : </strong><?php echo $accessory->sku; ?></small>
							 <?php 
								if( $accessory->accessory_qty > 0){
								}else{
									echo '<small class="label label-danger pull-right">Out of stock</small>';
								}
									?>
							<a href="#" class="product-name"> <?php echo $accessory->accessory_name; ?></a>



							<div class="small m-t-xs">
								<?php
								$str='';
								if(!empty($accessory->accessory_description)){
									$string=$accessory->accessory_description;
									$string = strip_tags($string);

										if (strlen($string) > 150) {

											// truncate string
											$stringCut = substr($string, 0, 150);

											// make sure it ends in a word so assassinate doesn't become ass...
											$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... '; 
										}
										echo $string;
								}
									//echo $str;
								?>
							</div>
							<div class="m-t text-righ">
								<?php 
								if( $accessory->accessory_qty > 0){
									?>
									<a href="javascript:void(0);" data-toggle="modal" data-target="#orderpopup<?php echo $accessory->accessoriesToken; ?>"  class="btn btn-primary">Buy now <i class="fa fa-shopping-cart"></i> </a>
								<?php	
								}else{
									?>
									<a href="javascript:void(0);" disabled="disabled" class="btn btn-primary">Buy now <i class="fa fa-shopping-cart"></i> </a>
									<?php
								}
								//$formurl="{{action('dealer\CartController@index')}}";
								$formurl=URL::to('/dealer/addtocart/');;
								?>
								
								<div class="modal inmodal" id="orderpopup<?php echo $accessory->accessoriesToken; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content animated fadeIn">

										<form action="<?php echo $formurl; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
												 
											
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
													<h4 class="modal-title"><?php echo $accessory->accessory_name; ?></h4>
												</div>
												<div class="modal-body col-md-12">
													<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
													<input type="hidden" name="accessoryToken" value="<?php echo  base64_encode($accessory->accessoryID) ;?>"/>
													<label class="col-md-2">Qty</label>
													<div class="col-md-4" id="touchspin1qtydiv_<?php echo $accessory->accessoryID; ?>">
														<input class="touchspin_<?php echo $num; ?>" id="touchspin1qty_<?php echo $accessory->accessoryID; ?>" type="text" value="1" min="1" name="qty" max="<?php echo $accessory->accessory_qty;?>">
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
													<input type="submit" class="btn btn-primary" value="Add to cart" />
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
		
	}
}
