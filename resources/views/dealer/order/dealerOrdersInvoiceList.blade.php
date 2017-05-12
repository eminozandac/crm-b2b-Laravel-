@extends('dealer.layouts.masterdealer')

@section('pagecss')
    <!-- Toastr style -->
    <link href="{{ asset('assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="http://localhost/b2bcrm/assets/css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/summernote/summernote.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/summernote/summernote-bs3.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
@stop

<?php
	$sessionData=Session::get('dealerLog');
	$id = $sessionData['dealerID'];
    function limit_words($string, $word_limit)
    {
        $words = explode(" ",$string);
        echo implode(" ", array_splice($words, 0, 5));
    }
?>

@section('contentPages')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>B2B CRM</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ URL::to('/dealer') }}" class="sds" data-nam="1">Home</a>
                </li>
                <li>
                    <a class="sds" data-nam="2">B2B CRM</a>
                </li>
                <li class="active">
                    <strong class="sds" data-nam="3">All Orders</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="wrapper wrapper-content animated fadeInRight ecommerce">
				<div class="col-lg-12">
					<div class="ibox">
						<div class="ibox-content">
							 
							<div class="tab-content">
							 	<ul class="nav nav-tabs" id="myTab">
										 
										
										<li class="active"><a data-toggle="tab" href="#tab-1">Pending Orders</a></li>
										 
										
										<li class=""><a data-toggle="tab" href="#tab-2">Completed Orders </a></li>
										
									</ul>
									<div class="tab-content"> 
			
								<div id="tab-1" class="tab-pane active">
									<div class="panel-body">
								 
										<br/>
										<div class="table-responsive">
										<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
											<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
												<thead>
													<tr>
														<th>Invoice Number</th>
														<th>Invoice Title</th>
														<th>Invoice Date</th>
														<th>Delivery Date</th>
														<th>Dealer Name</th>
														<th >Status</th>
														<th style="min-width: 55px;">Action</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$invoices=DB::table('order_invoice')->where('invoice_status','!=','complete')->where('dealerID','=',$id)->get();
													$num=0;
													//print_r($invoices);
													foreach($invoices as $invoice){
													$num=$invoice->invoiceNumber;
													$ordertranz=explode(",",$invoice->orderNoteTokenString)	;
												?>
													<tr>
														<td>
															<?php 
																if(!empty($invoice->invoiceNumber)){
																	echo $invoice->invoiceNumber;
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
															<?php 
																if(!empty($invoice->invoiceTitle)){
																	echo $invoice->invoiceTitle;
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
															<?php 
																if(!empty($invoice->created_at)){
																	echo date('d-m-Y',strtotime($invoice->created_at));
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
																	<?php
																	
												$order=DB::table('order_transaction')->where('finance','=','0')->where('orderNoteTokenString','=',$ordertranz[0])->first();
												if(!empty($order)){
													if(!empty($order->delivery_date))
														echo date('d-m-Y',strtotime($order->delivery_date));
												}else{
													echo '--';
												}
																	?>
																</td> 
														<td>
															<?php 
															if(!empty($invoice->dealerID)){
																//echo $invoice->dealerID; 
																$delaername=DB::table('dealer')->where('id','=',$invoice->dealerID)->first();
																
																if(!empty($delaername->first_name) && !empty($delaername->last_name)){
																	$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
																}
																echo $name;
																$name='';
															}
															?>
														</td>
														<td>
															<?php 
									 
									if($invoice->invoice_status=='invoiced'){
										echo '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
									}elseif($invoice->invoice_status=='paid'){
										echo '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
									}elseif($invoice->invoice_status=='complete'){
										echo '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
									}else{
										echo '--';
									}
									?>
														</td>
														<td>
														<?php 
														//print_r($ordertranz);
														if($invoice->serviceInvoice !='1'){
														?>
															<a href="#" title="View" data-toggle="modal" data-target="#invoicedata<?php echo $num; ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
														<?php  }?>
															<div class="modal inmodal fade" id="invoicedata<?php echo $num; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
																<div class="modal-dialog modal-lg">
																	<div class="modal-content">
																		<div class="modal-header">
																			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																			<h4 class="modal-title">Invoice No : <?php 
																				if(!empty($invoice->invoiceNumber)){
																					echo $invoice->invoiceNumber;
																				} 
																			?></h4>
																		</div> 
																		<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
							<thead>
								<tr>
									<th style="min-width: 150px;">Product Name</th>
									<th>Batch</th>
									<th style="min-width: 65px;">Category</th>
									<th >Order type</th>
									<th style="min-width: 65px;">Order Date</th>
									<th >Dealer Name</th>
									<th style="min-width: 65px;">Delivery Date</th>
									<th >Customer Name</th>
									<th style="min-width: 200px;" >Notes</th>
									<th style="min-width: 65px;">Color</th>
									<th >Qty</th>
									<th >Status</th>
								</tr>
							</thead>
							<tbody>	
								<?php
									for($j=0;$j<count($ordertranz);$j++){
									 
										$order=DB::table('order_transaction')->where('orderNoteTokenString','=',$ordertranz[$j])->first();
											if(!empty($order)){
								?>
								<tr>
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
															//echo $order->product_id;
											 if(!empty($order->qtystatus)){
												$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
												if($order->qtystatus =='instock'){
													echo '<small class="label label-info"> In Stock</small>';
												}else{
													if($order->mailstatus==0){
														if(!empty($order->stockdate)){
															echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
														}else{
															
															if(!empty($date->stockdat)){
																echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																
															}else{
																if($order->specialOrderID > 0){
																	$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																	echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																}else{
																	echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																}
															}
															
														}
													}else{
														echo '<small class="label label-info"> In Stock</small>';
													}
												}
											} 
											
										?>
									</td>
									<td>
										<?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at));}?>
									</td>
									<td>
										<?php 
										if(!empty($order->dealerID)){
											
											$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
											$name='';
											if(!empty($delaername->first_name) && !empty($delaername->last_name)){
												$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
											}
											echo $name;
										}
										?>
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
									<?php 
									 
									if($order->orderStatus=='invoiced'){
										echo '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
									}elseif($order->orderStatus=='paid'){
										echo '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
									}elseif($order->orderStatus=='complete'){
										echo '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
									}else{
										echo '--';
									}
									?>
										 
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
																		<div class="modal-footer">
																			<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div> 
															</div> 
															<?php 
																if(!empty($invoice->invoicepdf)){
																	$path=URL::to('uploads/invoicepdf/'.$invoice->invoicepdf);
																	echo '<a href="'.$path.'" target="_blank" title="View Invoice"class="btn btn-xs btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
																}
															?>
														</td>
													</tr>
												<?php
													}
													
												?>
													 
												</tbody>
											</table>
										</div>
									 
									</div>
								</div>
								<div id="tab-2" class="tab-pane">
									<div class="panel-body">
								 
										<br/>
										<div class="table-responsive">
										<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
											<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
												<thead>
													<tr>
														<th>Invoice Number</th>
														<th>Invoice Date</th>
														<th>Delivery Date</th>
														<th>Dealer Name</th>
														<th >Status</th>
														<th style="min-width: 55px;">Action</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$invoices=DB::table('order_invoice')->where('invoice_status','=','complete')->where('dealerID','=',$id)->get();
													$num=0;
													//print_r($invoices);
													foreach($invoices as $invoice){
													$num=$invoice->invoiceNumber;
													$ordertranz=explode(",",$invoice->orderNoteTokenString)	;
												?>
													<tr>
														<td>
															<?php 
																if(!empty($invoice->invoiceNumber)){
																	echo $invoice->invoiceNumber;
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
															<?php 
																if(!empty($invoice->created_at)){
																	echo date('d-m-Y',strtotime($invoice->created_at));
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
																	<?php
																	
												$order=DB::table('order_transaction')->where('finance','=','0')->where('orderNoteTokenString','=',$ordertranz[0])->first();
												if(!empty($order)){
													if(!empty($order->delivery_date))
														echo date('d-m-Y',strtotime($order->delivery_date));
												}else{
													echo '--';
												}
																	?>
																</td> 
														<td>
															<?php 
															if(!empty($invoice->dealerID)){
																//echo $invoice->dealerID; 
																$delaername=DB::table('dealer')->where('id','=',$invoice->dealerID)->first();
																
																if(!empty($delaername->first_name) && !empty($delaername->last_name)){
																	$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
																}
																echo $name;
																$name='';
															}
															?>
														</td>
														<td>
															<?php 
									 
									if($invoice->invoice_status=='invoiced'){
										echo '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
									}elseif($invoice->invoice_status=='paid'){
										echo '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
									}elseif($invoice->invoice_status=='complete'){
										echo '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
									}else{
										echo '--';
									}
									?>
														</td>
														<td>
														<?php 
														//print_r($ordertranz);
													 
														?>
															<a href="#" title="View" data-toggle="modal" data-target="#invoicedata<?php echo $num; ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
														 
															<div class="modal inmodal fade" id="invoicedata<?php echo $num; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
																<div class="modal-dialog modal-lg">
																	<div class="modal-content">
																		<div class="modal-header">
																			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																			<h4 class="modal-title">Invoice No : <?php 
																				if(!empty($invoice->invoiceNumber)){
																					echo $invoice->invoiceNumber;
																				} 
																			?></h4>
																		</div> 
																		<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
							<thead>
								<tr>
									<th style="min-width: 150px;">Product Name</th>
									<th>Batch</th>
									<th style="min-width: 65px;">Category</th>
									<th >Order type</th>
									<th style="min-width: 65px;">Order Date</th>
									<th >Dealer Name</th>
									<th style="min-width: 65px;">Delivery Date</th>
									<th >Customer Name</th>
									<th style="min-width: 200px;" >Notes</th>
									<th style="min-width: 65px;">Color</th>
									<th >Qty</th>
									<th >Status</th>
								</tr>
							</thead>
							<tbody>	
								<?php
									for($j=0;$j<count($ordertranz);$j++){
									 
										$order=DB::table('order_transaction')->where('orderNoteTokenString','=',$ordertranz[$j])->first();
											if(!empty($order)){
								?>
								<tr>
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
															//echo $order->product_id;
											 if(!empty($order->qtystatus)){
												$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
												if($order->qtystatus =='instock'){
													echo '<small class="label label-info"> In Stock</small>';
												}else{
													if($order->mailstatus==0){
														if(!empty($order->stockdate)){
															echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
														}else{
															
															if(!empty($date->stockdat)){
																echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																
															}else{
																if($order->specialOrderID > 0){
																	$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																	echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																}else{
																	echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																}
															}
															
														}
													}else{
														echo '<small class="label label-info"> In Stock</small>';
													}
												}
											} 
											
										?>
									</td>
									<td>
										<?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at));}?>
									</td>
									<td>
										<?php 
										if(!empty($order->dealerID)){
											
											$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
											$name='';
											if(!empty($delaername->first_name) && !empty($delaername->last_name)){
												$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
											}
											echo $name;
										}
										?>
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
									<?php 
									 
									if($order->orderStatus=='invoiced'){
										echo '<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>';
									}elseif($order->orderStatus=='paid'){
										echo '<label class="label label-info" style="text-transform:capitalize;">paid</label>';
									}elseif($order->orderStatus=='complete'){
										echo '<label class="label label-success" style="text-transform:capitalize;">Complete</label>';
									}else{
										echo '--';
									}
									?>
										 
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
																		<div class="modal-footer">
																			<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div> 
															</div> 
															<?php 
																if(!empty($invoice->invoicepdf)){
																	$path=URL::to('uploads/invoicepdf/'.$invoice->invoicepdf);
																	echo '<a href="'.$path.'" target="_blank" title="View Invoice"class="btn btn-xs btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
																}
															?>
														</td>
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
		</div>
	</div>


@stop()

@section('pagescript')

	@include('admin.includes.commonscript')
    <script src="{{ asset('assets/js/plugins/pace/pace.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
    <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/sweetalert/sweetalert.min.js')}}"></script>
	  <script src="{{asset('assets/js/plugins/summernote/summernote.min.js')}}"></script>
	    <script src="{{asset('assets/js/jquery-fileupload-btn.js')}}"></script>
	<script type="text/javascript">
	 deleted_data = null;
	 $(document).ready(function(){
		$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
			localStorage.setItem('activeTab', $(e.target).attr('href'));
		});
		var activeTab = localStorage.getItem('activeTab');
		if(activeTab){
			$('#myTab a[href="' + activeTab + '"]').tab('show');
		}
	});
	$(function() 
	{
		  $('.summernote').summernote();
		var order_table = $('.dataTables-example').DataTable({
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
		function removeData(valID,ordernotes){
			var order = valID;
			var orderstring = ordernotes;
			//console.log(order);
			swal({
				title: "Are you sure?",
				text: "you wish to delete reservation ?",
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
						
						
						//alert(order);
						$.ajax
						({
							type: "POST",
							url: "{{URL::to('dealer/ajax/log/deleteorder')}}",
							data: {'order':order,'_token':_token,'orderstring':orderstring},
							success: function(msg)
							{ 	 
								//$('#product_name').html(msg);
								//console.log(msg);
								swal("Deleted!", "Your Order Item has been deleted.", "success"); 
								location.reload();
								
							}
						});  
					}	 
				});
		}
		deleted_data = removeData;
				
    });
	
	function removedata(valID,ordernotes)
	{
		if(valID != '' && valID != 0 && ordernotes !=''){
			deleted_data(valID,ordernotes);
		}
	}
	</script>
@stop()