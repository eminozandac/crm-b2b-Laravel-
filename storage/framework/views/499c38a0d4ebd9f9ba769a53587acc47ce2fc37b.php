
<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/dataTables/datatables.min.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/iCheck/custom.css')); ?>" rel="stylesheet">
 
<?php $__env->stopSection(); ?>
<?php
	$sessionData=Session::get('adminLog');
    
?>
<?php $__env->startSection('contentPages'); ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Complete orders</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Complete Orders</strong>
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
									 
									 
									

										
											
										
		<!--------------------------------completed--------------------------------------->
										
										 
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="<?php echo e(action('admin\OrderController@generateInvoice')); ?>" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<!--<input type="submit" value="save"/>-->
												<br/>
												<div class="table-responsive">
												<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
													
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
									<thead>
										<tr>
											<th >Company Name</th>
											<th >Product Name</th>
											<th>Batch</th>
											 
											<th >Order type</th>
											<th style="min-width: 65px;">Color</th>
											<th >Qty</th>
											<th style="min-width: 60px;" >Order Date</th>
											<th style="min-width: 60px;">Delivery Date</th>
											<th >Customer Name</th>
											 
										</tr>
									</thead>
									<tbody>	
										<?php
											
											$orders=DB::table('order_transaction')->where('orderStatus','=','complete')->orWhere('orderStatus','=','finance completed')->get();
											if(!empty($orders)){
												foreach($orders as $order){
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
													if($order->specialOrderID > 0){
														echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
													}
													if($order->finance > 0){
														echo '<label class="label label-success" style="text-transform:capitalize;    padding: 5px 7px;border-radius: 10px;">F</label>';
													}
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
											//echo $order->product_id;
												if(!empty($order->qtystatus)){
													$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
													if($order->qtystatus =='instock'){
														echo '<small class="label label-info"> In Stock</small>';
													}else{
														 
														if($order->mailstatus==0){
															if(!empty($date->stockdat)){
																if($order->qtystatus== 'onseaukarrival'){
																	echo '<small class="label label-success"   style="background-color: #029dff;"> On Sea - UK Arrival('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																}else{
																	echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																 
																}
																
															}else{
																if($order->specialOrderID > 0){
																	$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																	if($order->qtystatus== 'onseaukarrival'){
																		echo '<small class="label label-success"   style="background-color: #029dff;"> On Sea - UK Arrival('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																	}else{
																		echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																	 
																	}
																 
																}else{
																	
																	if($order->qtystatus== 'onseaukarrival'){
																		echo '<small class="label label-success"   style="background-color: #029dff;"> On Sea - UK Arrival('.date('d-m-Y',strtotime($order->stockdate)).' ) </small>';
																	}else{
																		echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($order->stockdate)).' ) </small>';
																	 
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
												<?php
													if(!empty($order->variationID)){
														$color=DB::table('variation')->where('variationID','=',$order->variationID)->where('deleted_at','=',NULL)->first();
														if(!empty($color->product_color)){
															echo $color->	product_color;
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
												 
											
											 
											 
										</tr>
										<?php
											  }
											}
											
                                        ?>
									</tbody>
								</table>	
							</div>
																			
												</div>
											<!--</form>-->
											</div>
										 
									 
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
		<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<script src="<?php echo e(asset('assets/js/plugins/pace/pace.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/plugins/iCheck/icheck.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')); ?>"></script>
	<script type="text/javascript">
	deleted_data = null;
	deletedinv_data = null;
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
		function ordercheckbox(){
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
		$('.OrderToken').click(function(){
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
		});
		$('.selectorder').click(function(){
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
		});
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
							url: "<?php echo e(URL::to('admin/ajax/log/deleteaminorder')); ?>",
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
							url: "<?php echo e(URL::to('admin/ajax/log/deleteamininvoice')); ?>",
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
							url: "<?php echo e(URL::to('admin/ajax/log/deleteaminpaid')); ?>",
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
		
		$('#bookedinfordeliverybtn').click(function(){
			$('#bookedinfordelivery').submit();
			
		});
		$('.notebtn').click(function(){
			var fromid=$(this).attr('data-formid');
			//alert(fromid);
			$(fromid).submit();
		});
		$('#bookedform').find('[name="delivery_date"]')
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
	
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>