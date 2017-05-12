@extends('dealer.layouts.masterdealer')	
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Order Details</h2>
                    <ol class="breadcrumb">
                        <li>
                          <a href="{{URL::to('/dealer')}}">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Order Details</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <span class="pull-right">(<strong>1</strong>) items</span>
                            <h5>Items in Order</h5>
                        </div>
						 
						<?php 
						//echo base64_decode($orderID);
							$orderData=DB::table('special_order')->where('id','=',base64_decode($orderID))->first();
							$productData=DB::table('products')->where('product_id','=',$orderData->product_id)->first();
							$productImg=DB::table('productimages')->where('product_id','=',$orderData->product_id)->first();
							$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
							$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
							 
							
							if(!empty($productImg->productimage)){
								
								$cavatar='uploads/products/'.$productImg->productimage;
							} else{
								$cavatar='assets/img/placeholder300x300.png';
							}
							$dealerGroup=DB::table('dealer')->where('id','=',$orderData->dealerID)->first();
							$attributeData=DB::table('product_attribute')->where('product_id','=',$orderData->product_id)->get();
							$discountGroup=DB::table('discount')->where('product_id','=', $productData->product_id)->where('groupID','=',$dealerGroup->groupID)->first();
							
							 
						?>
                        <div class="ibox-content" style="min-height:70vh;">
                            <div class="table-responsive">
								<div class="col-md-6">
									<h2>Order Details</h2>
									<table class="table table-striped table-bordered table-hover">
										<tr>
											<th>Product Name:</th>
											<td>{{$productData->productName}}</td>
										</tr>
										<tr>
											<th>Category:</th>
											<td>{{$categoryData->categoryName}}</td>
										</tr>
										<tr>
											<th>Brand:</th>
											<td>{{$brandData->brandName}}</td>
										</tr>
										<tr>
											<th>Color:</th>
											<td>{{$orderData->product_color}}</td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									 <h2>Attributes</h2>
										<?php 
											if(!empty($orderData->attribute)){
												$attrid=explode(',',$orderData->attribute);
												// print_r($attrid);
												for($i=0;$i<count($attrid);$i++){
													
														$attrVal=DB::table('attribute')->where('attributeID','=',$attrid[$i])->first();
														 
														//print_r($attrVal);
														if(!empty($attrVal->attributeName)){
												?>
															<p><i class="fa fa-angle-right" aria-hidden="true">&nbsp;</i>{{$attrVal->attributeName}} </p>
												<?php
														}
													
												}
											}
										 
											if($orderData->finance == 1){
												?>
												<label class="label label-info">Finance Order</label>
											<?php
											}
											?>
									 
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
@stop()