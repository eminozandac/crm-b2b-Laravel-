@extends('admin.layouts.masteradmin')
@section('pagecss')
    <link href="{{asset('assets/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	
	<style>
		
		#pendingorder_table tfoot input {
			width: 100%;
			padding: 3px;
			box-sizing: border-box;
		}
		 #pendingorder_table tfoot {
			display: table-header-group !important;
		}
	</style>
@stop
<?php
	$sessionData=Session::get('adminLog');
    
?>
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Store Order Invoice</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{URL::to('/admin')}}">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Order Invoice</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>

			<div class="wrapper wrapper-content animated fadeInRight ecommerce">
				<div class="row">
					<div class="wrapper wrapper-content animated fadeInRight ecommerce">
						<div class="col-lg-12">
							<div class="ibox">
								<div class="ibox-content">
		<!--------------------------------invoiced--------------------------------------->
										
										<div id="tab-3" class="tab-pane">
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<!--<input type="submit" value="save"/>-->
											<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="order_edit" enctype="multipart/form-data">
												 
												<div class="col-sm-4">
													<div class="form-group">
														<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
														<input type="hidden" name="type" value="Generate Invoice"/>
														<input type="hidden" name="opentab" value="invoice"/>
														
														<label class="control-label">Select Company :</label>
														<select data-placeholder="Choose a Dealer..." id="dealerlist" class="chosen-select" style="width:100%;" name="dealer">
															<option value="">Select Company</option>
															<?php 
																$dealerData=DB::table('dealer')->where('deleted_at','=',NULL)->get(); 
																foreach($dealerData as $dealer){
																	echo '<option value="'.base64_encode($dealer->id).'">'.$dealer->company_name .'</option>';
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
														<input type="text" name="invoiceNumber" class="form-control"/>
													</div>
												</div>
												<div class="col-sm-4">
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
											
												<br/>
												<div class="table-responsive">
													<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
													<table class="table table-striped table-bordered table-hover dataTables-example" id="invoiceorder_table" style="width:100%;" >
														<thead>
															<tr>
																<th >#</th>
																<th >Company Name</th>
																<th style="min-width: 150px;">Product Name</th>
																<th >Batch</th>
																<th >Category</th>
																<th style="min-width: 65px;">Color</th>
																<th >Order type</th>
																<th style="min-width: 60px;">Order Date</th>
																<th >Delivery Date</th>
																<th >Qty</th>
																<th >Status</th>
																<th >Order Accessory</th>
																<th >Customer Name</th>
																<th style="min-width: 250px;">Notes</th>
															</tr>
														</thead>
														<tbody id="ordersData">
														 
							 
														</tbody>
													</table>
												</div>
											</form>
											</div>
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
	<script src="{{ asset('assets/js/plugins/pace/pace.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
    <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/iCheck/icheck.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
	<script src="{{asset('assets/js/plugins/select2/select2.full.min.js')}}"></script>
	 <!-- Chosen -->
    <script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
	
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
	deleted_data = null;
	deletedinv_data = null;
	 
	$(function() 
	{
		$('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
		});
	 
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
		$(".select2_demo_1").select2();
		$(".select2_demo_2").select2();
		$(".select2_demo_3").select2({
			placeholder: "Select a state",
			allowClear: true
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
		$('.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true,
			format: 'd-m-yyyy'
			 
		});

		$('#dealerlist').change(function(){
			var dealer = $(this).val();
			var _token = $('#token').val();
			$.ajax
				({
					type: "POST",
					url: "{{ URL::to('admin/getallinvoice') }}",
					data: {'dealer':dealer,'_token':_token},
						success: function(msg)
						{ 	 
							//console.log(msg);
							$('#ordersData').html(msg);
						}
				});
		});
		function showinvoicedorders( ){
			var valID =valID;
			var valprd =valprd;
			//console.log(valID);
			$('#showinvoicedorders').modal('show'); 
			$('#invicemodeltitle span').html(valprd); 
			var _token = $('#token').val();
				 
				$.ajax
				({
					type: "POST",
					url: "{{ URL::to('admin/adminorderlistinvoicedpopup') }}",
					data: {'valID':valID,'_token':_token},
						success: function(msg)
						{ 	 
							//console.log(msg);
							$('#invoicedordermodeltbl').html(msg);
						}
				});  
		}
		function incoicegen(valID){
			var orderIDdata='#'+valID;
			//$(orderIDdata).click(function(){
			//ordercheckbox();
			var ordercheckboxid=$(orderIDdata).attr('id');
			var ordertranztoken=$(orderIDdata).attr('data-tranztoken');
			var dealerToken=$(orderIDdata).attr('data-dealerToken');
			var qtystatus=$(orderIDdata).attr('data-qtystatus');
			var orderToken=$(orderIDdata).val();
			
			if($(orderIDdata).prop("checked") == true) {
			//alert(ordercheckboxid);
				var newmedDiv = $(document.createElement('div'))
				 .attr("id", 'invoicetxtbox' + ordertranztoken);
				 
				newmedDiv.after().html('<input type="hidden" name="orderTranzToken[]" class="ordertockentrz" value="'+ordertranztoken+'"/><input type="hidden" name="qtystatus[]" class="qtystatus" value="'+qtystatus+'"/><input value="'+dealerToken+'" type="hidden" name="dealerToken[]" class="dealertoken" /><input type="hidden" name="orderToken[]" class="orderToken" value="'+orderToken+'"/>');
					
				newmedDiv.appendTo("#invoicegroup");
			 }
			 if($(orderIDdata).prop("checked") == false) {
				$('#invoicetxtbox'+ordertranztoken).remove();
				 
			 }
		//});
		}
		incoicegen_data = incoicegen;
		  $('#order_edit').find('[name="dealer"]')
				.change(function(e) {
					$('#dealerlist').formValidation('revalidateField', 'dealerlist');
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
					dealer: {
						validators: {
							notEmpty: {
								message: 'Please Select Dealer !'
							}
							 
						}
					},
					invoiceNumber: {
						validators: {
							notEmpty: {
								message: 'Enter Invoice Number !'
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
	function incoicegen(valid)
	{
		if(valid != '' && valid != 0){
			incoicegen_data(valid);
		}
	}
	 
	
	</script>
@stop()