@extends('admin.layouts.masteradmin')
@section('pagecss')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	
@stop()
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Order Details</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{URL::to('/admin')}}">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Order Details</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-md-12">
                    <div class="ibox">
                        <div class="ibox-title">
                          
                            <h5>Items in Order</h5>
                        </div>
						 
						
                        <div class="ibox-content" >
                            <div class="table-responsive">
								<div class="col-md-12">
									<h2>Order Details</h2>
									<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
										<thead>
											<tr>
												<th >Product Name</th>
												<th>Category</th>
												<th>Brand</th>
												<th style="min-width: 60px;">Order Date</th>
												<th >Delivery Date</th>
												<th >Qty</th>
												<th >Status</th>
												<th >Price</th>
											 
												 
												 
											</tr>
										</thead>
										<tbody>
											<?php
											$getOrders=DB::table('accessory_order')->where('accessory_order_ID','=',$accesotyOrderID)->where('deleted_at','=',NULL)->first();
											
												$getOrderss=DB::table('accessory_order_tranz')->where('accessory_order_ID','=',$accesotyOrderID)->where('deleted_at','=',NULL)->get();
												foreach($getOrderss as $order){
													
													$dealername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
													
													$accessoryData=DB::table('product_accessories')->where('accessoryID','=',$order->accessoryID)->first();
													
													$catname=DB::table('accessory_category')->where('id','=',$accessoryData->category_id)->first();
													
													$brandname=DB::table('brand')->where('id','=',$accessoryData->brand_id)->first();
													
													$detailURL=URL::to('admin/accessoryordersdetail', base64_encode($order->accessory_order_tranz_ID));
													
													?>
											<tr>
												<td><?php echo $accessoryData->accessory_name; ?></td>
												<td><?php echo $catname->categoryName; ?></td>
												<td><?php echo $brandname->brandName; ?></td>
												<td><?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at)); }else{ echo '--';}?></td>
												<td><?php if(!empty($order->delivery_date)){echo date('d-m-Y',strtotime($order->delivery_date)); }else{ echo '--';}?></td>
												<td><?php echo $order->order_qty; ?></td>
												<td><?php 
													 if($order->orderStatus=='pending'){
														echo '<label class="label label-warning" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 } if($order->orderStatus=='invoiced'){
														echo '<label class="label label-danger" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 }
													 if($order->orderStatus=='paid'){
														echo '<label class="label label-info" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 }
													 if($order->orderStatus=='complete'){
														echo '<label class="label label-success" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
														 
													 }
												
												?></td>
												<td><?php  echo '&pound;'.$order->price; ?></td>
												 
											 
												 
											</tr>
												<?php
												}
											?> 
										</tbody>
									</table>
						
								</div>
								<div class="claerfix"></div>
								<div class="col-md-6">
									<?php 
										 
										if(!empty($dealername->address)){
											echo '<hr/><h2>Address</h2>';
											echo '<p>'.$dealername->address.'</p>';
										}
										if(!empty($dealername->pincode)){
											
											echo '<p><strong>Pin Code : </strong>'.$dealername->pincode.'</p>';
										}
									?>
								</div>	 
								<div class="col-md-6">
									<?php 
										if(!empty($getOrders->order_notes)){
											echo '<hr/><h2>Order Notes</h2>';
											echo '<p>'.$getOrders->order_notes .'</p>';
											 
										}
										 
									?>
								</div>	 
								<div class="claerfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
			@stop()
@section('pagescript')
		@include('admin.includes.commonscript')
			<script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
			<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
			<script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
           <script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
			<script>
				$(function(){
					 $('.date').datepicker({
                        todayBtn: "linked",
                        keyboardNavigation: false,
                        forceParse: false,
                        calendarWeeks: true,
						format: 'd-m-yyyy',
                        autoclose: true
                    });
					 var config = {
						'.chosen-select'           : {},
						'.chosen-select-deselect'  : {allow_single_deselect:true},
						'.chosen-select-no-single' : {disable_search_threshold:10},
						'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
						'.chosen-select-width'     : {width:"95%"}
					} 
					for (var selector in config) {
						$(selector).chosen(config[selector]);
					}
		
						
					$(".select2_demo_1").select2();
					$(".select2_demo_2").select2();
					$(".select2_demo_3").select2({
						placeholder: "Select a Product",
						allowClear: true
					});
					
					$('#order_edit').find('[name="product_name"]')
                            .change(function(e) {
                                $('#product_name').formValidation('revalidateField', 'product_name');
                            })
                            .end()
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
                                product_name: {
                                    validators: {
                                        callback: {
                                            message: 'Please Select Staff',
                                            callback: function(value, validator, $field) {
                                                /* Get the selected options */
                                                var options = validator.getFieldElements('product_name').val();
                                                return (options != null);
                                            }
                                        }
                                    }
                                },
                                title: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Title'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 30,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                },
                                assign_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Date'
                                        },
                                        date: {
                                            format: 'DD-MM-YYYY',
                                            max: 'completion_date',
                                            message: 'The Assign date is not a valid'
                                        }
                                    }
                                },
                                completion_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Date'
                                        },
                                        date: {
                                            format: 'DD-MM-YYYY',
                                            min: 'assign_date',
                                            message: 'The Completion date is not a valid'
                                        }
                                    }
                                },
                                description: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Description !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 200,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                }
                            }
                    });
				});
			</script>
@stop()