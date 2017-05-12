@extends('dealer.layouts.masterdealer')
@section('pagecss')
     
	 <link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
@stop
@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Special Order list</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/dealer')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/dealer/specialorders')}}">All Special Order List</a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Special Order List</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="{{URL::to('/dealer/specialorderadd')}}"  style="background-color: #18A689;">
                                    <i class="fa fa-users">&nbsp;&nbsp;</i>Add Special Order
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="specialorder_table" >
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Batch</th>
                                        <th>Color</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Action</th>
										<th style="max-width: 17px;">Admin Notes </th>
                                    </tr>
                                    </thead>
									<tbody>
										<?php 
										 $sessionData=Session::get('dealerLog');
										$id = $sessionData['dealerID'];
											$getSpeacialOrders=DB::table('special_order')->where('dealerID','=',$id)->where('is_noramlOrder','=','0')->where('deleted_at','=',NULL)->get();
											foreach($getSpeacialOrders as $spOrder){
												?>
											<tr>
												<td>
													<?php 
														if($spOrder->product_id != '')
														{
															$result_data = DB::table('products')->where('product_id','=',$spOrder->product_id)->first();
															if(!empty($result_data)){
																echo $result_data->productName;
															}else{
																echo '---';
															}
														}else{
															echo '---';
														}
													?>
												</td> 
												<td>
													<?php 
														if($spOrder->productbatch != '')
														{
															 
															echo $spOrder->productbatch;
															 
														}else{
															echo '---';
														}
													?>
												</td>
												<td>
													<?php 
														if($spOrder->product_color != '')
														{
															echo $spOrder->product_color;
														}else{
															echo '---';
														}
													?>
												</td>
												<td>
													<?php 
														if($spOrder->orderstatus != '')
														{
															if($spOrder->orderstatus == 'pending'){
																echo '<label class="label label-warning" style="text-transform:capitalize;">'.$spOrder->orderstatus.'</label>';
																
															}else if($spOrder->orderstatus=='booked') {
																echo '<label class="label label-danger" style="text-transform:capitalize;background-color:#F7609E;">'.$spOrder->orderstatus.'</label>';
																
															}else if($spOrder->orderstatus=='invoice'){
																echo '<label class="label label-danger" style="text-transform:capitalize;">'.$spOrder->orderstatus.'</label>';
																
															}else if($spOrder->orderstatus=='info'){
																echo '<label class="label label-success" style="text-transform:capitalize;">'.$spOrder->orderstatus.'</label>';
																
															}else{
																echo '<label class="label label-success" style="text-transform:capitalize;">'.$spOrder->orderstatus.'</label>';
															}
														}else{
															echo '---';
														}
													?>
												</td>
											
												<td>
													<?php 
														if($spOrder->today_date != '')
														{
															echo date('d-m-Y',strtotime($spOrder->today_date));
														}else{
															echo '---';
														}
													?>
												</td>
												<td>
													 <?php 
													 $uniqueQtyNumber = $spOrder->OrderNumber;
													   $notes=DB::table('admin_specialorder_notes')->where('orderTokenString','=',$uniqueQtyNumber)->first();
													   if(!empty($notes)){
														   $notestitle=$notes->name;
														   $datanotes=$notes->description;
													   }else{
														   $notestitle='No Notes Available';
														   $datanotes='<i class="fa fa-frown-o" aria-hidden="true" style="font-size:85px;color:#ccc;"></i>';
													   }
													   $html = '';
														$deleted = $spOrder->id;
														$html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('$deleted')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash-o\"></i></a>";
															$deatailurl = URL::to('dealer/dealerspecialorderdetail', base64_encode($spOrder->id));
																$html.= '<a href="'.$deatailurl.'"  data-toggle="tooltip" title="View order" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>';		
														echo  $html;
													   $popupdata='';
													 $popupdata .= '<div class="modal inmodal" id="adminnotes'.$spOrder->product_id.'" tabindex="-1" role="dialog"  aria-hidden="true">
																	<div class="modal-dialog">
																		<div class="modal-content animated fadeIn">

																				<div class="modal-header">
																					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																					<h4 class="modal-title">'.$notestitle.'</h4>
																				</div>
																				<div class="modal-body col-md-12">
																					<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																						<p style="text-align:center;">'.$datanotes.'</p> 
																						
																					</div>
																					<div class="clearfix"></div>
																				</div>
																				<div class="modal-footer">
																				 <div class="clearfix"></div>
																				 <br/>
																					<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																					
																				</div>
																			</form>
																		</div>
																	</div>
																</div>';
													echo  $popupdata;
													 ?>
												</td>
												<td>
													<?php 
													 
													 echo '<a htef="javascript:void(0)" class="btn btn-xs btn-default" title="Admin Notes" data-toggle="modal" data-target="#adminnotes'.$uniqueQtyNumber.'" ><i class="fa fa-file-text"></i></a>';
														 
														   $notes=DB::table('admin_order_notes')->where('orderTokenString','=',$uniqueQtyNumber)->where('product_id','=',$spOrder->product_id)->orderBy('admin_order_notesID','desc')->get();
															$actionadm=URL::to('/dealer/addordernotestoadmin/');
														$delaerName=DB::table('dealer')->where('id','=',$spOrder->dealerID)->first();
														  
														   
													?>
													<div class="modal inmodal fade" id="adminnotes<?php echo $uniqueQtyNumber; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
														<div class="modal-dialog modal-lg bs-example-modal-lg">
															<div class="modal-content">
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																	<h4 class="modal-title">Order Notes</h4>
																</div>  
																<form action="<?php echo $actionadm; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
																	<div class="modal-body col-md-12" style="max-height: 350px; overflow-y: scroll;">
																		<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																		<?php 
																			$sender= $sessionData['first_name'].' (Dealer)'
																			//echo $sender;
																				?>
																		<input type="hidden" name="opentab" value="pending"/>
																			<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
																			<input type="hidden" name="productToken" value="<?php echo  base64_encode($spOrder->product_id) ;?>'"/>
																			<input type="hidden" name="dealerID" value="<?php echo $sessionData['dealerID']; ?>"/>
																			<input type="hidden" name="sender" value="<?php echo $sender; ?>"/>
																			<input type="hidden" name="sendertype" value="<?php echo $sessionData['role']; ?>"/>
																			<input type="hidden" name="orderToken" value="<?php echo  base64_encode('0'); ?>"/>
																			<input type="hidden" name="orderTokenString" value="<?php echo $uniqueQtyNumber; ?>"/>
																			<input type="hidden" name="ordertype" value="special"/>
																			<!--<div class="form-group" style="width: 100%;">
																				<label class="control-label">Name:</label><br/>
																				<input type="text" style="width:100%" required name="name" class="form-control" placeholder="Name" value="" >
																			</div><br/><br/>-->
																					
																		</div>
																		<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
																			<div class="form-group" style="width: 100%;">
																			<label class="control-label">Notes:</label><br/>
																			<textarea style="width:100%"  required name="description" class="form-control" placehoder="Notes"></textarea></div>
																		</div>
																		<div class="clearfix"></div>
																		 
																			<?php
																			//	   print_r($notes); exit;
																			 if(isset($notes) && !empty($notes))
																			 { 
																			foreach($notes as $note)
																			{
																				if(!empty($note->name) || !empty($note->description)){
																					 $today = date('Y-m-d H:i:s');
																						$a = new DateTime($today);
																						$b = new DateTime($note->created_at);
																						$difference_time = $a->diff($b);

																						$time_text = '';
																						if(($difference_time->format("%d") != 0)){
																							$time_text.= $difference_time->format("%d").'days';
																							$time_text.= ' ';

																						}else if(($difference_time->format("%d") == 0) && ($difference_time->format("%h") != 0)){
																						   $time_text.= $difference_time->format("%h").'h';
																							$time_text.= ' ';
																						}
																						$time_text.= $difference_time->format("%i").'m ago';
																						if($note->sender=='admin'){
																							$sender='<small class="label label-info"> Admin</small>&nbsp;';
																						}else{
																							$sender='<small class="label label-success"> Dealer</small>&nbsp;&nbsp;'.$delaerName->first_name.'&nbsp;'.$delaerName->last_name;
																						}
																				   echo'<h3>'.$sender.'<small class="pull-right text-navy">'.$time_text.'</small></h3>';
																				   echo '<p>'.$note->description.'</p>
																				   <small class="text-muted">
																						Time : '. date('H:i A',strtotime($note->created_at)) .' - '. date('Y-m-d',strtotime($note->created_at)).'
																					</small>
																				   <hr/>';
																				   
																				}else{
																				   $notestitle='No Notes Available';
																				   $datanotes ='<i class="fa fa-frown-o" aria-hidden="true" style="font-size:85px;color:#ccc;"></i>';
																			   }
																			}
																															 
																		 }else{
																			 
																		 }
																		 
																	?>
																			 
																			
																	</div>
																	<div class="clearfix"></div>
																	<div class="modal-footer">
																		<div class="clearfix"></div>
																		 
																		<input type="submit" class="btn btn-primary" value="Save changes" />
																		<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																	</div>
																</form>
															</div>
														</div>
													</div>
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
        <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>
		@stop()

        @section('pagescript')
            @include('dealer.includes.commonscript')

            <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
            <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/sweetalert/sweetalert.min.js')}}"></script>
            <script type="text/javascript">
                deletedata = null;
                $(function (){

                    var customer_table = $('#specialorder_table').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
                        buttons: [
                            /* {extend: 'csv', title:'Customer Details'},
                            {extend: 'excel', title:'Customer Details'},
                            {extend: 'pdf', title:'Customer Details'}, */
                        ],
                      /*  "aoColumnDefs": [ { "bSortable": true, "aTargets": [ 1,2 ] } ],
                        "iDisplayLength": 20,
                        "aLengthMenu": [[10, 20, 30, 50, 100, 200, 500, -1], [10, 20, 30, 50, 100, 200, 500, "All"]],
                        "processing": true,
                        "serverSide": true,
                         "ajax": {
                            url: "{{ URL::to('dealer/ajax/log/specialdata') }}",
                            data:function(d){  }
                        },
                        "columns": [
                            {data: 'Product', name: 'product_id'},
                            {data: 'Color',  name: 'product_color'},
                            {data: 'Status', name: 'orderstatus'},
                            {data: 'Action', name: 'comments', orderable: false, searchable: false},
                        ]
 */
                    });


                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                                title: "Are you sure?",
                                text: "You will not be able to recover this  data",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes, delete Order!",
                                cancelButtonText: "No, cancel!",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            },
                            function(isConfirm){
                                if (isConfirm)
                                {
                                    $.ajax
                                    ({
                                        type: "POST",
                                        url: "{{ URL::to('dealer/ajax/log/specialorderdelete') }}",
                                        data: {	special_orderID:id, _token:tokendata },
                                        success: function (result) {
                                            swal("Deleted!", "Your Order has been deleted.", "success");
                                            setTimeout(function (){
                                                window.location.reload();
                                            },3000);
                                        }
                                    });
                                } else {
                                    swal("Cancelled", "Your Order is safe :)", "error");
                                }
                            });
                    }
                    deletedata = deleted;

                });

                function deleted(id){
                    if(id != 0 && id != ''){
                        deletedata(id);
                    }
                }
            </script>
        @stop()