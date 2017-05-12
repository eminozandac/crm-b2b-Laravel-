@extends('admin.layouts.masteradmin')
@section('pagecss')
    <link href="{{asset('assets/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
 
@stop
<?php
	$sessionData=Session::get('adminLog');
    
?>
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Store orders</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{URL::to('/admin')}}">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Accessory Orders</strong>
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
								<input type="hidden" name="_token"  id="token" value="<?php echo csrf_token(); ?>"/>
									<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
										<thead>
											<tr>
												<th >Product Name</th>
												<th>Category</th>
												<th>Brand</th>
												<th style="min-width: 60px;">Order Date</th>
												<th >Delivery Date</th>
												<th >Qty</th>
												<th >Status</th>
												<th >Price</th>
												<th >Dealer Name</th>
												<th >Notes</th>
												<th style="min-width: 55px;">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$getOrders=DB::table('accessory_order_tranz')->where('deleted_at','=',NULL)->get();
												foreach($getOrders as $order){
													
													$dealername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
													
													$accessoryData=DB::table('product_accessories')->where('accessoryID','=',$order->accessoryID)->first();
													
													$catname=DB::table('accessory_category')->where('id','=',$accessoryData->category_id)->first();
													
													$brandname=DB::table('brand')->where('id','=',$accessoryData->brand_id)->first();
													
													$detailURL=URL::to('admin/accessoryordersdetail', base64_encode($order->accessory_order_tranz_ID));
													
													?>
											<tr>
												<td><?php echo $accessoryData->accessory_name; ?></td>
												<td><?php echo $catname->categoryName; ?></td>
												<td><?php echo $brandname->brandName; ?></td>
												<td><?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at)); }else{ echo '--';}?></td>
												<td><?php if(!empty($order->delivery_date)){echo date('d-m-Y',strtotime($order->delivery_date)); }else{ echo '--';}?></td>
												<td><?php echo $order->order_qty; ?></td>
												<td><?php 
													 if($order->orderStatus=='pending'){
														echo '<label class="label label-warning" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 } if($order->orderStatus=='invoiced'){
														echo '<label class="label label-danger" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 }
													 if($order->orderStatus=='paid'){
														echo '<label class="label label-info" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 }
													 if($order->orderStatus=='complete'){
														echo '<label class="label label-success" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 }
												
												?></td>
												<td><?php  echo '&pound;'.$order->price; ?></td>
												<td><?php echo $dealername->first_name; ?></td>
												<td><?php echo $order->order_notes; ?></td>
												<td>
													<a href="<?php echo $detailURL; ?>" data-toggle="tooltip" title="Edit order" class="btn btn-xs btn-default"><i class="fa fa-pencil-square"></i></a>
													<?php if($order->orderStatus=='pending' || $order->orderStatus=='invoiced'){ ?>
													<a href="javascript:void(0);" data-toggle="tooltip" onclick="removedata(<?php echo $order->accessory_order_tranz_ID; ?>)"  title="Delete"  class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>
													<?php 
														}else{
															?>
															<a href="javascript:void(0);" disabled data-toggle="tooltip" title="Delete"  class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>
															<?php
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
@stop()
@section('pagescript')
		@include('admin.includes.commonscript')
	<script src="{{ asset('assets/js/plugins/pace/pace.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
    <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/iCheck/icheck.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
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
		 
		function removeData(valID){
			var order = valID;
			
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
							url: "{{URL::to('admin/admindeleteaccesoryorder')}}",
						data: {'order':order,'_token':_token},
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
		
		$('#bookedinfordeliverybtn').click(function(){
			$('#bookedinfordelivery').submit();
			
		});
		$('.notebtn').click(function(){
			var fromid=$(this).attr('data-formid');
			//alert(fromid);
			$(fromid).submit();
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
	</script>
@stop()