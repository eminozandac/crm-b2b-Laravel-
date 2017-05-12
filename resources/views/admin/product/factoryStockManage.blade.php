@extends('admin.layouts.masteradmin')
@section('pagecss')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
 
@stop()
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Product list</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{URL::to('/admin')}}">Home</a>
                        </li>
                        
                        <li class="active">
                            <strong>Product list</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
	
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
						  
							<div class="table-responsive">
							<?php $updateProductionDate=URL::to('/admin/updateProductionDate/'); ?>
							<form action="<?php echo $updateProductionDate; ?>" method="POST" enctype="multipart/form-data" class="products" id="inproductionDate"> 
																		 
								<input type="hidden" name="_token" id="_token" value="<?php echo csrf_token() ;?>"/>
															 
							</form>
							<form action="{{action('admin\NewStockController@updateInProductionStock')}}" method="POST" enctype="multipart/form-data" class="productss" id="stockmaintain">
								<input type="hidden" name="_token"  id="token" value="<?php echo csrf_token(); ?>"/>
								<input type="hidden" name="page"  id="token" value="factorystock"/>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label">&nbsp;</label>
											<br/>
											<div class="i-checks"><label> <input type="radio" value="instock" id="instock" name="qtytype"> <i></i> In Stock </label></div>
										</div>
									</div>
									 
									<div class="col-sm-2">
										<div class="form-group">
										 <label class="control-label">&nbsp;</label>
										 <br/>
										     <div class="i-checks"><label> <input type="radio"  value="onseaukarrival" id="ukarrival" name="qtytype"> <i></i> On Sea UK Arrival </label></div>
                                    
										</div>
									</div>
									<div class="col-sm-3" id="datetd1div">
										<div class="form-group">
											<label class="control-label">Date :</label>
											<input type="text" required id="datetd1"  class="form-control datetd date" placeholder="DD-MM-YYYY" name="stockdate">
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
										 <label class="control-label">&nbsp;</label>
										 <br/>
										     <input type="submit" class="btn btn-w-m btn-primary" id="stockupdate" value="Save" />
                                    
										</div>
									</div>
									
								<div class="clearfix"> </div>
								<table class="table table-striped table-bordered table-hover dataTables-examples" id="stock_table" style="width:100%;" >
									<thead>
										<tr>
											<th> <input type="checkbox" name="varationTokenAll" class="varationTokenAll"> </th>
											<th>Product Name</th>
											<th>Batch No.</th>
											<th>Product Status</th>
											<th>Company Name</th>
											<th>Customer Name</th>
											<th>Production Date</th>
											<th>Color</th>
											<th>Qty</th>
											 
										</tr>
									</thead>
									<tbody>
									 <?php 
									 
									 
									 
									 
									$datastoks = DB::table('variation')
									->where('product_status','=','factorystock')
									->where('productStock','>','0')
									->where('deleted_at','=',NULL)
									->orderBy('variationID','asc')->get();
									//print_r($datastoks); exit;
									foreach($datastoks as $stok){
										if($stok->product_status == 'instock' && $stok->productStock != 0 || $stok->product_status == 'factorystock' ){
										  
									if($stok->product_status == 'factorystock' ){
										$getInProductionList=DB::table('variation_tranz')
										->where('variationID','=',$stok->variationID)
										->where('qty','>','0')
										->where('deleted_at','=',NULL)
										->get();
										if(!empty($getInProductionList)){
										foreach($getInProductionList as $inProductionItem){
										$randID=rand(11111,99999);	
										
									?>
										<tr>
											<td>
												<input type="checkbox" name="varationToken[]" value="<?php echo $stok->variationID; ?>" data-inprdorder="0" onclick="changestatus('<?php echo 'variationprd_'.$randID; ?>')" id="<?php echo 'variationprd_'.$randID; ?>" class="varationToken"> 
												
												<input type="hidden" name="orderNoteTokenString[]" class="orderNoteTokenString" value="0" disabled />
												
												<input type="hidden" name="variationTokenString[]" class="variationTokenString" value="<?php echo $inProductionItem->variationTranzToken; ?>" disabled />
											</td>
											<td> 
												<?php
												 
												$productsName =DB::table('products')->where('product_id','=',$stok->product_id)->first();
												if($productsName->productName != ''){
													echo  $productsName->productName;
													
												}else{
													echo '---';
												}
												
												?>
											</td>
											<td>
												<?php 
												if($stok->batch != ''){
													echo $stok->batch;
												}else{
													echo '---';
												}
												?>
											</td>
											<td>
												<?php
												
												if($stok->product_status != ''){
													if($stok->product_status == 'instock'){
														if($stok->productStock > 0){
														echo '<label class="label label-info"> In Stock</label>';
															
														}else{
															echo '<label class="label label-danger"> Out of Stock</label>';
														}
													}elseif($stok->product_status == 'inproduction'){
														
														echo '<label class="label label-success"> In Production</label>';
															
														 
													}elseif($stok->product_status == 'factorystock'){
														
														echo '<label class="label label-primary"> FactoryStock</label>';
															
														 
													}else{
														echo '<label class="label label-danger"> Out of Stock</label>';
													}
													// return $stok->product_status;
												}else{
													echo '---';
												} 
                  
												?>
											</td>
											<td>
											--
											</td>
											<td>
											--
											</td>
											<td>
											<?php   
											$today=date("Y-m-d");
											
											if(!empty($inProductionItem->stockdate)){
											 
												 echo date('d-m-Y',strtotime($inProductionItem->stockdate));
											}else{
												echo '---';
											}
											
											?>
												<a href="#" data-toggle="modal" data-target="#productiondate<?php echo $inProductionItem->variationTranzToken?>"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>
												<div class="modal inmodal fade" id="productiondate<?php echo $inProductionItem->variationTranzToken?>" tabindex="-1" role="dialog"  aria-hidden="true">
													<div class="modal-dialog">
														<div class="modal-content">
														
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																<h4 class="modal-title">Change In Production Date</h4>
															</div>  
															 
																<div class="modal-body " style="max-height: 350px; overflow-y: scroll;">
																
																		<input type="hidden" name="inPrdToken" class="inPrdToken" value="<?php echo $inProductionItem->variationTranzToken?>"/>
																	
																		<div class="form-group" style="width: 100%;">
																			<label class="control-label">Select Date :</label><br/>
																			<input type="text" required id="datetd1"  class="form-control stockdate datetd date" placeholder="DD-MM-YYYY" name="stockdate"  value="">
																		 
																		</div>
																		 
																		
																</div>
																 
																<div class="modal-footer">
																	<input type="submit" id="" class="btn btn-primary changeDate" data-dismiss="modal" value="Save changes" />
																	<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																</div>
															 
														</div>
													</div>
												</div>
											</td>
											<td>
												<?php
												
												if($stok->product_color	 != ''){
													echo $stok->product_color	;
												}else{
													echo '---';
												}
                  
												?>
											</td>
											
											<td>
												1
											</td>
											
											 
										</tr>
										 
													<?php
													}
												}
											}
										}
									}
												$getOrder=DB::table('order_transaction')
												->where('qtystatus','=','factorystock')
												->where('deleted_at','=',NULL)
												->where('mailstatus','=',0)
												->get();
												foreach($getOrder as $order){
													$randID=rand(11111,99999);	
												?>
												
										<tr>
											<td>
												<input type="checkbox" name="varationToken[]" value="<?php echo $order->variationID; ?>" onclick="changestatus('<?php echo 'variationord_'.$randID; ?>')" id="<?php echo 'variationord_'.$randID; ?>"  data-inprdorder="<?php echo $order->orderNoteTokenString ; ?>" class="varationToken varationTokenOrder"> 
												<input type="hidden" name="orderNoteTokenString[]" class="orderNoteTokenString" value="<?php echo $order->orderNoteTokenString ; ?>" disabled />
												
												<input type="hidden" name="variationTokenString[]" class="variationTokenString" value="0" disabled />
											</td>
											<td> 
												<?php
												 
												$productsName =DB::table('products')->where('product_id','=',$order->product_id)->first();
												if($productsName->productName != ''){
													echo  $productsName->productName;
													if($order->specialOrderID > 0){
														echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
													}
													if($order->finance > 0){
														echo '<label class="label label-success" style="text-transform:capitalize;    padding: 5px 7px;border-radius: 10px;">F</label>';
													}
												}else{
													echo '---';
												}
												
												?>
											</td>
											<td>
												<?php 
												if($order->batch != ''){
													echo $order->batch;
												}else{
													echo '---';
												}
												?>
											</td>
											<td>
												<?php
												
												if($order->qtystatus != ''){
													if($order->qtystatus == 'instock'){
														if($order->qtystatus > 0){
														echo '<label class="label label-info"> In Stock</label>';
															
														}else{
															echo '<label class="label label-danger"> Out of Stock</label>';
														}
													}elseif($order->qtystatus == 'inproduction'){
														
														echo '<label class="label label-success"> In Production</label>';
															
														 
													}elseif($order->qtystatus == 'factorystock'){
														
														echo '<label class="label label-primary"> FactoryStock</label>';
															
														 
													}else{
														echo '<label class="label label-danger"> Out of Stock</label>';
													}
													// return $stok->product_status;
												}else{
													echo '---';
												} 
                  
												?>
											</td>
											<td>
											<?php 
												if(!empty($order)){
													$dealerName=DB::table('dealer')->where('id','=',$order->dealerID)->first();
													echo $dealerName->company_name ;
												}else{
													echo '--';
												}
											?>
											</td>
											<td>
											 <?php
												if(!empty($order)){
													if(!empty($order->customer_name)){
														echo $order->customer_name;
													}else{
														echo '--';
													}
													 
												}else{
													echo '--';
												}
	
											 ?>
											</td>
											<td>
											<?php   
											$today=date("Y-m-d");
											
											if(!empty($order->stockdate)){
												echo date('d-m-Y',strtotime($order->stockdate));
											}else{
												echo '---';
											}
											 
											?>
											<a href="#" data-toggle="modal" data-target="#productiondateorder<?php echo $order->orderNoteTokenString?>"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Edit Date</a>
												<div class="modal inmodal fade" id="productiondateorder<?php echo $order->orderNoteTokenString?>" tabindex="-1" role="dialog"  aria-hidden="true">
													<div class="modal-dialog">
														<div class="modal-content">
														
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																<h4 class="modal-title">Change In Production Date</h4>
															</div>  
															 
																<div class="modal-body " style="max-height: 350px; overflow-y: scroll;">
																
																		<input type="hidden" name="inPrdToken" class="inPrdToken" value=""/>
																		<input type="hidden" name="orderNoteTokenString" class="orderNoteTokenString" value="<?php echo $order->orderNoteTokenString?>"/>
																	
																		<div class="form-group" style="width: 100%;">
																			<label class="control-label">Select Date :</label><br/>
																			<input type="text" required id="datetd1"  class="form-control stockdate datetd date" placeholder="DD-MM-YYYY" name="stockdate"  value="">
																		 
																		</div>
																		 
																		
																</div>
																 
																<div class="modal-footer">
																	<input type="submit" id="" class="btn btn-primary changeDate" data-dismiss="modal" value="Save changes" />
																	<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																</div>
															 
														</div>
													</div>
												</div>
											</td>
											<td>
												<?php
												
												if($order->product_color	 != ''){
													if(!empty($order->product_side_color)){
														$pathcolorpanel=URL::to('colorDataJson/panelcolor.json');
														$coloesidejson= file_get_contents($pathcolorpanel);
														$coloesidejson = @json_decode($coloesidejson,true);
														echo $order->product_color .'( with '.$coloesidejson[$order->product_side_color].' sides)';
													}else{
														
														echo $order->product_color;
													}
												}else{
													echo '---';
												}
                  
												?>
											</td>
											
											<td>
												 
												1
											</td>
											
											 
										</tr>
												<?php
												}
											
										
									
									
										?>
									</tbody>
										 
								</table>
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
		<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
		<script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
        <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
		<script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
		<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
		<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
		 <script src="{{asset('assets/js/plugins/iCheck/icheck.min.js')}}"></script>
        
        
		<script>
		$(function (){
				function dateboxshow(){
					 
					/* $('#ukarrival').on('ifChecked', function(event){
					 // alert($(this).val()); // alert value
					 $("#datetd1div").show();
					});
					$('#ukarrival').on('ifUnchecked', function(event){
					 // alert($(this).val()); // alert value
					 $("#datetd1div").hide();
					});
					
					
					$('#instock').on('ifChecked', function(event){
					 // alert($(this).val()); // alert value
					 $("#instockdatetd1div").show();
					});
					$('#instock').on('ifUnchecked', function(event){
					 // alert($(this).val()); // alert value
					 $("#instockdatetd1div").hide();
					}); */
				}
				$('#ukarrival').change(function(){
					dateboxshow();
				});				
				$('#instock').change(function(){
					dateboxshow();
				});
				dateboxshow();
			
				 $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
				$('.changeDate').click(function(){
					var inPrdToken=$(this).parent().prev('div').find('.inPrdToken').val();
					var stockdate=$(this).parent().prev('div').find('.stockdate').val();
					var orderNoteTokenString=$(this).parent().prev('div').find('.orderNoteTokenString').val();
					var _token = $('#token').val();
					//console.log(orderNoteTokenString); 
					if(stockdate != ''){
						//$('#inPrdToken').val(inPrdToken);
						//$('#stockdate').val(stockdate);
						//$("#inproductionDate").trigger('submit');
						if(orderNoteTokenString != ''){
							$.ajax
							({
								type: "POST",
								url: "{{URL::to('admin/updateproductiondate/')}}",
								data: {'_token':_token,'inPrdToken':inPrdToken,'stockdate':stockdate,'orderNoteTokenString':orderNoteTokenString},
								success: function(msg)
								{ 	 
									//console.log(msg); 
									if(msg == '1'){
										toastr.options = {closeButton:true,preventDuplicates:true}
										toastr.success('Date Updated successfully !')
									}else{
										toastr.options = {closeButton:true,preventDuplicates:true}
										toastr.error('Some thig Went Wrong !')
									}
									 
									location.reload();
									
								}
							});  
						}else{
							
							$.ajax
							({
								type: "POST",
								url: "{{URL::to('admin/updateproductiondate/')}}",
								data: {'_token':_token,'inPrdToken':inPrdToken,'stockdate':stockdate},
								success: function(msg)
								{ 	 
									console.log(msg); 
									if(msg == '1'){
										toastr.options = {closeButton:true,preventDuplicates:true}
										toastr.success('Date Updated successfully !')
									}else{
										toastr.options = {closeButton:true,preventDuplicates:true}
										toastr.error('Some thig Went Wrong !')
									}
									 
								//	location.reload();
									
								}
							});  
						}
					}
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
					function selectcustomer(){
						
						$(".select2_demo_1").select2();
						$(".select2_demo_2").select2();
						$(".select2_demo_3").select2({
							placeholder: "Select a Customer",
							allowClear: true
						});
					}
				
				function txtboxdatebox(){
					var status=$('#product_stausss').find('option:selected').val();
					var color=$('#product_color').find('option:selected').val();
					var name=$('#product_name').find('option:selected').val();
					var batch=$('#batch').val();
					if(status !='' && color !='' && name !='' && batch != ''){
						$('#stockupdate').show();
						$('#product_stausdiv').show();
					}else{
						$('#stockupdate').hide();
						$('#product_stausdiv').hide();
						
					}
				}
					 $('.date').click(function(){
							$('body').find('.datepicker').css('z-index','10600');
					 });
				    $('.date').datepicker({
                        todayBtn: "linked",
                        keyboardNavigation: false,
                        forceParse: false,
                        calendarWeeks: true,
                        autoclose: true,
						format: 'd-m-yyyy'
						 
                    });

					var customer_table = $('#stock_table').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
						buttons: [
                            /* {extend: 'csv', title:'Product Details'},
                            {extend: 'excel', title:'Product Details'},
                            {extend: 'pdf', title:'Product Details'}, */
                        ],
						 "iDisplayLength": 20,
						  "aLengthMenu": [[10, 20, 30, 50, 100, 200, 500, -1], [10, 20, 30, 50, 100, 200, 500, "All"]],
						  "order": [[ 2, "desc" ]]
                        /* "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "{{ URL::to('admin/ajax/log/stockproductdatalist') }}",
                            data:function(d){  
								d.batch=$('#batch_filter').val();
								d.product_staus=$('#product_staus_filter').val();
								d.product_color=$('#product_color_filter').val();
							}
                        },
                        "columns": [
                            {data: 'Product Name', name: 'product_id'},
                            {data: 'Batch No', name: 'batch'},
                            {data: 'Color', name: 'product_color'},
                            {data: 'In Stock', name: 'product_staus'},
                            {data: 'In Production', name: 'product_staus'},
                            {data: 'In Stock Date', name: 'stockdate', orderable: false, searchable: false},
                        ] */

                    });
 
				$('#selectAlls').click(function(){
					if($(this).prop("checked") == true)
					{
						/* $('input:checkbox[class="varationToken"]').each(function() {
							$('.varationToken').prop("checked",true);
						});*/					}
						
						$('tbody input[type="checkbox"]').each(function() {
							$('.varationToken').prop("checked",true);
						});
						
					if($(this).prop("checked") == false){
						$('input:checkbox[class="varationToken"]').each(function() {
							$('.varationToken').prop("checked",false);
						});
					}
				});
				/*  $('.varationToken').click(function(){
					$(this).parent().parent().prev().find('thead input:checkbox').prop("checked",false);
					if(this.checked){
						$(this).next().prop('disabled',false);
					}else {
						$(this).next().prop('disabled',true);
					}
				});  */
				$('thead input:checkbox').click(function() 
				{
					var checkedStatus   = $(this).prop('checked');
					var table           = $(this).closest('table');

					if(this.checked){
						$('tbody input[name="varationToken[]"]:checkbox').each(function() 
						{
							var val = $('tbody input:checkbox').val();
							$(this).prop("checked",true);
							$('.orderNoteTokenString').prop("disabled",false);
							$('.variationTokenString').prop("disabled",false);
							
						});
				   }else {
						$('tbody input[name="varationToken[]"]:checkbox').each(function () {
							this.checked = false;
							$('.orderNoteTokenString').prop("disabled",true);
							$('.variationTokenString').prop("disabled",true);
							 
						})
					}
				});
				function changestatus(valID){
					var  variationToken= '#'+valID;
					//alert(variationToken);
					 
					$(variationToken).next().prop('disabled',false);
					$(variationToken).parent().parent().prev().find('thead input:checkbox').prop("checked",false);
				 
						if($(variationToken).prop('checked') == true){
							$(variationToken).next().prop('disabled',false);
							$(variationToken).next().next().prop('disabled',false);
						}else {
							$(variationToken).next().prop('disabled',true);
							$(variationToken).next().next().prop('disabled',true);
						}  
					
				}
				change_status = changestatus;	
				 
	
				function randomNumber(min, max) {
					return Math.floor(Math.random() * (max - min + 1) + min);
				};
				$('#captchaOperation').html([randomNumber(1, 100), '+', randomNumber(1, 200), '='].join(' '));
				
				$('#stockmaintain').find('[name="stockdate"]')
						.change(function(e) {
							$('#stockmaintain').formValidation('revalidateField', 'stockdate');
						})
                            .end().formValidation({
					message: 'This value is not valid',
					icon: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
					},
					fields: {
						batch: {
						   validators: {
								notEmpty: {
									message: 'Enter  batch no !'
								}
							}
						}, 
						product_name: {
						   validators: {
								notEmpty: {
									message: 'Select Prodct !'
								}
							}
						},
						product_color: {
						   validators: {
								notEmpty: {
									message: 'Select Color !'
								}
							}
						},
						productStock: {
						   validators: {
								notEmpty: {
									message: 'Enter  stock !'
								},
								numeric: {
									message: 'Enter Proper stock !'
								}
							}
						}, 
						product_stausss: {
						   validators: {
								notEmpty: {
									message: 'Select status !'
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
						}						
						
							
					}
				});
				
			
			
		});
		function changestatus(valID)
		{
			if( valID!='' && valID != 0){
				//alert(valID);
				change_status(valID);
			}
		}
		</script>
  @stop()     