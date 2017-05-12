

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
                    <strong>All Product</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox-content m-b-sm border-bottom">
            <div class="row">
				<form class="m-t form-horizontal"  role="form" method="post" action="<?php echo e(action('dealer\DealerProductController@productByCategory')); ?>" id="form_customer_profile" >
					<div class="col-sm-4">
						<div class="form-group">
							<label for="product_name" class="control-label">Select Category</label>
							<?php
								$sessionData=Session::get('dealerLog');
								$id = $sessionData['dealerID'];
								$getcategory=DB::table('dealer')->where('id','=',$id)->first();
								$category_ar =explode(',',$getcategory->categoryID);
								$showforall=DB::table('category')->where('showforall','=','1')->where('deleted_at','=',NULL)->get();
								//print_r($showforall);exit;

								foreach($showforall as $showall)
								{
									if (!in_array($showall->id, $category_ar)) {
										
										array_push($category_ar,$showall->id);
									} 
								}
								//print_r($category_ar);
							?>	
							<select class="form-control" name="category_id" id="category_id">
								<option value="">Select Category</option>
								<?php
								for($i=0;$i< count($category_ar);$i++){
									$categoryDatas=DB::table('category')->where('id','=',$category_ar[$i])->orWhere('showforall','=','1')->where('deleted_at','=',NULL)->get();
									$tree='';
									if(!empty($categoryDatas))
									{
										$rows = array();
											$mk=0;
										foreach($categoryDatas as $data)
										{
											/* foreach($data as $k=>$v)
											{
												$rows[$mk][$k]= $v;
											}
											$mk++; */
											echo '<option value="'.$data->id .'">'.$data->categoryName .'</option>';
										}

										//print_r($rows);
									   /*  function buildTree(array $elements, $parentId = 0)
										{
											$branch = array();
											foreach ($elements as $element) {
												if ($element['parent_id'] == $parentId) {
													$children = buildTree($elements, $element['id']);
													if ($children) {
														$element['children'] = $children;
													}
													$branch[] = $element;
												}
											}

											return $branch;
										}
										$tree = buildTree($rows); */
									}
								}
									

									/* function displayArrayRecursively($arr,$category_ar,$iden='')
									{
										$show_pass =  0;
										if ($arr)
										{
											 foreach ($arr as $v)
											 {
												// print_r($v);
												$disable = '';
												$var = '';

												if($iden!=''){$var=$iden;}else{$var='';}

												 if(is_array($category_ar))
												 {
													if (in_array($v['id'],$category_ar))
													{
														$show_pass = 1;
														$disable='';
														$style='style="color:#555;"';
													}else{
														$show_pass = 0;
														$disable='disabled';
														$style='style="color:#ccc;"';
													}
												 }

												// $disable='';
												//print_r($category_ar);
												 if($show_pass == 1)
												 {
													 echo '<option '.$disable.' '.$style.' value="'.$v['id'].'">'.$var.$v['categoryName'].'</option>';
												 }



												$arc=0;
												if (isset($v['children']))
												{
													if (is_array($v['children'])) {
														displayArrayRecursively($v['children'],$category_ar,$iden .'- ');
													}
												}
											}
										}
									}
									displayArrayRecursively($tree,$category_ar); */
								?>
							</select>
							<input type="hidden" name="_token"  id="token" value="<?php echo csrf_token(); ?>"/>
						</div>
					</div>   
					<div class="col-sm-2">
						<label for="product_name" class="control-label">&nbsp;</label><br/>
						<button class="btn btn-primary" id="searchcat">Search</button>
					</div>   
				</form>
            </div>
        </div>

        <div class="row">
			<div class="col-lg-12">
				<div class="ibox">
					<div class="ibox-content">
						<div class="table-responsive" id="productlists">
							<?php
			$sessionData=Session::get('dealerLog');
			$id = $sessionData['dealerID'];
			$getcategory=DB::table('dealer')->where('id',$id)->first();
			$category_ar =explode(',',$getcategory->categoryID);
			//	print_r($category_ar);
				$catidlist='';
				for($i=0;$i< count($category_ar);$i++){
					if($i==0){
						
						$catidlist .="'".$category_ar[$i]."'";
					}else{
						$catidlist .=",'".$category_ar[$i]."'";
					}
				} 
			if(isset($category_id) && !empty($category_id)){
				//echo $category_id;
				$qry = "SELECT products.*, variation.*, category.* FROM variation INNER JOIN products ON products.product_id=variation.product_id INNER JOIN category ON products.category_id=category.id WHERE variation.product_status != 'outofstock' AND products.category_id ='".$category_id."' AND variation.productStock > '0' AND products.visibility='1' AND variation.deleted_at IS NULL AND products.deleted_at IS NULL";
				
			}else{
				
				$qry = "SELECT products.*, variation.*, category.* FROM variation INNER JOIN products ON products.product_id=variation.product_id INNER JOIN category ON products.category_id=category.id WHERE variation.product_status != 'outofstock' AND variation.productStock > '0' AND variation.deleted_at IS NULL  AND products.visibility='1'  AND  products.deleted_at IS NULL AND products.category_id IN (".$catidlist.") OR category.showforall = 1";
			}
			//echo  '<br/>'.$qry;exit;
			$datastoks=DB::select(DB::raw($qry));
			
				?>
				<table class="table table-striped table-bordered table-hover dataTables-examples" id="stock_table" style="width:100%;" >
					<thead>
						<tr>
							<th>Product Name</th>
							<th>Batch No.</th>
							<th>Category</th>
							<th>Product Status</th>
							 
							<th>Price</th>
							<th>Color</th>
							<th>Qty</th>
							<th>Action</th>
							 
						</tr>
					</thead>
					<tbody>
				<?php
			if(!empty($datastoks)){
				$qtycount=0;
				foreach($datastoks as $stok){
					 $qtycount++;
					if($stok->product_status == 'instock' || $stok->product_status == 'factorystock' ||  $stok->product_status == 'inproduction' || $stok->product_status == 'onseaukarrival' ){
						if($stok->product_status == 'inproduction'){
							  $countOrder=0;
							$orderinsts=DB::table('inproduction_order')->where('product_id','=',$stok->product_id)->where('product_color','=',$stok->product_color)->get();
							foreach($orderinsts as $inPrdOrder){
								$countOrder = $countOrder + $inPrdOrder->orderqty;
							}
						}
				
						if($stok->productStock > 0){
						
						?>
						
					<tr>
						<td>
							<?php
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
								 if(!empty($productsName->category_id)){
									$category=DB::table('category')->where('id','=',$productsName->category_id)->first();
									if(!empty($category->categoryName)){
										echo $category->categoryName;
									}else{
										echo '---';
									}
								}else{
									echo '---';
								}
							
							?>
						</td>
						<td>
							<?php
							
							if($stok->product_status != ''){
								if($stok->product_status == 'instock'){
									if($stok->productStock > 0){
									echo '<label class="label label-info"> InStock</label>';
										
									}else{
										echo '<label class="label label-danger"> OutofStock</label>';
									}
								}elseif($stok->product_status == 'inproduction'){
									
									$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('updated_at','DESC')->first();
										if(!empty($getDateTranz)){
									 
											echo '<label class="label label-success"> InProduction ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
									 
										}else{
											$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('variationTranzToken','DESC')->first();
											if(!empty($getDateTranzS)){
												
													echo '<label class="label label-success"> InProduction ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												
											}else{
												if($getDateTranz->stockdate != '' && $getDateTranz->stockdate != '0000-00-00'){
													
													echo '<label class="label label-success"> InProduction ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												} 
											}
										}
									 
										
									 
								}else if($stok->product_status == 'onseaukarrival'){
									 
								 
									
									$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('updated_at','DESC')->first();
										if(!empty($getDateTranz)){
									 
											echo '<label class="label label-success" style="background-color: #029dff;"> OnSea-UKArrival ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
									 
										}else{
											$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('variationTranzToken','DESC')->first();
											if(!empty($getDateTranzS)){
												
													echo '<label class="label label-success" style="background-color: #029dff;"> OnSea-UKArrival ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												
											}else{
												if($getDateTranz->stockdate != '' && $getDateTranz->stockdate != '0000-00-00'){
													
													echo '<label class="label label-success" style="background-color: #029dff;"> OnSea-UKArrival ('.date('d-m-Y',strtotime($getDateTranz->stockdate)).')</label>';
												} 
											}
										}
									 
								}else if($stok->product_status == 'factorystock'){

									$getDateTranz=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('updated_at','DESC')->first();
										if(!empty($getDateTranz)){
									 
											echo '<label class="label label-primary"> Factory Stock</label>';
									 
										}else{
											$getDateTranzS=DB::table('variation_tranz')->where('variationID','=', $stok->variationID)->orderBy('variationTranzToken','DESC')->first();
											if(!empty($getDateTranzS)){
												
													echo '<label class="label label-primary"> FactoryStock</label>';
												
											}else{
												if($getDateTranz->stockdate != '' && $getDateTranz->stockdate != '0000-00-00'){
													
													echo '<label class="label label-primary"> Factory Stock</label>``';
												} 
											}
										}
									 
								}else{
									echo '<label class="label label-danger"> Out of Stock</label>';
								}
								// return $stok->product_status;
							}else{
								echo '---';
							} 

							?>
						</td>
						 
						<td>
							<?php
								if(!empty($productsName->real_price)){
									echo '&pound;'.$productsName->real_price;
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
							<?php
							 
								 if($stok->productStock > 0){
									 echo $stok->productStock;
								
								 }else{
									 echo '---';
									 
								 }
							 $action=URL::to('/dealer/dealerplaceorder');
							?>
						
						</td>
						<td>
							
							
							<a href="#" data-toggle="modal"  data-targets="#placeorders<?php echo $qtycount; ?>"  class="btn btn-primary btn-sm " onclick="showqtymodel('placeorder<?php echo $qtycount; ?>')"><i class="fa fa-shopping-cart">&nbsp;</i> Buy Now</a>
							
							<div class="modal inmodal fade" id="placeorder<?php echo $qtycount; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content animated fadeIn">

										<form action="<?php echo $action; ?>" method="GET" enctype="multipart/form-data" class="products" id="">
											<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
											<input type="hidden" name="productToken" value="<?php echo  base64_encode($stok->product_id) ;?>"/>
											<input type="hidden" name="varaintToken" id="varaintToken_<?php echo $qtycount; ?>" value="<?php echo base64_encode($stok->variationID); ?>"/>
											<input type="hidden" name="qtystatus" id="qtystatus_<?php echo $qtycount; ?>" value="<?php echo base64_encode($stok->product_status); ?>"/>
											
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
												<h4 class="modal-title">Buy Now </h4>
											</div>
											<div class="modal-body col-md-12" style=" max-height: 350px;overflow-y: scroll;">
												<label class="col-md-2">Qty</label>
												<div class="col-md-4" id="touchspin1qtydiv_<?php echo $qtycount; ?>">
													<input class="touchspin_<?php echo $qtycount; ?>" id="touchspin1qty_<?php echo $qtycount; ?>" type="text" value="1" name="qty" min="1" max="<?php echo $stok->productStock;?>" maxlength="9" />
												</div>
												
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
												<input type="submit" class="btn btn-primary" value="Buy now" />
											</div>
										</form>
									</div>
								</div>
							</div>
						</td>
					 
						 
					</tr>
					
							<?php
						}
					
					}
				}
			}else{
				echo '<td colspan="9">No Data Found</td>';
			}
				?>
				
				</tbody>
							</table>							
						</div>
						 
					</div>
				</div>
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
			var no = $('#productlists tr').length;
			//alert($('#productlist tr').length);
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
		function getProductData(){
			var _token = $('#token').val();
			var category_id=$('#category_id').find(':selected').val();
			$.ajax
			({
				type: "POST",
				url: "<?php echo e(URL::to('dealer/ajax/log/getdelaerproducs')); ?>",
				data: {'_token':_token,'category_id':category_id},
				success: function(msg)
				{ 	 
					/* alert(msg); */
					$('#productlist').html(msg);
					orderqty();
					//datatblload.draw();
				//console.log(msg); 
					datatblload();
					 
					
					 
				}
			});  
		}
		$('#searchcat').click(function(){
			//getProductData();
		});
		getProductData();
		//orderqty();
		function showqtymodel(valID){
			var  variationToken= '#'+valID;
			//alert(variationToken);
			 
			$(variationToken).modal('show');;
			  $(".inmodal").on("show.bs.modal", function(e) {
					$('.modal.fade').removeClass('fade');
				});
			
		}
		show_qty_model = showqtymodel;
    });
	function showqtymodel(valID)
		{
			if( valID!='' && valID != 0){
				//alert(valID);
				show_qty_model(valID);
			}
		}
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dealer.layouts.masterdealer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>