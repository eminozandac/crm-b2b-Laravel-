@extends('dealer.layouts.masterdealer')

@section('pagecss')
    <!-- Toastr style -->
    <link href="{{ asset('assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="http://localhost/b2bcrm/assets/css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/summernote/summernote.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/summernote/summernote-bs3.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
@stop

<?php
	$sessionData=Session::get('dealerLog');
	$id = $sessionData['dealerID'];
    function limit_words($string, $word_limit)
    {
        $words = explode(" ",$string);
        echo implode(" ", array_splice($words, 0, 5));
    }
?>

@section('contentPages')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>B2B CRM</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ URL::to('/dealer') }}" class="sds" data-nam="1">Home</a>
                </li>
                <li>
                    <a class="sds" data-nam="2">B2B CRM</a>
                </li>
                <li class="active">
                    <strong class="sds" data-nam="3">All Orders</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="wrapper wrapper-content animated fadeInRight ecommerce">
				<div class="col-lg-12">
					<div class="ibox">
						<div class="ibox-content">
							<ul class="nav nav-tabs" id="myTab">
							
								<li class="<?php  if(!Session::get('opentab')){ echo'active';} if(Session::get('opentab')=='pending'){ echo'active';}?>"><a data-toggle="tab" href="#tab-1">Pending Orders (<?php $ordordersCountInvoiced=DB::table('order_transaction')->where('dealerID','=',$id)->where('finance','=','0')->where('orderStatus','=','pending')->where('deleted_at','=',NULL)->count(); echo $ordordersCountInvoiced;?>)</a></li>
										
								<li class="<?php if(Session::get('opentab')=='booked'){ echo'active';}?>"><a data-toggle="tab" href="#tab-2">Booked in for delivery (<?php $ordordersCountBooked=DB::table('order_transaction')->where('finance','=','0')->where('dealerID','=',$id )->where('orderStatus','=','booked in for delivery')->where('deleted_at','=',NULL)->count(); echo $ordordersCountBooked;?>)</a></li>
								
								<li class="<?php if(Session::get('opentab')=='invoice'){ echo'active';}?>"><a data-toggle="tab" href="#tab-3">Invoices (<?php $ordordersCountInvoiced=$invoices=DB::table('order_invoice')->where('invoice_status','=','invoiced')->where('dealerID','=',$id )->where('deleted_at','=',NULL)->count(); echo $ordordersCountInvoiced;?>)</a></li>
								
								<li class="<?php if(Session::get('opentab')=='paid'){ echo'active';}?>"><a data-toggle="tab" href="#tab-4">Paid Orders (<?php $ordordersCountPaid=DB::table('order_invoice')->where('invoice_status','=','paid')->where('deleted_at','=',NULL)->where('dealerID','=',$id )->count(); echo $ordordersCountPaid;?>)</a></li>
								
								<li class="<?php if(Session::get('opentab')=='complete'){ echo'active';}?>"><a data-toggle="tab" href="#tab-5">Completed Orders (<?php $ordordersCountCompleted=DB::table('order_invoice')->where('invoice_status','=','complete')->where('dealerID','=',$id )->where('deleted_at','=',NULL)->count(); echo $ordordersCountCompleted;?>)</a></li>
								
							</ul>
							<div class="tab-content">
									
			<!--------------------------------Panding Orders--------------------------------------->
			
								<div id="tab-1" class="tab-pane <?php  if(!Session::get('opentab')){ echo'active';} if(Session::get('opentab')=='pending'){ echo'active';}?>">
									<div class="panel-body">
										<br/>
									   <div class="table-responsive">
										<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
											<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
												<thead>
													<tr>
														<th>#</th> 
														<th style="min-width: 150px;">Product Name</th>
														<th>Batch</th>
														<th style="min-width: 65px;">Category</th>
														<th>Order type</th>
														<th style="min-width: 65px;">Color</th>
														<th>Qty</th>
														<th>Status</th>
														<th style="min-width: 65px;">Order Date</th>
														<th style="min-width: 65px;">Delivery Date</th>
														<th>Customer Name</th>
														<th style="min-width: 200px;">Notes</th>
														<th style="min-width: 81px;">Action</th>
														<th style="max-width: 17px;">Admin Notes </th>
														 
													</tr>
												</thead>
												<tbody>
												<?php 
													
													//$orders=DB::table('product_order')->where('dealerID','=',$id)->where('orderStatus','=','pending')->get();
												 $qry="SELECT * FROM product_order LEFT JOIN order_transaction ON product_order.orderID=order_transaction.orderID WHERE product_order.dealerID=".$id." AND order_transaction.orderStatus='pending' AND order_transaction.deleted_at IS NULL AND product_order.deleted_at IS NULL";
													//echo $qry;
													 //exit;
													$orders=DB::select(DB::raw($qry));
													$num=0;
													$uniqueQtyNumber = '';
                                                    $qtycount=0;
													$i=0;
													foreach($orders as $order){
														$i++;
														if($order->orderStatus=='pending'){
															 
                                                               $num++;
                                                                $uniqueQtyNumber = $order->orderNoteTokenString;
														//for($i=1;$i<=$order->qty;$i++){
															 
															 $qtycount ++;
															
												?>
													<tr>
													
													<td> <?php echo $num; //$i++; ?>  </td>
														<td><?php  
															$productName=DB::table('products')->where('product_id','=',$order->product_id)->first();
															 if(!empty($productName->productName)){
																 
															 echo $productName->productName;
															 if($order->specialOrderID > 0){
																echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
															}
															  
															 }else{
																 echo  '--'; 
															 }?>
														 </td>
														 <td>
															<?php 
																if(!empty($order->batch)){
																	 
																		echo $order->batch;
																	 
																}else{
																	echo '---';
																}
															
															?>
														 </td>
														<td>
															<?php 
																 if(!empty($productName->category_id)){
																	$category=DB::table('category')->where('id','=',$productName->category_id)->first();
																	if(!empty($category->categoryName)){
																		echo $category->categoryName;
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
															//echo $order->qtystatus;
																 if(!empty($order->qtystatus)){
																	$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
																	if($order->qtystatus =='instock'){
																	 
																		if(!empty($order->stockdate)){
																			
																		echo '<small class="label label-info"> InStock ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																		}else{
																			
																		echo '<small class="label label-info"> InStock</small>';
																		}
																	}else{
																		if($order->mailstatus==0){
																			if(!empty($order->stockdate)){
																				if($order->qtystatus== 'onseaukarrival'){
																					echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																				}elseif($order->qtystatus== 'factorystock'){
																						echo '<label class="label label-primary"> FactoryStock</label>';
																				}else{
																					echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																				}
																				 
																			}else{
																				
																				if(!empty($date->stockdat)){
																					
																					if($order->qtystatus== 'onseaukarrival'){
																						echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																					}elseif($order->qtystatus== 'factorystock'){
																						echo '<label class="label label-primary"> FactoryStock</label>';
																					}else{
																						echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																					}
																					
																				}else{
																					if($order->specialOrderID > 0){
																						$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																							if($order->qtystatus== 'onseaukarrival'){
																								echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UK Arrival ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).') </small>';
																							}elseif($order->qtystatus== 'factorystock'){
																									echo '<label class="label label-primary"> FactoryStock</label>';
																							}else{
																								echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																							}
																						 
																					}else{
																						if($order->qtystatus== 'onseaukarrival'){
																							echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																						}elseif($order->qtystatus== 'factorystock'){
																								echo '<label class="label label-primary"> FactoryStock</label>';
																						}else{
																							echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																						}
																					}
																				}
																				
																			}
																		}else{
																			 
																			if($order->qtystatus== 'onseaukarrival'){
																					echo '<small   style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																			}elseif($order->qtystatus== 'inproduction'){
																					echo '<small  class="label label-success">InProduction ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																			}else{
																				
																			echo '<small class="label label-info"> InStock</small>';
																			}
																		}
																	}
																} 
																
															?>
														</td>
														<td>
															<?php 
															 
															 if(!empty($order->product_color)){
																 
															 echo $order->product_color;
															 }else{
																 echo  '--'; 
															 }
															
															?>
															
															
														</td>
														<td>
															<?php
															if($order->qty != ''){
																// echo $order->qty;                 
																echo '1';                 
															}else{
																echo '---';
															}
															?>
														</td>
														<td>
															<?php 
															 if($order->orderStatus != ''){
																if($order->orderStatus=='pending'){
																	echo '<label class="label label-warning" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																	 
																}
																if($order->orderStatus=='cancelled') {
																	 
																	echo '<label class="label label-danger" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																 }if($order->orderStatus=='Complete'){
																	 echo '<label class="label label-success" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																 }
															}else{
																echo '--';
															}
															
															?>
															
														</td>
														<td>
															<?php  echo date('d-m-Y',strtotime($order->created_at))?>
														</td>
														<td>
															<?php
																if(!empty($order->delivery_date)){
																	
																	echo date('d-m-Y',strtotime($order->delivery_date));
																}else{
																	echo '---';
																}
																?>
														</td>
														<td>
															<?php
															$orderNoteTokenString=$order->orderNoteTokenString;
															 
																if(!empty($order->customer_name)){
																	echo $order->customer_name;
																}else{
																	echo '<a href="#" data-toggle="modal" data-target="#Customername'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Add Name</a>';
																}
																$addcutomeraction=URL::to('/dealer/addcustomername/');
															?>
															 <input type="hidden" name="orderstring" id="orderstring" value="<?php echo $orderNoteTokenString; ?>"/>
															 
															<div class="modal inmodal fade" id="Customername<?php echo $uniqueQtyNumber; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
																<div class="modal-dialog modal-lg bs-example-modal-lg">
																	<div class="modal-content">
																		<div class="modal-header">
																			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																			<h4 class="modal-title">Customer Name</h4>
																		</div>  
																		<form action="<?php echo $addcutomeraction; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
																			<div class="modal-body col-md-12" style="max-height: 350px; overflow-y: scroll;">
																				<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																				<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>	 
																				<input type="hidden" name="opentab" value="pending"/>	 
																				<input type="hidden" name="orderTokenString" value="<?php echo $order->orderNoteTokenString ;?>"/>	 
																					 
																							
																				</div>
																				<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
																					<div class="form-group" style="width: 100%;">
																					<label class="control-label">Customer Name :</label><br/>
																					<input type="text" style="width:100%"  class="form-control" name="order_notes_titles" id="order_notes_title" placeholder="Customer Name"></div>
																					<div class="form-group" style="width: 100%;">
																					<label class="control-label">Notes:</label><br/>
																					<textarea style="width:100%"  name="order_notes_descriptionss" class="form-control" placehoder="Notes"></textarea></div>
																				</div>
																				<div class="clearfix"></div>
																				<hr/>
																					
																					 
																					
																			</div>
																			<div class="clearfix"></div>
																			<div class="modal-footer">
																				<div class="clearfix"></div>
																				<br/>
																				<input type="submit" class="btn btn-primary" value="Save changes" />
																				<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																			</div>
																		</form>
																	</div>
																</div>
															</div>
															 
														</td>
														 
														<td>
															<?php 
																if(!empty($order->order_notes_descriptions)){
																	echo $order->order_notes_descriptions;
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
														<?php
														
															$url = URL::to('dealer/editOrder', base64_encode($order->orderID));
															 
															 $productName=DB::table('products')->where('product_id','=',$order->product_id)->first();
															 $deatailurl = URL::to('dealer/dealerorderdetials', base64_encode($order->orderID.'&'.$orderNoteTokenString));
															   
															//$delurl = URL::to('admin/deletevariation', base64_encode($data->variationID));
															if($order->orderStatus=='cancelled') {}else{
																 $notes=DB::table('dealer_order_notes')->where('orderID','=',$order->orderID)->where('orderTokenString','=',$uniqueQtyNumber)->first();
																 $action=URL::to('/dealer/addNotes/');
																 
															echo '<a href="javascript:void(0)"  data-toggle="modal" data-target="#notes'.$uniqueQtyNumber.'"  title="Add Notes" class="btn btn-xs btn-default"><i class="fa fa-file-text-o"></i></a>
															
																<div class="modal inmodal" id="notes'.$uniqueQtyNumber.'" tabindex="-1" role="dialog"  aria-hidden="true">
																	<div class="modal-dialog">
																		<div class="modal-content animated fadeIn">
																		
																			<form action="'.$action.'" method="POST" enctype="multipart/form-data" class="products" id="">
																				<input type="hidden" name="_token" value="'.csrf_token().'"/>
																				<input type="hidden" name="opentab" value="pending"/>
																				<input type="hidden" name="productToken" value="'.base64_encode($order->product_id).'"/>
																				<input type="hidden" name="variationToken" value="'.base64_encode($order->variationID).'"/>
																				<input type="hidden" name="orderToken" value="'.base64_encode($order->orderID).'"/>
																				<input type="hidden" value="'.$order->orderNoteTokenString.'" name="orderNoteTokenString"/>
																				<div class="modal-header">
																					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																					<h4 class="modal-title">'.$productName->productName.'</h4>
																				</div>
																				<div class="modal-body col-md-12" style="background-color:#fff;">
																					<div class="col-md-12 col-sm-12 col-xs-12">
																						 
																						<div class="form-group" style="width: 100%;">
																						<label class="control-label">Customer Name:</label><br/>
																						<input type="text" style="width:100%" required name="name" class="form-control" value="'.$order->customer_name.'" placeholder="Name" ></div><br/><br/>
																						
																					</div>
																					<div class="col-md-12 col-sm-12 col-xs-12">
																						<div class="form-group" style="width: 100%;">
																						<label class="control-label">Notes:</label><br/>
																						<textarea style="width:100%;border:1px solid #ccc;background-color:#ccc;" name="discription" class="summernote" placehoder="Notes">'.$order->order_notes_descriptions.'</textarea></div>
																					</div>
																				</div>
																				<div class="modal-footer">
																					<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																					<input type="submit" class="btn btn-primary" value="Save changes" />
																				</div>
																			</form>
																		</div>
																	</div>
																</div>';
															/* echo '<a href="javascript:void(0);" onclick="remove('.$order->orderID.')" data-order="'.$order->orderID.'" class="btn btn-xs btn-default sdss"><i class="fa fa-trash"></i></a>'; */
															 echo '<a href="'.$deatailurl.'"   data-toggle="tooltip" title="View order" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a> ';
															 echo '<a href="'.$deatailurl.'"   data-toggle="tooltip" title="Add Accessory" class="btn btn-xs btn-default"><i class="fa fa-plus"></i></a> ';
															echo "<a href=\"javascript:void(0);\" title=\"Delete order\" onclick=\"removedata('".$order->orderID."','".$orderNoteTokenString."')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a>";
															}
															
														?>
														</td>
														<td>
															<?php 
															 echo '<a htef="javascript:void(0)" class="btn btn-xs btn-default" title="Admin Notes" data-toggle="modal" data-target="#adminnotes'.$uniqueQtyNumber.'" ><i class="fa fa-file-text"></i></a>';
																 
																    $notes=DB::table('admin_order_notes')->where('orderTokenString','=',$order->orderNoteTokenString)->where('product_id','=',$order->product_id)->orderBy('admin_order_notesID','desc')->get();
																    $actionadm=URL::to('/dealer/addordernotestoadmin/');
																$delaerName=DB::table('dealer')->where('id','=',$order->dealerID)->first();
																  
																   
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
																				<input type="hidden" name="opentab" value="pending"/>
																					<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
																					<input type="hidden" name="sendertype" value="<?php echo $sessionData['role']; ?>"/>
																					<input type="hidden" name="productToken" value="<?php echo  base64_encode($order->product_id) ;?>'"/>
																					<?php 
																						$sender= $sessionData['first_name'].' (Dealer)'
																						//echo $sender;
																							?>
																					 <input type="hidden" name="dealerID" value="<?php echo $sessionData['dealerID']; ?>"/>
																					 <input type="hidden" name="sender" value="<?php echo $sender; ?>"/>
																					<input type="hidden" name="orderToken" value="<?php echo  base64_encode($order->orderID); ?>"/>
																					<input type="hidden" name="orderTokenString" value="<?php echo $uniqueQtyNumber; ?>"/>
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
																				<hr/>
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
																								/* if($note->sender=='admin'){
																									$sender='<small class="label label-info"> Admin</small>&nbsp;';
																								}else{
																									$sender='<small class="label label-success"> Dealer</small>&nbsp;&nbsp;'.$delaerName->first_name.'&nbsp;'.$delaerName->last_name;
																								} */
																								$sender='<small class="label label-success">'.$note->sender.'</small>';
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
																				<br/>
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
													}
														 
													
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								
									
			<!--------------------------------Booked in for delivery Orders--------------------------------------->
			
								<div id="tab-2" class="tab-pane <?php if(Session::get('opentab')=='booked'){ echo'active';}?>">
									<div class="panel-body">
										<br/>
									   <div class="table-responsive">
										<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
											<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
												<thead>
													<tr>
														<th>#</th> 
														<th style="min-width: 150px;">Product Name</th>
														<th>Batch</th>
														<th style="min-width: 65px;">Category</th>
														<th>Order type</th>
														<th style="min-width: 65px;">Color</th>
														<th>Qty</th>
														<th>Status</th>
														<th style="min-width: 65px;">Order Date</th>
														<th style="min-width: 65px;">Delivery Date</th>
														<th>Customer Name</th>
														<th style="min-width: 200px;">Notes</th>
														<th style="min-width: 81px;">Action</th>
														<th style="max-width: 17px;">Admin Notes </th>
													</tr>
												</thead>
												<tbody>
												<?php 
													
													//$orders=DB::table('product_order')->where('dealerID','=',$id)->where('orderStatus','=','pending')->get();
													 $qry="SELECT * FROM product_order LEFT JOIN order_transaction ON product_order.orderID=order_transaction.orderID WHERE product_order.dealerID=".$id." AND order_transaction.orderStatus='booked in for delivery' AND order_transaction.deleted_at IS NULL AND product_order.deleted_at IS NULL";
													//echo $qry;
													 //exit;
													 $orders=DB::select(DB::raw($qry));
													$num=0;
													$uniqueQtyNumber = '';
                                                    $qtycount=0;
													$i=0;
													foreach($orders as $order){
														$i++;
														
															 $num++;
                                                              $uniqueQtyNumber = $order->orderNoteTokenString;
															 $qtycount ++;
															
												?>
													<tr>
													
													<td> <?php echo $num; //$i++; ?></td>
														<td><?php  
															$productName=DB::table('products')->where('product_id','=',$order->product_id)->first();
															 if(!empty($productName->productName)){
																 
															 echo $productName->productName;
															  if($order->specialOrderID > 0){
																echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
															}
															  
															 }else{
																 echo  '--'; 
															 }?>
														 </td>
														 <td>
															<?php 
																if(!empty($order->qtystatus)){
																	$batch=DB::table('variation')->where('variationID','=',$order->variationID)->first();
																	if(!empty($batch->batch)){
																		echo $batch->batch;
																	}else{
																		echo $order->batch;
																	}
																}else{
																	echo '---';
																}
															
															?>
														 </td>
														 <td>
															<?php 
																 if(!empty($productName->category_id)){
																	$category=DB::table('category')->where('id','=',$productName->category_id)->first();
																	if(!empty($category->categoryName)){
																		echo $category->categoryName;
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
															//echo $order->qtystatus;
																 if(!empty($order->qtystatus)){
																	$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
																	if($order->qtystatus =='instock'){
																	 
																		if(!empty($order->stockdate)){
																			
																		echo '<small class="label label-info"> InStock ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																		}else{
																			
																		echo '<small class="label label-info"> InStock</small>';
																		}
																	}else{
																		if($order->mailstatus==0){
																			if(!empty($order->stockdate)){
																				if($order->qtystatus== 'onseaukarrival'){
																					echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																				}elseif($order->qtystatus== 'factorystock'){
																						echo '<label class="label label-primary"> FactoryStock</label>';
																				}else{
																					echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																				}
																				 
																			}else{
																				
																				if(!empty($date->stockdat)){
																					
																					if($order->qtystatus== 'onseaukarrival'){
																						echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																					}elseif($order->qtystatus== 'factorystock'){
																						echo '<label class="label label-primary"> FactoryStock</label>';
																				}	else{
																						echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																					}
																					
																				}else{
																					if($order->specialOrderID > 0){
																						$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																							if($order->qtystatus== 'onseaukarrival'){
																								echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).') </small>';
																							}elseif($order->qtystatus== 'factorystock'){
																								echo '<label class="label label-primary"> FactoryStock</label>';
																							}else{
																								echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																							}
																						 
																					}else{
																						if($order->qtystatus== 'onseaukarrival'){
																							echo '<small style="background-color: #029dff;" class="label label-success">OnSea-UKArrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																						}elseif($order->qtystatus== 'factorystock'){
																							echo '<label class="label label-primary"> FactoryStock</label>';
																						}else{
																							echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																						}
																					}
																				}
																				
																			}
																		}else{
																			 
																			if($order->qtystatus== 'onseaukarrival'){
																					echo '<small   style="background-color: #029dff;" class="label label-success">On Sea-UKArrival ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																			}elseif($order->qtystatus== 'inproduction'){
																					echo '<small  class="label label-success">InProduction ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																			}elseif($order->qtystatus== 'factorystock'){
																					echo '<label class="label label-primary"> FactoryStock</label>';
																				}else{
																				
																			echo '<small class="label label-info"> InStock</small>';
																			}
																		}
																	}
																} 
																
															?>
														</td>
														<td>
															<?php 
															 
															 if(!empty($order->product_color)){
																 
															 echo $order->product_color;
															 }else{
																 echo  '--'; 
															 }
															
															?>
															
															
														</td>
														<td>
															<?php
															if($order->qty != ''){
																// echo $order->qty;                 
																echo '1';                 
															}else{
																echo '---';
															}
															?>
														</td>
														<td>
															<label class="label label-warning" style="text-transform:capitalize;background-color:#F7609E;">booked in for delivery</label>
															
														</td>
														<td>
															<?php  echo date('d-m-Y',strtotime($order->created_at))?>
														</td>
														<td>
															<?php
																if(!empty($order->delivery_date)){
																	
																	echo date('d-m-Y',strtotime($order->delivery_date));
																}else{
																	echo '---';
																}
																?>
														</td>
														<td>
															<?php
															$orderNoteTokenString=$order->orderNoteTokenString;
															 
																if(!empty($order->customer_name)){
																	echo $order->customer_name;
																}else{
																	echo '<a href="#" data-toggle="modal" data-target="#Customername'.$uniqueQtyNumber.'"  class="btn btn-xs btn-default"><i class="fa fa-plus-square">&nbsp;</i>Add Name</a>';
																}
																$addcutomeraction=URL::to('/dealer/addcustomername/');
															?>
															 <input type="hidden" name="orderstring" id="orderstring" value="<?php echo $orderNoteTokenString; ?>"/>
															 
															<div class="modal inmodal fade" id="Customername<?php echo $uniqueQtyNumber; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
																<div class="modal-dialog modal-lg bs-example-modal-lg">
																	<div class="modal-content">
																		<div class="modal-header">
																			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																			<h4 class="modal-title">Customer Name</h4>
																		</div>  
																		<form action="<?php echo $addcutomeraction; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
																			<div class="modal-body col-md-12" style="max-height: 350px; overflow-y: scroll;">
																				<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																				<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>	 
																				<input type="hidden" name="opentab" value="booked"/>	 
																				<input type="hidden" name="orderTokenString" value="<?php echo $order->orderNoteTokenString ;?>"/>	 
																					 
																							
																				</div>
																				<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
																					<div class="form-group" style="width: 100%;">
																					<label class="control-label">Customer Name :</label><br/>
																					<input type="text" style="width:100%"  class="form-control" name="order_notes_titles" id="order_notes_title" placeholder="Customer Name"></div>
																					<div class="form-group" style="width: 100%;">
																					<label class="control-label">Notes:</label><br/>
																					<textarea style="width:100%"  name="order_notes_descriptionss" class="form-control" placehoder="Notes"></textarea></div>
																				</div>
																				<div class="clearfix"></div>
																				<hr/>
																					
																			</div>
																			<div class="clearfix"></div>
																			<div class="modal-footer">
																				<div class="clearfix"></div>
																				<br/>
																				<input type="submit" class="btn btn-primary" value="Save changes" />
																				<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																			</div>
																		</form>
																	</div>
																</div>
															</div>
														</td>
														 
														<td>
															<?php 
																if(!empty($order->order_notes_descriptions)){
																	echo $order->order_notes_descriptions;
																}else{
																	echo '---';
																}
															?>
														</td>
														
														<td>
														<?php
														
															$url = URL::to('dealer/editOrder', base64_encode($order->orderID));
															 
															 $productName=DB::table('products')->where('product_id','=',$order->product_id)->first();
															 $deatailurl = URL::to('dealer/dealerorderdetials', base64_encode($order->orderID.'&'.$orderNoteTokenString));
															  
															//$delurl = URL::to('admin/deletevariation', base64_encode($data->variationID));
															if($order->orderStatus=='cancelled') {}else{
																 $notes=DB::table('dealer_order_notes')->where('orderID','=',$order->orderID)->where('orderTokenString','=',$uniqueQtyNumber)->first();
																 $action=URL::to('/dealer/addNotes/');
																 if(!empty($notes)){
																	 
																 $dataold='<p><strong>Name :</strong>'.$notes->name.'</p>
																			<p><strong>Description :</strong>'.$notes->discription.'</p>';
																 }else{
																	 $dataold='';
																 }
															echo '<a href="javascript:void(0)"  data-toggle="modal" data-target="#notes'.$uniqueQtyNumber.'"  title="Add Notes" class="btn btn-xs btn-default"><i class="fa fa-file-text-o"></i></a>
															
															<div class="modal inmodal" id="notes'.$uniqueQtyNumber.'" tabindex="-1" role="dialog"  aria-hidden="true">
																<div class="modal-dialog">
																	<div class="modal-content animated fadeIn">
																	
																		<form action="'.$action.'" method="POST" enctype="multipart/form-data" class="products" id="">
																			<input type="hidden" name="_token" value="'.csrf_token().'"/>
																			 
																			<input type="hidden" name="opentab" value="booked"/>
																			<input type="hidden" name="productToken" value="'.base64_encode($order->product_id).'"/>
																			<input type="hidden" name="variationToken" value="'.base64_encode($order->variationID).'"/>
																			<input type="hidden" name="orderToken" value="'.base64_encode($order->orderID).'"/>
																			<input type="hidden" value="'.$order->orderNoteTokenString.'" name="orderNoteTokenString"/>
																			<div class="modal-header">
																				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																				<h4 class="modal-title">'.$productName->productName.'</h4>
																			</div>
																			<div class="modal-body col-md-12">
																				<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																					 
																					<div class="form-group" style="width: 100%;">
																					<label class="control-label">Customer Name:</label><br/>
																					<input type="text" style="width:100%" required name="name" class="form-control" value="'.$order->customer_name.'" placeholder="Name" ></div><br/><br/>
																					
																				</div>
																				<div class="col-md-12 col-sm-12 col-xs-12">
																					<div class="form-group" style="width: 100%;">
																					<label class="control-label">Notes:</label><br/>
																					<textarea style="width:100%;border:1px solid #ccc;background-color:#ccc;"  required name="discription" class="summernote" placehoder="Notes">'.$order->order_notes_descriptions.'</textarea></div>
																				</div>
																			</div>
																			<div class="modal-footer">
																				<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																				<input type="submit" class="btn btn-primary" value="Save changes" />
																			</div>
																		</form>
																	</div>
																</div>
															</div>';
															/* echo '<a href="javascript:void(0);" onclick="remove('.$order->orderID.')" data-order="'.$order->orderID.'" class="btn btn-xs btn-default sdss"><i class="fa fa-trash"></i></a>'; */
															 echo '<a href="'.$deatailurl.'"   data-toggle="tooltip" title="View Order" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a> ';
															 echo '<a href="'.$deatailurl.'"   data-toggle="tooltip" title="View Order" class="btn btn-xs btn-default"><i class="fa fa-plus"></i></a> ';
															echo "<a href=\"javascript:void(0);\" title=\"Delete Order\" onclick=\"removedata('".$order->orderID."','".$orderNoteTokenString."')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash\"></i></a> ";
															}
															
														?>
														</td>
														<td>
															<?php 
															 echo '<a htef="javascript:void(0)" class="btn btn-xs btn-default" title="Admin Notes" data-toggle="modal" data-target="#adminnotes'.$uniqueQtyNumber.'" ><i class="fa fa-file-text"></i></a>';
																 
																   $notes=DB::table('admin_order_notes')->where('orderTokenString','=',$order->orderNoteTokenString)->where('product_id','=',$order->product_id)->orderBy('admin_order_notesID','desc')->get();
																    $actionadm=URL::to('/dealer/addordernotestoadmin/');
																$delaerName=DB::table('dealer')->where('id','=',$order->dealerID)->first();
																  
																   
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
																					<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
																					
																					<input type="hidden" name="opentab" value="booked"/>
																					<input type="hidden" name="sendertype" value="<?php echo $sessionData['role']; ?>"/>
																					 <input type="hidden" name="dealerID" value="<?php echo $sessionData['dealerID']; ?>"/>
																					<input type="hidden" name="productToken" value="<?php echo  base64_encode($order->product_id) ;?>'"/>
																					 <?php 
																						$sender= $sessionData['first_name'].' (Dealer)'
																						//echo $sender;
																							?>
																					 <input type="hidden" name="sender" value="<?php echo $sender; ?>"/>
																					<input type="hidden" name="orderToken" value="<?php echo  base64_encode($order->orderID); ?>"/>
																					<input type="hidden" name="orderTokenString" value="<?php echo $uniqueQtyNumber; ?>"/>
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
																				<hr/>
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
/* 																								if($note->sender=='admin'){
																									$sender='<small class="label label-info"> Admin</small>&nbsp;';
																								}else{
																									$sender='<small class="label label-success"> Dealer</small>&nbsp;&nbsp;'.$delaerName->first_name.'&nbsp;'.$delaerName->last_name;
																								} */
																								$sender='<small class="label label-success"> '.$note->sender.'</small>';
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
																				<br/>
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
								
			<!--------------------------------invoiced Orders--------------------------------------->
			
								<div id="tab-3" class="tab-pane <?php if(Session::get('opentab')=='invoice'){ echo'active';}?>">
									<div class="panel-body">
									<!--<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="form_customer_profile" >-->
									<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
									<!--<input type="submit" value="save"/>-->
										<br/>
										<div class="table-responsive">
										<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
											<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
												<thead>
													<tr>
														<th>Invoice Number</th>
														<th>Invoice Date</th>
														<th>Delivery Date</th>
														<th>Dealer Name</th>
														<th >Status</th>
														<th style="min-width: 55px;">Action</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$invoices=DB::table('order_invoice')->where('invoice_status','=','invoiced')->where('dealerID','=',$id)->get();
													$num=0;
													//print_r($invoices);
													foreach($invoices as $invoice){
													$num=$invoice->invoiceNumber;
													$ordertranz=explode(",",$invoice->orderNoteTokenString)	;
												?>
													<tr>
														<td>
															<?php 
																if(!empty($invoice->invoiceNumber)){
																	echo $invoice->invoiceNumber;
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
															<?php 
																if(!empty($invoice->created_at)){
																	echo date('d-m-Y',strtotime($invoice->created_at));
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
																	<?php
																	
												$order=DB::table('order_transaction')->where('finance','=','0')->where('orderStatus','=','invoiced')->where('orderNoteTokenString','=',$ordertranz[0])->first();
												if(!empty($order)){
													if(!empty($order->delivery_date))
														echo date('d-m-Y',strtotime($order->delivery_date));
												}else{
													echo '--';
												}
																	?>
																</td> 
														<td>
															<?php 
															if(!empty($invoice->dealerID)){
																//echo $invoice->dealerID; 
																$delaername=DB::table('dealer')->where('id','=',$invoice->dealerID)->first();
																
																if(!empty($delaername->first_name) && !empty($delaername->last_name)){
																	$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
																}
																echo $name;
																$name='';
															}
															?>
														</td>
														<td>
															<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>
														</td>
														<td>
														<?php 
														//print_r($ordertranz);
														
														?>
															<a href="#" title="View" data-toggle="modal" data-target="#invoicedata<?php echo $num; ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
															<div class="modal inmodal fade" id="invoicedata<?php echo $num; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
																<div class="modal-dialog modal-lg">
																	<div class="modal-content">
																		<div class="modal-header">
																			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																			<h4 class="modal-title">Invoice No : <?php 
																				if(!empty($invoice->invoiceNumber)){
																					echo $invoice->invoiceNumber;
																				} 
																			?></h4>
																		</div> 
																		<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
							<thead>
								<tr>
									<th style="min-width: 150px;">Product Name</th>
									<th>Batch</th>
									<th style="min-width: 65px;">Category</th>
									<th >Order type</th>
									<th style="min-width: 65px;">Order Date</th>
									<th >Dealer Name</th>
									<th style="min-width: 65px;">Delivery Date</th>
									<th >Customer Name</th>
									<th style="min-width: 200px;" >Notes</th>
									<th style="min-width: 65px;">Color</th>
									<th >Qty</th>
									<th >Status</th>
								</tr>
							</thead>
							<tbody>	
								<?php
									for($j=0;$j<count($ordertranz);$j++){
									 
										$order=DB::table('order_transaction')->where('orderStatus','=','invoiced')->where('orderNoteTokenString','=',$ordertranz[$j])->first();
											if(!empty($order)){
								?>
								<tr>
									<td>
									<?php 
										$getProductData=DB::table('products')->where('product_id','=', $order->product_id)->first();
										if(!empty($getProductData->productName)){
											echo $getProductData->productName;
										}
									?>
									</td>
									<td>
										<?php 
											if(!empty($order->batch)){
												echo $order->batch;
											}
										?>
									</td>
									<td>
										<?php 
											$getCategoryData=DB::table('category')->where('id','=', $getProductData->category_id)->first();
											if(!empty($getCategoryData->categoryName)){
												echo $getCategoryData->categoryName;
											}
										?>
									</td>
									<td>
										<?php
															//echo $order->product_id;
											 if(!empty($order->qtystatus)){
												$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
												if($order->qtystatus =='instock'){
													echo '<small class="label label-info"> In Stock</small>';
												}else{
													if($order->mailstatus==0){
														if(!empty($order->stockdate)){
															echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
														}else{
															
															if(!empty($date->stockdat)){
																echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																
															}else{
																if($order->specialOrderID > 0){
																	$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																	echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																}else{
																	echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																}
															}
															
														}
													}else{
														echo '<small class="label label-info"> In Stock</small>';
													}
												}
											} 
											
										?>
									</td>
									<td>
										<?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at));}?>
									</td>
									<td>
										<?php 
										if(!empty($order->dealerID)){
											
											$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
											$name='';
											if(!empty($delaername->first_name) && !empty($delaername->last_name)){
												$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
											}
											echo $name;
										}
										?>
									</td>
									<td>
										<?php
											if(!empty($order->delivery_date)){
												
												echo date('d-m-Y',strtotime($order->delivery_date));
											}else{
												echo '---';
											}
											?>
									</td>
									<td>
										<?php
										$orderNoteTokenString=$order->orderNoteTokenString;
										//echo $orderNoteTokenString;
										//$getOrderNotes=DB::table('order_notes')->where('orderNoteTokenString','=',$orderNoteTokenString)->first();
											if(!empty($order->customer_name)){
												echo $order->customer_name;
											}else{
												echo '---';
											}
										?>
									</td>
										<td>
										<?php 
											if(!empty($order->order_notes_descriptions)){
												echo $order->order_notes_descriptions;
											}else{
												echo '---';
											}
										?>
									</td>
									<td>
										<?php
											if(!empty($order->variationID)){
												$color=DB::table('variation')->where('variationID','=',$order->variationID)->where('deleted_at','=',NULL)->first();
												if(!empty($color->product_color)){
													echo $color->	product_color;
												}
											}
										?>
									</td>
									<td>1</td>
									<td>
										<label class="label label-danger" style="text-transform:capitalize;">invoiced</label>
									</td>
									 
								</tr>
								<?php
									  }
									}
								?>
							</tbody>
						</table>	
					</div>
																		</div> 
																		<div class="modal-footer">
																			<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div> 
															</div> 
															<?php 
																if(!empty($invoice->invoicepdf)){
																	$path=URL::to('uploads/invoicepdf/'.$invoice->invoicepdf);
																	echo '<a href="'.$path.'" target="_blank" title="View Invoice"class="btn btn-xs btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
																}
															?>
														</td>
													</tr>
												<?php
													}
													
												?>
													 
												</tbody>
											</table>
										</div>
									<!--</form>-->
									</div>
								</div>
								
		<!--------------------------------paid Orders--------------------------------------->
			
								<div id="tab-4" class="tab-pane <?php if(Session::get('opentab')=='paid'){ echo'active';}?>">
									<div class="panel-body">
									<!--<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="form_customer_profile" >-->
									<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
									<!--<input type="submit" value="save"/>-->
										<br/>
										<div class="table-responsive">
										<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
											<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
												<thead>
													<tr>
														<th>Invoice Number</th>
														<th>Invoice Date</th>
														 
														<th>Dealer Name</th>
														<th >Status</th>
														<th style="min-width: 55px;">Action</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$invoices=DB::table('order_invoice')->where('invoice_status','=','paid')->where('dealerID','=',$id)->groupBy('invoiceNumber')->orderBy('order_invoice_ID','desc')->get();
													$num=0;
													foreach($invoices as $invoice){
													$num=$invoice->invoiceNumber;
													$ordertranz=explode(",",$invoice->orderNoteTokenString)	;
												?>
													<tr>
														<td>
															<?php 
																if(!empty($invoice->invoiceNumber)){
																	echo $invoice->invoiceNumber;
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
															<?php 
																if(!empty($invoice->created_at)){
																	echo date('d-m-Y',strtotime($invoice->created_at));
																}else{
																	echo '---';
																}
															?>
														</td>
														<td>
															<?php 
															if(!empty($invoice->dealerID)){
																
																$delaername=DB::table('dealer')->where('id','=',$invoice->dealerID)->first();
																$name='';
																if(!empty($delaername->first_name) && !empty($delaername->last_name)){
																	$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
																}
																echo $name;
															}
															?>
														</td>
														<td>
															<label class="label label-info" style="text-transform:capitalize;">paid</label>
														</td>
														<td>
															<a href="#" title="View" data-toggle="modal" data-target="#invoicedata<?php echo $num; ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
															<div class="modal inmodal fade" id="invoicedata<?php echo $num; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
																<div class="modal-dialog modal-lg">
																	<div class="modal-content">
																		<div class="modal-header">
																			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																			<h4 class="modal-title">Invoice No : <?php 
																				if(!empty($invoice->invoiceNumber)){
																					echo $invoice->invoiceNumber;
																				} 
																			?></h4>
																		</div> 
																		<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
							<thead>
								<tr>
									<th style="min-width: 150px;" >Product Name</th>
									<th>Batch</th>
									<th style="min-width: 65px;">Category</th>
									<th >Order type</th>
									<th style="min-width: 65px;">Order Date</th>
									<th >Dealer Name</th>
									<th style="min-width: 65px;">Delivery Date</th>
									<th >Customer Name</th>
									<th style="min-width: 200px;" >Notes</th>
									<th style="min-width: 65px;">Color</th>
									<th >Qty</th>
									<th >Status</th>
								</tr>
							</thead>
							<tbody>	
								<?php
									for($j=0;$j<count($ordertranz);$j++){
									 
										$order=DB::table('order_transaction')->where('orderStatus','=','paid')->where('orderNoteTokenString','=',$ordertranz[$j])->first();
										if(!empty($order)){
								?>
								<tr>
									<td>
									<?php 
										$getProductData=DB::table('products')->where('product_id','=', $order->product_id)->first();
										if(!empty($getProductData->productName)){
											echo $getProductData->productName;
										}
									?>
									</td>
									<td>
										<?php 
											if(!empty($order->batch)){
												echo $order->batch;
											}
										?>
									</td>
									<td>
										<?php 
											$getCategoryData=DB::table('category')->where('id','=', $getProductData->category_id)->first();
											if(!empty($getCategoryData->categoryName)){
												echo $getCategoryData->categoryName;
											}
										?>
									</td>
									<td>
										<?php
																	//echo $order->product_id;
																		 if(!empty($order->qtystatus)){
																			$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
																			if($order->qtystatus =='instock'){
																				echo '<small class="label label-info"> InStock</small>';
																			}else{
																				 
																				if($order->mailstatus==0){
																					if(!empty($date->stockdat)){
																						echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																						
																					}else{
																						if($order->specialOrderID > 0){
																							$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																							echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																						}else{
																							echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																						}
																					}
																				}else{
																					echo '<small class="label label-info"> InStock</small>';
																				}
																			}
																		} 
																		
																	?>
									</td>
									<td>
										<?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at));}?>
									</td>
									<td>
										<?php 
										if(!empty($order->dealerID)){
											
											$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
											$name='';
											if(!empty($delaername->first_name) && !empty($delaername->last_name)){
												$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
											}
											echo $name;
										}
										?>
									</td>
									<td>
										<?php
											if(!empty($order->delivery_date)){
												
												echo date('d-m-Y',strtotime($order->delivery_date));
											}else{
												echo '---';
											}
											?>
									</td>
									<td>
										<?php
										$orderNoteTokenString=$order->orderNoteTokenString;
										//echo $orderNoteTokenString;
										//$getOrderNotes=DB::table('order_notes')->where('orderNoteTokenString','=',$orderNoteTokenString)->first();
											if(!empty($order->customer_name)){
												echo $order->customer_name;
											}else{
												echo '---';
											}
										?>
									</td>
										<td>
										<?php 
											if(!empty($order->order_notes_descriptions)){
												echo $order->order_notes_descriptions;
											}else{
												echo '---';
											}
										?>
									</td>
									<td>
										<?php
											if(!empty($order->variationID)){
												$color=DB::table('variation')->where('variationID','=',$order->variationID)->where('deleted_at','=',NULL)->first();
												if(!empty($color->product_color)){
													echo $color->product_color;
												}
											}
										?>
									</td>
									<td>1</td>
									<td>
										<label class="label label-info" style="text-transform:capitalize;">paid</label>
									</td>
									 
								</tr>
								<?php
									  }
									}
								?>
							</tbody>
						</table>	
					</div>
																		</div> 
																		<div class="modal-footer">
																			<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																		</div>
																	</div>
																</div> 
															</div> 
															 <?php 
																if(!empty($invoice->invoicepdf)){
																	$path=URL::to('uploads/invoicepdf/'.$invoice->invoicepdf);
																	echo '<a href="'.$path.'" target="_blank" title="View Invoice"class="btn btn-xs btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
																}
															?>
														</td>
													</tr>
												<?php
													}
													
												?>
													 
												</tbody>
											</table>
										</div>
									<!--</form>-->
									</div>
								</div>
						
																
		<!--------------------------------complete Orders--------------------------------------->
			
										<div id="tab-5" class="tab-pane <?php if(Session::get('opentab')=='complete'){ echo'active';}?>">
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="{{action('admin\OrderController@generateInvoice')}}" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<!--<input type="submit" value="save"/>-->
												<br/>
												<div class="table-responsive">
												<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
													<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
														<thead>
															<tr>
																<th>Invoice Number</th>
																<th>Invoice Date</th>
															 
																<th>Dealer Name</th>
																<th >Status</th>
																<th style="min-width: 55px;">Action</th>
															</tr>
														</thead>
														<tbody>
														<?php
															$invoices=DB::table('order_invoice')->where('dealerID','=',$id)->where('invoice_status','=','complete')->groupBy('invoiceNumber')->orderBy('order_invoice_ID','desc')->get();
															$num=0;
															foreach($invoices as $invoice){
															$num=$invoice->invoiceNumber;
															$ordertranz=explode(",",$invoice->orderNoteTokenString)	;
														?>
															<tr>
																<td>
																	<?php 
																		if(!empty($invoice->invoiceNumber)){
																			echo $invoice->invoiceNumber;
																		}else{
																			echo '---';
																		}
																	?>
																</td>
																<td>
																	<?php 
																		if(!empty($invoice->created_at)){
																			echo date('d-m-Y',strtotime($invoice->created_at));
																		}else{
																			echo '---';
																		}
																	?>
																</td>
																<td>
																	<?php 
																	if(!empty($invoice->dealerID)){
																		
																		$delaername=DB::table('dealer')->where('id','=',$invoice->dealerID)->first();
																		$name='';
																		if(!empty($delaername->first_name) && !empty($delaername->last_name)){
																			$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
																		}
																		echo $name;
																	}
																	?>
																</td>
																<td>
																	<label class="label label-success" style="text-transform:capitalize;">Complete</label>
																</td>
																<td>
																	<a href="#" title="View" data-toggle="modal" data-target="#invoicedata<?php echo $num; ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
																	<div class="modal inmodal fade" id="invoicedata<?php echo $num; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
																		<div class="modal-dialog modal-lg">
																			<div class="modal-content">
																				<div class="modal-header">
																					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																					<h4 class="modal-title">Invoice No : <?php 
																						if(!empty($invoice->invoiceNumber)){
																							echo $invoice->invoiceNumber;
																						} 
																					?></h4>
																				</div> 
																				<div class="modal-body">
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
									<thead>
										<tr>
											<th style="min-width: 150px;">Product Name</th>
											<th>Batch</th>
											<th style="min-width: 65px;">Category</th>
											<th >Order type</th>
											<th style="min-width: 65px;">Order Date</th>
											<th >Dealer Name</th>
											<th style="min-width: 65px;">Delivery Date</th>
											<th >Customer Name</th>
											<th style="min-width: 200px;" >Notes</th>
											<th style="min-width: 65px;">Color</th>
											<th >Qty</th>
											<th >Status</th>
										</tr>
									</thead>
									<tbody>	
										<?php
											for($j=0;$j<count($ordertranz);$j++){
											 
												$order=DB::table('order_transaction')->where('orderStatus','=','complete')->where('orderNoteTokenString','=',$ordertranz[$j])->first();
												if(!empty($order)){
										?>
										<tr>
											<td>
											<?php 
												$getProductData=DB::table('products')->where('product_id','=', $order->product_id)->first();
												if(!empty($getProductData->productName)){
													echo $getProductData->productName;
												}
											?>
											</td>
											<td>
												<?php 
													if(!empty($order->batch)){
														echo $order->batch;
													}
												?>
											</td>
											<td>
												<?php 
													$getCategoryData=DB::table('category')->where('id','=', $getProductData->category_id)->first();
													if(!empty($getCategoryData->categoryName)){
														echo $getCategoryData->categoryName;
													}
												?>
											</td>
											<td>
												<?php
												//echo $order->product_id;
													 if(!empty($order->qtystatus)){
														$date= DB::table('variation')->where('product_color','=',$order->product_color)->where('product_status','=','inproduction')->where('product_id','=',$order->product_id)->first(); 
														if($order->qtystatus =='instock'){
															echo '<small class="label label-info"> InStock</small>';
														}else{
															 
															if($order->mailstatus==0){
																if(!empty($date->stockdat)){
																	echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																	
																}else{
																	if($order->specialOrderID > 0){
																		$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																		echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																	}else{
																		echo '<small class="label label-success">InProduction ('.date('d-m-Y',strtotime($date->stockdate)).' ) </small>';
																	}
																}
															}else{
																echo '<small class="label label-info"> InStock</small>';
															}
														}
													} 
													
												?>
											</td>
											<td>
												<?php if(!empty($order->created_at)){echo date('d-m-Y',strtotime($order->created_at));}?>
											</td>
											<td>
												<?php 
												if(!empty($order->dealerID)){
													
													$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
													$name='';
													if(!empty($delaername->first_name) && !empty($delaername->last_name)){
														$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
													}
													echo $name;
												}
												?>
											</td>
											<td>
												<?php
													if(!empty($order->delivery_date)){
														
														echo date('d-m-Y',strtotime($order->delivery_date));
													}else{
														echo '---';
													}
													?>
											</td>
											<td>
												<?php
												$orderNoteTokenString=$order->orderNoteTokenString;
												//echo $orderNoteTokenString;
												//$getOrderNotes=DB::table('order_notes')->where('orderNoteTokenString','=',$orderNoteTokenString)->first();
													if(!empty($order->customer_name)){
														echo $order->customer_name;
													}else{
														echo '---';
													}
												?>
											</td>
											<td>
												<?php 
													if(!empty($order->order_notes_descriptions)){
														echo $order->order_notes_descriptions;
													}else{
														echo '---';
													}
												?>
											</td>
											<td>
												<?php
													if(!empty($order->variationID)){
														$color=DB::table('variation')->where('variationID','=',$order->variationID)->where('deleted_at','=',NULL)->first();
														if(!empty($color->product_color)){
															echo $color->	product_color;
														}
													}
												?>
											</td>
											<td>1</td>
											<td>
												<label class="label label-success" style="text-transform:capitalize;">Complete</label>
											</td>
											 
										</tr>
										<?php
											  }
											}
                                        ?>
									</tbody>
								</table>	
							</div>
																				</div> 
																				<div class="modal-footer">
																					<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
																				</div>
																			</div>
																		</div> 
																	</div> 
																	 <?php 
																	$invoiceediturl = URL::to('admin/adminorderinvoiceedit', base64_encode($invoice->order_invoice_ID));
																		 
																	 ?>
																	 <?php 
																if(!empty($invoice->invoicepdf)){
																	$path=URL::to('uploads/invoicepdf/'.$invoice->invoicepdf);
																	echo '<a href="'.$path.'" target="_blank" title="View Invoice"class="btn btn-xs btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
																}
															?>
																</td>
															</tr>
														<?php
															}
															
														?>
															 
														</tbody>
													</table>
												</div>
											<!--</form>-->
											</div>
										</div>
								
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
    <script src="{{ asset('assets/js/plugins/pace/pace.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
    <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/sweetalert/sweetalert.min.js')}}"></script>
	  <script src="{{asset('assets/js/plugins/summernote/summernote.min.js')}}"></script>
	<script type="text/javascript">
	 deleted_data = null;
	 $(document).ready(function(){
		$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
			localStorage.setItem('activeTab', $(e.target).attr('href'));
		});
		var activeTab = localStorage.getItem('activeTab');
		if(activeTab){
			$('#myTab a[href="' + activeTab + '"]').tab('show');
		}
	});
	$(function() 
	{
		  $('.summernote').summernote();
		var order_table = $('.dataTables-example').DataTable({
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
		function removeData(valID,ordernotes){
			var order = valID;
			var orderstring = ordernotes;
			//console.log(order);
			swal({
				title: "Are you sure?",
				text: "you wish to delete reservation ?",
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
						var _token = $('#token').val();
						
						
						//alert(order);
						$.ajax
						({
							type: "POST",
							url: "{{URL::to('dealer/ajax/log/deleteorder')}}",
							data: {'order':order,'_token':_token,'orderstring':orderstring},
							success: function(msg)
							{ 	 
								//$('#product_name').html(msg);
								//console.log(msg);
								swal("Deleted!", "Your Order Item has been deleted.", "success"); 
								location.reload();
								
							}
						});  
					}	 
				});
		}
		deleted_data = removeData;
				
    });
	
	function removedata(valID,ordernotes)
	{
		if(valID != '' && valID != 0 && ordernotes !=''){
			deleted_data(valID,ordernotes);
		}
	}
	</script>
@stop()