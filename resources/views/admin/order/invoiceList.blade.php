@extends('admin.layouts.masteradmin')
@section('pagecss')
    <link href="{{asset('assets/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
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
									<ul class="nav nav-tabs" id="myTab">
										<li class="active"><a data-toggle="tab" href="#tab-1">Pending Orders</a></li>
										<li class=""><a data-toggle="tab" href="#tab-2">Completed Orders </a></li>
									</ul>
									<div class="tab-content"> 
		<!--------------------------------invoiced--------------------------------------->
										
										<div id="tab-1" class="tab-pane active">
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<!--<input type="submit" value="save"/>-->
											<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@paidInvoice')}}" id="bookedinfordeliverys" >
												<div class="col-sm-4">
													<div class="form-group">
														<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
														<input type="hidden" name="opentab" value="invoice"/>
														<input type="hidden" name="type" value="Generate Invoice"/>
														<label class="control-label">Order Status :</label>
														<select class="form-control" name="orderStatus" required id="orderStatus" >
															<option value="">Select Order Status</option>
															<option value="paid">Paid</option>
															<option value="complete">Complete</option>
														</select>
													</div>
													<div id="invoicegroup">
													
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label class="control-label">&nbsp;</label>
														<br/>
														<input type="submit" class="btn btn-primary" name="type" value="Save" id="bookedinfordeliverybtn"/>
												 
													</div> 
												</div> 
												<div class="col-sm-6">
													<div class="form-group pull-right">
														<label class="control-label">&nbsp;</label>
														<br/>													
														<a href="{{URL('/admin/createserviceinvoice')}}" class="btn btn-primary"> Create Service Invoice </a>
													</div>
													
													<div class="form-group pull-right">
														<label class="control-label">&nbsp;</label>
														<br/>													
														<a href="{{URL('/admin/createinvoice')}}" class="btn btn-primary"> Create Invoice </a>&nbsp;
													&nbsp;
													</div>
													
												</div>
											 
												<div class="clearfix"></div>
											
												<br/>
												<div class="table-responsive">
													<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
													<table class="table table-striped table-bordered table-hover dataTables-example" id="invoiceorder_table" style="width:100%;" >
														<thead>
															<tr>
																<th> </th>
																<th>Invoice Number</th>
																<th>Invoice Title</th>
																<th>Invoice Date</th>
																<th>Delivery Date</th>
																<th>Company Name</th>
																<th >Status</th>
																<th style="min-width: 100px;">Action</th>
															</tr>
														</thead>
														<tbody>
														 
							 
														</tbody>
													</table>
												</div>
											</form>
											</div>
										</div>
										<div id="tab-2" class="tab-pane <?php if(Session::get('opentab')=='complete'){ echo'active';}?>">
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<!--<input type="submit" value="save"/>-->
												<br/>
												<div class="table-responsive">
												<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
													<table class="table table-striped table-bordered table-hover dataTables-example" id="completeorder_table" style="width:100%;" >
														<thead>
															<tr>
																<th>Invoice Number</th>
																<th>Invoice Title</th>
																<th>Invoice Date</th>
																<th>Delivery Date</th>
																<th>Company Name</th>
																<th >Status</th>
																<th style="min-width: 85px;">Action</th>
															</tr>
														</thead>
														<tbody>
														 
														</tbody>
													</table>
												</div>
											<!--</form>-->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal inmodal fade" id="productiondate" tabindex="-1" role="dialog"  aria-hidden="true">
				<div class="modal-dialog">
					<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@inStockOrderDate')}}" id="" >
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title">Stock Date</h4>
							</div>  
							<div class="modal-body " style="max-height: 350px; overflow-y: scroll;">
								<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
								<input type="hidden" name="inStkToken" class="inStkToken" id="inStkToken" value=" "/>
								<div class="form-group" style="width: 100%;">
									<label class="control-label">Select Date :</label><br/>
									<input type="text" required id="datetd1"  class="form-control stockdate datetd date" placeholder="dd-mm-yyyy" name="stockdate"  value="">
								</div>
							</div>
							<div class="modal-footer">
								<input type="submit" id="" class="btn btn-primary changeDate" value="Save changes" />
								<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="modal inmodal" id="adminaddnotes" tabindex="-1" role="dialog"  aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content animated fadeIn">
						<?php   $action=URL::to('/admin/addordernotes/'); ?>
						<form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="products" id="noteform">
							<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
							<input type="hidden" name="sendertype" value="<?php echo $sessionData['role']; ?>"/>
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title" id="notedmodeltitle">Product</h4>
							</div>
							<div class="modal-body col-md-12" style=" max-height: 350px;overflow-y: scroll;" id="ordernotesData">
							
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
								<input type="submit"  class="btn btn-primary notebtn" value="Save changes" />
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div class="modal inmodal fade" id="customernamemodel" tabindex="-1" role="dialog"  aria-hidden="true">
				<div class="modal-dialog modal-lg bs-example-modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title">Customer Name</h4>
						</div>  
						<?php  $addcutomeraction=URL::to('/admin/addadmincustomername/'); ?>
						<form action="<?php echo $addcutomeraction; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
							<div class="modal-body col-md-12" style="max-height: 350px; overflow-y: scroll;">
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
								<input type="hidden" name="opentab" value="pending"/>
									<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
									 
									<input type="hidden" name="orderTokenString" id="orderTokenStringCustomername" value=""/>
									 
											
								</div>
								<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
									<div class="form-group" style="width: 100%;">
									<label class="control-label">Customer Name :</label><br/>
									<input type="text" style="width:100%"  class="form-control" name="order_notes_title" required id="order_notes_title" placeholder="Customer Name"></div>
									<div class="form-group" style="width: 100%;">
									<label class="control-label">Notes:</label><br/>
									<textarea style="width:100%"  required name="order_notes_descriptions" class="form-control" placehoder="Notes"></textarea></div>
								</div>
								<div class="clearfix"></div>
								<hr/>
									
									 
									
							</div>
							<div class="clearfix"></div>
							<div class="modal-footer">
								<div class="clearfix"></div>
								<br/>
								<input type="submit" class="btn btn-primary" value="Save changes" />
								<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal inmodal fade" id="showinvoicedorders" tabindex="-1" role="dialog"  aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="invicemodeltitle">Invoice No :  <span></span></h4>
						</div> 
						<div class="modal-body">
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover dataTables-example" id="" style="width:100%;" >
									<thead>
										<tr>
											<th >Company Name</th>
											<th style="min-width: 150px;">Product Name</th>
											<th>Batch</th>
											<th>Category</th>
											<th >Order type</th>
											<th style="min-width: 110px;">Color</th>
											<th >Qty</th>
											<th style="min-width: 60px;">Order Date</th>
											<th style="min-width: 60px;">Delivery Date</th>
											<th >Customer Name</th>
											<th style="min-width: 150px;">Notes</th>
											<th >Status</th>
										</tr>
									</thead>
									<tbody id="invoicedordermodeltbl">	
										
									</tbody>
								</table>	
							</div>
						</div> 
						<div class="modal-footer">
							<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
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

		//$('.dataTables-example').DataTable();
		$('#pendingorder_table tfoot th div').each( function () {
			var title = $(this).text();
			$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
		} );
 
		 var panding_table = $('#pendingorder_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: "{{ URL::to('admin/adminorderlistpending') }}",
				data: function (d) {
				
					d.searchdata = $('input[type="search"]').val();
					
				}
			},
			"columns": [
				{data: '#', name: 'check', orderable: false, searchable: false},
				{data: 'Company Name', name: 'company_name'},
				{data: 'Product Name', name: 'productName'},
				{data: 'Batch', name: 'batch'},
				{data: 'Category', name: 'categoryName'},
				{data: 'Color', name: 'product_color'},
				{data: 'Order type', name: 'qtystatus'},
				{data: 'Order Date', name: 'order_transaction.created_at'},
				{data: 'Delivery Date', name: 'delivery_date'},
				{data: 'Qty', name: 'qty'},
				{data: 'Status', name: 'orderStatus'},
				{data: 'Order Accessory', name: 'orderStatus',searchable: false},
				{data: 'Customer Name', name: 'customer_name'},
				{data: 'Notes', name: 'order_notes_descriptions'},
				{data: 'Action', name: 'Action', orderable: false, searchable: false},
			 
			]

		}); 
		panding_table.columns().every( function () {
        var that = this;
 
			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
						.search( this.value )
						.draw();
				}
			} );
		} );
		
		
		
		/* $("input[type='search']").change(function (e){
                panding_table.draw();
                e.preventDefault();
            }); */
		var bookedorder_table = $('#bookedorder_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: "{{ URL::to('admin/adminorderlistbooked') }}",
				data: function (d) {
				}
			},
			"columns": [
				{data: '#', name: 'check', orderable: false, searchable: false},
				{data: 'Company Name', name: 'company_name'},
				{data: 'Product Name', name: 'productName'},
				{data: 'Batch', name: 'batch'},
				{data: 'Category', name: 'categoryName'},
				{data: 'Color', name: 'product_color'},
				{data: 'Order type', name: 'qtystatus'},
				{data: 'Order Date', name: 'order_transaction.created_at'},
				{data: 'Delivery Date', name: 'delivery_date'},
				{data: 'Qty', name: 'qty'},
				{data: 'Status', name: 'orderStatus'},
				{data: 'Order Accessory', name: 'orderStatus',searchable: false},
				{data: 'Customer Name', name: 'customer_name'},
				{data: 'Notes', name: 'order_notes_descriptions'},
				{data: 'Action', name: 'Action', orderable: false, searchable: false},
			]

		});
		var invoiceorder_table = $('#invoiceorder_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: "{{ URL::to('admin/adminorderlistinvoiced') }}",
				data: function (d) {
				}
			},
			"columns": [
				{data: '#', name: 'check', orderable: false, searchable: false},
				{data: 'Invoice Number', name: 'invoiceNumber'},
				{data: 'Invoice Title', name: 'invoiceTitle'},
				{data: 'Invoice Date', name: 'order_invoice.created_at'},
				{data: 'Delivery Date', name: 'orderNoteTokenString'},
				{data: 'Company Name', name: 'company_name'},
				{data: 'Status', name: 'invoice_status'},
				{data: 'Action', name: 'Action', orderable: false, searchable: false},
			]

		});
		 
		var paidorder_table = $('#paidorder_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: "{{ URL::to('admin/adminorderlistpaid') }}",
				data: function (d) {
				}
			},
			"columns": [
				{data: '#', name: 'check', orderable: false, searchable: false},
				{data: 'Invoice Number', name: 'invoiceNumber'},
				{data: 'Invoice Date', name: 'order_invoice.created_at'},
				{data: 'Delivery Date', name: 'delivery_date'},
				{data: 'Company Name', name: 'company_name'},
				{data: 'Status', name: 'invoice_status'},
				{data: 'Action', name: 'Action', orderable: false, searchable: false},
			]

		});
		var completeorder_table = $('#completeorder_table').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: "{{ URL::to('admin/adminorderlistcomplete') }}",
				data: function (d) {
				}
			},
			"columns": [
				{data: 'Invoice Number', name: 'invoiceNumber'},
				{data: 'Invoice Title', name: 'invoiceTitle'},
				{data: 'Invoice Date', name: 'order_invoice.created_at'},
				{data: 'Delivery Date', name: 'orderID'},
				{data: 'Company Name', name: 'company_name'},
				{data: 'Status', name: 'invoice_status'},
				{data: 'Action', name: 'Action', orderable: false, searchable: false},
			]

		});
		 
		 
		
		function ordercheckbox()
		{
			$('.selectorder').each(function(){
				if(this.checked == true) {
					$(this).parent().parent().parent().parent().css('background','#F5F5F6');
					var ordertranztoken=$(this).attr('data-tranztoken');
					$(this).parent().parent().next().val(ordertranztoken);
					$(this).parent().parent().parent().find('.ordertockentrz').prop('disabled',false);
					$(this).parent().parent().parent().find('.dealertoken').prop('disabled',false);
					 
					//disableclass
				}
				if(this.checked == false) {
					$(this).parent().parent().parent().parent().removeAttr('style');
					$(this).parent().parent().parent().find('.ordertockentrz').prop('disabled',true);
					$(this).parent().parent().parent().find('.dealertoken').prop('disabled',true);
				}
			});
		} 
		
		/*$('.OrderToken').click(function()
		{
			 var OrderToken=$(this).val();
			var ordercheckboxid=$(this).attr('id');
			var prdtoken=$(this).attr('data-prdtoken');
			var colortoken=$(this).attr('data-colortoken');
			
			if(this.checked == true) {
			//alert(ordercheckboxid);
				var newmedDiv = $(document.createElement('div'))
				 .attr("id", 'orderbook' + ordercheckboxid);
				 
				 var newmedDivbook = $(document.createElement('div'))
				 .attr("id", 'orderSwap' + ordercheckboxid);
				 
				newmedDiv.after().html('<input type="hidden" name="OrderToken[]" class="ordertockentrz" value="'+OrderToken+'"/><input type="hidden" name="prdtoken[]" class="prdtoken" value="'+prdtoken+'"/><input value="'+colortoken+'" type="hidden" name="colortoken[]" class="colortoken" />');
				
				newmedDivbook.after().html('<input type="hidden" name="OrderToken[]" class="ordertockentrz" value="'+OrderToken+'"/><input type="hidden" name="prdtoken[]" class="prdtoken" value="'+prdtoken+'"/><input value="'+colortoken+'" type="hidden" name="colortoken[]" class="colortoken" />');
					
				newmedDiv.appendTo("#orderSwap");
				newmedDivbook.appendTo("#orderBooked");
			 }
			 if(this.checked == false) {
				$('#orderbook'+ordercheckboxid).remove();
				$('#orderSwap'+ordercheckboxid).remove();
				 
			 } 
		});*/
		
		function orderforbooked(valID){
			var orderIDdata='#'+valID;
			var OrderToken=$(orderIDdata).val();
			var ordercheckboxid=$(orderIDdata).attr('id');
			var prdtoken=$(orderIDdata).attr('data-prdtoken');
			var colortoken=$(orderIDdata).attr('data-colortoken');
			
			if($(orderIDdata).prop("checked") == true) {
			//alert(ordercheckboxid);
				var newmedDiv = $(document.createElement('div'))
				 .attr("id", 'orderbook' + ordercheckboxid);
				 
				 var newmedDivbook = $(document.createElement('div'))
				 .attr("id", 'orderSwap' + ordercheckboxid);
				 
				newmedDiv.after().html('<input type="hidden" name="OrderToken[]" class="ordertockentrz" value="'+OrderToken+'"/><input type="hidden" name="prdtoken[]" class="prdtoken" value="'+prdtoken+'"/><input value="'+colortoken+'" type="hidden" name="colortoken[]" class="colortoken" />');
				
				newmedDivbook.after().html('<input type="hidden" name="OrderToken[]" class="ordertockentrz" value="'+OrderToken+'"/><input type="hidden" name="prdtoken[]" class="prdtoken" value="'+prdtoken+'"/><input value="'+colortoken+'" type="hidden" name="colortoken[]" class="colortoken" />');
					
				newmedDiv.appendTo("#orderSwap");
				newmedDivbook.appendTo("#orderBooked");
			 }
			if($(orderIDdata).prop("checked") == false) {
				$('#orderbook'+ordercheckboxid).remove();
				$('#orderSwap'+ordercheckboxid).remove();
				 
			 } 
		}
		orderforbooked_data=orderforbooked;
		
		/* $('.selectorder').click(function(){
			//ordercheckbox();
			var ordercheckboxid=$(this).attr('id');
			var ordertranztoken=$(this).attr('data-tranztoken');
			var dealerToken=$(this).attr('data-dealerToken');
			var qtystatus=$(this).attr('data-qtystatus');
			var orderToken=$(this).val();
			
			if(this.checked == true) {
			//alert(ordercheckboxid);
				var newmedDiv = $(document.createElement('div'))
				 .attr("id", 'invoicetxtbox' + ordertranztoken);
				 
				newmedDiv.after().html('<input type="hidden" name="orderTranzToken[]" class="ordertockentrz" value="'+ordertranztoken+'"/><input type="hidden" name="qtystatus[]" class="qtystatus" value="'+qtystatus+'"/><input value="'+dealerToken+'" type="hidden" name="dealerToken[]" class="dealertoken" /><input type="hidden" name="orderToken[]" class="orderToken" value="'+orderToken+'"/>');
					
				newmedDiv.appendTo("#invoicegroup");
			 }
			 if(this.checked == false) {
				$('#invoicetxtbox'+ordertranztoken).remove();
				 
			 }
		}); */
		
		function invoicegenD(valID)
		{
			var orderIDdata='#'+valID;
			
			var ordercheckboxid=$(orderIDdata).attr('id');
			var ordertranztoken=$(orderIDdata).attr('data-tranztoken');
			var dealerToken=$(orderIDdata).attr('data-dealerToken');
			var qtystatus=$(orderIDdata).attr('data-qtystatus');
			var orderToken=$(orderIDdata).val();
			
			if($(orderIDdata).prop("checked") == true) 
			{
				//alert(ordercheckboxid);
				var newmedDiv = $(document.createElement('div'))
				 .attr("id", 'invoicetxtbox' + ordertranztoken);
				 
				newmedDiv.after().html('<input type="hidden" name="orderTranzToken[]" class="ordertockentrz" value="'+ordertranztoken+'"/><input type="hidden" name="qtystatus[]" class="qtystatus" value="'+qtystatus+'"/><input value="'+dealerToken+'" type="hidden" name="dealerToken[]" class="dealertoken" /><input type="hidden" name="orderToken[]" class="orderToken" value="'+orderToken+'"/>');
					
				newmedDiv.appendTo("#invoicegroup");
			 }
			 if($(orderIDdata).prop("checked") == false) 
			 {
				$('#invoicetxtbox'+ordertranztoken).remove();	 
			 }
		}
		invoicegenDatas = invoicegenD;
		
		function removeData(valID,ordernotes){
			var order = valID;
			var orderstring = ordernotes;
			//console.log(order);
			swal({
				title: "Are you sure?",
				text: "This order Will be deleted?",
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
						var _token = $('#token').val();
					 
						$.ajax
						({
							type: "POST",
							url: "{{URL::to('admin/ajax/log/deleteaminorder')}}",
						data: {'order':order,'_token':_token,'orderstring':orderstring},
							success: function(msg)
							{ 	 
								//console.log(msg);
								//alert(msg);
								//order_table.draw();
								swal("Deleted!", "Your Order Item has been deleted.", "success"); 
								location.reload();
								
							}
						});  
					}	 
				});
				
		}
		deleted_data = removeData;
		
		function removedatainvoice(invoice){
			var invoiceData = invoice;
			 
			//console.log(order);
			swal({
				title: "Are you sure?",
				text: "This Invoice Will be deleted?",
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
						var _token = $('#token').val();
					 
						$.ajax
						({
							type: "POST",
							url: "{{URL::to('admin/ajax/log/deleteamininvoice')}}",
						data: {'invoice':invoiceData,'_token':_token},
							success: function(msg)
							{ 	 
								//console.log(msg);
								//order_table.draw();
								swal("Deleted!", "Your Invoice has been deleted.", "success"); 
								location.reload();
								
							}
						});  
					}	 
				});
				
		}
		deletedinv_data = removedatainvoice;
		
		function removedatapaid(invoicepaid){
			var invoiceData = invoicepaid;
			 
			//console.log(order);
			swal({
				title: "Are you sure?",
				text: "This order saved back to invoice ?",
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
						var _token = $('#token').val();
					 
						$.ajax
						({
							type: "POST",
							url: "{{URL::to('admin/ajax/log/deleteaminpaid')}}",
						data: {'invoice':invoiceData,'_token':_token},
							success: function(msg)
							{ 	 
								//console.log(msg);
								//order_table.draw();
								swal("Deleted!", "Your order saved back to invoice.", "success"); 
								location.reload();
								
							}
						});  
					}	 
				});
				
		}
		deletedpaid_data = removedatapaid;
		
		
		/* $('#bookedinfordeliverybtn').click(function(){
			$('#bookedinfordelivery').submit();
			
		}); */
		function editdate(valID){
			var valID =valID;
			//console.log(valID);
			$('#productiondate').modal('show'); 
			$('#inStkToken').val(valID); 
		}
		editdate_data= editdate;
		function customername(valID){
			var valID =valID;
			//console.log(valID);
			$('#customernamemodel').modal('show'); 
			$('#orderTokenStringCustomername').val(valID); 
			 
		}
		customername_data= customername;
		function addnotes(valID,valprd){
			var valID =valID;
			var valprd =valprd;
			//console.log(valID);
			$('#adminaddnotes').modal('show'); 
			$('#notesorderTokenString').val(valID); 
			$('#notedmodeltitle').html(valprd); 
			var _token = $('#token').val();
					 
			$.ajax
			({
				type: "POST",
				url: "{{URL::to('admin/ordernoteslistadmin')}}",
				data: {'orderdata':valID,'_token':_token},
				success: function(msg)
				{ 	 
					//console.log(msg);
					//order_table.draw();
					$('#ordernotesData').html(msg);
					
				}
			});
		}
		addnotes_data= addnotes;
		function showinvoicedorders(valID,valprd){
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
		showinvoicedorders_data = showinvoicedorders;
		$('.notebtn').click(function()
		{
			var fromid=$(this).attr('data-formid');
			//alert(fromid);
			$(fromid).submit();
		});
		
		$('.form-horizontal').find('[name="delivery_date"]')
			.change(function(e) {
				$('#bookedform').formValidation('revalidateField', 'delivery_date');
			})
            .end().formValidation({
			message: 'This value is not valid',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				delivery_date: {
				   validators: {
						notEmpty: {
							message: 'Select Date !'
						}
					}
				},
				orderStatus: {
				   validators: {
						notEmpty: {
							message: 'Select Order Status !'
						}
					}
				}, 
				 
			}
		});
		 
    });
	
	function removedata(valID,ordernotes)
	{
		if(valID != '' && valID != 0 && ordernotes !=''){
			deleted_data(valID,ordernotes);
		}
	}
	
	function removedatainvoice(invoice)
	{
		if(invoice != '' && invoice != 0){
			deletedinv_data(invoice);
		}
	}
	
	function removedatapaid(invoicepaid)
	{
		if(invoicepaid != '' && invoicepaid != 0){
			deletedpaid_data(invoicepaid);
		}
	}
	
	function invoicegen(valid)
	{
		if(valid != '' && valid != 0)
		{
			invoicegenDatas(valid);
		}
	}
	
	function orderforbooked(valid)
	{
		if(valid != '' && valid != 0){
			orderforbooked_data(valid);
		}
	}
	function editdate(valid)
	{
		if(valid != '' && valid != 0){
			editdate_data(valid);
		} 
	}
	function addnotes(valid,valprd)
	{
		if(valid != '' && valid != 0 && valprd!=''){
			addnotes_data(valid,valprd);
		}
	}
	function customername(valid)
	{
		if(valid != '' && valid != 0){
			customername_data(valid);
		}
	}
	function showinvoicedorders(valid,valprd)
	{
		if(valid != '' && valid != 0 && valprd!=''){
			showinvoicedorders_data(valid,valprd);
		}
	}
	
	</script>
@stop()