
<?php $__env->startSection('contentPages'); ?>

			<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>shoping cart</h2>
                    <ol class="breadcrumb">
						<li>
							<a href="<?php echo e(URL::to('/dealer')); ?>">Home</a>
						</li>
                        <li>
							<a href="<?php echo e(URL::to('/dealer/product')); ?>">Main Product</a>
						</li>
                        <li class="active">
                            <strong>Shoping cart</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
			<div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
		
                <div class="col-md-9">
					<div class="ibox">
					<form action="<?php echo e(action('dealer\CartController@updateCart')); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
						<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
                        <div class="ibox-title">
                            <span class="pull-right">(<strong><?php if(isset($cart)){echo $cart;} ?></strong>) items</span>
                            <h5>Items in your cart</h5>
                        </div>
                        <div class="ibox-content">
							<?php 
							 $sessionData=Session::get('dealerLog');
							$id = $sessionData['dealerID'];
							$maxqty=0;
							$cartTotal=0;
							$qtybox=0;
							$no = 0;
							$orgprice=0;
							//print_r($cartData);
							foreach($cartData as $item){
								$qtybox ++; 
								$no++;
								$maxqty=0;
							if(!empty($item['id']))
							{
								//print_r( $item);
							$productData=DB::table('product_accessories')->where('accessoryID','=',$item['attributes']['accessoriesToken'])->first();
							
								if(!empty($productData->accessory_image)){
								 
									$cavatar='uploads/accessories/'.$productData->accessory_image;
								} else{
									$cavatar='assets/img/placeholder300x300.png';
								}
							 
							 $brandData=DB::table('brand')->where('id','=',$productData->brand_id)->first();
							$categoryData=DB::table('accessory_category')->where('id','=',$productData->category_id)->first();
							
							 
							$dealerGroup=DB::table('dealer')->where('id','=',$id)->first();
							$discountData=DB::table('group')->where('groupID','=',$dealerGroup->groupID)->first();
							$discount=$discountData->discount;
							if(!empty($productData->price)){
								$orgprice = $orgprice + $productData->price * $item['quantity'];
								$pricePrint='&pound;'.$productData->price;
								
								//$itemTotal=$productData->price*$item['quantity'].'<br/>- '.$discount.'%';
								$itemTotal=$productData->price * $item['quantity'].'<br/>';
							} 
							
							?>

							<input type="hidden" name="cartToken[]" value="<?php echo base64_encode($item['id']); ?>"/>
							 
                            <div class="table-responsive">
                                <table class="table shoping-cart-table">
                                    <tbody>
										<tr>
											<td width="90">
												<img src="<?php echo e(URL::to($cavatar)); ?>" class="img-responsive"/>
											</td>
											<td class="desc" width="250"> 
												<h3>
												<a href="javascript:void(0)" class="text-navy">
												<?php echo e($productData->accessory_name); ?>

												</a>
												</h3>
												
												
												<table class="table table-striped table-bordered table-hover">
													<tbody>
														 
														<tr>
															<th width="80px">Category:</th>
															<td style="border: 1px solid #e7eaec !important;"><?php echo e($categoryData->categoryName); ?></td>
														</tr>
														<tr >
															<th>Brand:</th>
															<td style="border: 1px solid #e7eaec !important;"><?php echo e($brandData->brandName); ?></td>
														</tr>
														<tr >
															<th>Price:</th>
															<td style="border: 1px solid #e7eaec !important;"><?php echo $pricePrint; ?></td>
														</tr>
														 
													</tbody>
												</table>

												<div class="m-t-sm">
													
													<a href="javascript:void(0)" class="text-muted" onclick="removedata('<?php echo $item['id']; ?>')"  style="color:Red;"><i class="fa fa-trash"></i> Remove item</a>
												</div>
											</td>

											<td>
											
											</td>
											<td width="200px">
												 <input class="touchspin_<?php echo e($no); ?>" type="text" value="<?php echo e($item['quantity']); ?>" data-value="<?php echo e($item['quantity']); ?>" min="1" max="<?php echo $productData->accessory_qty;?>"  name="qty[]">
											</td>
											<td>            
												<h4>
												&pound;<?php echo $itemTotal; ?>
												</h4>
											</td>
										</tr>
                                    </tbody>
                                </table>
                            </div>
							<?php 
								}else{ echo '<h1>Cart is empty !</h1>';}
							}
								if(Cart::isEmpty()){}else{
							?>
					 <div class="ibox-content">
						<?php 
							 if(Cart::isEmpty()){}else{
						?>
                            <input type="submit" name="submit" value="Update Cart" class="btn btn-primary pull-right" />
							 <?php }?>
                            <a href="<?php echo e(URL::to('/dealer/accessorylist')); ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Continue shopping</a>
                        </div>
					</form>
							<hr/>
					<form class="m-t form-horizontal"  role="form" method="post" action="<?php echo e(action('dealer\CartController@placeOrder')); ?>" id="customer_billing" >
						<input type="hidden" name="_token"value="<?php echo csrf_token(); ?>"/>
							<div class="form-group">
								<label class="col-sm-3 control-label">Order Note</label>
								<div class="col-sm-9">
									<textarea  class="form-control" name="order_notes" id="order_notes_descriptions" placeholder="Notes"></textarea>
								</div>
							</div>
							<?php  
								}
							?>
                        </div>
						
                       
					
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Cart Summary</h5>
                        </div>
                        <div class="ibox-content">
						<?php 
							if(isset($orgprice) && !empty($orgprice)){
						?>
                           <table class="table">
                            <thead>
                            
                            </thead>
                            <tbody>
                            <tr>
                                <th>Amount</th>
								<td>&pound;<?php echo $orgprice; ?></td>
                            </tr>
                            <tr>
                                <th>Discount</th>
                                <td><?php if(isset($discount) && !empty($discount)){echo $discount.'%';}else{echo '0%';} ?></td>
                            </tr>
                            <tr>
                                <th>Total </th>
                                <td> &pound;<?php echo e(Cart::total()); ?></td>
                            </tr>
                            </tbody>
                        </table>
						<?php 
							}
						?>
                            <hr/>
                            <span class="text-muted small">
                                 
                            </span>
                            <div class="m-t-sm">
                                <div class="btn-group">
								<?php 
								if(Cart::isEmpty()){}else{
								?>
                               <input type="submit" class="btn btn-primary btn-sm" value="Place Order"> 
								 <?php }?>
                              
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				</form>
            </div>
        </div>
	<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
	<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<script src="<?php echo e(asset('assets/js/plugins/slick/slick.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/bootstrap-select.js')); ?>"></script>
	   <!-- TouchSpin -->
    <script src="<?php echo e(asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')); ?>"></script>
	<script type="text/javascript">
	$(function() {
        $('.product-images').slick({
            dots: true
        });
		function orderqty()
		{
			var no = <?php echo $no; ?>;
			
			for(var i=0; i<=no; i++)
			{
				var touchspain = '.touchspin_'+(i+1);
				
				$(touchspain).TouchSpin({
					buttondown_class: 'btn btn-white',
					buttonup_class: 'btn btn-white',
					min: 1, 
					max:$(touchspain).attr('max')
				});
			}
			/* $(".touchspin1").TouchSpin({
					buttondown_class: 'btn btn-white',
					buttonup_class: 'btn btn-white',
					min: 1, 
					max:<?php echo $maxqty;?>
			}); */
		}
		orderqty();
		function get_price(){
			var sale=$('#product_color').find(':selected').attr('data-sale');
			var real=$('#product_color').find(':selected').attr('data-real');
			var stk=$('#product_color').find(':selected').attr('data-av');
			var variation=$('#product_color').find(':selected').attr('data-variant');
			var stkstatus='';
			var printprice='';
			$('#varaintToken').val(variation);
			if(stk == 'instock'){
				stkstatus='&nbsp;&nbsp;<small class="label label-info"> In Stock</small>';
				$("#cart-btn").show('fade');
			}else if(stk =="inproduction"){
				stkstatus='&nbsp;&nbsp;<small class="label label-success">In Production</small>';
				$("#cart-btn").show('fade');
			}else{
				stkstatus='&nbsp;&nbsp;<small class="label label-danger"> Out Of Stock </small>';
				$("#cart-btn").hide('fade');
					
			}
			if(sale =='0'){
				printprice='$ '+real+'<small class="text-muted">&nbsp;&nbsp;Exclude Tax</small>'+stkstatus;
				$('#itmprc').val(real);
			}
			else{
				printprice='$ '+sale+'&nbsp;&nbsp;<span class="real-price">$ '+real+'</span>'+'<small class="text-muted">&nbsp;&nbsp;Exclude Tax</small>'+stkstatus;
				$('#itmprc').val(sale);
			}
				$('#product-main-price').html(printprice);
			
		}
		get_price();
		$('#product_color').change(function(){
			get_price();
		});
		
		function removeData(valID,ordernotes){
			var cartitm = valID;
			 
			//console.log(order);
			swal({
				title: "Are you sure?",
				text: "This item will be removed ?",
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
							url: "<?php echo e(URL::to('/dealer/removeCartItem/')); ?>",
							data: {'rowID':cartitm,'_token':_token,},
							success: function(msg)
							{ 	 
								//console.log(msg);
								//alert(msg);
								//order_table.draw();
								swal("Deleted!", "Your Item has been removed.", "success"); 
								location.reload();
								
							}
						});  
					}	 
				});
				
		}
		deleted_data = removeData;
		
    });
	function removedata(valID)
	{
		if(valID != '' && valID != 0){
			deleted_data(valID);
		}
	}
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dealer.layouts.masterdealer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>