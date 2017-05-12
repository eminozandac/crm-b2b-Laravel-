@extends('admin.layouts.masteradmin')
@section('pagecss')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
@stop()
@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Update Variation</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
						<?php 
						$decodeID=base64_decode($id);
						//echo $decodeID ; exit;
						$varIDarr= explode("&", $decodeID, 2);
						$varID = $varIDarr[0];
						 
						$variationData=DB::table('variation')->where('variationID','=',$varID)->first();
						$link= '/admin/editProducts/'.base64_encode($variationData->product_id); 
						
						?>
                         <a href="{{URL::to($link)}}">Variations</a>
                    </li>
                    <li class="active">
                        <strong>Update Variation</strong>
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
							<div id="tab-2" lass="tab-pane active">
								<div class="panel-body">
								<?php // print_r($variationData); ?>
									<form action="{{action('admin\ProductController@updateVariation')}}" method="POST" enctype="multipart/form-data" class="form-horizontalssffg" id="variationEdit">
								 
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
										<input type="hidden" name="variationToken" value="<?php echo base64_encode($variationData->variationID); ?>" />
										<input type="hidden" name="productToken" value="<?php echo base64_encode($variationData->product_id); ?>" />
										<fieldset class="form-horizontal">
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Product Status:</label>
													<select class="form-control product_staus" required name="product_status">
														<option <?php if($variationData->product_status =="instock") {echo "selected=selected";}?> value="instock">In Stock</option>
														<option  <?php if($variationData->product_status =="inproduction") {echo "selected=selected";}?> value="inproduction">In Production</option>
														<option value="onseaukarrival" <?php if($variationData->product_status =="onseaukarrival") {echo "selected=selected";}?>>On Sea - UK Arrival</option>
														<option value="factorystock" <?php if($variationData->product_status =="factorystock") {echo "selected=selected";}?>>Factory Stock</option>
														<option value="outofstock" <?php if($variationData->product_status =="outofstock") {echo "selected=selected";}?> >Out of Stock</option>
													</select>
												</div>
											</div>
											<div class="col-sm-3" id="datetd1div">
												<div class="form-group">
													<label class="control-label">Date :</label>
													<?php if($variationData->stockdate !='0000-00-00' && $variationData->stockdate !=NULL){$date=date('Y-m-d',strtotime($variationData->stockdate));}else{$date='';} ?>
													<input type="text" required id="datetd1"  class="form-control datetd date" placeholder="mm-dd-yyyy" name="stockdate"  value="<?php if(!empty($date)){echo date('d-m-Y',strtotime($date)); }?>">
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
												<label class="control-label">Color :</label>
												<input type="hidden" name="product_color" value="<?php echo $variationData->product_color; ?>"/>
												<select class="selectpicker form-control" required name="product_colors" disabled id="product_color" >
												<option value="">Select Color</option>
												<option <?php if($variationData->product_color =="none") {echo "selected=selected";}?> value="none">None</option>
												
												<option <?php if($variationData->product_color =="Tuscan Sun") {echo "selected=selected";}?>  value="Tuscan Sun" data-thumbnail="{{asset('assets/img/tuscan.jpg')}}">Tuscan Sun</option>
										 
												<option <?php if($variationData->product_color =="Pearl White") {echo "selected=selected";}?>  value="Pearl White" data-thumbnail="{{asset('assets/img/pearl.jpg')}}">Pearl White</option>
												
												<option <?php if($variationData->product_color =="Sterling Silver") {echo "selected=selected";}?>  value="Sterling Silver" data-thumbnail="{{asset('assets/img/sterling.jpg')}}">Sterling Silver</option>
												
												<option <?php if($variationData->product_color =="Cameo") {echo "selected=selected";}?>  value="Cameo" data-thumbnail="{{asset('assets/img/cameo.jpg')}}">Cameo</option>
												
												<option <?php if($variationData->product_color =="Tranquility") {echo "selected=selected";}?>  value="Tranquility" data-thumbnail="{{asset('assets/img/tranq.jpg')}}">Tranquility</option>
												
												<option <?php if($variationData->product_color =="Storm Clouds") {echo "selected=selected";}?>  value="Storm Clouds" data-thumbnail="{{asset('assets/img/strom.jpg')}}">Storm Clouds</option>
												
												<option <?php if($variationData->product_color =="Cinnabar") {echo "selected=selected";}?>  value="Cinnabar" data-thumbnail="{{asset('assets/img/cinnabar.jpg')}}">Cinnabar</option>
												
												<option <?php if($variationData->product_color =="Midnight Canyon") {echo "selected=selected";}?>  value="Midnight Canyon" data-thumbnail="{{asset('assets/img/midnight.jpg')}}">Midnight Canyon</option>
												
												<option <?php if($variationData->product_color =="Winter Solstice") {echo "selected=selected";}?>  value="Winter Solstice" data-thumbnail="{{asset('assets/img/winter.jpg')}}">Winter Solstice</option>
												
												<option <?php if($variationData->product_color =="Blue") {echo "selected=selected";}?>  value="Blue" data-thumbnail="{{asset('assets/img/winter.jpg')}}">Blue</option>
												
												<option <?php if($variationData->product_color =="White with Grey Sides") {echo "selected=selected";}?>  value="White with Grey Sides" data-thumbnail="{{asset('assets/img/winter.jpg')}}">White with Grey Sides</option>
												
												<option <?php if($variationData->product_color =="White with Brown Sides") {echo "selected=selected";}?>  value="White with Brown Sides" data-thumbnail="{{asset('assets/img/winter.jpg')}}">White with Brown Sides</option>
												
												<option <?php if($variationData->product_color =="Black with Black Sides") {echo "selected=selected";}?>  value="Black with Black Sides" data-thumbnail="{{asset('assets/img/winter.jpg')}}">Black with Black Sides</option>
												<option <?php if($variationData->product_color =="Black with Grey Sides") {echo "selected=selected";}?>  value="Black with Grey Sides" data-thumbnail="{{asset('assets/img/winter.jpg')}}">Black with Grey Sides</option>
												
												</select>
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Model :</label>
													<input type="text" name="model" class="form-control" placeholder="Model" id="model" value="<?php if($variationData->model !='') {echo $variationData->model;}?>">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label class="control-label">Batch No :</label>
													<input type="text" required class="form-control" name="batch" placeholder="Batch No." value="<?php if($variationData->batch !='') {echo $variationData->batch;}?>" readonly>
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<label class="control-label">SKU:</label>
													<input type="text" name="sku" class="form-control" placeholder="SKU" value="<?php if($variationData->sku !='') {echo $variationData->sku;}?>">
												</div>
											</div>
											<div class="col-sm-2" id="productStockdiv">
												<div class="form-group">
													<label class="control-label"> Product Qty:</label>
													 <input type="text" name="productStock" required class="form-control" placeholder="1100" value="<?php if($variationData->productStock ){echo $variationData->productStock;}?>">
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
        </div>
		@stop()

        @section('pagescript')
		    @include('admin.includes.commonscript')
			
            <script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
            <script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
            <script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
			
			<script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
            <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>

            <script type="text/javascript">
            data_datechange = null;
            $(function(){
				 $('.date').datepicker({
					todayBtn: "linked",
					keyboardNavigation: false,
					forceParse: false,
					calendarWeeks: true,
					format: 'd-m-yyyy',
					autoclose: true
				});
				function checkstatus(){
				 var inprod=$('select[name="product_status"]').find(':selected').val();
				 
					if(inprod =='outofstock'){
						$('#productStockdiv').hide();
						 
					}else{
						$('#productStockdiv').show();
						 
					}
					if(inprod =='inproduction'  || inprod =='onseaukarrival'){
						$('#datetd1').prop('disabled',false);
						 
					}else{
						$('#datetd1').prop('disabled',true);
					}
				}
				$('select[name="product_status"]').change(function(){
					//var inprod=$(this).find(':selected').val();
					 checkstatus();
				 });
					checkstatus();
                    $('#product_color').change(function(){
                        var color=$('#product_color option:selected').attr('data-color');
                        //alert(color);

                        $('#product_color').addClass(color)

                    });

                     $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green',
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
                     
                   
					$('#variationEdit').find('[name="stockdate"]')
                            .change(function(e) {
                                $('#variationEdit').formValidation('revalidateField', 'stockdate');
                            })
                            .end().formValidation({
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