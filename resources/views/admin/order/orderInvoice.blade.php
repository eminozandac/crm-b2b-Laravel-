@extends('admin.layouts.masteradmin')
@section('pagecss')
    
@stop
@section('contentPages')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-8">
                    <h2>Invoice</h2>
					<?php 
					//print_r($orderData->dealerID);
					$dealerData=DB::table('dealer')->where('id','=',$orderData->dealerID)->first();
					?>
                    <ol class="breadcrumb">
                        <li>
                            <a href="#">Home</a>
                        </li>
                        <li>
                            Other Pages
                        </li>
                        <li class="active">
                            <strong>Invoice</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-4">
                    <div class="title-action">
                        <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
                        <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
                        <a href="invoice_print.html" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print Invoice </a>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInRight">
                    <div class="ibox-content p-xl">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5>From:</h5>
                                    <address>
                                        <strong>Corporate, Inc.</strong><br>
                                        112 Street Avenu, 1080<br>
                                        Miami, CT 445611<br>
                                        <abbr title="Phone">P:</abbr> (123) 601-4590
                                    </address>
                                </div>

                                <div class="col-sm-6 text-right">
                                    <h4>Invoice No.</h4>
                                    <h4 class="text-navy">INV-000567F7-00</h4>
                                    <span>To:</span>
                                    <address>
                                        <strong><?php  echo $dealerData->first_name.'&nbsp;'.$dealerData->last_name; ?></strong><br>
										<?php echo $dealerData->billing_address; ?><br>
										<?php echo $dealerData->billing_city.', '.$dealerData->billing_zipcode; ?><br>
										<?php echo $dealerData->billing_state.', '.$dealerData->billing_country; ?><br>
                                        
                                        <abbr title="Phone">P:</abbr> <?php echo $dealerData->phone; ?>
                                    </address>
                                    <p>
                                        <span><strong>Invoice Date:</strong><?php if(!empty($dealerData->invoiceDate)){echo $dealerData->invoiceDate;}else{echo '--';}?></span><br/>
                                        <span><strong>Due Date:</strong> <?php if(!empty($dealerData->duDate)){echo $dealerData->duDate;}else{echo '--';}?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="table-responsive m-t">
                                <table class="table invoice-table">
                                    <thead>
                                    <tr>
                                        <th>Item List</th>
                                        <th>Quantity</th>
                                        <th>Real Price</th>
                                        <th>Sale Price</th>
                                        <th>Discount</th>
                                        <th>Tax</th>
                                        <th>Total Price</th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php $productsata=DB::table('products')->where('product_id','=',$orderData->product_id)->first(); ?>
                                    <tr>
                                        <td><div><strong><?php echo $productsata->productName; ?></strong></div>
                                            <small><?php echo $productsata->description; ?></small></td>
                                        <td><?php echo $orderData->qty; ?></td>
                                        <td><?php echo $orderData->qty; ?></td>
                                        <td>$5.98</td>
                                        <td>$5.98</td>
                                        <td>$5.98</td>
                                        <td>$31,98</td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div><!-- /table-responsive -->

                            <table class="table invoice-total">
                                <tbody>
                                <tr>
                                    <td><strong>Sub Total :</strong></td>
                                    <td>$1026.00</td>
                                </tr>
                                <tr>
                                    <td><strong>TAX :</strong></td>
                                    <td>$235.98</td>
                                </tr>
                                <tr>
                                    <td><strong>TOTAL :</strong></td>
                                    <td>$1261.98</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="text-right">
                                <!--<button class="btn btn-primary"><i class="fa fa-dollar"></i> Make A Payment</button>-->
                            </div>

                            <div class="well m-t"><strong>Comments</strong>
                                It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less
                            </div>
                        </div>
                </div>
            </div>
       </div>
	   @stop()
@section('pagescript')
		@include('admin.includes.commonscript')
	<script src="{{ asset('assets/js/plugins/pace/pace.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
  
	 
@stop()
         