
<?php $__env->startSection('pagecss'); ?>
 <link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Edit Accessory</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                         <a href="<?php echo e(URL::to('/admin/accessorylist')); ?>">Accessory</a>
                    </li>
                    <li class="active">
                        <strong>Edit Accessory</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tabs-container">
						<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#tab-1"> Accessory info</a></li>
						 
						</ul>
						<div class="tab-content">
							<div id="tab-1" class="tab-pane active">
								<div class="panel-body">
								<?php 
									$accessoryData=DB::table('product_accessories')->where('accessoryID','=',base64_decode($accessoryID))->first();
								?>
									<form action="<?php echo e(action('admin\AccessoryController@updateAccessory')); ?>" method="POST" enctype="multipart/form-data" class="form_product" id="form_product">
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
										<input type="hidden" name="accessory" value="<?php echo $accessoryID; ?>"/>
										<fieldset class="form-horizontal">
											<div class="form-group"><label class="col-sm-2 control-label">Name:</label>
												<div class="col-sm-10"><input type="text" name="accessory_name" class="form-control" placeholder="Accessory name" value="<?php echo $accessoryData->accessory_name; ?>"></div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Category:</label>
												<div class="col-sm-10">
													<select class="form-control" name="category" id="category">
														<option value="">Select Category</option>
														<?php 
														$category=DB::table('accessory_category')->where('deleted_at','=',NULL)->get();
														foreach($category as $cat){
															if($accessoryData->category_id == $cat->id){$selected='selected=selected';}else{$selected='';}
															echo '<option '.$selected.' value="'.$cat->id.'">'.$cat->categoryName.'</option>';
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Brand:</label>
												<div class="col-sm-10">
													<select class="form-control" name="brand" id="brand">
														<option value="">Select Brand</option>
														<?php 
														$brand=DB::table('brand')->get();
														foreach($brand as $brandlist){
															if($accessoryData->brand_id == $brandlist->id){$selected='selected=selected';}else{$selected='';}
															
															echo '<option '.$selected.' value="'.$brandlist->id.'">'.$brandlist->brandName.'</option>';
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Price:</label>
                                                <div class="col-sm-10"><input type="text" name="price" class="form-control" value="<?php echo $accessoryData->price; ?>" placeholder="Accessory price"></div>
                                            </div>
											<div class="form-group"><label class="col-sm-2 control-label">SKU:</label>
                                                <div class="col-sm-10"><input type="text" value="<?php echo $accessoryData->sku; ?>" placeholder="SKU" name="sku" class="form-control"></div>
                                            </div>
											<div class="form-group"><label class="col-sm-2 control-label">Warehouse Location:</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="warehouse" placeholder="Warehouse Location" value=" <?php echo $accessoryData->warehouse; ?>" /> 
												</div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Qty:</label>
                                                <div class="col-sm-10"><input type="text" name="accessory_qty" class="form-control" placeholder="Accessory stock qty"  value="<?php echo $accessoryData->accessory_qty; ?>"></div>
                                            </div>
											 <?php 
											 
												if(!empty($accessoryData->accessory_image)){
													 
														$cavatar='uploads/accessories/'.$accessoryData->accessory_image;
													} else{
														$cavatar='assets/img/placeholder300x300.png';
													}
												
												?>
											<div class="col-sm-2 col-sm-offset-2">
												<img src="<?php echo e(URL::to($cavatar)); ?>" class="img-responsive"/>
											</div>
											<div class="clearfix"></div><br/><br/>
											<div class="form-group"><label class="col-sm-2 control-label">Add newImage:</label>
                                                <div class="col-sm-6"><input type="file" name="accessory_image" class="form-control"></div>
                                            </div>
											<div class="form-group"><label class="col-sm-2 control-label">Enable / Disable:</label>
                                                <div class="col-sm-10">
												   <div class="switch">
														<div class="onoffswitch">
															<input type="checkbox"  <?php if($accessoryData->visibility =='1'){echo 'checked';}else{} ?> class="onoffswitch-checkbox" name="visibility" id="example1">
															<label class="onoffswitch-label" for="example1">
																<span class="onoffswitch-inner"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
													</div>
                                                </div>
                                            </div>
											 
											<div class="form-group"><label class="col-sm-2 control-label">Description:</label>
												<div class="col-sm-10">
													<textarea class="summernote" name="accessory_description"><?php echo $accessoryData->accessory_description; ?></textarea>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-offset-2">
													<input type="submit" class="btn btn-primary" value="Save" >
												</div>
											</div>
										</fieldset>
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
			
			<script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>
            
			
            <script type="text/javascript">
            $(function(){
				
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
					
                $('.summernote').summernote();
                var edit = function() {
                    $('.click2edit').summernote({focus: true});
                };
                var save = function() {
                    var aHTML = $('.click2edit').code(); //save HTML If you need(aHTML: array).
                    $('.click2edit').destroy();
                };
                $('#real_price').blur(function(){
                    var real =parseFloat($(this).val());
                    var sale =parseFloat($('#sale_price').val());
                    if(sale != ''){
                        if(real < sale){
                            toastr.options = {closeButton:true,preventDuplicates:true}
                            toastr.error('real price cannot be less then sale price');
                            $(this).val('');
                        }
                    }
                });
                $('#sale_price').blur(function(){
                    var sale=parseFloat($(this).val());
                    var real =parseFloat($('#real_price').val());
                    if(real != ''){
                        if(sale > real){
                            toastr.options = {closeButton:true,preventDuplicates:true}
                            toastr.error('sale price cannot be greater then real price');
                            $(this).val('');
                        }
                    }
                });
                $('#product_color').change(function(){
                    var color=$('#product_color option:selected').attr('data-color');
                    //alert(color);

                    $('#product_color').addClass(color)

                });
				
				
				$('#form_product').find('[name="dealerID[]"]')
					.change(function(e) {
						$('#form_product').formValidation('revalidateField', 'dealerID[]');
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
							'dealerID[]': {
								validators: {
									callback: {
										message: 'Please select customer',
										callback: function(value, validator, $field) {
											/* Get the selected options */
											var options = validator.getFieldElements('dealerID[]').val();
											return (options != null);
										}
									}
								}
							},
							accessory_name: {
								validators: {
									notEmpty: {
										message: 'Enter accessory name!'
									}
								}
							},
							
							discription: {
								validators: {
									notEmpty: {
										message: 'Enter discription!'
									}
								}
							},
							brand: {
								validators: {
									notEmpty: {
										message: 'Select brand!'
									}
								}
							},
							accessory_qty: {
								validators: {
									notEmpty: {
										message: 'Enter qty!'
									},
									integer: {
										message: 'Enter proper qty!'
									}
								}
							},
							price: {
								validators: {
									notEmpty: {
										message: 'Enter price!'
									},
									integer: {
										message: 'Enter proper price!'
									}
								}
							},
							accessory_image: {
								validators: {
									  
									file: {
										extension: 'jpeg,jpg,png',
										type: 'image/jpeg,image/png',
										maxSize: 1024000,   // 100 kb
										message: 'Select jpg,jpeg,png less than 1MB File !'
									}
								}
							},
							category: {
								validators: {
									notEmpty: {
										message: 'Select  category !'
									}
								}
							}
						}
					});
            });
            </script>
			<script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-form-validation.js')); ?>"></script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>