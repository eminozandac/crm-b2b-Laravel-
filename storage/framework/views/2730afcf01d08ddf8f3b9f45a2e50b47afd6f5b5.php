
<?php $__env->startSection('pagecss'); ?>
	<link href="<?php echo e(asset('assets/css/bootstrap-select.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
	<link href="css/plugins/select2/select2.min.css" rel="stylesheet">
	 <script src="js/plugins/select2/select2.full.min.js"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Batch Wise Stock</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                        </li>
                        
                        <li class="active">
                            <strong>Batch Wise Stock</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <!--<div class="ibox-content m-b-sm border-bottom">
				<div class="row">
					<div class="col-xs-12"><h2>Filter Product</h2></div>
					<div class="col-xs-12" id="filter_error" style="display:none;">
						<div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                Please enter any Batch number or select any option.
                        </div>
					</div>
					<div class="col-sm-3">
						 <div class="form-group">
						 <label class="control-label">Filter By Batch No:</label>
							<input type="text" class="form-control" name="batch" placeholder="Batch No" id="batch_filter"/>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Filter by Product Status:</label>
							 <select class="form-control product_staus" required name="product_staus" id="product_staus_filter">
								<option value="">Select Status</option>
								<option value="instock">In Stock</option>
								<option value="inproduction">In Production</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Filter by Color :</label>
							<?php 
								$variationColor=array(
									'0' => 'none',
									'1' => 'Tuscan Sun',
									'2' => 'Pearl White',
									'3' => 'Sterling Silver',
									'4' => 'Tranquility',
									'5' => 'Storm Clouds',
									'6' => 'Cinnabar',
								);
								$variationColorThumb=array(
									'0' => 'none',
									'1' => 'tuscan',
									'2' => 'pearl',
									'3' => 'cameo',
									'4' => 'tranq',
									'5' => 'strom',
									'6' => 'cinnabar',
									 
								);
								 
								 
								?>
							<select class="selectpicker form-control" required name="product_color" id="product_color_filter" >
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
							<label class="control-label">&nbsp;</label>
							<br/>
							<input type="submit" class="btn btn-w-m btn-primary" value="Filter Stock" id="fliter_button" />
						</div>
					</div>
				</div>
            </div>-->
			
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover dataTables-examples" id="stock_table" style="width:100%;" >
									<thead>
										<tr>
											<th>Product Name</th>
											<th>Batch No.</th>
											<th>Product Status</th>
											<th>Color</th>
											<th>Company Name</th>
											<th>Customer Name</th>
											<th>Qty</th>
											 
										</tr>
									</thead>
									<tbody>
									 <?php 
									$datastoks = DB::table('variation')
									 
									->where('productStock','>',0)
									->where('deleted_at','=',NULL)
									->orderBy('variationID','asc')->get();
									//print_r($datastoks); exit;
									foreach($datastoks as $stok){
										if($stok->productStock > 0){
										  
								 
										$getInProductionList=DB::table('variation_tranz')->where('variationID','=',$stok->variationID)->where('variationID','=',$stok->variationID)->where('qty','>','0')
										->where('deleted_at','=',NULL)->get();
										if(!empty($getInProductionList)){
										foreach($getInProductionList as $inProductionItem){
											
										
									?>
										<tr>
											 
											<td>
												<?php
												//echo'('.$inProductionItem->variation_tranzID.')';
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
													if($stok->stockdate != ''){
														$stockdate='('.date('d-m-Y',strtotime($stok->stockdate)).')';
													}else{
														$stockdate='';
													}
													if($stok->product_status == 'instock'){
														 
														echo '<label class="label label-info"> InStock '.$stockdate.'</label>';
															
														 
													}elseif($stok->product_status == 'inproduction'){
														
														echo '<label class="label label-success"> InProduction '.$stockdate.'</label>';
															
														 
													}else if($stok->product_status == 'onseaukarrival'){
														 
														echo '<label class="label label-success"  style="background-color: #029dff;"> OnSea-UKArrival '.$stockdate.'</label>';
														 
													}else if($stok->product_status == 'factorystock'){	
														 
														echo '<label class="label label-primary"> FactoryStock</label>';
														 
													}else{
														echo '<label class="label label-danger"> OutofStock '.$stockdate.'</label>';
													}
													// return $stok->product_status;
												}else{
													echo '---';
												} 
                  
												?>
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
											--
											</td>
											<td>
											--
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
												
									
									$getOrder=DB::table('order_transaction')
									->where('deleted_at','=',NULL)
									->get();
									foreach($getOrder as $order){
									?>
												
										<tr>
											 
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
													if($order->stockdate != ''){
														$stockdate='('.date('d-m-Y',strtotime($order->stockdate)).')';
													}else{
														$stockdate='';
													}
													if($order->qtystatus == 'instock'){
														 
														echo '<label class="label label-info"> InStock '.$stockdate.'</label>';
															
														 
													}elseif($order->qtystatus == 'inproduction'){
														
														echo '<label class="label label-success"> InProduction '.$stockdate.'</label>';
															
														 
													}else if($order->qtystatus == 'onseaukarrival'){
														 
														echo '<label class="label label-success"  style="background-color: #029dff;"> OnSea-UKArrival '.$stockdate.'</label>';
														 
													}else if($order->qtystatus == 'factorystock'){	
														 
														echo '<label class="label label-primary"> FactoryStock</label>';
														 
														 
													}else{
														echo '<label class="label label-danger"> OutofStock '.$stockdate.'</label>';
													}
													// return $stok->product_status;
												}else{
													echo '---';
												} 
                  
												?>
											</td>
											 
											 
											<td>
												<?php
												
												if($order->product_color!= ''){
													echo $order->product_color	;
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
												 
												1
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
		<?php $__env->stopSection(); ?>
	<?php $__env->startSection('pagescript'); ?>
		<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-form-validation.js')); ?>"></script>
		<script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>
		<script src="<?php echo e(asset('assets/js/bootstrap-select.js')); ?>"></script>
		<script src="<?php echo e(asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')); ?>"></script>
		<script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>
		<script>
		$(function (){
			
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
					  
				    $('.date').datepicker({
                        todayBtn: "linked",
                        keyboardNavigation: false,
                        forceParse: false,
                        calendarWeeks: true,
                        autoclose: true,
						dateFormat: 'yy-mm-dd'
						 
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
                        /* "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "<?php echo e(URL::to('admin/ajax/log/stockproductdatalist')); ?>",
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

				$('#fliter_button').click(function(e){
					var batch_filter=$('#batch_filter').val();
					var product_staus_filter=$('#product_staus_filter').val();
					var product_color_filter=$('#product_color_filter').val();
					var token=$('#token').val();
					if(batch_filter != '' || product_staus_filter !='' || product_color_filter !='')
                    {
                        customer_table.draw();
						//console.log(product_color_filter);
						
						// filterData();
						$('#filter_error').hide();
					}else{
						$('#filter_error').show();
						setTimeout(function(){ $('#filter_error').fadeOut('slow'); }, 3000);
					}
					e.preventDefault();
				});

				$('#batch').keyup(function(){
					var _token = $('#token').val();
					var batch = $('#batch').val();
					  $.ajax
					({
						type: "POST",
						url: "<?php echo e(URL::to('admin/ajax/log/getProducts')); ?>",
						data: {'batch':batch,'_token':_token},
						success: function(msg)
						{ 	 
							$('#product_name').html(msg);
							//console.log(msg);
							 
						}
					}); 
					
					txtboxdatebox();					
				});
				
				$('#product_name').change(function(){
					 
					var _token = $('#token').val();
					var product_name = $('#product_name').val();
					$.ajax
					({
						type: "POST",
						url: "<?php echo e(URL::to('admin/ajax/log/getProductsColor')); ?>",
						data: {'product_name':product_name,'_token':_token},
						success: function(msg)
						{ 	 
							/* alert(msg); */
							$('#product_color').html(msg);
							 

							//console.log(msg); 
						}
					});  
					
					txtboxdatebox();
				});
				
				$('#product_color').change(function(){
					 var _token = $('#token').val();
					var stk=$('#product_color').find('option:selected').attr('data-totlstk');
					var instk=$('#product_color').find('option:selected').attr('data-instk');
					var inprd=$('#product_color').find('option:selected').attr('data-inprd');
					var inprdorder=$('#product_color').find('option:selected').attr('data-inprdorder');
					var variation=$('#product_color').find('option:selected').attr('data-variation');
					var status=$('select[name="product_stausss"]').find('option:selected').val();
					//console.log(variation);
					if(status == 'instock'){
						if(inprd != 0){
							$('#changestck').attr('max',inprd);
							//$('#changestck').prop('disabled',false);
							$('#stockupdate').show();
							$('#stockupdate').show();
							
						//	$('#dealerIDdiv').show();
							//$('#stockupdate').show();
						}else{
							
							$('select[name="product_stausss"]').val();
							//$('#changestck').prop('disabled',true);
							$('#stockupdate').hide();
							if(inprdorder !=0){
								$('#stockupdate').hide();
							}else{
								
							toastr.options = {closeButton:true,preventDuplicates:true}
							toastr.error('No stock in production')
							
							}
							 
						}
					}else{
						if(instk != 0){
							$('#changestck').attr('max',instk);
							//$('#changestck').prop('disabled',false);
							$('#stockupdate').show();
						}else{
							//$('#product_stausss option').removeAttr('selected');
							//$('#changestck').prop('disabled',true);
							$('#stockupdate').hide();
							//toastr.options = {closeButton:true,preventDuplicates:true}
							//toastr.error('No producs in stock availabel')
						}
						
					}
					 $.ajax
					({
						type: "POST",
						url: "<?php echo e(URL::to('admin/getProductsOrder')); ?>",
						data: {'variation':variation,'_token':_token},
						success: function(msg)
						{ 	 
							// alert(msg); 
							$('#dealerID').html('');
							$('#dealerID').html(msg);
							selectcustomer();
							//$("#dealerID").val('').trigger("chosen:updated");
							if(msg !=''){
								//$('#product_qtydiv').hide();
								$('#inproductionopiton').hide();
								//$('#dealerID').show();
							}else{
								$('#product_qtydiv').show();
								$('#inproductionopiton').show();
								//$('#dealerID').hide();
							}
							//	chosenbox();
							//console.log(msg); 
						}
					});  
					$('#changestck').attr('max',stk);
					txtboxdatebox();
				});
				
			 
				$('#product_stausss').change(function(){
					
					var inprod=$(this).find(':selected').val();
					//alert(inprod);
						var _token = $('#token').val();
						var txtval= parseInt($('#changestck').val());
						var maxstk= parseInt($('#changestck').attr('max'));
						var instk=$('#product_color').find('option:selected').attr('data-instk');
						var inprdorder=$('#product_color').find('option:selected').attr('data-inprdorder');
						var inprd=$('#product_color').find('option:selected').attr('data-inprd');
						var stk=$('#product_color').find('option:selected').attr('data-totlstk');
						var variation=$('#product_color').find('option:selected').attr('data-variation');
						if(inprod == 'instock'){
							if(inprd != 0){
								$('#changestck').attr('max',inprd);
								//$('#changestck').prop('disabled',false);
								$('#stockupdate').show();
								 $.ajax
									({
										type: "POST",
										url: "<?php echo e(URL::to('admin/getProductsOrder')); ?>",
										data: {'variation':variation,'_token':_token},
										success: function(msg)
										{ 	 
											// alert(msg); 
											//$("#dealerID").val('').trigger("chosen:updated");
											//$('#dealerID').html('');
											//$('#dealerID').html(msg);
											if(msg !=''){
												//$('#product_qtydiv').hide();
												$('#inproductionopiton').hide();
												 
											}else{
												$('#product_qtydiv').show();
												$('#inproductionopiton').show();
											}
											//console.log(msg); 
										}
									});   
									//$('#dealerdivnotes').show();
								//$('#dealerIDdiv').show();
							}else{
							//	$('#changestck').prop('disabled',true);
								$('#stockupdate').hide();
								//$('#dealerdivnotes').hide();
							//	$('#dealerIDdiv').hide();
								/* toastr.options = {closeButton:true,preventDuplicates:true}
								toastr.error('No stock in production')
								$('#datebox').show();
								$('#datetd1').prop('disabled',false); */
								if(inprdorder !=0){
									$('#stockupdate').show();
								}else{
									$('#stockupdate').hide();
									toastr.options = {closeButton:true,preventDuplicates:true}
									toastr.error('No stock in production')
								
								}
							}
						}else{
							if(instk != 0){
								$('#changestck').attr('max',instk);
								//$('#changestck').prop('disabled',false);
								$('#stockupdate').show();
							}else{
								//$('#changestck').prop('disabled',true);
								$('#stockupdate').hide();
								toastr.options = {closeButton:true,preventDuplicates:true}
								toastr.error('No stock availabel')
								$('#datebox').hide();
								$('#datetd1').prop('disabled',true);
							}
						}
					txtboxdatebox();
				 });
				 	txtboxdatebox();
				 $('#changestck').keyup(function(){
					 txtboxdatebox();
					var maxtext= parseInt($('#changestck').attr('max'));
					var stockval=parseInt($('#changestck').val());
					var status=$('select[name="product_stausss"]').find('option:selected').val();
					if(status ='inproduction'){
						if(maxtext < stockval){
							 $('#changestck').val('');
							toastr.options = {closeButton:true,preventDuplicates:true}
							toastr.error('Stock value cannot be more than actual stock');
							$('#datebox').show();
							$('#datetd1').prop('disabled',false); 
						}else if(maxtext == stockval){
							$('#datelabel').text("Remaining Stock Date :");
							$('#datebox').hide();
							$('#datetd1').prop('disabled',true); 
						}else{
							$('#datelabel').text("Remaining Stock Date :");
							$('#datebox').show();
							$('#datetd1').prop('disabled',false); 
						}
					}else{
						if(maxtext < stockval){
							 $('#changestck').val('');
							toastr.options = {closeButton:true,preventDuplicates:true}
							toastr.error('Stock value cannot be more than actual stock');
							$('#datebox').hide();
							$('#datetd1').prop('disabled',true);
						}else if(maxtext == stockval){
							$('#datelabel').text("Remaining Stock Date :");
							$('#datebox').hide();
							$('#datetd1').prop('disabled',true);
						}else{
							$('#datelabel').text("Remaining Stock Date :");
							$('#datebox').show();
							$('#datetd1').prop('disabled',false); 
						}
					}
					txtboxdatebox();
				});
				 
	
				function randomNumber(min, max) {
					return Math.floor(Math.random() * (max - min + 1) + min);
				};
				$('#captchaOperation').html([randomNumber(1, 100), '+', randomNumber(1, 200), '='].join(' '));
				
				$('#stockmaintain').formValidation({
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
									message: 'Select stock date !'
								}
							}
						}, 
						
							
					}
				});
			
		});
		</script>
  <?php $__env->stopSection(); ?>     
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>