
<?php $__env->startSection('pagecss'); ?>
    <link href="css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-10">
				<h2>Edit Category</h2>
				<ol class="breadcrumb">
					<li>
						<a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
					</li>
					<li>
						<a>Store</a>
					</li>
					<li class="active">
						<strong>Categories</strong>
					</li>
				</ol>
			</div>
			<div class="col-lg-2">

			</div>
		</div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="ibox-content m-b-sm border-bottom">
				<form action="<?php echo e(action('admin\AccessoryController@accessoryUpdateCategory')); ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="acccat_edit">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<input type="hidden" name="categoryToken" value="<?php echo base64_encode($categoryData->id); ?>"/>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="order_id">Brand Name</label>
								<input type="text" id="categoryName" name="categoryName" value="<?php  echo $categoryData->categoryName;?>" placeholder="Brand Name" class="form-control">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="order_id">Select Parent Category</label>
								<select class="form-control m-b" name="parentCategory">
								<option value="">Select Category</option>
								<option <?php if($categoryData->parent_id==0){echo 'selected=selected';} ?> value="<?php echo base64_encode('0'); ?>">None</option>
									<?php 
										$category=DB::table('accessory_category')->where('id','!=',$categoryData->id)->where('deleted_at','=',NULL)->get();
										foreach($category as $cat){
											if($categoryData->id !=$cat->parent_id){
												if($cat->id == $categoryData->parent_id){$selected='selected=selected';}else{$selected='';}
												echo '<option '.$selected.' value="'.base64_encode($cat->id).'">'.$cat->categoryName.'</option>';
											}
										}
									?>
								</select>
							</div>
						</div>
						<div class="clearfix"> </div>
						 
						<div class="col-sm-2">
							 <?php 
									if(!empty($categoryData->categoryAvatar)){
										$cavatar='uploads/accessoriescategories/'.$categoryData->categoryAvatar;
									} else{
										$cavatar='assets/img/placeholder300x300.png';
									}
									
									?>
									<label class="control-label" for="order_id">Current Image</label><br/><br/>
										 
							 <img alt="image" class="img-circles" src="<?php echo e(asset($cavatar)); ?>" style="width:100px;">
						</div>
						<div class="col-sm-8">
							<div class="btn-group">
								<label class="control-label" for="order_id">&nbsp;</label><br/>
								<div class="fileupload fileupload-new" data-provides="fileupload">
									<span class="btn btn-primary btn-file invoice-pdf">
										<span class="fileupload-new">Select image</span>
										<span class="fileupload-exists">Change</span>         
										<input type="file"  accept="image/*" name="categoryAvatar" id="categoryAvatar"/>
									</span>
									<span class="fileupload-preview"></span>
									<a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none"><i class="fa fa-times" aria-hidden="true"></i></a>
								</div>
								
								
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label" for="order_id">&nbsp;</label><br/>
								<input type="submit" class="btn btn-primary" value="Update" >
							</div>
						</div>
					</div>
				</form>
            </div>
             
        </div>
			<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
		<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<!--<script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-form-validation.js')); ?>"></script>-->
		<script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>
		<script  src="<?php echo e(asset('assets/js/plugins/iCheck/icheck.min.js')); ?>"></script>
	  <script src="<?php echo e(asset('assets/js/jquery-fileupload-btn.js')); ?>"></script>
        <script>
            $(document).ready(function () {
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
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
				 $('#acccat_edit')
                            .formValidation({
                        framework: 'bootstrap',
                            excluded: ':disabled',
                            message: 'This value is not valid',
                            icon: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                
								categoryAvatar: {
									validators: {
										 
										file: {
											extension: 'jpeg,jpg,png',
											type: 'image/jpeg,image/png',
											maxSize: 2097152,   // 2048 * 1024
											message: 'Please select only Image file Less then 2MB!'
										}
									}
								},
								categoryName: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter category Name'
                                        },
                                        stringLength: {
                                            min: 2,
                                            max: 30,
                                            message: 'The Field must be more than 2 characters long'
                                        }
                                    }
                                },
                            }
                    });
            });
		</script>
  <?php $__env->stopSection(); ?>     
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>