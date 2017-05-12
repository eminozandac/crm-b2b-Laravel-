

<?php $__env->startSection('pagecss'); ?>
    <!-- Toastr style -->
    <link href="<?php echo e(asset('assets/css/plugins/toastr/toastr.min.css')); ?>" rel="stylesheet">

    <link href="<?php echo e(asset('assets/css/animate.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/style.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php
    function limit_words($string, $word_limit)
    {
        $words = explode(" ",$string);
        return implode(" ", array_splice($words, 0, 5));
    }
?>

<?php $__env->startSection('contentPages'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>B2B CRM</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(URL::to('/dealer')); ?>">Home</a>
                </li>
                <li>
                    <a>B2B CRM</a>
                </li>
                <li class="active">
                    <strong>All Accessories</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox ">
					<div class="ibox-title">
						<h5>Search</h5>
					</div>
					<div class="ibox-content col-lg-12">
						<div class="col-md-4">
							<p class="font-bold">
                                Search Accessories
                            </p>
							<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
							<select class="select2_demo_1 form-control" id="accessoriesFilter">
								<option value="all">All</option>
								<?php
									$getallAccessories=DB::table('product_accessories')->where('deleted_at','=',NULL)->get();
									foreach($getallAccessories as $accessory){
										?>
										<option value="<?php echo e($accessory->accessoriesToken); ?>">
										<?php echo e($accessory->accessory_name); ?>

										</option>
										<?php
									}
								?>
							</select>

						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-12"><br/></div>
			
			<div class="col-lg-12" id="searchedData"><br/>
			
				<div class="spiner-example" style="padding-top:50px;" id="spinner">
					<div class="sk-spinner sk-spinner-wave">
						<div class="sk-rect1"></div>
						<div class="sk-rect2"></div>
						<div class="sk-rect3"></div>
						<div class="sk-rect4"></div>
						<div class="sk-rect5"></div>
					</div>
				</div>
				<div class="clearfix"></div>
			<br/>
			</div>
			 
			
			<?php 
			if(isset($getCats) && !empty($getCats)){
				$clrfix=0;
				foreach($getCats as $cat){
					$clrfix++;
			?>
		
			<div class="col-md-3 allaccesories">
				<div class="ibox">
					<div class="ibox-content product-box">

						<div class="product-imitation"  style="min-height: 185px;">
							 <?php 
							if(!empty($cat->categoryAvatar)){
								$cavatar='uploads/accessoriescategories/thumb/'.$cat->categoryAvatar;
							} else{
								$cavatar='assets/img/placeholder300x300.png';
							}
							
							?>
							 <img alt="image" class="img-circles" src="<?php echo e(asset($cavatar)); ?>" style="width: auto;max-width: 100%;">
						</div>
						<div class="product-desc">
							 
							 
							<a href="#" class="product-name"> <?php echo $cat->categoryName; ?></a>



							<div class="small m-t-xs">
								<?php
									
								/* if (strlen($cat->categoryName) > 10)
								$str = substr($str, 0, 7) . '...'; */
							  $deatailurl = URL::to('dealer/accessoryitemlist', base64_encode($cat->id));
								?>
							</div>
							<div class="m-t text-righ">

								<a href="<?php echo $deatailurl; ?>" class="btn btn-xs btn-outline btn-primary">View Accessories <i class="fa fa-long-arrow-right"></i> </a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			if($clrfix==4){
					echo '<div class="clearfix"></div>';
					$clrfix=0;
				}
				}
			}
			?>
			
			<?php 
			if(isset($getSubCats) && !empty($getSubCats)){
				$clrfix=0;
				foreach($getSubCats as $SubCat){
					$clrfix++;
			?>
		
			<div class="col-md-3 allaccesories">
				<div class="ibox">
					<div class="ibox-content product-box">

						<div class="product-imitation"  style="min-height: 185px;">
							 <?php 
							if(!empty($SubCat->categoryAvatar)){
								$cavatar='uploads/accessoriescategories/thumb/'.$SubCat->categoryAvatar;
							} else{
								$cavatar='assets/img/placeholder300x300.png';
							}
							
							?>
							 <img alt="image" class="img-circles" src="<?php echo e(asset($cavatar)); ?>" style="width: auto;max-width: 100%;">
						</div>
						<div class="product-desc">
							 
							 
							<a href="#" class="product-name"> <?php echo $SubCat->categoryName; ?></a>



							<div class="small m-t-xs">
								<?php
									
								/* if (strlen($SubCat->categoryName) > 10)
								$str = substr($str, 0, 7) . '...'; */
							  $deatailurl = URL::to('dealer/accessoryitemlist', base64_encode($SubCat->id));
								?>
							</div>
							<div class="m-t text-righ">

								<a href="<?php echo $deatailurl; ?>" class="btn btn-xs btn-outline btn-primary">View Accessories <i class="fa fa-long-arrow-right"></i> </a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			if($clrfix==4){
					echo '<div class="clearfix"></div>';
					$clrfix=0;
				}
				}
			}
			?>
			
			
			<?php 
		 
			 if(isset($getAccessoryData) && !empty($getAccessoryData)){
				$num=0;
				$clrfix=0;
				foreach($getAccessoryData as $accessory){
					$catName=DB::table('accessory_category')->where('id','=',$accessory->category_id)->where('deleted_at','=',NULL)->first();
				$num++;
				$clrfix++;
			?>
	
			<div class="col-md-3 allaccesories">
				<div class="ibox">
					<div class="ibox-content product-box">

						<div class="product-imitation" style="min-height: 185px;">
							 <?php 
							if(!empty($accessory->accessory_image)){
								$cavatar='uploads/accessories/thumb/'.$accessory->accessory_image;
							} else{
								$cavatar='assets/img/placeholder300x300.png';
							}
							
							?>
							 <img alt="image" class="img-circles" src="<?php echo e(asset($cavatar)); ?>" style="width: auto;max-width: 100%;">
						</div>
						<div class="product-desc">
							<span class="product-price">
                                   &pound;<?php echo $accessory->price; ?>
                                </span>
							<small class="text-muted"><?php echo $catName->categoryName; ?></small><br/>
							<small class="text-muted"><strong>SKU : </strong><?php echo $accessory->sku; ?></small>
							 <?php 
								if( $accessory->accessory_qty > 0){
								}else{
									echo '<small class="label label-danger pull-right">Out of stock</small>';
								}
									?>
							<a href="#" class="product-name"> <?php echo $accessory->accessory_name; ?></a>



							<div class="small m-t-xs">
								<?php
								$str='';
								if(!empty($accessory->accessory_description)){
									$string=$accessory->accessory_description;
									$string = strip_tags($string);

										if (strlen($string) > 150) {

											// truncate string
											$stringCut = substr($string, 0, 150);

											// make sure it ends in a word so assassinate doesn't become ass...
											$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... '; 
										}
										echo $string;
								}
									//echo $str;
								?>
							</div>
							<div class="m-t text-righ">
								<?php 
								if( $accessory->accessory_qty > 0){
									?>
									<a href="javascript:void(0);" data-toggle="modal" data-target="#orderpopup<?php echo $accessory->accessoriesToken; ?>"  class="btn btn-primary">Buy now <i class="fa fa-shopping-cart"></i> </a>
								<?php	
								}else{
									?>
									<a href="javascript:void(0);" disabled="disabled" class="btn btn-primary">Buy now <i class="fa fa-shopping-cart"></i> </a>
									<?php
								}
								$formurl="{{action('dealer\CartController@index')}}";
								?>
								
								<div class="modal inmodal" id="orderpopup<?php echo $accessory->accessoriesToken; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content animated fadeIn">

										<form action="<?php echo e(action('dealer\CartController@index')); ?>" method="POST" enctype="multipart/form-data" class="products" id="">
												 
											
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
													<h4 class="modal-title"><?php echo $accessory->accessory_name; ?></h4>
												</div>
												<div class="modal-body col-md-12">
													<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
													<input type="hidden" name="accessoryToken" value="<?php echo  base64_encode($accessory->accessoryID) ;?>"/>
													<label class="col-md-2">Qty</label>
													<div class="col-md-4" id="touchspin1qtydiv_<?php echo $accessory->accessoryID; ?>">
														<input class="touchspin_<?php echo $num; ?>" id="touchspin1qty_<?php echo $accessory->accessoryID; ?>" type="text" value="1" min="1" name="qty" max="<?php echo $accessory->accessory_qty;?>">
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
													<input type="submit" class="btn btn-primary" value="Add to cart" />
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
			<?php
				if($clrfix==4){
					echo '<div class="clearfix"></div>';
					$clrfix=0;
				}
				 
				}
			 }
			?>

		</div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('pagescript'); ?>

	<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script src="<?php echo e(asset('assets/js/plugins/pace/pace.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')); ?>"></script>
		<script type="text/javascript">
	$(function() {
		$(".select2_demo_1").select2();
            $(".select2_demo_2").select2();
            $(".select2_demo_3").select2({
                placeholder: "Select a state",
                allowClear: true
            });
			function datatblload(){
				 $('#stock_table').DataTable({
					dom: '<"html5buttons"B>lTfgitp',
					buttons: [
						/* {extend: 'csv', title:'Product Details'},
						{extend: 'excel', title:'Product Details'},
						{extend: 'pdf', title:'Product Details'}, */
					],
				   
				});
			}
		function orderqty()
		{
			var no = $('div.modal').length;
			//alert(no);
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
			 
		}
		orderqty();
		function accessoriesFilter(){
			var datavaal=$('#accessoriesFilter').val();
			//console.log(datavaal);
			if(datavaal == 'all'){
				$('.allaccesories').show();
				$('#spinner').fadeOut();
				$('#searchedData').hide();
			}else{
				$('.allaccesories').hide();
				$('#spinner').fadeIn();
				$('#searchedData').show();
				
				var _token = $('#token').val();
					 
						$.ajax
						({
							type: "POST",
							url: "<?php echo e(URL::to('dealer/ajax/log/accessoryfilter')); ?>",
							data: {'accessoryData':datavaal,'_token':_token},
							success: function(msg)
							{ 	 
								//console.log(msg);
								//order_table.draw();
								 $('#searchedData').html(msg);
								 orderqty();
								$('#spinner').hide();
								
							}
						});  
			}
		}
		$('#accessoriesFilter').change(function(){
			accessoriesFilter();
			
		});
		accessoriesFilter();
    });
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dealer.layouts.masterdealer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>