@extends('admin.layouts.masteradmin')
@section('pagecss')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	
@stop()

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Update Product  (<?php echo $productData->productName;?>)</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                         <a href="{{URL::to('/admin/productList')}}">Products</a>
                    </li>
                    <li class="active">
                        <strong>Update Product</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tabs-container">
						<ul class="nav nav-tabs" id="myTab">
							<li class=" <?php if(Session::get('variation') || Session::get('attribute')){}else{echo 'active';} ?>"><a data-toggle="tab" href="#tab-1"> Product info</a></li>
							<li class="<?php if(Session::get('variation')){echo 'active';} ?>"><a data-toggle="tab" href="#tab-2"> Variation</a></li>
							<li class=""><a data-toggle="tab" href="#tab-5"> Attributes</a></li>
							<!--<li class=""><a data-toggle="tab" href="#tab-3"> Discount</a></li>-->
							<li class=""><a data-toggle="tab" href="#tab-4"> Images</a></li>
						</ul>
						<div class="tab-content">
							<div id="tab-1" class="tab-pane <?php if(Session::get('variation') || Session::get('attribute')){}else{echo 'active';} ?>">
							
								<div class="panel-body">
                                    <form action="{{action('admin\ProductController@updateProductsinfo')}}" method="POST" enctype="multipart/form-data" class="products" id="">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                                        <input type="hidden" name="productToken" value="<?php echo base64_encode($productData->product_id); ?>"/>
                                        <fieldset class="form-horizontal">
                                            <div class="form-group"><label class="col-sm-2 control-label">Name:</label>
                                                <div class="col-sm-10"><input type="text" name="productName" class="form-control" placeholder="Product name" value="<?php echo $productData->productName;?>"></div>
                                            </div>
                                            <div class="form-group"><label class="col-sm-2 control-label">Category:</label>
                                                <div class="col-sm-10">
												
                                                <select class="form-control" name="category_id" id="category_id">
                                                    <option value="">Select Category</option>
													
                                                    <?php
                                                    $category=DB::table('category')->get();

                                                    foreach($category as $cat){
                                                        if($productData->category_id == $cat->id){$selected='selected=selected';}else{$selected='';}
                                                        echo '<option '.$selected.' value="'.$cat->id.'">'.$cat->categoryName.'</option>';
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
																if($productData->brand_id == $brandlist->id){$selected='selected=selected';}else{$selected='';}
																echo '<option '.$selected.' value="'.$brandlist->id.'">'.$brandlist->brandName.'</option>';
															}
														?>
													</select>
                                                </div>
                                            </div>
										 
											<div class="form-group"><label class="col-sm-2 control-label">Price:</label>
												<div class="col-sm-10">
													<input type="text" name="real_price" id="real_price" class="form-control" placeholder="Enter Price" value="<?php echo $productData->real_price;?>">
												</div>
											</div>
									 
											<!--<div class="form-group">
												<label class="col-sm-2 control-label"> Sale Price:</label>
												<div class="col-sm-10">
													<input type="text" name="sale_price" id="sale_price" class="form-control" placeholder="Enter Sale Price" value="<?php echo $productData->sale_price;?>">
												</div>
											</div>-->
											<div class="form-group"><label class="col-sm-2 control-label">Enable / Disable:</label>
                                                <div class="col-sm-10">
												   <div class="switch">
														<div class="onoffswitch">
															<input type="checkbox" <?php if($productData->visibility =='1'){echo 'checked';}else{} ?> class="onoffswitch-checkbox" name="visibility" id="example2">
															<label class="onoffswitch-label" for="example2">
																<span class="onoffswitch-inner"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
														 
													</div>
                                                </div>
                                            </div>
											 
                                            
                                            <div class="form-group"><label class="col-sm-2 control-label">Description:</label>
                                                <div class="col-sm-10">
                                                   <textarea class="summernote note-codable" name="description"><?php echo $productData->description;?></textarea>
													  
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
							<div id="tab-2" class="tab-pane <?php if(Session::get('variation')){echo 'active';} ?>">
								<div class="panel-body">
							
									<form action="{{action('admin\ProductController@addProductsVariation')}}" method="POST" enctype="multipart/form-data" class="form-horizontalssffg" id="attributes">
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
										<input type="hidden" name="productToken" value="<?php echo base64_encode($productData->product_id); ?>"/>
										<fieldset class="form-horizontal">
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
											<div class="col-sm-3" id="datetd1div">
												<div class="form-group">
													<label class="control-label">Date :</label>
													<input type="text" required id="datetd1"  class="form-control datetd date" placeholder="dd-mm-yyyy" name="stockdate"  disabled="disabled">
												</div>
											</div>
											<div class="col-sm-3">
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
											</div>
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
											<div class="clearfix"></div>
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">SKU:</label>
													<input type="text" name="sku" class="form-control" placeholder="SKU">
												</div>
											</div>
											<div class="col-sm-3" id="productStockdiv">
												<div class="form-group">
													<label class="control-label"> Product Qty:</label>
													<input type="text"  name="productStock" required class="form-control" placeholder="1100">
												</div>
											</div>
											<div class="clearfix"></div>
											 
											
											<div class="clearfix"></div>
											<div class="col-sm-2">
												<div class="form-group">
													<input type="submit" class="btn btn-primary" value="Save" >
												</div>
											</div>
										</fieldset>
									</form>
										
									<div class="clearfix"><hr/><br/></div>
									<div class="table-responsive">
										<table class="table table-striped table-bordered table-hover dataTables-example" id="prodcut_tables" style="width:100%;" >
											<thead>
												<tr>
													<th >
														Status
													</th>
													<th>
														Date
													</th>
													 
												   <th >
													   Color
													</th>
													<th>
													   Batch
													</th>
													<th>
														Product Qty
													</th>
												 
													<th>
														Action
													</th>
												</tr>
											</thead>
											<tbody>
											<?php
												$variations=DB::table('variation')->where('product_id','=',$productData->product_id)->where('productStock','>',0)->where('deleted_at','=',NULL)->orderBy('variationID','asc')->get();
												 
												foreach($variations as $data){
												
												
											?>
												<tr>
													<td>
														<?php 
														if($data->product_status != ''){
															if($data->product_status == 'instock'){
																if($data->productStock > 0){
																echo '<label class="label label-info"> In Stock</label>';
																	
																}else{
																	echo '<label class="label label-danger"> Out of Stock</label>';
																}
															}elseif($data->product_status == 'inproduction'){
																if($data->productStock > 0){
																echo '<label class="label label-success"> In Production</label>';
																	
																}else{
																	echo '<label class="label label-danger"> Out of Stock</label>';
																}
															}else if($data->product_status == 'onseaukarrival'){
																if($data->productStock > 0){
																echo '<label class="label label-success"  style="background-color: #029dff;"> On Sea - UK Arrival</label>';
																	
																}else{
																	echo '<label class="label label-danger"> Out of Stock</label>';
																}
															}else if($data->product_status == 'factorystock'){
																if($data->productStock > 0){
																echo '<label class="label label-primary"> Factory Stock</label>';
																	
																}else{
																	echo '<label class="label label-danger"> Out of Stock</label>';
																}
															}else{
																echo '<label class="label label-danger"> Out of Stock</label>';
															}
															// return $data->product_status;
														}else{
															echo '---';
														} 
														 
														?>
													</td>
													<td><?php   
													/* if(!empty($data->stockdate)){
														echo date('d-m-Y',strtotime($data->stockdate));
													}else{
														echo '--';
													} */
														$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $data->variationID)->orderBy('updated_at','DESC')->first();
														//print_r($getDateTranz->product_status);exit;
														 if(!empty($getDateTranz)){
															if($getDateTranz->product_status =='onseaukarrival' || $getDateTranz->product_status =='inproduction'){
																echo date('d-m-Y',strtotime($getDateTranz->stockdate));
															}else{
																$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $data->variationID)->orderBy('variationTranzToken','DESC')->first();
																if(!empty($getDateTranzS)){
																	if(!empty($getDateTranzS->stockdate)){ 
																	echo date('d-m-Y',strtotime($getDateTranzS->stockdate));
																	}else{
																		echo '--';
																	}
																	
																}else{
																	if($data->stockdate != '' && $data->stockdate != '0000-00-00'){
																		echo date('d-m-Y',strtotime($data->stockdate));
																	}else{
																		echo '---';
																	}
																}
															}
														}else{
															echo '---';
														}   
														
														?>
													</td>
													 
													<td><?php 
														if($data->product_color != ''){
															echo $data->product_color;
														}else{
															echo '---';
														}
													?></td>
													<td><?php
														if($data->batch != ''){
															echo $data->batch;
														}else{
															echo '---';
														}
													?></td>
													<td><?php
													
														if($data->productStock != ''){
															echo $data->productStock;
														}else{
															echo '0';
														} 
													?></td>
													 
													<td><?php 
														$url = URL::to('admin/editvariation', base64_encode($data->variationID.'&'.$data->product_id));
														//$delurl = URL::to('admin/deletevariation', base64_encode($data->variationID.'&'.$data->product_id));
														$idprd="'".base64_encode($data->variationID.'&'.$data->product_id)."'";
													 
														 echo '<a href="'.$url.'"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a><a  href="javascript:void(0)" onclick="removedata('.$idprd.')" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>'; 
														 
													?></td>
													 
												</tr>	
												<?php
													
												}
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div id="tab-4" class="tab-pane">
								<div class="panel-body">
                                    <form action="{{action('admin\ProductController@updateProductimage')}}" method="POST" enctype="multipart/form-data" class="products" id="">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                                        <input type="hidden" name="productToken" value="<?php echo base64_encode($productData->product_id); ?>"/>
                                        <fieldset class="form-horizontal">
										<?php 
										$image=DB::table('productimages')->where('product_id','=',$productData->product_id)->first();
										if(!empty($image->productimage)){
											 
												$cavatar='uploads/products/'.$image->productimage;
											} else{
												$cavatar='assets/img/placeholder300x300.png';
											}
										
										?>
											<div class="col-sm-2 col-sm-offset-2">
												<img src="{{URL::to($cavatar)}}" class="img-responsive"/>
											</div>
											<div class="clearfix"></div><br/><br/>
                                            <div class="form-group"><label class="col-sm-2 control-label">Image:</label>
                                                <div class="col-sm-6"><input type="file" name="productimage" class="form-control"></div>
                                            </div>
											<div class="clearfix"></div>
											<div class="col-sm-2 col-sm-offset-2">
												<div class="form-group">
													<input type="submit" class="btn btn-primary" value="Save" >
												</div>
											</div>
										</fieldset>
									</form>
								</div>
							</div>
							<div id="tab-5" class="tab-pane <?php if(Session::get('attribute')){echo 'active';} ?>">
								<div class="panel-body">
									<form action="{{action('admin\ProductController@addproductsattributes')}}" method="POST" enctype="multipart/form-data" class="form-horizontalssffg" id="attributes">
										<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
										 <input type="hidden" name="productToken" value="<?php echo base64_encode($productData->product_id); ?>"/>
										<fieldset class="form-horizontal">
											<div class="col-sm-3">
												<div class="form-group">
													<label class="control-label">Attributes:</label>
													 <select class="form-control" required name="product_attributes" id="product_attributes">
													 <option value="">Select Attribute</option>
													 <?php	 $attrs=DB::table('attribute')->get();
													 foreach($attrs as $attr){
													 ?>
														<option value="<?php echo $attr->attributeID; ?>"><?php echo $attr->	attributeName; ?></option>
													<?php 
													 }
													?>													
													</select>
												</div>
											</div>
										 
											 
											<div class="col-sm-2">
												<div class="form-group">
												<label class="control-label">&nbsp;</label><br/>
													<input type="submit" class="btn btn-primary" value="Save" >
												</div>
											</div>
										</fieldset>
									</form>
									<div class="clearfix"><hr/><br/></div>
									<div class="table-responsive">
										<table class="table table-striped table-bordered table-hover dataTables-example" id="" style="width:100%;" >
											<thead>
												<tr>
													<th >
														Attribute
													</th>
													 
													<th>
														Action
													</th>
												</tr>
											</thead>
											<tbody>
												<?php 
													$attrs=DB::table('product_attribute')->where('product_id','=',$productData->product_id)->get();
													foreach($attrs as $attr){
														$attrsName=DB::table('attribute')->where('attributeID','=',$attr->attributeID)->first();
													//	$delurl = URL::to('admin/deleteatributes', base64_encode($attr->product_attributeID.'&'.$productData->product_id));
													$idprdattr="'".base64_encode($attr->product_attributeID.'&'.$productData->product_id)."'";
														$action='<a href="javascript:void(0)" onclick="removeattrdata('.$idprdattr.')" data-toggle="tooltip" title="Delete" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>';
													?>	
												<tr>
													<td>{{$attrsName->attributeName}}</td>
												 
													<td><?php echo $action; ?></td>
												</tr>
													<?php
													}
												?>
											</tbody>
										</table>
									</div>
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

            <script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
            <script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
			
			<script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
            <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
			
			<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
			
			<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
<script type="text/javascript">
		$(document).ready(function()
		{
			$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
				localStorage.setItem('activeTab', $(e.target).attr('href'));
			});
			var activeTab = localStorage.getItem('activeTab');
			if(activeTab){
				$('#myTab a[href="' + activeTab + '"]').tab('show');
			}
		});
	</script>
            <script type="text/javascript">
            data_datechange = null;
            $(function()
			{		function checkstatus(){
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
					
					$('.dataTables-example').DataTable({
						dom: '<"html5buttons"B>lTfgitp',
						buttons: [
							/* { extend: 'copy'},
							{extend: 'csv'},
							{extend: 'excel', title: 'ExampleFile'},
							{extend: 'pdf', title: 'ExampleFile'},
							*/
							/* {extend: 'print',
							 customize: function (win){
									$(win.document.body).addClass('white-bg');
									$(win.document.body).css('font-size', '10px');

									$(win.document.body).find('table')
											.addClass('compact')
											.css('font-size', 'inherit');
							}
							} */
						]

					});
					 
				 
					$('#attributes').find('[name="stockdate"]')
                            .change(function(e) {
                                $('#attributes').formValidation('revalidateField', 'stockdate');
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
								}
							}
						});
				function removeData(valid){
				
				var product= valid;
				if( product != ''){
				//console.log(accssory);
					 swal({
						title: "Are you sure?",
						text: "This product Will be deleted?",
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
							 
							 window.location.replace("{{ URL::to('admin/deletevariation') }}/"+product);
							 
						}	 
					}); 
				}
			}
			
			deleted_data = removeData;
			function removeattrdata(valid){
				
				var productattr= valid;
				if( productattr != ''){
				//console.log(accssory);
					 swal({
						title: "Are you sure?",
						text: "This attribute Will be deleted?",
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
							 
							 window.location.replace("{{ URL::to('admin/deleteatributes') }}/"+productattr);
							 
						}	 
					}); 
				}
			}
			deleted_attrdata = removeattrdata;
						   
            });
		function removedata(valID)
		{
			if(valID != '' && valID != 0){
				deleted_data(valID);
			}
		}
		function removeattrdata(valID)
		{
			if(valID != '' && valID != 0){
				deleted_attrdata(valID);
			}
		}
            </script>
        @stop()