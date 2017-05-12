@extends('admin.layouts.masteradmin')
@section('pagecss')
 <link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop
@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Add Accessory</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                         <a href="{{URL::to('/admin/accessorylist')}}">Accessory</a>
                    </li>
                    <li class="active">
                        <strong>Add Accessory</strong>
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
									<form action="{{action('admin\AccessoryController@addAccessoryDB')}}" method="POST" enctype="multipart/form-data" class="form_product" id="form_product">
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
										<fieldset class="form-horizontal">
											<div class="form-group"><label class="col-sm-2 control-label">Name:</label>
												<div class="col-sm-10"><input type="text" name="accessory_name" class="form-control" placeholder="Accessory name"></div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Category:</label>
												<div class="col-sm-10">
													<select class="form-control" name="category" id="category">
														<option value="">Select Category</option>
														<?php 
														$category=DB::table('accessory_category')->where('deleted_at','=',NULL)->get();
														foreach($category as $cat){
															echo '<option value="'.$cat->id.'">'.$cat->categoryName.'</option>';
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
															echo '<option value="'.$brandlist->id.'">'.$brandlist->brandName.'</option>';
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Price:</label>
                                                <div class="col-sm-10"><input type="text" placeholder="Accessory price" name="price" class="form-control"></div>
                                            </div>
											<div class="form-group"><label class="col-sm-2 control-label">SKU:</label>
                                                <div class="col-sm-10"><input type="text" placeholder="SKU" name="sku" class="form-control"></div>
                                            </div>
											<div class="form-group"><label class="col-sm-2 control-label">Warehouse Location:</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="warehouse" placeholder="Warehouse Location" /> 
												</div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Qty:</label>
                                                <div class="col-sm-10"><input type="text" placeholder="Accessory stock qty" name="accessory_qty" class="form-control"></div>
                                            </div>
											<div class="form-group"><label class="col-sm-2 control-label">Image:</label>
                                                <div class="col-sm-6"><input type="file" name="accessory_image" class="form-control"></div>
                                            </div>
											<div class="form-group"><label class="col-sm-2 control-label">Enable / Disable:</label>
                                                <div class="col-sm-10">
												   <div class="switch">
														<div class="onoffswitch">
															<input type="checkbox" checked class="onoffswitch-checkbox" name="visibility" id="example1">
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
													<textarea class="summernote" name="accessory_description"></textarea>
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
		@stop()

        @section('pagescript')
		    @include('admin.includes.commonscript')
			
			<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
            
			
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
										maxSize: 102400*20,   // 100 kb
										message: 'Select jpg,jpeg,png less than 100kb File !'
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
			<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
        @stop()