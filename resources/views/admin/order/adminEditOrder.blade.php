@extends('admin.layouts.masteradmin')
@section('contentPages')

			<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Edit Order</h2>
                    <ol class="breadcrumb">
                       <li>
							<a href="{{ URL::to('/admin') }}">Home</a>
						</li>
                        <li>
							<a href="{{ URL::to('/admin/orderLis') }}">Orders</a>
						</li>
                        <li class="active">
                            <strong>Edit Order</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
			<div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-md-9">
					<div class="ibox">
					<form action="{{action('admin\OrderController@adminupdateorder')}}" method="post" enctype="multipart/form-data" class="form-horizontal">
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                        <div class="ibox-title">
                             
                            <h5>Edit Order</h5>
                        </div>
                        <div class="ibox-content">
							<?php 
							foreach($cartData as $item){
							//print_r($item->orderID);exit;
								 
							if(!empty($item->orderID)){
								//print_r( $item);exit;
							$productData=DB::table('products')->where('product_id','=',$item->product_id)->first();
							$productImg=DB::table('productimages')->where('product_id','=',$item->product_id)->first();
							$variationData=DB::table('variation')->where('variationID','=',$item->variationID)->first();
								 
							$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
							$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
							$PRDidURL='/dealer/productdetail/'.base64_encode($item->product_id);
							$removeitm='/dealer/CancelOrder/'.base64_encode($item->orderID);
							$pricePrint='';
							 
						 
								$dealerGroup=DB::table('dealer')->where('id','=',$item->dealerID)->first();
							
								$discountGroup=DB::table('discount')->where('product_id','=', $productData->product_id)->where('groupID','=',$dealerGroup->groupID)->first();
							$itemTotal=0;
							$itemGrandTotal=0;
							if(!empty($variationData->sale_price)){
								if(!empty($discountGroup->discountPer)){
									$aftDiscount=($variationData->sale_price - $variationData->sale_price * $item->discount /100 )*$item->qty;
									$discount= '- '. $discountGroup->discountPer.'%<hr/>$'.$aftDiscount;
									}else{$discount='';}
								$itemGrandTotal= + $aftDiscount;
								$pricePrint='$'.$variationData->sale_price.'
								<s class="small text-muted">$'.$variationData->real_price.'</s>';
								$itemTotal=$variationData->sale_price*$item->qty.'<br/>'.$discount;
							}else{
								if(!empty($discountGroup->discountPer)){
									$aftDiscount=($variationData->sale_price - $variationData->sale_price * $item->discount /100 )*$item->qty;
									$discount= '- '. $discountGroup->discountPer.'%<hr/>$'.$aftDiscount;}else{$discount='';}
								$pricePrint='$'.$variationData->real_price;
								$itemGrandTotal= + $aftDiscount;
								$itemTotal=$variationData->real_price*$item->qty.'<br/>'.$discount;;
							}
							 
								if(!empty($productImg->productimage)){
								 
									$cavatar='uploads/products/'.$productImg->productimage;
								} else{
									$cavatar='assets/img/placeholder300x300.png';
								}
							   
							?>

							<input type="hidden" name="orderToken" value="<?php echo base64_encode($item->orderID); ?>"/>
							 
                            <div class="table-responsive">
                                <table class="table shoping-cart-table">
                                    <tbody>
										<tr>
											<td width="90">
												<img src="{{URL::to($cavatar)}}" class="img-responsive"/>
											</td>
											<td class="desc" width="120"> 
												<h3>
													<a href="{{URL::to($PRDidURL)}}" class="text-navy">
													{{ $productData->productName}} 
													</a>
												</h3>
												
												<dl class="small m-b-none">
													<dt>Category</dt>
													<dd> - {{$categoryData->categoryName}}</dd>
													<dt>Brand</dt>
													<dd> - {{$brandData->brandName}}</dd>
													<dt>Color</dt>
													<dd> - {{$variationData->product_color}}</dd>
												</dl>

												<div class="m-t-sm">
													 
													
												</div>
												
											</td>

											<td>
												<?php echo $pricePrint; ?>
											</td>
											<td width="120px">
												<input class="touchspin1" type="text" value="{{$item->qty}}" min="1" name="qty">
											</td>
											<td>
												<h4>
												$<?php echo $itemTotal; ?>
												</h4>
											</td>
										</tr>
										<tr>
											<td></td>
											<td colspan="2">
												<strong style="text-align:left;float:left;">Order Status</strong>
												<select class="form-control m-b" name="orderStatus">
													<option value="pending" <?php if($item->orderStatus=='pending'){echo 'selected=selected';} ?>>Pending</option>
													<option value="cancelled" <?php if($item->orderStatus=='cancelled'){echo 'selected=selected';} ?>>Cancelled</option>
													<option value="invoiced" <?php if($item->orderStatus=='invoiced'){echo 'selected=selected';} ?>>Invoiced</option>
													<option value="shipped" <?php if($item->orderStatus=='shipped'){echo 'selected=selected';} ?>>Shipped</option>
													<option value="completed" <?php if($item->orderStatus=='completed'){echo 'selected=selected';} ?>>Completed</option>
												</select>
											</td>
										</tr>
                                    </tbody>
                                </table>
                            </div>
							<hr/>
							<?php 
								} 
							}
								
							?>

                        </div>
						 
                        <div class="ibox-content">
						 
                            <input type="submit" name="submit" value="Update Order" class="btn btn-primary pull-right" />
							 
                            <a href="{{URL::to('/admin/orderLis')}}" class="btn btn-white"><i class="fa fa-arrow-left"></i> Cancel</a>
                        </div>
					</form>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Order Summary</h5>
                        </div>
                        <div class="ibox-content">
                            <span>
                                Total
                            </span>
                            <h2 class="font-bold">
                                ${{$itemGrandTotal}}
                            </h2>

                            <hr/>
                            <span class="text-muted small">
                                *For United States, France and Germany applicable sales tax will be applied
                            </span>
                            <div class="m-t-sm">
                                <div class="btn-group">
								  <!--<a href="{{URL::to('/admin/adminOrderInvoice/'.base64_encode($item->orderID))}}" class="btn btn-primary"> View Invoice</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	@stop()
@section('pagescript')
	@include('admin.includes.commonscript')
	     <script src="{{ asset('assets/js/plugins/slick/slick.min.js')}}"></script>
	  <script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
	   <!-- TouchSpin -->
    <script src="{{asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
	<script type="text/javascript">
	$(function() {
        $('.product-images').slick({
            dots: true
        });
		$(".touchspin1").TouchSpin({
                buttondown_class: 'btn btn-white',
                buttonup_class: 'btn btn-white',
				min: 1
        });
		function get_price(){
			var sale=$('#product_color').find(':selected').attr('data-sale');
			var real=$('#product_color').find(':selected').attr('data-real');
			var stk=$('#product_color').find(':selected').attr('data-av');
			var variation=$('#product_color').find(':selected').attr('data-variant');
			var stkstatus='';
			var printprice='';
			$('#varaintToken').val(variation);
			if(stk == 'instock'){
				stkstatus='&nbsp;&nbsp;<small class="label label-info"> In Stock</small>';
				$("#cart-btn").show('fade');
			}else if(stk =="inproduction"){
				stkstatus='&nbsp;&nbsp;<small class="label label-success">In Production</small>';
				$("#cart-btn").show('fade');
			}else{
				stkstatus='&nbsp;&nbsp;<small class="label label-danger"> Out Of Stock </small>';
				$("#cart-btn").hide('fade');
					
			}
			if(sale =='0'){
				printprice='$ '+real+'<small class="text-muted">&nbsp;&nbsp;Exclude Tax</small>'+stkstatus;
				$('#itmprc').val(real);
			}
			else{
				printprice='$ '+sale+'&nbsp;&nbsp;<span class="real-price">$ '+real+'</span>'+'<small class="text-muted">&nbsp;&nbsp;Exclude Tax</small>'+stkstatus;
				$('#itmprc').val(sale);
			}
				$('#product-main-price').html(printprice);
			
		}
		get_price();
		$('#product_color').change(function(){
			get_price();
		});
		
    });
	</script>
@stop()