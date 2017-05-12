@extends('admin.layouts.masteradmin')
@section('pagecss')
 <link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop
@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Add Product</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                         <a href="{{URL::to('/admin/productList')}}">Products</a>
                    </li>
                    <li class="active">
                        <strong>Add Product</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tabs-container">
						<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#tab-1"> Product info</a></li>
							<li class=""><a data-toggle="tab" href="#tab-2"> Variation</a></li>
							<li class=""><a data-toggle="tab" href="#tab-4"> Images</a></li>
						</ul>
						<div class="tab-content">
							<div id="tab-1" class="tab-pane active">
								<div class="panel-body">
									<form action="{{action('admin\ProductController@addProductsDB')}}" method="POST" enctype="multipart/form-data" class="form_product" id="form_product">
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
										<fieldset class="form-horizontal">
											<div class="form-group"><label class="col-sm-2 control-label">Name:</label>
												<div class="col-sm-10"><input type="text" name="productName" class="form-control" placeholder="Product name"></div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Category:</label>
												<div class="col-sm-10">
													<select class="form-control" name="category_id" id="category_id">
														<option value="">Select Category</option>
														<?php 
														$category=DB::table('category')->get();
														foreach($category as $cat){
															echo '<option value="'.$cat->id.'">'.$cat->categoryName.'</option>';
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group"><label class="col-sm-2 control-label">Brand:</label>
												<div class="col-sm-10">
													<select class="form-control" name="brand_id" id="brand_id">
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
											<!--<div class="form-group">
												<label class="col-sm-2 control-label">Batch No :</label>
												<div class="col-sm-10">
													<input type="text" required class="form-control" name="batch" placeholder="Batch No." >
												</div>
											</div>-->
										 
											<div class="form-group">
												<label class="col-sm-2 control-label">  Price:</label>
												<div class="col-sm-10">
													<input type="text" name="real_price" id="real_price" class="form-control" placeholder="Enter Price">
												</div>
											</div>
									 
											<!--<div class="form-group">
												<label class="col-sm-2 control-label"> Sale Price:</label>
												<div class="col-sm-10">
													<input type="text" name="sale_price" id="sale_price" class="form-control" placeholder="Enter Sale Price">
												</div>
											</div>-->
											 
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
													<textarea class="summernote" name="description"></textarea>
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
							name: {
								validators: {
									notEmpty: {
										message: 'Enter name!'
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
							batch: {
								validators: {
									notEmpty: {
										message: 'Enter batch no!'
									}
								}
							},
							product_staus: {
								validators: {
									notEmpty: {
										message: 'Select Product Status !'
									}
								}
							},
							productName: {
								validators: {
									notEmpty: {
										message: 'Enter Product Name !'
									}
								}
							}, 
							
							brand_id: {
								validators: {
									notEmpty: {
										message: 'Select Brand!'
									}
								}
							},
							category_id: {
								validators: {
									notEmpty: {
										message: 'Select Categoty!'
									}
								}
							}, 
							product_color: {
								validators: {
									notEmpty: {
										message: 'Select Color!'
									}
								}
							},
							productimage:{
								validators: {
									notEmpty: {
										message: 'Select 800x800 Image !'
									},
									file: {
										extension: 'jpeg,jpg,png',
										type: 'image/jpeg,image/png',
										maxSize: 102400,   // 100 kb
										message: 'Select jpg,jpeg,png less than 100kb File !'
									}
								}
							},
							group: {
								validators: {
									notEmpty: {
										message: 'Select group!'
									}
								}
							},
							discountPer: {
								validators: {
									notEmpty: {
										message: 'Enter Discount!'
									}
								}
							},
						}
					});
            });
            </script>
			<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
        @stop()