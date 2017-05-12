
<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/dataTables/datatables.min.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Accessory list</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                        </li>
                        
                        <li class="active">
                            <strong>Accessory list</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="ibox-content m-b-sm border-bottom">
				<div class="row">
					<div class="col-sm-4">
						 <div class="form-group">
						 <input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
							<a href="<?php echo e(action('admin\AccessoryController@addAccessory')); ?>" class="btn btn-w-m btn-primary">Add Accessory</a>
						</div>
					</div>
				</div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
                           <div class="table-responsive">
							<table class="table table-striped table-bordered table-hover dataTables-example" id="prodcut_tables" style="width:100%;" >
                                <thead>
									<tr>
										<th >Accessory Name</th>
										<th >Category</th>
										<th >Brand</th>
										<th >SKU</th>
										<th style="min-width:200px;">Warehouse Location</th>
										<th >Qty</th>
										<th >Price</th>
										<th >Description</th>
										<th >Status</th>
										<th>Action</th>
									</tr>
                                </thead>
                                <tbody>
									<?php
										$productDatas=DB::table('product_accessories')->where('deleted_at','=',NULL)->get();
										foreach($productDatas as $productData){
											
											$category=DB::table('accessory_category')->where('id','=',$productData->category_id)->first();
											$brand=DB::table('brand')->where('id','=',$productData->brand_id)->first();
											$url = URL::to('admin/editaccessory', base64_encode($productData->accessoryID));
											$urldel = URL::to('admin/deleteProducts', base64_encode($productData->accessoryID));
											if($productData->visibility == 1){
												$stuts='<span class="label label-primary">Enable</span>';
											}else{
												$stuts='<span class="label label-danger">Disable</span>';
												
											}
											$string = strip_tags($productData->accessory_description);

											if (strlen($string) > 100) {

												// truncate string
												$stringCut = substr($string, 0, 100);

												// make sure it ends in a word so assassinate doesn't become ass...
												$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... '; 
											}
											 
									?>
									<tr>
										<td><?php echo $productData->accessory_name;?></td>
										<td><?php echo $category->categoryName;?></td>
										<td><?php echo $brand->brandName;?></td>
										<td><?php echo $productData->sku;?></td>
										<td><?php echo $productData->warehouse;?></td>
										<td><?php echo $productData->accessory_qty;?></td>
										<td>&pound;<?php echo $productData->price;?></td>
										<td><?php echo $string ; ?></td>
										<td><?php echo $stuts;?></td>
										<td><?php echo '<a href="'.$url.'"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a><a href="#" onclick="removedata('.$productData->accessoryID.')" data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a' ;?>
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
		<script>
		  $(function (){
			  
			/* var customer_table = $('#prodcut_tables').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
						buttons: [
                            /* {extend: 'csv', title:'Product Details'},
                            {extend: 'excel', title:'Product Details'},
                            {extend: 'pdf', title:'Product Details'},  
                        ],
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "<?php echo e(URL::to('admin/ajax/log/productdatalist')); ?>",
                            data:function(d){  }
                        },
                        "columns": [
                            {data: 'Product Name', name: 'productName'},
                            {data: 'Category', name: 'category_id'},
                            {data: 'Brand', name: 'brand_id'},
                            {data: 'Batch', name: 'batch'},
                            {data: 'Description', name: 'description'},
                            {data: 'Status', name: 'visibility'},
                            {data: 'Action', name: 'Action', orderable: false, searchable: false},
                        ]

                    }); */
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
					var accessory = valID;
				 
					//console.log(order);
					swal({
						title: "Are you sure?",
						text: "This accessory Will be deleted?",
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
									url: "<?php echo e(URL::to('admin/ajax/log/deleteaccessory')); ?>",
								data: {'_token':_token,'accessory':accessory},
									success: function(msg)
									{ 	 
										//console.log(msg);
										//alert(msg);
										//order_table.draw();
										swal("Deleted!", "Your Accessory has been deleted.", "success"); 
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
			if(valID != '' && valID != 0){
				deleted_data(valID);
			}
		}
		</script>
  <?php $__env->stopSection(); ?>     
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>