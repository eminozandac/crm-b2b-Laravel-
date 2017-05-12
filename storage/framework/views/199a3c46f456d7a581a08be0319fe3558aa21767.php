
<?php $__env->startSection('pagecss'); ?>
	<link href="<?php echo e(asset('assets/css/bootstrap-select.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
	
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Special Order Details</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Special Order Details</strong>
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
							$itemTotal=0;
							$finalprice=0;
							 
						?>
                        <div class="ibox-content" style="min-height:70vh;">
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
											<td><?php echo e($orderData->product_color); ?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									 <h2>Attributes</h2>
										<?php 
											if(!empty($orderData->attribute)){
												$attrid=explode(',',$orderData->attribute);
												 
												for($i=0;$i<count($attrid);$i++){
													
														$attrVal=DB::table('attribute')->where('attributeID','=',$attrid[$i])->first();
														//print_r($attrVal);
														if(!empty($attrVal->attributeName)){
												?>
															<p><i class="fa fa-angle-right" aria-hidden="true">&nbsp;</i><?php echo e($attrVal->attributeName); ?> </p>
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
								<div class="claerfix"></div>
							<div class="col-md-12">	<hr/></div>
								<div class="claerfix"></div>
								<div class="col-md-12">
									<form action="<?php echo e(action('admin\ManageSpecialOrderController@adminUpdateSpecialOrder')); ?>" method="POST" enctype="multipart/form-data" class="productss" id="order_edit">
										<hr/>
										<div class="col-sm-12"><h2>Order Edit</h2><br/></div>
										<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
										<input type="hidden" name="orderToken" id="orderToken" value="<?php echo $orderID; ?>" />
									 
										 
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Product Name:</label>
											 
												<select data-placeholder="Choose a Product..." class="select2_demo_3 form-control" style="width: 100%"  tabindex="4" name="product_name"  required id="product_name">
													<option value="">Select Product</option>
													<?php 
														$getProducts=DB::table('products')->where('deleted_at','=',NULL)->get();
														foreach($getProducts as $getProduct){
														?>
															<option value="<?php echo base64_encode($getProduct->product_id); ?>" <?php if($getProduct->product_id==$orderData->product_id){echo 'selected';} ?>><?php echo e($getProduct->productName); ?></option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Product Color</label>
											
												<?php 
													$variationColor=array(
														'0' => 'none',
														'1' => 'Tuscan Sun',
														'2' => 'Pearl White',
														'3' => 'Sterling Silver',
														'4' => 'Tranquility',
														'5' => 'Storm Clouds',
														'6' => 'Cinnabar',
														'7' => 'Midnight Canyon',
														'8' => 'Winter Solstice',
														'9' => 'Cameo',
													);
													$variationColorThumb=array(
														'0' => 'none',
														'1' => 'tuscan',
														'2' => 'pearl',
														'3' => 'sterling',
														'4' => 'tranq',
														'5' => 'strom',
														'6' => 'cinnabar',
														'7' => 'midnight',
														'8' => 'winter',
														'9' => 'cameo',
														 
													);
													 
													 
													?>
												<select class="selectpicker form-control" required name="product_color" id="product_color_filter" >
													<option value="">Select Color</option>
													<?php
													$color=count($variationColor);
														for($i=0;$i<$color;$i++){
															if($variationColor[$i]==$orderData->product_color){$selected='selected="selected"';}else{$selected='';}
															if($variationColor[$i]=='none'){
																echo '<option '.$selected.' value="none">None</option>';
															}else{
																$thumb="assets/img/".$variationColorThumb[$i].".jpg";
																echo'<option '.$selected.' value="'.$variationColor[$i].'" data-thumbnail="'.URL::to($thumb).'">'.$variationColor[$i].'</option>';
															}
																
														}
													?>
												</select>
											</div>
                                 
										</div>
										 
										<div class="col-sm-4" id="">
											<div class="form-group">
												<label class="control-label" id="datelabel">Enter Batch Number :</label>
												<input type="text" class="form-control" name="productbatch" id="productbatch" value="<?php echo $orderData->productbatch ?>">
													 
													
												</select>
											</div>
										</div>
										
										<div class="col-sm-4" id="datebox">
											<div class="form-group">
												<label class="control-label" id="datelabel">In Production Date :</label>
												<input type="text"  id="datetd1" class="form-control datetd date" placeholder="DD-MM-YYYY" name="inproduction_date" value="<?php  date('d-m-Y',strtotime($orderData->inproduction_date)); ?>">
											</div>
										</div>
										
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Order type :</label>
												<select class="form-control" name="qtystatus" required id="qtystatus" >
													<option value="inproduction">In Production</option>
													 
												</select>
											</div>
										</div>
										
										<?php
											if($orderData->finance == 1){
										?>
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">Order Status :</label>
												<select class="form-control" name="orderStatus" required id="orderStatus" >
													<option value="">Select Status</option>
													<option value="finance new">Finance New</option>
													<option value="finance link sent">Finance Link Sent</option>
													<option value="finance accepted">Finance Accepted</option>
													<option value="finance verified"> Finance Verified</option>
													<option value="finance awaiting delivery slip">Finance awaiting delivery slip</option>
													<option value="finance completed">Finance Completed</option>
													<option value="Finance Declined">Finance Declined</option>
												</select>
											</div>
										</div>
											<?php }?>
										 
										<div class="col-sm-4">
											<div class="form-group">
												<label class="control-label">&nbsp;</label><br/>
												<input type="submit" class="btn btn-w-m btn-primary" id="stockupdate" value="Update" />
												 <a href="<?php echo e(URL::to('/admin/specialorderslist')); ?>" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Back </a>
											</div>
										</div>
									</form>
								</div>
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
		<script>
			$(function(){
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
					 
					
					function getProductColor(){
						var _token = $('#token').val();
						var product_name = $('#product_name').val();
						var selected_option='<?php echo $orderData->product_color; ?>';
						$.ajax
						({
							type: "POST",
							url: "<?php echo e(URL::to('admin/ajax/log/getproductscoloreditspeacilorder')); ?>",
							data: {'product_name':product_name,'_token':_token,'selected_option':selected_option},
							success: function(msg)
							{ 	 
								/* alert(msg); */
								if(msg != ''){
									$('#product_color').html(msg);
									
								}else{
									$('#product_color').html('<option value="">No Data Found</option>');
								}
								 
								 
							}
						});  
					}
					
					$('#product_name').change(function(){
						//getProductColor();
						
					});
					 
					getProductColor();
					 
					
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
                                }
                           
                            }
                    });
				});
			</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>