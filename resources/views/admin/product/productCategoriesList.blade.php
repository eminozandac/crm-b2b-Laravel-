@extends('admin.layouts.masteradmin')
@section('pagecss')
    <link href="css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
@stop

@section('contentPages')
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-10">
				<h2>Add Product Categories</h2>
				<ol class="breadcrumb">
					<li>
						<a href="{{URL::to('/admin')}}">Home</a>
					</li>
					<li>
						<a>Store</a>
					</li>
					<li class="active">
						<strong>Product Categories</strong>
					</li>
				</ol>
			</div>
			<div class="col-lg-2">

			</div>
		</div>
        <div class="wrapper wrapper-content animated fadeInRight ecommerce">
            <div class="ibox-content m-b-sm border-bottom">
			<form action="{{action('admin\ProductController@productCategoriesAdd')}}" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="order_id">Category Name</label>
								<input type="text" id="categoryName" name="categoryName" value="" placeholder="Category Name" class="form-control">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label" for="order_id">Select Parent Category</label>
								<select class="form-control m-b" name="parentCategory">
								<option value="">Select Category</option>
									<?php 
										$category=DB::table('category')->where('deleted_at','=',NULL)->get();
										foreach($category as $cat){
											echo '<option value="'.base64_encode($cat->id).'">'.$cat->categoryName.'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="btn-group">
							<label class="control-label" for="order_id">&nbsp;</label><br/>
								<label title="Upload image file" for="inputImage" class="btn btn-primary">
									<input type="file" accept="image/*" name="categoryAvatar" id="inputImage" class="hide">
									Upload new image
								</label>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-3">
							 <div class="i-checks"><label> <input type="checkbox" name="showforall" value="1"> <i></i> Show for all dealer </label></div>
						</div>
						 
						<div class="col-sm-2">
							<div class="form-group">
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
                                    <th>Category Image</th>
                                    <th data-hide="phone">Category Name</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                               <?php 
							   $categoryDatas=DB::table('category')->where('deleted_at','=',NULL)->get();
							   $tree='';
									if(!empty($categoryDatas)){
										$rows = array();
											$mk=0;
										foreach($categoryDatas as $data){
											foreach($data as $k=>$v){
												$rows[$mk][$k]= $v;
											}
											$mk++;
										} 
										 
										//print_r($rows);
										function buildTree(array $elements, $parentId = 0) {
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

										$tree = buildTree($rows);
										 
									}
									displayArrayRecursively($tree);
									function displayArrayRecursively($arr,$iden='') {
										if ($arr) {
											 foreach ($arr as $v) {
												// print_r($v);
												if($iden!=''){$var=$iden;}else{$var='';}
												$url = URL::to('admin/editCategory', base64_encode($v['id']));
												$urldel = URL::to('admin/deleteCategory', base64_encode($v['id']));
												if(!empty($v['categoryAvatar'])){
													$cavatar=URL::to('uploads/categories/'.$v['categoryAvatar']);
												} else{
													$cavatar='assets/img/placeholder300x300.png';
												}
												 echo'<tr><td><img alt="image" class="img-circles tbl-listing-img" src="'.$cavatar.'"></td><td style="font-size: 16px;">'.$var.$v['categoryName'].'</td><td align="right"><div class="btn-group">
														<a href="'.$url.'" class="btn-white btn btn-xs">Edit</a>
														<a href="'.$urldel.'" class="btn-white btn btn-xs">Delete</a>
													</div></td></tr>';
												 $arc=0;
													if (isset($v['children'])){
														if (is_array($v['children'])) {
															displayArrayRecursively($v['children'],$iden .'- ');
														} 
													} 
											}
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
		@stop()
@section('pagescript')
		@include('admin.includes.commonscript')
		<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
		<script src="js/plugins/iCheck/icheck.min.js"></script>
        <script>
            $(document).ready(function () {
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
				 
            });
		</script>
@stop()
  