
<?php $__env->startSection('contentPages'); ?>
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-10">
				<h2>Add Brands</h2>
				<ol class="breadcrumb">
					<li>
						<a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
					</li>
					<li>
						<a>Store</a>
					</li>
					<li class="active">
						<strong>Brands</strong>
					</li>
				</ol>
			</div>
			<div class="col-lg-2">

			</div>
		</div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="ibox-content m-b-sm border-bottom">
				<form action="<?php echo e(action('admin\ProductController@addBrand')); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="order_id">Brand Name</label>
								<input type="text" id="brandName" name="brandName" value="" placeholder="Brand Name" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="btn-group">
							<label class="control-label" for="order_id">&nbsp;</label><br/>
								<label title="Upload image file" for="inputImage" class="btn btn-primary">
									<input type="file" accept="image/*" name="brandAvatar" required id="inputImage" class="hide">
									Upload new image
								</label>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="order_id">&nbsp;</label><br/>
								<input type="submit" class="btn btn-primary" value="Save" >
							</div>
						</div>
					</div>
				</form>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
                            <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
                                <thead>
									<tr>
										<th>Brand Image</th>
										<th data-hide="phone">Brand Name</th>
										<th class="text-right">Action</th>
									</tr>
                                </thead>
                                <tbody>
								<?php 
									if(!empty($branddata)){
										foreach($branddata as $brand){
									
								?>
									<tr>
										<td>
										<?php 
											if(!empty($brand->brandAvatar)){
												$cavatar='uploads/brands/'.$brand->brandAvatar;
											} else{
												$cavatar='assets/img/placeholder300x300.png';
											}
											
											?>
										 
										   <img alt="image" class="img-circles tbl-listing-img" src="<?php echo e(asset($cavatar)); ?>">
										</td>
										<td>
											<?php echo $brand->brandName; 
											 $url = URL::to('admin/editBrand', base64_encode($brand->id));
											 $urldel = URL::to('admin/deleteBrand', base64_encode($brand->id));
											?>
										</td>
										<td class="text-right">
											<div class="btn-group">
												<a href="<?php echo e(URL::to($url)); ?>" class="btn-white btn btn-xs">Edit</a>
												<a href="<?php echo e(URL::to($urldel)); ?>" class="btn-white btn btn-xs">Delete</a>
											</div>

										</td>
									</tr> 
												
									<?php
									
										}
									}
									?>
                                </tbody>
                                <tfoot>
									<tr>
										<td colspan="7">
											<ul class="pagination pull-right"></ul>
										</td>
									</tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
		<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-form-validation.js')); ?>"></script>
  <?php $__env->stopSection(); ?>     
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>