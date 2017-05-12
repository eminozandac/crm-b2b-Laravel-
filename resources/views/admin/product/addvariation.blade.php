@extends('admin.layouts.masteradmin')
@section('pagecss')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	
@stop()

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Add Product Variations</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                         <a href="{{URL::to('/admin/productList')}}">Products</a>
                    </li>
                    <li class="active">
                        <strong>Add Product Variations</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tabs-container">
						<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#tab-2"> Variation</a></li>
						</ul>
												
						<div class="tab-content">
							<div class="panel-body">
								<form action="{{action('admin\ProductController@addProductsVariationDirect')}}" method="POST" enctype="multipart/form-data" class="form-horizontalssffg" id="variationData">
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
									 
										<fieldset class="form-horizontal">
										<div class="col-sm-3">
											<div class="form-group">
												<label class="control-label">Product Name:</label>
												<select data-placeholder="Choose a Product..." class="select2_demo_3 form-control" style="width: 100%"  tabindex="4" name="product_name"  required id="product_name">
													<option value="">Select Product</option>
													<?php 
														$getProducts=DB::table('products')->where('deleted_at','=',NULL)->get();
														foreach($getProducts as $getProduct){
															echo '<option value="'.$getProduct->product_id.'">'.$getProduct->productName.'</option>';
														}
													?>
												</select>
											</div>
										</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Product Status:</label>
													 <select class="form-control product_staus" required name="product_status">
														<option value="instock">In Stock</option>
														<option value="inproduction">In Production</option>
														<option value="onseaukarrival">On Sea - UK Arrival</option>
														<option value="factorystock">Factory Stock</option>
													</select>
												</div>
											</div>
											<div class="col-sm-3" >
												<div class="form-group">
													<label class="control-label">Date :</label>
													<input type="text" required id="datetd1"  class="form-control datetd date" placeholder="dd-mm-yyyy" name="stockdate"  disabled="disabled">
												</div>
											</div>
											<!--<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Color :</label>
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
															'10' => 'Blue',
															'11' => 'White with Grey Sides',
															'12' => 'White with Brown Sides',
															'13' => 'Black with Black Sides',
															'14' => 'Black with Grey Sides',
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
															'10' => 'blue',
															'11' => 'sterling',
															'12' => 'sterling',
															'13' => 'black',
															'14' => 'black',
															 
														);
														 
														?>
													<select class="selectpicker form-control" required name="product_color" id="product_color" >
													<option value="">Select Color</option>
													<?php
													$color=count($variationColor);
														for($i=0;$i<$color;$i++){
															if($variationColor[$i]=='none'){
																echo '<option  value="none">None</option>';
															}else{
																$thumb="assets/img/".$variationColorThumb[$i].".jpg";
																echo'<option  value="'.$variationColor[$i].'" data-thumbnail="'.URL::to($thumb).'">'.$variationColor[$i].'</option>';
															}
																
														}
													?>
														
													</select>
												</div>
											</div>-->
											<div class="clearfix"></div>
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Batch No :</label>
													<input type="text" required class="form-control" name="batch" placeholder="Batch No." >
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Model :</label>
													<input type="text" name="model" class="form-control" placeholder="Model" id="model">
												</div>
											</div>
											
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">SKU:</label>
													<input type="text" name="sku" class="form-control" placeholder="SKU">
												</div>
											</div>
											<!--<div class="col-sm-3" id="productStockdiv">
												<div class="form-group">
													<label class="control-label"> Product Qty:</label>
													<input type="text"  name="productStock" required class="form-control" placeholder="Product Qty">
												</div>
											</div>-->
											<div class="clearfix"></div>
											 <div class="col-sm-12" id="productStockdiv">
												<div class="form-group">
													<label class="control-label"> Enter Stock:</label>
													<div class="clearfix"></div>
													
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
															'10' => 'Blue',
															'11' => 'White with Grey Sides',
															'12' => 'White with Brown Sides',
															'13' => 'Black with Black Sides',
															'14' => 'Black with Grey Sides',
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
															'10' => 'blue',
															'11' => 'sterling',
															'12' => 'sterling',
															'13' => 'black',
															'14' => 'black',
															 
														);
														 
													$color=count($variationColor);
														for($i=0;$i<$color;$i++){
															if($i==0){
																echo '<div class="col-md-6">';
															}
																echo'<div class="col-sm-6 col-xs-6 pl0"><div class="colorsOfProducts">' ;
															if($variationColor[$i]=='none'){
																echo '<span>None</span><input type="hidden" name="colorName[]" value="none">';
															}else{
																$thumb="assets/img/".$variationColorThumb[$i].".jpg";
																 
																	echo '<img src="'.URL::to($thumb).'" class="color-img-icon"/><span>'.$variationColor[$i].'</span><input type="hidden" name="colorName[]" value="'.$variationColor[$i].'">';
															}
																
															echo '</div></div><div class="col-sm-6 col-xs-6"><div class="form-group"><input type="text" class="form-control" name="colorStock[]" placeholder="Enter Stock"/></div></div><div class="clearfix"></div>';
															if($i==7){
																echo '</div><div class="col-md-6">';
															}
															if($i==$color - 1 ){
																echo '</div>';
															}
														}
													?>
													
												</div>
											</div>
											
											<div class="clearfix"></div>
											<div class="col-sm-2">
												<div class="form-group">
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
		@stop()

        @section('pagescript')
		@include('admin.includes.commonscript')

            <script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
            <script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
			
			<script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
            <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
			
			<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
			
			<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>

            <script type="text/javascript">
            data_datechange = null;
            $(function()
			{		
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
					
					
					
					function checkstatus(){
					 var inprod=$('select[name="product_status"]').find(':selected').val();
						if(inprod =='outofstock'){
							$('#productStockdiv').hide();
							 
						}else{
							$('#productStockdiv').show();
						}
						if(inprod == 'inproduction'){
							$('#datetd1div').show();
						}else{
							$('#datetd1div').hide();
						}
					}
					checkstatus();
					
				
                     $('select[name="product_attributes"]').change(function(){
                        var inprod=$(this).find(':selected').text();
						//alert(inprod);
						$('#attr_val').attr('placeholder',inprod);
								 
                     });
					$('select[name="product_status"]').change(function(){
                        var inprod=$(this).find(':selected').val();
						//alert(inprod);
						var dateboxid=$(this).parent().parent().next().find('input.datetd').attr('id');
                            if(inprod =='inproduction' || inprod =='onseaukarrival'){
                            	//console.log($(this).parent().parent().next().find('input.datetd').val());
								//alert(dateboxid);
                                $('#'+dateboxid).prop('disabled',false);
								$('#datetd1div').show();
                            }else{
                                $("#"+dateboxid).prop('disabled',true);
                                $("#"+dateboxid).val('');
								$('#datetd1div').hide();
                            }
							if(inprod =='outofstock'){
								$('#productStockdiv').hide();
								 
								$('#datetd1div').hide();
							}else{
								$('#productStockdiv').show();
								 
								 
							}
                     });
                     $('#vargroup').on("click",".vardel", function(e){
                        e.preventDefault(); $(this).parent().parent('tr').remove();
                        counter--;
                        //totalcost -= prcprice;
                     });

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

                     $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green',
                    });

                       $('.date').datepicker({
                        todayBtn: "linked",
                        keyboardNavigation: false,
                        forceParse: false,
                        calendarWeeks: true,
						format: 'd-m-yyyy',
                        autoclose: true
                    });
					
					 var customer_table = $('#variation_tables').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
						buttons: [
                            /* {extend: 'csv', title:'Customer Details'},
                            {extend: 'excel', title:'Customer Details'},
                            {extend: 'pdf', title:'Customer Details'}, */
                        ],
                        
                       

                    }); 
					$('#variationData').find('[name="stockdate"]')
                            .change(function(e) {
                                $('#variationData').formValidation('revalidateField', 'stockdate');
                            })
                            .end()
						.formValidation({
						message: 'This value is not valid',
						icon: {
							valid: 'glyphicon glyphicon-ok',
							invalid: 'glyphicon glyphicon-remove',
							validating: 'glyphicon glyphicon-refresh'
						},
						fields: {
							product_name: {
								validators: {
									notEmpty: {
										message: 'Select product name!'
									}
								}
							},
							stockdate: {
								 validators: {
									notEmpty: {
										message: 'The date is required'
									},
									date: {
										format: 'DD-MM-YYYY',
										message: 'The date is not a valid'
									}
								}
							},
							'colorStock[]': {
								validators: {
									numeric: {
										message: 'Enter Valid product Stock !'
									}
								}
							},
							product_attributes: {
								validators: {
									notEmpty: {
										message: 'Select product Attributes!'
									}
								}
							},
							attr_val: {
								validators: {
									notEmpty: {
										message: 'Enter Attribute Value!'
									}
								}
							},
							product_staus: {
								validators: {
									notEmpty: {
										message: 'Select product staus!'
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
							 
							productStock: {
								validators: {
									notEmpty: {
										message: 'enter product Stock!'
									},
									numeric: {
										message: 'Enter Valid product Stock !'
									}
								},
							},
							real_price: {
								validators: {
									
									numeric: {
										message: 'Enter Valid price !'
									},
								}
							},
							sale_price: {
								validators: {
									numeric: {
										message: 'Enter Valid price !'
									},
								}
							}
							
						}
					});
									 
						   
            });

            </script>
        @stop()