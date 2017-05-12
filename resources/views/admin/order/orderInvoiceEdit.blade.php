@extends('admin.layouts.masteradmin')
@section('pagecss')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	
@stop()
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Invoice Details</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{URL::to('/admin')}}">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Invoice Details</strong>
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
                            <h5>Invoice Items</h5>
                        </div>
						<?php 
							$getinvoiceid=explode('&',$invoceid);
							 $invoceid=$getinvoiceid[0];
								$invoice=DB::table('order_invoice')->where('order_invoice_ID','=',base64_decode($invoceid))->first();
								//$getOrderTran=DB::table('order_transaction')->where('orderNoteTokenString','=',$orderID[1])->first();
								$ordertranz=explode(",",$invoice->orderNoteTokenString)	;
							
							?>
						<div class="ibox-content" <?php if($invoice->serviceInvoice==1){echo 'style="display:none;"';} ?>>
							<h2>Invoice Edit</h2>
							
							
							
							<form action="{{action('admin\OrderController@adminOrderInvoiceUpdate')}}" method="POST" enctype="multipart/form-data" class="productss" id="order_edit">
							<input type="hidden" name="_token"  id="token" value="<?php echo csrf_token(); ?>"/>
							<input type="hidden" name="invoiceToken"  id="invoiceToken" value="<?php echo $invoceid; ?>"/>
							<input type="hidden" name="opentab"  id="opentab" value="<?php echo $getinvoiceid[1]; ?>"/>
								<!--<div class="col-sm-4" id="datebox">
									<div class="form-group">
										<label class="control-label" id="datelabel">Delivery Date :</label>
										<input type="text"  id="datetd1" class="form-control datetd date" placeholder="mm/dd/yyyy" name="delivery_date" value="<?php if(!empty($invoice->delivery_date) && $invoice->delivery_date !='0000-00-00'){ echo date('m/d/Y',strtotime($invoice->delivery_date)); }?>">
									</div>
								</div>-->
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">Order Status :</label>
										<select class="form-control" name="orderStatus" required id="orderStatus" >
											<option value="">Select Status</option>
											<option <?php if($invoice->invoice_status=='invoiced'){echo 'selected=selected';}?> value="invoiced">Invoiced</option>
											<option <?php if($invoice->invoice_status=='paid'){echo 'selected=selected';}?> value="paid">Paid</option>
											<option <?php if($invoice->invoice_status=='complete'){echo 'selected=selected';}?> value="complete">Complete</option>
										</select>
									</div>
								</div>
								<?php 
									if($invoice->invoice_status == 'invoiced'){
										?>
								<div class="col-sm-8">
									<label class="control-label">Upload Invoice :</label>
									<div class="fileupload fileupload-new" data-provides="fileupload">
										<span class="btn btn-primary btn-file invoice-pdf">
											<span class="fileupload-new">Select file</span>
											<span class="fileupload-exists">Change</span>         
											<input type="file"  accept="pdf/*" name="invoicepdf" id="invoicepdf"/>
										</span>
										<span class="fileupload-preview"></span>
										<a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none"><i class="fa fa-times" aria-hidden="true"></i></a>
									</div>
								</div>
									<?php } ?>
								<div class="clearfix"></div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label">&nbsp;</label><br/>
										<input type="submit" class="btn btn-w-m btn-primary" id="stockupdate" value="Update" />
										 <a href="{{URL::to('/admin/invoicelist')}}" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Back</a>
									</div>
								</div>
							</form>
								<div class="clearfix"></div>
							<hr/>
							<div class="clearfix"></div>
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover dataTables-example" id="" style="width:100%;" >
									<thead>
										<tr>
											<th >Company Name</th>
											<th >Product Name</th>
											<th >Batch</th>
											<th >Category</th>
											<th >Order type</th>
											<th >Color</th>
											<th >Qty</th>
											<th >Order Date</th>
											<th >Delivery Date</th>
											<th >Customer Name</th>
											<th >Notes</th>
											<th >Status</th>
										</tr>
									</thead>
									<tbody>	
										<?php
										
											$num=0;
											 
											$num++;
											for($j=0;$j<count($ordertranz);$j++){
											 
												$order=DB::table('order_transaction')->where('orderNoteTokenString','=',$ordertranz[$j])->first();
												if(!empty($order)){
										?>
										<tr>
											<td>
												<?php 
												if(!empty($order->dealerID)){
													
													$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
													$name='';
													if(!empty($delaername->company_name)){
														$name= $delaername->company_name;
													}
													echo $name;
												}
												?>
											</td>
											<td>
											<?php 
												$getProductData=DB::table('products')->where('product_id','=', $order->product_id)->first();
												if(!empty($getProductData->productName)){
													echo $getProductData->productName;
												}
											?>
											</td>
											<td>
												<?php 
													if(!empty($order->batch)){
														echo $order->batch;
													}
												?>
											</td>
											<td>
												<?php 
													$getCategoryData=DB::table('category')->where('id','=', $getProductData->category_id)->first();
													if(!empty($getCategoryData->categoryName)){
														echo $getCategoryData->categoryName;
													}
												?>
											</td>
											<td>
												<?php
													if(!empty($order->qtystatus)){
														 
														if($order->qtystatus =='instock'){
															echo '<small class="label label-info"> In Stock</small>';
														}else if($order->qtystatus =='onseaukarrival'){
															echo '<small class="label label-success"   style="background-color: #029dff;"> On Sea - UK Arrival </small>';
														}else{
															echo '<small class="label label-success">In Production </small>';
														}
													}
													
												?>
											</td>
											<td>
												<?php
													if(!empty($order->variationID)){
														$color=DB::table('variation')->where('variationID','=',$order->variationID)->where('deleted_at','=',NULL)->first();
														if(!empty($color->product_color)){
															echo $color->product_color;
														}
													}
												?>
											</td>
											<td>1</td>
											<td>
												<?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at));}?>
											</td>
											
											<td>
												<?php
													if(!empty($order->delivery_date)){
														
														echo date('d-m-Y',strtotime($order->delivery_date));
													}else{
														echo '---';
													}
													?>
											</td>
											<td>
												<?php
												$orderNoteTokenString=$order->orderNoteTokenString;
												//echo $orderNoteTokenString;
												//$getOrderNotes=DB::table('order_notes')->where('orderNoteTokenString','=',$orderNoteTokenString)->first();
													if(!empty($order->customer_name)){
														echo $order->customer_name;
													}else{
														echo '---';
													}
												?>
											</td>
												<td>
												<?php 
													if(!empty($order->order_notes_descriptions)){
														echo $order->order_notes_descriptions;
													}else{
														echo '---';
													}
												?>
											</td>
											
											<td>
											<?php 
												$colorLabel='';
												if($order->orderStatus=='invoiced'){
													$colorLabel='danger';	
												}elseif($order->orderStatus=='paid'){
													$colorLabel='info';
												}else{
													$colorLabel='success';
												}
											?>
												<label class="label label-<?php echo $colorLabel;?>" style="text-transform:capitalize;"><?php 
												echo $order->orderStatus;
												?></label>
											</td>
											 
										</tr>
										<?php
											  }
											}
											 
										?>
									</tbody>
								</table>	
							</div>
							
                        </div>
						<div class="ibox-content" <?php if($invoice->serviceInvoice==0){echo 'style="display:none;"';} ?>>
		<!--------------------------------invoiced--------------------------------------->
										
										<div id="tab-3" class="tab-pane active">
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<!--<input type="submit" value="save"/>-->
											<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateServiceInvoiceDataUpdate')}}" id="order_edit" enctype="multipart/form-data">
												 
												<div class="col-sm-4">
													<div class="form-group">
														<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
														 <input type="hidden" name="invoiceToken"  id="invoiceToken" value="<?php echo $invoceid; ?>"/>
														<label class="control-label">Order Status :</label>
														<select data-placeholder="Choose a Dealer..." id="dealerlist" class="chosen-select" style="width:100%;" name="dealer">
															<option value="">Select Order Dealer</option>
															<?php 
																$dealerData=DB::table('dealer')->where('deleted_at','=',NULL)->get(); 
																foreach($dealerData as $dealer){
																	if($dealer->id == $invoice->dealerID){
																		$select='selected="seleced"';
																	}else{
																		$select='';
																	}
																	echo '<option '.$select.' value="'.base64_encode($dealer->id).'">'.$dealer->first_name .'&nbsp;'.$dealer->last_name .'</option>';
																}
																?>
														</select>
													</div>
													<div id="invoicegroup">
														
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label class="control-label">invoice Number :</label>
														<input type="text" name="invoiceNumber" value="<?php echo $invoice->invoiceNumber; ?>" class="form-control"/>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label class="control-label">invoice Title :</label>
														<input type="text" name="invoiceTitle" value="<?php echo $invoice->invoiceTitle; ?>" class="form-control"/>
													</div>
												</div>
												<div class="col-sm-10">
													<label class="control-label">Upload Invoice :</label>
													<div class="fileupload fileupload-new" data-provides="fileupload">
														<span class="btn btn-primary btn-file invoice-pdf">
															<span class="fileupload-new">Select file</span>
															<span class="fileupload-exists">Change</span>         
															<input type="file"  accept="pdf/*" name="invoicepdf" id="invoicepdf"/>
														</span>
														<span class="fileupload-preview"></span>
														<a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none"><i class="fa fa-times" aria-hidden="true"></i></a>
													</div>
													 
												</div>
												<div class="clearfix"></div>
												<div class="col-sm-4">
													<div class="form-group">
														<label class="control-label">&nbsp;</label>
														<br/>
														<input type="submit" class="btn btn-primary" name="Save" value="Save" id="bookedinfordeliverybtn"/>
												 
													</div> 
												</div> 
												<div class="clearfix"></div>
											  
											</form>
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
			<script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
			<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
			<script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
			<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
		    <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
		    <script src="{{asset('assets/js/jquery-fileupload-btn.js')}}"></script>
			<script>
				$(function(){
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
					
					 $('.date').datepicker({
                        todayBtn: "linked",
                        keyboardNavigation: false,
                        forceParse: false,
                        calendarWeeks: true,
                        autoclose: true,
						dateFormat: 'yy-mm-dd'
						 
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
                                            format: 'MM/DD/YYYY',
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
                                            format: 'MM/DD/YYYY',
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
                                },
								invoicepdf: {
									validators: {
										 
										file: {
											extension: 'pdf',
											type: 'application/pdf',
											maxSize: 2097152,   // 2048 * 1024
											message: 'Please select only pdf file Less then 2MB!'
										}
									}
								}
                            }
                    });
				});
				
			</script>
@stop()