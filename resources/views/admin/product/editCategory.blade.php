@extends('admin.layouts.masteradmin')
@section('pagecss')
    <link href="css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
@stop
@section('contentPages')
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-10">
				<h2>Edit Category</h2>
				<ol class="breadcrumb">
					<li>
						<a href="{{URL::to('/admin')}}">Home</a>
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
				<form action="{{action('admin\ProductController@updateCategory')}}" method="post" enctype="multipart/form-data" class="form-horizontal">
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
										$category=DB::table('category')->where('id','!=',$categoryData->id)->where('deleted_at','=',NULL)->get();
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
						<div class="col-sm-3">
							<label class="control-label" for="order_id">&nbsp;</label><br/>
							 <div class="i-checks"><label> <input type="checkbox" <?php if($categoryData->showforall =='1'){echo 'checked';} ?> name="showforall" value="1"> <i></i> Show for all dealer </label></div>
						</div>
						<div class="col-sm-3" style="display:none;visibility: hidden;">
							<label class="control-label" for="order_id">&nbsp;</label><br/>
							 <div class="i-checks"><label> <input type="checkbox" <?php if($categoryData->asAccessory =='1'){echo 'checked';} ?> name="asAccessory" value="1"> <i></i>  Set as Accessory </label></div>
						</div>
                        <input type="hidden" name="asAccessory" value="0">
						<div class="col-sm-12">
							 <?php 
									if(!empty($categoryData->categoryAvatar)){
										$cavatar='uploads/categories/'.$categoryData->categoryAvatar;
									} else{
										$cavatar='assets/img/placeholder300x300.png';
									}
									
									?>
									<label class="control-label" for="order_id">Current Image</label><br/><br/>
										 
							 <img alt="image" class="img-circles" src="{{asset($cavatar)}}" style="width:100px;">
						</div>
						<div class="col-sm-2">
							<div class="btn-group">
								<label class="control-label" for="order_id">&nbsp;</label><br/>
								<label title="Upload image file" for="inputImage" class="btn btn-primary">
									<input type="file" accept="image/*" name="categoryAvatar" required id="inputImage" class="hide">
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
		<script  src="{{asset('assets/js/plugins/iCheck/icheck.min.js')}}"></script>
        <script>
            $(document).ready(function () {
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
            });
		</script>
  @stop()     