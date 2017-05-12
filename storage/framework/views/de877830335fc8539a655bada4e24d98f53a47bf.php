
<?php $__env->startSection('pagecss'); ?>
	<link href="<?php echo e(asset('assets/css/bootstrap-select.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
	
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Finance Order Details</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Finance Order Details</strong>
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
                          
                            <h5>Items in Order</h5>
                        </div>
						 
						<?php 
						 //print_r($orderID); 
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
										$finalprice=round($orderData->real_price-$discountGroup->discountPer*$orderData->sale_price/100 );
										$discount= '- '. $discountGroup->discountPer.'%<hr/>&pound;'.$finalprice;
									}else{$discount='';$finalprice='&pound;'.$orderData->real_price;}
								//}
								/* if(!empty($productData->sale_price)){
									
									$pricePrint='&pound;'.$productData->sale_price.'
									<s class="small text-muted">&pound;'.$productData->real_price.'</s>';
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
                        <div class="ibox-content" >
                            <div class="table-responsive">
								<div class="col-md-6">
									<h2>Order Details</h2>
									<table class="table table-striped table-bordered table-hover">
										<tr>
											<th>Product Name:</th>
											<td><?php echo e($productData->productName); ?></td>
										</tr>
										<tr>
											<th>Category:</th>
											<td><?php echo e($categoryData->categoryName); ?></td>
										</tr>
										<tr>
											<th>Brand:</th>
											<td><?php echo e($brandData->brandName); ?></td>
										</tr>
										<tr>
											<th>Color:</th>
											<td>
											<?php 
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
										}else {
											echo '<hr/><h2>Address</h2>';
											
											echo '<p>'.$dealerGroup->address.'</p>';
										}
										if(!empty($dealerGroup->pincode)){
											
											echo '<p><strong>Pin Code : </strong>'.$dealerGroup->pincode.'</p>';
										}
									?>
								</div>
								<div class="claerfix"></div>
								<div class="col-md-12">
								<form action="<?php echo e(action('admin\OrderController@adminupdateorder')); ?>" method="POST" enctype="multipart/form-data" class="productss" id="order_edit">
										<hr/>
										<div class="col-sm-12"><h2>Order Edit</h2><br/></div>
										<input type="hidden" name="_token"  id="token" value="<?php echo csrf_token(); ?>"/>
										<input type="hidden" name="orderToken"  id="orderToken" value="<?php echo base64_encode($orderID[0]); ?>" />
										<input type="hidden" name="orderTokenID"  id="orderTokenID" value="<?php echo base64_encode($orderID[1]); ?>"/>
										<input type="hidden" name="ProductOrderTokenIDVar"  id="ProductOrderTokenIDVar" value="<?php echo $getOrderTran->variationID; ?>"/>
										 
										<input type="hidden" name="ordertype" value="finance"/>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Product Name:</label>
											 
												<select data-placeholder="Choose a Product..." class="select2_demo_3 form-control" style="width: 100%"  tabindex="4" name="product_name"  required id="product_name">
													<option value="">Select Product</option>
													<?php 
														$getProducts=DB::table('products')->where('deleted_at','=',NULL)->get();
														foreach($getProducts as $getProduct){
														?>
															<option value="<?php echo base64_encode($getProduct->product_id); ?>" <?php if($getProduct->product_id==$getOrderTran->product_id){echo 'selected';} ?>><?php echo e($getProduct->productName); ?></option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Color :</label>
												<select class="form-control" name="product_color" required id="product_color" >
													<option value="">Select Color</option>
												</select>
											</div>
										</div>
										<div class="col-sm-4" id="">
											<div class="form-group">
												<label class="control-label" id="datelabel">Select Product status :</label>
												<select class="form-control" name="productStatus" required id="productStatus" >
													<option value="">Select Status</option>
													
												</select>
											</div>
										</div>
										<div class="col-sm-4" id="">
											<div class="form-group">
												<label class="control-label" id="datelabel">Select Batch Number :</label>
												<select class="form-control" name="productbatch" required id="productbatch" >
													<option value="">Select Batch Number</option>
													
												</select>
											</div>
										</div>
										<div class="col-sm-4" id="datebox">
											<div class="form-group">
												<label class="control-label" id="datelabel">Delivery Date :</label>
												<input type="text"  id="datetd1" class="form-control datetd date" placeholder="DD-MM-YYYY" name="delivery_date" value="<?php if(!empty($getOrderTran->delivery_date) && $getOrderTran->delivery_date !='0000-00-00'){ echo date('d-m-Y',strtotime($getOrderTran->delivery_date)); }?>">
											</div>
										</div>
										
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Order Status :</label>
												<select class="form-control" name="orderStatus" required id="orderStatus" >
													<option value="">Select Status</option>
													<option <?php if($getOrderTran->orderStatus=='finance new'){echo 'selected=selected';}?> value="finance new">Finance New</option>
													<option <?php if($getOrderTran->orderStatus=='finance link sent'){echo 'selected=selected';}?> value="finance link sent">Finance Link Sent</option>
													<option <?php if($getOrderTran->orderStatus=='finance accepted'){echo 'selected=selected';}?> value="finance accepted">Finance Accepted</option>
													<option <?php if($getOrderTran->orderStatus=='finance verified'){echo 'selected=selected';}?> value="finance verified"> Finance Verified</option>
													<option <?php if($getOrderTran->orderStatus=='finance awaiting delivery slip'){echo 'selected=selected';}?> value="finance awaiting delivery slip">Finance awaiting delivery slip</option>
													<option <?php if($getOrderTran->orderStatus=='finance completed'){echo 'selected=selected';}?> value="finance completed">Finance Completed</option>
													<option <?php if($getOrderTran->orderStatus=='finance declined'){echo 'selected=selected';}?> value="finance declined">Finance Declined</option>
												</select>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-sm-8">
											<div class="form-group">
												<div class="form-group">
												<label class="control-label">Accessory Name:</label>
											  
												<select data-placeholder="Select Accessories..." class="chosen-select" style="width: 100%" tabindex="4" name="accessory_name" id="accessory_name">
												 <option value="" selected>Select Accessory</option>
													<?php 
														$getProductsAcc=DB::table('product_accessories')->where('deleted_at','=',NULL)->get();
														foreach($getProductsAcc as $productAcc){
														?>
															<option value="<?php echo base64_encode($productAcc->accessoryID); ?>" ><?php echo e($productAcc->accessory_name); ?></option>
													<?php
														}
													?>
												</select>
											</div>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
												<label class="control-label">Qty:</label>
												<input class="touchspin" type="text" value="1" name="qty">
												  
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">&nbsp;</label><br/>
												<input type="submit" class="btn btn-w-m btn-primary" id="stockupdate" value="Update" />
												 <a href="<?php echo e(URL::to('/admin/financeorder')); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Back </a>
											</div>
										</div>
									</div>
									<?php   if(!empty($getOrderTran->accessoryID)){ ?>
								<div class="clearfix"></div><hr/>
								<div class="col-sm-12">
									<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
										<thead>
											<tr>
												<th>#</th> 
												<th>Accessory Name</th>
												<th>Qty</th>
												<th style="min-width: 55px;">Action</th>
 
												 
											</tr>
										</thead>
										<tbody>
											<?php
												//$arrayAccessory=explode(',',$getOrderTran->accessoryID);
												//print_r($arrayAccessory);]
												
												if(!empty($getOrderTran->accessoryID)){
												 $acessorydata = json_encode($getOrderTran->accessoryID,true);
												$data = json_decode($acessorydata,true);
												$acessorydata = json_decode($data,true);
												$number=0;
													foreach($acessorydata as $k=>$v){
													//echo $acessory['accessory_qty'];
													$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$k)->first();
													$number++;
											?>
												<tr>
													<td><?php echo e($number); ?></td>
													<td><?php echo e($acessoryName->accessory_name); ?></td>
													<td>
															<div class="form-group" style="max-width:150px;margin-bottom:0px;">
																<input type="hidden" name="accessory_nameeedit[]" value="<?php echo base64_encode($k); ?>"/>
																<input class="touchspin touchspinlist" data-tmid="<?php echo $k; ?>" id="touchspinlist<?php echo $k; ?>" style="max-width:150px;" name="qtyedits[]" type="text" value="<?php echo $v; ?>">
																<input style="max-width:150px;" class="touchspindd" data-qmid="<?php echo $k; ?>" id="touchspindd<?php echo $k; ?>" name="qtyedit[]" type="hidden" value="<?php echo $v; ?>">
																
																  
															</div>
														</td>
													<td><a href="javascript:void(0);" title="Delete order" onclick="removedata('<?php echo base64_encode($k); ?>','<?php  echo base64_encode($getOrderTran->order_transactionID); ?>')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a></td>
												</tr>
											<?php	
													}
												 }
											?>
										</tbody>
									</table>
								</div>
								<?php } ?>
								</form>
                            </div>
                        </div>
                         
                    </div>
                   
                </div>
                 
            </div>
        </div>
			<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
		<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<script src="<?php echo e(asset('assets/js/bootstrap-select.js')); ?>"></script>
			<script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>
			<script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
           <script src="<?php echo e(asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')); ?>"></script>
		    <script src="<?php echo e(asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')); ?>"></script>
			<script>
				$(function(){
					var touchspain = '.touchspin';
					$(touchspain).TouchSpin({
						buttondown_class: 'btn btn-white',
						buttonup_class: 'btn btn-white',
						min: 1
						 
					});
					function initqtylistacc(tmid){
						var tmid=tmid
					 
						var tbox='#touchspinlist'+tmid;
						var qbox='#touchspindd'+tmid;
						//$('.touchspinlist').parent().parent().find('.touchspindd').val($('.touchspinlist').val()); 
						var tboxdata= $(tbox).val();
						$(qbox).val(tboxdata);
						console.log(qbox);
					}
					$('.touchspinlist').change(function(){
						//console.log($(this).attr('data-tmid'));
						var tmid=$(this).attr('data-tmid');
					 
						initqtylistacc(tmid);
					});
					  $('.date').datepicker({
                        todayBtn: "linked",
                        keyboardNavigation: false,
                        forceParse: false,
                        calendarWeeks: true,
						format: 'd-m-yyyy',
                        autoclose: true
                    });
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
					function getProductBatch(nobatch){
						//var nobatchoption=nobatch;
						var _token = $('#token').val();
						var datacolor=$('#product_color').find(':selected').val();
						var product_name = $('#product_name').find(':selected').val();
						var datastatus=$('#productStatus').find(':selected').val();
						var selectedbatch='<?php echo $getOrderTran->batch; ?>';
						//console.log(datastatus);
						$.ajax
						({
							type: "POST",
							url: "<?php echo e(URL::to('admin/ajax/log/getproductsbatcheditorder')); ?>",
							data: {'product_name':product_name,'_token':_token,'datacolor':datacolor,'datastatus':datastatus,'selectedbatch':selectedbatch},
							success: function(msg)
							{ 	 
								/* alert(msg); */
								$('#productbatch').html('');
								//$('#productbatch').html(msg);
								 if(msg != ''){
									$('#productbatch').html(msg);
									
								}else{
									if(nobatch != ''){
										
										$('#productbatch').html('<option value="'+selectedbatch+'">'+selectedbatch+'</option>');
									}else{
										$('#productbatch').html('<option value="">No Data Found</option>');
									}
								}

								//console.log(msg); 
								 
							}
						});  
					}
					function getProductStatus(nostatus){
						var _token = $('#token').val();
						var datacolor=$('#product_color').find(':selected').val();
						var product_name = $('#product_name').val();
						var seletedstatus='<?php echo $getOrderTran->qtystatus; ?>';
						$.ajax
						({
							type: "POST",
							url: "<?php echo e(URL::to('admin/ajax/log/getproductsstatuseditorder')); ?>",
							data: {'product_name':product_name,'_token':_token,'datacolor':datacolor,'seletedstatus':seletedstatus},
							success: function(msg)
							{ 	 
								/* alert(msg); */
								//$('#productStatus').html(msg);
								// getProductBatch();
								 
								if(msg != ''){
									$('#productStatus').html(msg);
									getProductBatch();
									
								}else{
									 $('#productStatus').html('');
									if(nostatus != ''){
										
										$('#productStatus').html('<option value="'+seletedstatus+'">'+seletedstatus+'</option>');
									}else{
										$('#productStatus').html('<option value="">No Data Found</option>');
									}
								}
								//console.log(msg); 
								 
							}
						});  
					}
					
					function getProductColor(){
						var _token = $('#token').val();
						var product_name = $('#product_name').val();
						var selected_option='<?php echo $getOrderTran->product_color; ?>';
						$.ajax
						({
							type: "POST",
							url: "<?php echo e(URL::to('admin/ajax/log/getproductscoloreditorder')); ?>",
							data: {'product_name':product_name,'_token':_token,'selected_option':selected_option},
							success: function(msg)
							{ 	 
								/* alert(msg); */
								if(msg != ''){
									$('#product_color').html(msg);
									
								}else{
									$('#product_color').html('<option value="">No Data Found</option>');
								}
								//getProductStatus();
								//getProductBatch();
								//console.log(msg); 
							}
						});  
					}
					
					$('#product_name').change(function(){
						getProductColor();
						
					});
					
					$('#product_color').change(function(){
						 
						var datavarID=$('#product_color').find(':selected').attr('data-variation');
						var datacolor=$('#product_color').find(':selected').val();
						//alert(datacolor);
						 
						$('#ProductOrderTokenIDVar').val(datavarID);
						getProductStatus();
						
						 
					});	
					$('#productStatus').change(function(){
						 
						 
						var datastatus=$('#productStatus').find(':selected').val();
						//alert(datacolor);
						 
						 
						getProductBatch();
						
						 
					});
					$('#orderStatus').change(function(){
						var orderStatus=$('#orderStatus').find(':selected').val();
						if(orderStatus == 'booked in for delivery'){
							$('#datetd1').prop('required',true);
							var date =$('#datetd1').val();
							
							if(date == ''){
								//alert('empty');
								toastr.options = {closeButton:true,preventDuplicates:true}
								toastr.error('Please select Delivery Date !')
								$('#stockupdate').hide();
							}else{
								$('#stockupdate').show();
							}
						}else{
							$('#datetd1').prop('required',false);
							$('#stockupdate').show();
						}
					});
					$('#datetd1').change(function(){
						var date =$('#datetd1').val();
						var orderStatus=$('#orderStatus').find(':selected').val();
						if(date != ''){
							$('#orderStatus option[value="booked in for delivery"]').attr('selected','selected');
								
						}
						if(date == '' && orderStatus=='booked in for delivery'){
								//alert('empty');
								toastr.options = {closeButton:true,preventDuplicates:true}
								toastr.error('Please select Delivery Date !')
								$('#stockupdate').hide();
							}else{
								$('#stockupdate').show();
							}
					});
					getProductColor();
					getProductStatus();
					getProductBatch();
					
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
                                            format: 'DD/MM/YYYY',
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
                                            format: 'DD/MM/YYYY',
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
									url: "<?php echo e(URL::to('dealer/ajax/log/deleteorderaccessory')); ?>",
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>