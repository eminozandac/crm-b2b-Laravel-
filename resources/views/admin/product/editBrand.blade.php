@extends('admin.layouts.masteradmin')
@section('contentPages')
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-10">
				<h2>Edit Brands</h2>
				<ol class="breadcrumb">
					<li>
						<a href="{{URL::to('/admin')}}">Home</a>
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
				<form action="{{action('admin\ProductController@updateBrand')}}" method="post" enctype="multipart/form-data" class="form-horizontal">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<input type="hidden" name="brandToken" value="<?php echo base64_encode($brandData->id); ?>"/>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="order_id">Brand Name</label>
								<input type="text" id="brandName" name="brandName" value="<?php  echo $brandData->brandName;	?>" placeholder="Brand Name" class="form-control">
							</div>
						</div>
						<div class="col-sm-12">
							 <?php 
									if(!empty($brandData->brandAvatar)){
										$cavatar='uploads/brands/'.$brandData->brandAvatar;
									} else{
										$cavatar='assets/img/placeholder300x300.png';
									}
									
									?>
									<label class="control-label" for="order_id">Current Image</label><br/>
										 
							 <img alt="image" class="img-circle" src="{{asset($cavatar)}}" style="width:100px;">
						</div>
						<div class="clearfix"> </div>
						<div class="col-sm-2">
							<div class="btn-group">
								<label class="control-label" for="order_id">&nbsp;</label><br/>
								<label title="Upload image file" for="inputImage" class="btn btn-primary">
									<input type="file" accept="image/*" name="brandAvatar" required id="inputImage" class="hide">
									Upload new image
								</label>
							</div>
						</div>
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
			@stop()
@section('pagescript')
			@include('admin.includes.commonscript')
			<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
  @stop()     