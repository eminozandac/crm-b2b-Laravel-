
<?php $__env->startSection('pagecss'); ?>
	<link href="<?php echo e(asset('assets/css/bootstrap-select.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
	<style>
	#page-wrapper > .border-bottom{border-bottom:none !important;}
	.navbar-static-side{display:none;width:0px;}
	.sidebar-collapse{display:none !important;}
	.navbar-static-top{display:none !important;}
	#page-wrapper{margin:0px !important;}
	</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>
             
        <div class="wrapper wrapper-content animated fadeInRight" >
            <div class="row" style="border-bottom:none !important;">
                <div class="col-md-12">
                    <div class="iboxs">
                         
						 
						<?php 
						 //print_r($orderID); 
							$orderData=DB::table('product_order')->where('orderID','=',$orderID[0])->first();
							$productData=DB::table('products')->where('product_id','=',$orderData->product_id)->first();
							$productImg=DB::table('productimages')->where('product_id','=',$orderData->product_id)->first();
							$categoryData=DB::table('category')->where('id','=',$productData->category_id)->first();
							$brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
							$variationData=DB::table('variation')->where('variationID','=',$orderData->variationID)->first();
							
							$getOrderTran=DB::table('order_transaction')->where('orderNoteTokenString','=',$orderID[1])->first();
							
							if(!empty($productImg->productimage)){
								 
								$cavatar='uploads/products/'.$productImg->productimage;
							} else{
								$cavatar='assets/img/placeholder300x300.png';
							}
							$dealerGroup=DB::table('dealer')->where('id','=',$orderData->dealerID)->first();
							$attributeData=DB::table('product_attribute')->where('product_id','=',$orderData->product_id)->get();
							$discountGroup=DB::table('discount')->where('product_id','=', $productData->product_id)->where('groupID','=',$dealerGroup->groupID)->first();
							$itemTotal=0;
							$finalprice=0;
							if(!empty($orderData->real_price)){
								/* if(!empty($orderData->sale_price)){
									if(!empty($discountGroup->discountPer)){
										$finalprice=round($orderData->sale_price-$discountGroup->discountPer*$orderData->sale_price/100 );
										$discount= '- '. $discountGroup->discountPer.'%<hr/>&pound;'.$finalprice;
									}else{$discount='';$finalprice='&pound;'.$orderData->sale_price;}
								}else{ */
									if(!empty($discountGroup->discountPer)){
										$finalprice=round($orderData->real_price-$discountGroup->discountPer*$orderData->sale_price/100 );
										$discount= '- '. $discountGroup->discountPer.'%<hr/>&pound;'.$finalprice;
									}else{$discount='';$finalprice='&pound;'.$orderData->real_price;}
								//}
								/* if(!empty($productData->sale_price)){
									
									$pricePrint='&pound;'.$productData->sale_price.'
									<s class="small text-muted">&pound;'.$productData->real_price.'</s>';
									$itemTotal=($productData->sale_price*1).'<br/>'.$discount;
								}else{ */
									$pricePrint='&pound;'.$productData->real_price;
									$itemTotal=($productData->real_price*1).'<br/>'.$discount;;
								//}
							}else{
								$pricePrint ='';
								$itemTotal ='';
								$finalprice ='';
							}
						?>
                        <div class="ibox-contents" >
                            <div class="table-responsive">
								<div class="col-md-6">
									<h2>Order Details</h2>
									<table class="table table-striped table-bordered table-hover">
										<tr>
											<th>Dealer Name:</th>
											<td><?php echo e($dealerGroup->first_name); ?> <?php echo e($dealerGroup->last_name); ?></td>
										</tr>
										<tr>
											<th>Dealer Company:</th>
											<td><?php echo e($dealerGroup->company_name); ?>  </td>
										</tr>
										<tr>
											<th>Product Name:</th>
											<td><?php echo e($productData->productName); ?></td>
										</tr>
										<tr>
											<th>Category:</th>
											<td><?php echo e($categoryData->categoryName); ?></td>
										</tr>
										<tr>
											<th>Brand:</th>
											<td><?php 
												echo $brandData->brandName;
											?></td>
										</tr>
										<tr>
											<th>Batch:</th>
											<td><?php echo e($getOrderTran->batch); ?></td>
										</tr>
										<tr>
											<th>Color:</th>
											<td><?php 
												if(!empty($variationData->product_color)){
													echo $variationData->product_color;
												}else{
													echo $getOrderTran->product_color;
												}
											?></td>
										</tr>
										<tr>
											<th>Order Date:</th>
											<td><?php echo  date('d-m-Y',strtotime($getOrderTran->created_at)); ?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									
									<?php 
										$getCname=DB::table('order_transaction')->where('orderNoteTokenString','=',$orderID[1])->first();
										if(!empty($getCname)){
											echo '<h2>Order Notes</h2>';
											echo '<p><strong>Customer Name : </strong>'.$getCname->customer_name.'</p>';
											if(!empty($getCname->order_notes_descriptions)){
												echo '<p><strong>Notes : </strong>'.$getCname->order_notes_descriptions.'</p>';
											}
											 
										}
										if(!empty($getOrderTran->address)){
											echo '<hr/><h2>Address</h2>';
											echo '<p>'.$getOrderTran->address.'</p>';
										}else {
											echo '<hr/><h2>Address</h2>';
											
											echo '<p>'.$dealerGroup->address.'</p>';
										}
										if(!empty($dealerGroup->pincode)){
											
											echo '<p><strong>Pin Code : </strong>'.$dealerGroup->pincode.'</p>';
										}
									?>
								</div>
								
								<div class="claerfix"></div>
								 
								<?php if(!empty($getOrderTran->accessoryID)){ ?>
								<div class="clearfix"></div><hr/>
								<div class="col-sm-12"><h2>Accessory</h2><br/></div>
								<div class="col-sm-12">
									<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
										<thead>
											<tr>
												<th>#</th> 
												<th>Accessory Name</th>
												<th>Qty</th>
												 
												 
 
												 
											</tr>
										</thead>
										<tbody>
											<?php
												//$arrayAccessory=explode(',',$getOrderTran->accessoryID);
												 $acessorydata = json_encode($getOrderTran->accessoryID,true);
												$data = json_decode($acessorydata,true);
												$acessorydata = json_decode($data,true);
												$number=0;
												foreach($acessorydata as $k=>$v){
													//echo $acessory['accessory_qty'];
													$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$k)->first();
													$number++;
											?>
												<tr>
													<td><?php echo e($number); ?></td>
													<td><?php echo e($acessoryName->accessory_name); ?></td>
													<td><?php echo e($v); ?></td>
													 
												</tr>
											<?php	
												}
											?>
										</tbody>
									</table>
								</div>
								<?php } ?>
                            </div>
                        </div>
                         
                    </div>
                   
                </div>
                 
            </div>
        </div>
			<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
		<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<script src="<?php echo e(asset('assets/js/bootstrap-select.js')); ?>"></script>
			<script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>
			<script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
           <script src="<?php echo e(asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')); ?>"></script>
		   <script>
 
    window.print();
 
</script>
			 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>