@extends('admin.layouts.masteradmin')
@section('pagecss')
    <link href="{{asset('assets/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
@stop
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Product list</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{URL::to('/admin')}}">Home</a>
                        </li>
                        
                        <li class="active">
                            <strong>Product list</strong>
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
							<a href="{{action('admin\ProductController@addProducts')}}" class="btn btn-w-m btn-primary">Add Products</a>
						</div>
					</div>
				</div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
                           <div class="table-responsive">
						   <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
							<table class="table table-striped table-bordered table-hover dataTables-example" id="prodcut_tables" style="width:100%;" >
                                <thead>
									<tr>
										<th >Product Name</th>
										<th >Category</th>
										<th >Brand</th>
										<th >Price</th>
										 
										<th >Description</th>
										<th >Status</th>
										<th>Action</th>
									</tr>
                                </thead>
                                <tbody>
									<?php
										$productDatas=DB::table('products')->where('deleted_at','=',NULL)->get();
										foreach($productDatas as $productData){
											$variations=DB::table('variation')->where('deleted_at','=',NULL)->where('product_id','=',$productData->product_id)->first();
											
											$category=DB::table('category')->where('id','=',$productData->category_id)->first();
											$brand=DB::table('brand')->where('id','=',$productData->brand_id)->first();
											$url = URL::to('admin/editProducts', base64_encode($productData->product_id));
											//$urldel = URL::to('admin/deleteProducts', base64_encode($productData->product_id));
											$idprd="'".base64_encode($productData->product_id)."'";
											if($productData->visibility == 1){
												$stuts='<span class="label label-primary">Enable</span>';
											}else{
												$stuts='<span class="label label-danger">Disable</span>';
												
											}
											$string = strip_tags($productData->description);

											if (strlen($string) > 100) {

												// truncate string
												$stringCut = substr($string, 0, 100);

												// make sure it ends in a word so assassinate doesn't become ass...
												$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... '; 
											}
											 
									?>
									<tr>
										<td><?php echo $productData->productName ;?></td>
										<td><?php echo $category->categoryName ;?></td>
										<td><?php echo $brand->brandName ;?></td>
										<td><?php if(!empty($productData->real_price)){echo '&pound;'.$productData->real_price;}else{ echo '---';}?></td>
										<td><?php echo $string ; ?></td>
										<td><?php echo $stuts;?></td>
										<td><?php echo '<a href="'.$url.'"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a><a href="javascript:void(0)" onclick="removedata('.$idprd.')"  data-toggle="tooltip" title="Delete " class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a' ;?>
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
		@stop()
@section('pagescript')
		@include('admin.includes.commonscript')
		<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
		<script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
        <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
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
                            url: "{{ URL::to('admin/ajax/log/productdatalist') }}",
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
			function removeData(valid){
				
				var product= valid;
				if( product != ''){
				//console.log(accssory);
					 swal({
						title: "Are you sure?",
						text: "This product Will be deleted?",
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
							 
							 window.location.replace("{{ URL::to('admin/deleteProducts') }}/"+product);
							 
						}	 
					}); 
				}
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
		
  @stop()     