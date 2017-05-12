@extends('admin/layouts/masteradmin')
@section('pagecss')
	<link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
@stop()
@section('contentPages')
			<div class="wrapper wrapper-content">
				<div class="row">
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5>Orders</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php
								$orderCount= DB :: table('product_order')->where('dealerID','=',base64_decode($id))->where('deleted_at','=',NULL)->count();
								echo $orderCount;
								?></h1></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>All orders</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5>Pending Orders</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php
								$orderCount= DB :: table('product_order')->where('dealerID','=',base64_decode($id))->where('deleted_at','=',NULL)->where('orderStatus','=','pending')->count();
								echo $orderCount;
								?></h1></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>All orders</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5>Cancelled Orders</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php
								$orderCount= DB :: table('product_order')->where('dealerID','=',base64_decode($id))->where('deleted_at','=',NULL)->where('orderStatus','=','cancelled')->count();
								echo $orderCount;
								?></h1></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>All orders</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5>Completed Orders</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php
								$orderCount= DB :: table('product_order')->where('dealerID','=',base64_decode($id))->where('deleted_at','=',NULL)->where('orderStatus','=','completed')->count();
								echo $orderCount;
								?></h1></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>All orders</small>
                            </div>
                        </div>
					</div>
				</div>
				<div class="row">
					<div class="wrapper wrapper-content animated fadeInRight ecommerce">
						<div class="col-lg-12">
							<div class="ibox">
								<div class="ibox-content">
								   <div class="table-responsive">
										<table class="table table-striped table-bordered table-hover dataTables-example" id="dealer_order_table" style="width:100%;" >
											<thead>
												<tr>
													<th >Order Number</th>
													<th >Product Name</th>
													<th >Color</th>
													<th >Qty</th>
													<th >Amount</th>
													<th >Status</th>
													<th>Action</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
       
	@stop()
@section('pagescript')
	@include('admin.includes.commonscript')

	  <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
	
	<script type="text/javascript">
	$(function() {
		var customer_table = $('#dealer_order_table').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
						buttons: [
                            /* {extend: 'csv', title:'Product Details'},
                            {extend: 'excel', title:'Product Details'},
                            {extend: 'pdf', title:'Product Details'}, */
                        ],
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "{{ URL::to('admin/ajax/log/adminDealerOrderList') }}",
                            data:{id:'<?php echo $id; ?>' }
                        },
                        "columns": [
                            {data: 'Order Number', name: 'OrderNumber'},
                            {data: 'Product Name', name: 'product_id'},
                            {data: 'Color', name: 'variationID'},
                            {data: 'Qty', name: 'qty'},
                            {data: 'Amount', name: 'final_price'},
                            {data: 'Status', name: 'orderStatus'},
                            {data: 'Action', name: 'Action', orderable: false, searchable: false},
                        ]

                    });
    });
	</script>
@stop()