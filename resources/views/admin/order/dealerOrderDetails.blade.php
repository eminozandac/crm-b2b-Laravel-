@extends('dealer.layouts.masterdealer')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/summernote/summernote.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/summernote/summernote-bs3.css') }}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
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
							$orderData=DB::table('product_order')->where('orderID','=',$orderID[0])->first();
							$productData=DB::table('products')->where('product_id','=',$orderData->product_id)->first();
							$productImg=DB::table('productimages')->where('product_id','=',$orderData->product_id)->first();
							$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
							$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
							$variationData=DB::table('variation')->where('variationID','=',$orderData->variationID)->first();
							$getOrderTran=DB::table('order_transaction')->where('orderNoteTokenString','=',$orderID[1])->first();
							if(!empty($productImg->productimage)){
								 
								$cavatar='uploads/products/'.$productImg->productimage;
							} else{
								$cavatar='assets/img/placeholder300x300.png';
							}
							$dealerGroup=DB::table('dealer')->where('id','=',$orderData->dealerID)->first();
							$attributeData=DB::table('product_attribute')->where('product_id','=',$orderData->product_id)->get();
							$discountGroup=DB::table('discount')->where('product_id','=', $productData->product_id)->where('groupID','=',$dealerGroup->groupID)->first();
							$itemTotal=0;
							$finalprice=0;
							if(!empty($orderData->real_price)){
								/* if(!empty($orderData->sale_price)){
									if(!empty($discountGroup->discountPer)){
										$finalprice=round($orderData->sale_price-$discountGroup->discountPer*$orderData->sale_price/100 );
										$discount= '- '. $discountGroup->discountPer.'%<hr/>&pound;'.$finalprice;
									}else{$discount='';$finalprice='&pound;'.$orderData->sale_price;}
								}else{ */
									if(!empty($discountGroup->discountPer)){
										$finalprice=round($orderData->real_price-$discountGroup->discountPer*$orderData->real_price/100 );
										$discount= '- '. $discountGroup->discountPer.'%<hr/>&pound;'.$finalprice;
									}else{$discount='';$finalprice='&pound;'.$orderData->real_price;}
								//}
								/* if(!empty($productData->sale_price)){
									
									$pricePrint='&pound;'.$productData->sale_price.'
									<s class="small text-muted">$'.$productData->real_price.'</s>';
									$itemTotal=($productData->sale_price*1).'<br/>'.$discount;
								}else{ */
									$pricePrint='&pound;'.$productData->real_price;
									$itemTotal=($productData->real_price*1).'<br/>'.$discount;;
								//} 
							}else{
								$pricePrint ='';
								$itemTotal ='';
								$finalprice ='';
							}
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
											<td><?php 
												if(!empty($variationData->product_color)){
													echo $variationData->product_color;
												}else{
													echo $getOrderTran->product_color;
													
												}
												
											?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									
									<?php 
								 
										//$getNotes=DB::table('order_notes')->where('orderID','=',$orderData->orderID[0])->first();
										$getCname=DB::table('order_transaction')->where('orderNoteTokenString','=',$orderID[1])->first();
										if(!empty($getCname)){
											echo '<h2>Order Notes</h2>';
											echo '<p><strong>Customer Name : </strong>'.$getCname->customer_name.'</p>';
											if(!empty($getCname->order_notes_descriptions)){
												echo '<p><strong>Notes : </strong>'.$getCname->order_notes_descriptions.'</p>';
											}
											 
										}
										if(!empty($getOrderTran->address)){
											echo '<hr/><h2>Address</h2>';
											echo '<p>'.$getOrderTran->address.'</p>';
										}
										
									?>
									<p> <strong> Order Status : </strong>
										<?php 
										if($getOrderTran->finance > 0){
											 if($getOrderTran->orderStatus=='finance new'){
												echo '<label class="label label-warning" style="text-transform:capitalize;">'.$getOrderTran->orderStatus.'</label>';
												 
											 }else if($getOrderTran->orderStatus=='finance link sent') {
												echo '<label class="label label-danger" style="text-transform:capitalize;">'.$getOrderTran->orderStatus.'</label>';
												
											 }else if($getOrderTran->orderStatus=='finance accepted') {
												echo '<label class="label label-info" style="text-transform:capitalize;">'.$getOrderTran->orderStatus.'</label>';
												
											 }else if($getOrderTran->orderStatus=='finance verified'){
												 echo '<label class="label label-primary" style="text-transform:capitalize;">'.$getOrderTran->orderStatus.'</label>';
												 
											 }else if($getOrderTran->orderStatus=='finance awaiting delivery slip'){
												 echo '<label class="label label-success" style="text-transform:capitalize;background-color:#F7609E;">'.$getOrderTran->orderStatus.'</label>';
												 
											 }else{
												 echo '<label class="label label-success" style="text-transform:capitalize;">'.$getOrderTran->orderStatus.'</label>';
											 }
										}else{
											
											if($orderData->orderStatus=='pending'){
												echo '<label class="label label-warning" style="text-transform:capitalize;">'.$orderData->orderStatus.'</label>';
											}elseif($orderData->orderStatus=='booked') {
												echo '<label class="label label-danger" style="text-transform:capitalize;background-color:#F7609E;">'.$orderData->orderStatus.'</label>';
											}elseif($orderData->orderStatus=='invoice'){
												echo '<label class="label label-danger" style="text-transform:capitalize;">'.$orderData->orderStatus.'</label>';
											}elseif($orderData->orderStatus=='paid'){
												echo '<label class="label label-info" style="text-transform:capitalize;">'.$orderData->orderStatus.'</label>';
											}else{
												echo '<label class="label label-success" style="text-transform:capitalize;">'.$orderData->orderStatus.'</label>';
											}	
										} 
										?>
									</p>
								</div>
                                
								
								<hr/>
								 
								<div class="col-sm-12"><h2>Add Accessories</h2><br/></div>
								<form action="{{action('dealer\OrderController@updateorder')}}" method="POST" enctype="multipart/form-data" class="productss" id="order_edit">
								<input type="hidden" name="_token"  id="token" value="<?php echo csrf_token(); ?>"/>
								<input type="hidden" name="orderToken"  id="orderToken" value="<?php echo base64_encode($getOrderTran->order_transactionID); ?>" />
									<div class="col-sm-8">
										<div class="form-group">
											<label class="control-label">Accessory Name:</label>
										  
											 <select data-placeholder="Select Accessories..." class="chosen-select" multiple style="width: 100%" required tabindex="4" name="product_name[]" id="product_name">
												<?php 
													$getProducts=DB::table('product_accessories')->where('deleted_at','=',NULL)->get();
													foreach($getProducts as $getProduct){
													?>
														<option value="<?php echo base64_encode($getProduct->accessoryID); ?>" >{{$getProduct->accessory_name}}</option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label">&nbsp;</label><br/>
											<input type="submit" class="btn btn-w-m btn-primary" id="stockupdate" value="Update" />
											<a href="{{URL::to('/dealer/dealerorders')}}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back </a>
										</div>
									</div>
								</form>
								<div class="clearfix"></div><hr/>
								<?php if(!empty($getOrderTran->accessoryID)){ ?>
								<div class="col-sm-12">
									<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
										<thead>
											<tr>
												<th>#</th> 
												<th>Accessory Name</th>
												 
												<th style="min-width: 55px;">Action</th>
 
												 
											</tr>
										</thead>
										<tbody>
											<?php
												$arrayAccessory=explode(',',$getOrderTran->accessoryID);
												//print_r($arrayAccessory);]
												$number=0;
												for($i=0;$i<count($arrayAccessory);$i++){
													$number++;
													$accessoryName=DB::table('product_accessories')->where('accessoryID','=',$arrayAccessory[$i])->where('deleted_at','=',NULL)->first();
											?>
												<tr>
													<td>{{$number}}</td>
													<td>{{$accessoryName->accessory_name}}</td>
													<td><a href="javascript:void(0);" title="Delete order" onclick="removedata('<?php  echo $arrayAccessory[$i]; ?>','<?php  echo $getOrderTran->order_transactionID; ?>')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a></td>
												</tr>
											<?php	
												}
											?>
										</tbody>
									</table>
								</div>
							<?php } ?>
                            </div>
                        </div>
                        <div class="ibox-content">
							
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
			@stop()
@section('pagescript')
		@include('dealer.includes.commonscript')
			<script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
			<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
				<script src="{{asset('assets/js/plugins/sweetalert/sweetalert.min.js')}}"></script>
				<script src="{{asset('assets/js/plugins/sweetalert/sweetalert.min.js')}}"></script>
		<script>
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
		
						
					$(".select2_demo_1").select2();
					$(".select2_demo_2").select2();
					$(".select2_demo_3").select2({
						placeholder: "Select a Product",
						allowClear: true
					});
					
					$('#order_edit').find('[name="product_name"]')
						.change(function(e) {
							$('#product_name').formValidation('revalidateField', 'product_name');
						})
					.end()
					.formValidation({
                        framework: 'bootstrap',
                            excluded: ':disabled',
                            message: 'This value is not valid',
                            icon: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                                product_name: {
                                    validators: {
                                        callback: {
                                            message: 'Please Select Staff',
                                            callback: function(value, validator, $field) {
                                                /* Get the selected options */
                                                var options = validator.getFieldElements('product_name').val();
                                                return (options != null);
                                            }
                                        }
                                    }
                                },
                                title: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Title'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 30,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                },
                                assign_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Date'
                                        },
                                        date: {
                                            format: 'DD-MM-YYYY',
                                            max: 'completion_date',
                                            message: 'The Assign date is not a valid'
                                        }
                                    }
                                },
                                completion_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Date'
                                        },
                                        date: {
                                            format: 'DD-MM-YYYY',
                                            min: 'assign_date',
                                            message: 'The Completion date is not a valid'
                                        }
                                    }
                                },
                                description: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Description !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 200,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                }
                            }
                    });
			$(function() {
				
				function removeData(accvalID,ordertranz){	
					var ordertranz = ordertranz;
					var accvalID = accvalID;
			 
					//console.log(order);
					swal({
					title: "Are you sure?",
					text: "you wish to delete Accessory ?",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes",
					cancelButtonText: "No",
					closeOnConfirm: true,
					closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) {
							var _token = $('#token').val();
							
							
							//alert(order);
							$.ajax
							({
								type: "POST",
								url: "{{URL::to('dealer/ajax/log/deleteorderaccessory')}}",
								data: {'ordertranz':ordertranz,'_token':_token,'accvalID':accvalID},
								success: function(msg)
								{ 	 
									//$('#product_name').html(msg);
									//console.log(msg);
									swal("Deleted!", "Your Accessory Item has been deleted.", "success"); 
									location.reload();
									
								}
							});  
						}	 
					});
				}
				deleted_data = removeData;
			});
			function removedata(accvalID,ordertranz)
			{
				if(ordertranz != '' && ordertranz != 0 && accvalID != '' && accvalID != 0){
					deleted_data(accvalID,ordertranz);
				}
			}
		</script>
@stop()