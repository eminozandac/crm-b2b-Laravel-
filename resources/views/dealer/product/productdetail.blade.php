@extends('delaer.layouts.masterdealer')

@section('pagecss')
    <!-- Toastr style -->
    <link href="{{ asset('assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">

    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
	 <link href="{{ asset('css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">
<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop


@section('contentPages')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>B2B CRM Product Details</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ URL::to('/dealer') }}">Home</a>
                </li>
                <li>
                    <a href="{{ URL::to('/dealer/product') }}">Main Product</a>
                </li>
                <li class="active">
                    <strong>Product detail</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
			
                <div class="ibox product-detail">
                    <div class="ibox-content">

                        <div class="row">
                            <div class="col-md-5">

                                <div class="product-images">

                                    <div>
                                        <div class="image-imitation">
                                          <?php   
										     $images=DB::table('productimages')->where('product_id','=',$product->product_id)->first();
												if(!empty($images->productimage)){
												 
													$cavatar='uploads/products/'.$images->productimage;
												} else{
													$cavatar='assets/img/placeholder300x300.png';
												}
							  ?>
							  <img src="{{URL::to($cavatar)}}" class="img-responsive"/>
                                        </div>
                                    </div>
										
                                   <!-- <div>
                                        <div class="image-imitation">
                                            [IMAGE 2]
                                        </div>
                                    </div>
                                    <div>
                                        <div class="image-imitation">
                                            [IMAGE 3]
                                        </div>
                                    </div>-->

                                </div>

                            </div>
                            <div class="col-md-7">
						<form action="{{action('dealer\CartController@index')}}" method="post" enctype="multipart/form-data" class="form-horizontal">
							<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
							<input type="hidden" name="productToken" value="<?php echo base64_encode($product->product_id); ?>"/>
                                <h2 class="font-bold m-b-xs">
                                   <?php echo $product->productName; ?>
                                </h2>
                                <small><strong>Category : </strong> <?php echo $product->categoryName; ?></small><br/>
                                <small><strong>Brand : </strong> <?php echo $product->brandName; ?></small>
                                <div class="m-t-md">
                                    <h2 class="product-main-price" id="product-main-price"><small class="text-muted">Exclude Tax</small> <small class="text-muted"></small></h2>
									
                                </div>
								
									<?php 
									 $sessionData=Session::get('dealerLog');
										$id = $sessionData['dealerID'];
											$dealerGroup=DB::table('dealer')->where('id','=',$id)->first();
										
											$discountGroup=DB::table('discount')->where('product_id','=', $product->product_id)->where('groupID','=',$dealerGroup->groupID)->first();
											if(!empty($discountGroup->discountPer)){

											echo '<label class="label label-warning">'.$discountGroup->discountPer.'% Discount</label>';
											}
																	
									?>
								
								   
								  
								<br/>
								<div class="clearfix"></div><br/>	
									 
									<br/>
								<div class="clearfix"></div><br/>
								<?php
									$variations=DB::table('variation')->where('product_id','=',$product->product_id)->get();
 
									/* foreach($variations as $variations){
										
									} */
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
								?>
								<div class="col-md-5">
									<input type="hidden" name="varaintToken" id="varaintToken" value=""/>
									<input type="hidden" name="qtystatus" id="qtystatus" value=""/>
									<select class="selectpicker form-control" required name="product_color" id="product_color" >
										<?php
											$getvariations=DB::table('variation')->where('product_id','=',$product->product_id)->groupBy('product_color')->get();
											foreach($getvariations as $variation){
												$color=count($variationColor);
												$inProduction=0;
												$inStock=0;
												for($i=0;$i<$color;$i++){
													
														//echo $variationColor[$i];
													if($variation->product_color==$variationColor[$i]){	
														if($product->sale_price !=''){$sale_pr=$product->sale_price;}else{$sale_pr=0;}
														
															$getinstoks=DB::table('variation')->where('product_id','=',$product->product_id)->where('product_color','=',$variationColor[$i])->where('product_status','=','instock')->get();
															foreach($getinstoks as $getinstok){
																$inStock = $inStock + $getinstok->productStock;
															}															
															//$inStock=$variation->productStock;
														
															$getinproductions=DB::table('variation')->where('product_id','=',$product->product_id)->where('product_color','=',$variationColor[$i])->where('product_status','=','inproduction')->get();
															foreach($getinproductions as $getinproduction){
																$inProduction = $inProduction + $getinproduction->productStock;
															}

															//$inProduction=$variation->productStock;
													
														
														if($variationColor[$i]=='none'){
															echo '<option data-av="'.$variation->product_status.'" data-variant="'.$variation->variationID.'" data-sale="'.$sale_pr.'" data-qty="'.$variation->productStock.'" data-instk="'.$inStock.'" data-inprd="'.$inProduction.'" data-real="'.$product->real_price.'" value="none">None</option>';
														}else{
															$thumb="assets/img/".$variationColorThumb[$i].".jpg";
															
															echo'<option  data-variant="'.$variation->variationID.'" data-av="'.$variation->product_status.'" data-sale="'.$sale_pr.'" data-qty="'.$variation->productStock.'" data-instk="'.$inStock.'"  data-inprd="'.$inProduction.'"  data-real="'.$product->real_price.'" value="'.$variationColor[$i].'" data-thumbnail="'.URL::to($thumb).'">'.$variationColor[$i].'</option>';
														}
													}
												}
											}
											
										?>
									</select>
								</div>
								<div class="col-md-4" id="touchspin1qtydiv">
									<input class="touchspin1" id="touchspin1qty" type="text" value="1" min="1" name="qty">
								</div>
								<div class="clearfix">
								</div>
                                <hr>
								<?php 
								$attributes=DB::table('product_attribute')->where('product_id','=',$product->product_id)->get();
								if(!empty($attributes)){
								?>	
                                <h4>Product Attributes</h4>
								 <div class="small text-muted">
								<?php						
								foreach($attributes as $attribute)
									$attrname=DB::table('attribute')->where('attributeID','=',$attribute->attributeID)->first();
								{ ?>
									<label class="label label-default" style="font-size: 13px;padding: 2px 5px;">
										{{ $attrname->attributeName }}
									</label>&nbsp;&nbsp;&nbsp;
								<?php  } ?>
                                </div>
								<?php } ?>
                                <hr>

                                <h4>Product description</h4>

                                <div class="small text-muted">
                                   <?php echo $product->description; ?>
                                </div>
                                
                                <hr>

                                <div>
								<?php 
									if(!empty($variations)){ 
								?>
								<button class="btn btn-primary btn-sm" id="cart-btn"><i class="fa fa-cart-plus"></i> Add to cart</button>
								<?php 
									}
								?>
                                </div>
							</form>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-footer">
						<span class="pull-right">
							
						</span>
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
	<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
    <script src="{{asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
	<script type="text/javascript">
	$(function() {
		var config = {
						'.chosen-select'           : {},
						'.chosen-select-deselect'  : {allow_single_deselect:true},
						'.chosen-select-no-single' : {disable_search_threshold:10},
						'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
						'.chosen-select-width'     : {width:"95%"}
					} 
					for (var selector in config) {
						$(selector).chosen(config[selector]);
					}
        $('.product-images').slick({
            dots: true
        });
			$(".touchspin1").val(1);
		 $(window).keydown(function(event){
			if(event.keyCode == 13) {
			  event.preventDefault();
			  return false;
			}
		  });
		function maxqty(qty){
			$(".touchspin1").TouchSpin({
					buttondown_class: 'btn btn-white',
					buttonup_class: 'btn btn-white',
					min: 1,
					max:qty
			});
		}
		function get_price(){
			var sale=$('#product_color').find(':selected').attr('data-sale');
			var real=$('#product_color').find(':selected').attr('data-real');
			var stk=$('#product_color').find(':selected').attr('data-av');
			var stkqty=$('#product_color').find(':selected').attr('data-qty');
			var variation=$('#product_color').find(':selected').attr('data-variant');
			var instock=$('#product_color').find(':selected').attr('data-instk');
			var inproduction=$('#product_color').find(':selected').attr('data-inprd');
			//var inproductionOrder=$('#product_color').find(':selected').attr('data-inprdordr');
			var stkstatus='';
			var printprice='';
			$('#varaintToken').val(variation);
			if(instock !=undefined && inproduction !=undefined && real !=undefined && sale !=undefined && inproduction!=undefined){
			 
				if(instock > 0 || inproduction > 0){
					stkstatus='<br/><small class="label label-info"> In Stock ('+instock+')</small>&nbsp;&nbsp;';
					if(inproduction > 0){
						stkstatus += '<small class="label label-success">In Production ('+inproduction+')</small>';
					}
					$("#cart-btn").show('fade');
					$('#touchspin1qtydiv').show();
					$('#touchspin1qty').attr('max',instock);
					$('#qtystatus').val('instock');
					maxqty(instock);
					$(".touchspin1").trigger("touchspin.uponce");
					$(".touchspin1").trigger("touchspin.updatesettings", {max: instock});
				}
				 if(inproduction > 0 && instock ==0){
					stkstatus='&nbsp;&nbsp;<br/><small class="label label-success">In Production  ('+inproduction+')</small>';
					$("#cart-btn").show('fade');
					$('#touchspin1qtydiv').show();
					$('#qtystatus').val('inproduction');
					var maxqtyval= inproduction;
					$('#touchspin1qty').attr('max',maxqtyval);
					maxqty(maxqtyval);
					$(".touchspin1").trigger("touchspin.uponce");
					$(".touchspin1").trigger("touchspin.updatesettings", {max: maxqtyval});
				} 
				if(inproduction == 0 && instock ==0){
			 
					stkstatus='&nbsp;&nbsp;<br/><small class="label label-danger"> Out Of Stock </small>';
					$("#cart-btn").hide('fade');
					$('#touchspin1qtydiv').hide();
				} 
				if(sale =='0'){
					printprice='&pound; '+real+'<small class="text-muted">&nbsp;&nbsp;Exclude Tax</small>'+stkstatus;
					$('#itmprc').val(real);
				}else if(sale == real){
					printprice='&pound; '+real+'<small class="text-muted">&nbsp;&nbsp;Exclude Tax</small>'+stkstatus;
					$('#itmprc').val(real);
				}else{
					printprice='&pound; '+sale+'&nbsp;&nbsp;<span class="real-price">&pound; '+real+'</span>'+'<small class="text-muted">&nbsp;&nbsp;Exclude Tax</small>'+stkstatus;
					$('#itmprc').val(sale);
				}
					$('#product-main-price').html(printprice);
			}else{
				$('#product-main-price').html('');
			}
			$(".touchspin1").val(1);
			
		}
		get_price();
		$('#qtystatus').keyup(function(){
			get_price();
		});
		$('#product_color').change(function(){
			
			get_price();
		});
		
    });
	</script>
@stop()