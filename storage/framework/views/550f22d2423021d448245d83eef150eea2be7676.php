
<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/dataTables/datatables.min.css')); ?>" rel="stylesheet">
	 <link href="<?php echo e(asset('assets/css/plugins/iCheck/custom.css')); ?>" rel="stylesheet">
	 
<?php $__env->stopSection(); ?>
<?php
	$sessionData=Session::get('adminLog');
    
?>
<?php $__env->startSection('contentPages'); ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Finance orders</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                        </li>
                        <li>
                            <a>Store</a>
                        </li>
                        <li class="active">
                            <strong>Finance Orders</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>

			<div class="wrapper wrapper-content animated fadeInRight ecommerce">
				<div class="row">
					<div class="wrapper wrapper-content animated fadeInRight ecommerce">
						<div class="col-lg-12">
							<div class="ibox">
								<div class="ibox-content">
									 <ul class="nav nav-tabs" id="myTab">
										 
										
										<li class="active"><a data-toggle="tab" href="#tab-1">Pending Orders</a></li>
										 
										
										<li class=""><a data-toggle="tab" href="#tab-2">Completed Orders </a></li>
										
									</ul>
									<div class="tab-content"> 
									
			<!--------------------------------Pending Orders--------------------------------------->
			
										<div id="tab-1" class="tab-pane active"> 
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="<?php echo e(action('admin\OrderController@generateInvoice')); ?>" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<input type="hidden" name="opentab" value="pending"/>
											<!--<input type="submit" value="save"/>-->
												<br/>
												<div class="table-responsive">
												<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
													<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
														<thead>
															<tr>
																<!--<th >  </th>-->
																<!--<th >Order Number</th>-->
																<th >Company Name</th>
																<th >Product Name</th>
																<th>Batch</th>
																<th style="min-width: 65px;">Category</th>
																<th style="min-width: 65px;">Color</th>
																<th >Order type</th>
																<th style="min-width: 60px;" >Order Date</th>
																<th style="min-width: 60px;">Delivery Date</th>
																<th >Qty</th>
																<th >Status</th>
																<th >Customer Name</th>
																<th >Notes</th>
																<th style="min-width: 85px;">Action</th>
															</tr>
														</thead>
														<tbody>
														<?php
															//$orders=DB::table('product_order')->where('orderStatus','=','pending')->orderBy('orderID','desc')->get();
															$qry="SELECT * FROM product_order LEFT JOIN order_transaction ON product_order.orderID=order_transaction.orderID WHERE order_transaction.finance=1 AND order_transaction.orderStatus !='finance declined' AND order_transaction.orderStatus !='finance completed' AND order_transaction.deleted_at IS NULL AND product_order.deleted_at IS NULL ORDER BY product_order.orderID DESC";
															// echo $qry;
															// exit;
															 $orders=DB::select(DB::raw($qry));
															$num=0;
                                                            $qtycount=0;
                                                            $uniqueQtyNumber = '';
															$i=0;
															 
															foreach($orders as $order)
                                                            {	
																$i++;
																$num++;
                                                                $uniqueQtyNumber = $order->orderNoteTokenString;
                                                                
                                                              //  for($i=0;$i<$order->qty;$i++) {
                                                                     
																	//$qtycount++;
                                                                    $qtycount ++;
															 
														?>
															<tr>
																<!--<td align="center">
																	 <div class="i-checkss"><label> <input type="checkbox" class="selectorder disableclass<?php echo $order->orderID; ?>" name="orderID[]" data-tranztoken="<?php echo $order->orderNoteTokenString; ?>" data-order="<?php echo $order->orderID; ?>" value="<?php echo $order->orderID; ?>"> <i></i> </label></div>
																	 <input type="hidden" name="orderTranzToken[]" class="ordertockentrz" disabled value="<?php echo $order->orderNoteTokenString; ?>"/>
																</td>-->
																<!--<td>
																	<?php
																		if(!empty($order->OrderNumber)){
																			echo '<label class="label label-default">'.$order->OrderNumber.'</label>';
																			
																		}
																	?>
																</td>-->
																<td>
																	<?php 
																	if(!empty($order->dealerID)){
																		
																		$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
																		$name='';
																		if(!empty($delaername->company_name)){
																			$name= $delaername->company_name;
																		}
																		echo $name;
																	}
																	?>
																</td>
																<td>
																	<?php
																		if(!empty($order->product_id)){
																			$productName=DB::table('products')->where('product_id','=',$order->product_id)->where('deleted_at','=',NULL)->first();
																			if(!empty($productName->productName)){
																				echo $productName->	productName;
																				if($order->specialOrderID > 0){
																					echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
																				}
																			}
																		}
																	?>
																</td>
																
																<td>
																	<?php 
																		if(!empty($order->qtystatus)){
																			$batch=DB::table('variation')->where('variationID','=',$order->variationID)->first();
																			if(!empty($batch->batch)){
																				echo $batch->batch;
																			} else{
																				echo $order->batch;
																			}
																		}else{
																			echo '--';
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
																		if(!empty($order->variationID)){
																			$color=DB::table('variation')->where('variationID','=',$order->variationID)->where('deleted_at','=',NULL)->first();
																			if(!empty($color->product_color)){
																				echo $color->product_color;
																			}else{
																				
																			}
																		}else{
																				echo $order->product_color;
																			
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
																						if($order->qtystatus== 'onseaukarrival'){
																							echo '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																						}elseif($order->qtystatus == 'factorystock'){
														
																								echo '<label class="label label-primary"> FactoryStock</label>';
																									
																								 
																							}else{
																							echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																						}
																					}else{
																						
																						if(!empty($date->stockdat)){
																							 
																							if($order->qtystatus== 'onseaukarrival'){
																								echo '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																							}elseif($order->qtystatus == 'factorystock'){
														
																								echo '<label class="label label-primary"> FactoryStock</label>';
																									
																								 
																							}else{
																								echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																							}
																							
																						}else{
																							if($order->specialOrderID > 0){
																								$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																								echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																							}else{
																								if($order->qtystatus== 'onseaukarrival'){
																									echo '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																								}elseif($order->qtystatus == 'factorystock'){
														
																								echo '<label class="label label-primary"> FactoryStock</label>';
																									
																								 
																							}else{
																									echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																								}
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
																		if(!empty($order->delivery_date)){
																			
																			echo date('d-m-Y',strtotime($order->delivery_date));
																		}else{
																			echo '---';
																		}
																		?>
																</td>
																
																<td>1</td>
																 
																<td>
																
																	<?php
																	if($order->orderStatus != ''){
																		 if($order->orderStatus=='finance new'){
																			echo '<label class="label label-warning" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																			 
																		 }else if($order->orderStatus=='finance link sent') {
																			echo '<label class="label label-danger" style="text-transform:capitalize;background-color:#72b733 !important;">'.$order->orderStatus.'</label>';
																			
																		 }else if($order->orderStatus=='finance accepted') {
																			echo '<label class="label label-info" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																			
																		 }else if($order->orderStatus=='finance verified'){
																			 echo '<label class="label label-primary" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																			 
																		 }else if($order->orderStatus=='finance awaiting delivery slip'){
																			 echo '<label class="label label-success" style="text-transform:capitalize;background-color:#F7609E;">'.$order->orderStatus.'</label>';
																			 
																		 }else if($order->orderStatus=='finance completed'){
																			 echo '<label class="label label-success" style="text-transform:capitalize;background-color:#1ab357;">'.$order->orderStatus.'</label>';
																			 
																		 }else{
																			 echo '<label class="label label-danger" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																		 }
																	}else{
																		echo '--';
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
																			$addcutomeraction=URL::to('/admin/addadmincustomername/');
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
																							<input type="hidden" name="opentab" value="pending"/>
																								<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
																								<input type="hidden" name="productToken" value="<?php echo  base64_encode($order->product_id) ;?>'"/>
																								 <input type="hidden" name="otdertypepage" value="finance"/>
																								<input type="hidden" name="orderToken" value="<?php echo  base64_encode($order->orderID); ?>"/>
																								<input type="hidden" name="orderTokenString" value="<?php echo $uniqueQtyNumber; ?>"/>
																								 
																										
																							</div>
																							<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
																								<div class="form-group" style="width: 100%;">
																								<label class="control-label">Customer Name :</label><br/>
																								<input type="text" style="width:100%"  class="form-control" name="order_notes_title" required id="order_notes_title" placeholder="Customer Name"></div>
																								<div class="form-group" style="width: 100%;">
																								<label class="control-label">Notes:</label><br/>
																								<textarea style="width:100%"  required name="order_notes_descriptions" class="form-control" placehoder="Notes"></textarea></div>
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
                                                                    $invoiceurl = URL::to('admin/adminOrderInvoice', base64_encode($order->orderID));
                                                                    $delurl = URL::to('admin/deleteadminorder', base64_encode($order->orderID));
                                                                    $deatailurl = URL::to('admin/financeorderdetail', base64_encode($order->orderID.'&'.$orderNoteTokenString));
																	
																	
																	$printurl = URL::to('admin/orderPrint', base64_encode($order->orderID.'&'.$orderNoteTokenString.'&pending'));
																	
                                                                    $action=URL::to('/admin/addordernotes/');
                                                                    $actionInProductionOrder=URL::to('/admin/inproductionorder/');
																	 
                                                                    $notes=DB::table('admin_order_notes')->where('orderTokenString','=',$uniqueQtyNumber)->where('orderID','=',$order->orderID)->where('product_id','=',$order->product_id)->orderBy('admin_order_notesID','desc')->get();
																	//echo $uniqueQtyNumber.'<br/>';
																	$delaerName=DB::table('dealer')->where('id','=',$order->dealerID)->first();
																	 
																$sender="";
																 
																 if($sessionData['role']=='admin'){
																	$sender='admin';
																}else{
																	if($sessionData['role']=='staff' && isset($sessionData['first_name']) && !empty($sessionData['first_name'])){
																		$sender=$sessionData['first_name'].' (Staff)';
																	}
																} 
																//echo $sender;
																						 

																	 ?>
																	 	<a href="javascript:void(0)"  data-toggle="modal" data-target="#notes<?php echo $order->orderID.$qtycount; ?>"  title="add notes" class="btn btn-xs btn-default"><i class="fa fa-file-text-o"></i></a>
																		<div class="modal inmodal" id="notes<?php echo $order->orderID.$qtycount; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content animated fadeIn">

                                                                                    <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="products" id="noteform<?php echo $order->orderID.$qtycount; ?>">
                                                                                        <input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
                                                                                        <input type="hidden" name="productToken" value="<?php echo  base64_encode($order->product_id) ;?>"/>
                                                                                        <input type="hidden" name="sender" value="<?php echo $sender; ?>"/>
																						<input type="hidden" name="dealerID" value="<?php echo $order->dealerID; ?>"/>
                                                                                      	<input type="hidden" name="ordertype" value="finance"/>
																						<input type="hidden" name="sendertype" value="<?php echo $sessionData['role']; ?>"/>
                                                                                      	<input type="hidden" name="opentab" value="pending"/>
                                                                                        <input type="hidden" name="orderToken" value="<?php echo  base64_encode($order->orderID); ?>"/>
                                                                                        <input type="hidden" name="orderTokenString" value="<?php echo $uniqueQtyNumber; ?>"/>
                                                                                        <div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                                            <h4 class="modal-title"><?php echo  $productName->productName; ?></h4>
                                                                                        </div>
                                                                                        <div class="modal-body col-md-12" style=" max-height: 350px;overflow-y: scroll;">
                                                                                            <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																							<!--<div class="form-group" style="width: 100%;">
                                                                                                <label class="control-label">Name:</label><br/>
                                                                                                <input type="text" style="width:100%" required name="name" class="form-control" placeholder="Name" value="<?php echo  $name ; ?>" ></div><br/><br/>-->
																								
                                                                                            </div> 
                                                                                            <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
                                                                                                <div class="form-group" style="width: 100%;">
                                                                                                <label class="control-label">Notes:</label><br/>
                                                                                                <textarea style="width:100%"  required name="description" class="form-control" placehoder="Notes"></textarea></div>
																								 <label class="control-label">Send Mail:</label><br/>
                                                                                                <input type="checkbox" name="sendMail" value="1"> 
                                                                                            </div>
																							<div class="clearfix"></div>
																							<hr/>
																							<?php
																					foreach($notes as $note){
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
																								if($note->role=='admin'){
																									$sender='<small class="label label-success">Admin </small>';
																								}elseif($note->role=='staff'){
																									$sender='<small class="label label-success"> '.$note->sender .'</small>&nbsp;&nbsp;' ;
																								}else{
																									$sender='<small class="label label-success"> '.$note->sender .'</small>&nbsp;&nbsp';
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
																					?>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                                                            <input type="submit" data-formid="noteform<?php echo $order->orderID.$qtycount; ?>" class="btn btn-primary notebtn" value="Save changes" />
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
																	  <a href="<?php echo $deatailurl; ?>"  data-toggle="tooltip" title="Edit order" class="btn btn-xs btn-default"><i class="fa fa-pencil-square"></i></a>

																	  <a href="<?php echo $printurl; ?>"  data-toggle="tooltip" title="Print Order" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-print"></i></a>
																	  
																	<a href="javascript:void(0);" data-toggle="tooltip" title="Delete"  onclick="removedata(<?php echo $order->orderID; ?>,'<?php echo $orderNoteTokenString; ?>')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>
																	
																	
																	<div class="modal inmodal" id="inProductionMailNotes<?php echo $order->orderID.$qtycount; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content animated fadeIn">

                                                                                    <form action="<?php echo $actionInProductionOrder; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
                                                                                        <input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
                                                                                        <input type="hidden" name="productToken" value="<?php echo  base64_encode($order->product_id) ;?>"/>
                                                                                        <input type="hidden" name="sender" value="admin"/>
																						<input type="hidden" name="opentab" value="pending"/>
                                                                                        <input type="hidden" name="orderToken" value="<?php echo  base64_encode($order->orderID); ?>"/>
                                                                                        <input type="hidden" name="orderTokenString" value="<?php echo $order->orderNoteTokenString; ?>"/>
																					
                                                                                        <div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                                            <h4 class="modal-title"><?php if(!empty($delaername->first_name) && !empty($delaername->last_name)){
																		$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
																	}
																	echo $name; ?></h4>
                                                                                        </div>
                                                                                        <div class="modal-body col-md-12" style=" max-height: 350px;overflow-y: scroll;">
                                                                                            <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																							<!--<div class="form-group" style="width: 100%;">
                                                                                                <label class="control-label">Name:</label><br/>
                                                                                                <input type="text" style="width:100%" required name="name" class="form-control" placeholder="Name" value="<?php echo  $name ; ?>" ></div><br/><br/>-->
																								
                                                                                            </div> 
                                                                                            <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
                                                                                                <div class="form-group" style="width: 100%;">
                                                                                                <label class="control-label">Notes:</label><br/>
                                                                                                <textarea style="width:100%" required name="mailnotes" class="form-control" placehoder="Notes"></textarea></div>
                                                                                            </div>
																							<div class="clearfix"></div>
																							<hr/>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                                                            <input type="submit" class="btn btn-primary" value="Send Mail" />
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
																	 
																</td>
															</tr>
														<?php	 
																/* } */
															}
														?>
															 
														</tbody>
													</table>
												</div>
												 
											<!--</form>-->
											</div>
										</div>
										<div id="tab-2" class="tab-pane"> 
											<div class="panel-body">
											<!--<form class="m-t form-horizontal"  role="form" method="post" action="<?php echo e(action('admin\OrderController@generateInvoice')); ?>" id="form_customer_profile" >-->
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<input type="hidden" name="opentab" value="pending"/>
											<!--<input type="submit" value="save"/>-->
												<br/>
												<div class="table-responsive">
												<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
													<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
														<thead>
															<tr>
																<!--<th >  </th>-->
																<!--<th >Order Number</th>-->
																<th >Company Name</th>
																<th >Product Name</th>
																<th>Batch</th>
																<th style="min-width: 65px;">Category</th>
																<th style="min-width: 65px;">Color</th>
																<th >Order type</th>
																<th style="min-width: 60px;" >Order Date</th>
																<th style="min-width: 60px;">Delivery Date</th>
																<th >Qty</th>
																<th >Status</th>
																<th >Customer Name</th>
																<th >Notes</th>
																<th style="min-width: 85px;">Action</th>
															</tr>
														</thead>
														<tbody>
														<?php
															//$orders=DB::table('product_order')->where('orderStatus','=','pending')->orderBy('orderID','desc')->get();
															$qry="SELECT * FROM product_order LEFT JOIN order_transaction ON product_order.orderID=order_transaction.orderID WHERE order_transaction.finance=1 AND order_transaction.orderStatus='finance declined' OR order_transaction.orderStatus='finance completed' AND order_transaction.deleted_at IS NULL AND product_order.deleted_at IS NULL ORDER BY product_order.orderID DESC";
															// echo $qry;
															// exit;
															 $orders=DB::select(DB::raw($qry));
															$num=0;
                                                            $qtycount=0;
                                                            $uniqueQtyNumber = '';
															$i=0;
															 
															foreach($orders as $order)
                                                            {	
																$i++;
																$num++;
                                                                $uniqueQtyNumber = $order->orderNoteTokenString;
                                                                
                                                              //  for($i=0;$i<$order->qty;$i++) {
                                                                     
																	//$qtycount++;
                                                                    $qtycount ++;
															 
														?>
															<tr>
																<!--<td align="center">
																	 <div class="i-checkss"><label> <input type="checkbox" class="selectorder disableclass<?php echo $order->orderID; ?>" name="orderID[]" data-tranztoken="<?php echo $order->orderNoteTokenString; ?>" data-order="<?php echo $order->orderID; ?>" value="<?php echo $order->orderID; ?>"> <i></i> </label></div>
																	 <input type="hidden" name="orderTranzToken[]" class="ordertockentrz" disabled value="<?php echo $order->orderNoteTokenString; ?>"/>
																</td>-->
																<!--<td>
																	<?php
																		if(!empty($order->OrderNumber)){
																			echo '<label class="label label-default">'.$order->OrderNumber.'</label>';
																			
																		}
																	?>
																</td>-->
																<td>
																	<?php 
																	if(!empty($order->dealerID)){
																		
																		$delaername=DB::table('dealer')->where('id','=',$order->dealerID)->first();
																		$name='';
																		if(!empty($delaername->company_name)){
																			$name= $delaername->company_name;
																		}
																		echo $name;
																	}
																	?>
																</td>
																<td>
																	<?php
																		if(!empty($order->product_id)){
																			$productName=DB::table('products')->where('product_id','=',$order->product_id)->where('deleted_at','=',NULL)->first();
																			if(!empty($productName->productName)){
																				echo $productName->	productName;
																				if($order->specialOrderID > 0){
																					echo '<label class="label label-warning" style="text-transform:capitalize;border-radius: 10px;">S</label>';
																				}
																			}
																		}
																	?>
																</td>
																
																<td>
																	<?php 
																		if(!empty($order->qtystatus)){
																			$batch=DB::table('variation')->where('variationID','=',$order->variationID)->first();
																			if(!empty($batch->batch)){
																				echo $batch->batch;
																			} else{
																				echo $order->batch;
																			}
																		}else{
																			echo '--';
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
																		if(!empty($order->variationID)){
																			$color=DB::table('variation')->where('variationID','=',$order->variationID)->where('deleted_at','=',NULL)->first();
																			if(!empty($color->product_color)){
																				echo $color->product_color;
																			}else{
																				
																			}
																		}else{
																				echo $order->product_color;
																			
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
																						if($order->qtystatus== 'onseaukarrival'){
																							echo '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																						}elseif($order->qtystatus == 'factorystock'){
														
																								echo '<label class="label label-primary"> FactoryStock</label>';
																									
																								 
																							}else{
																							echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($order->stockdate)).') </small>';
																						}
																					}else{
																						
																						if(!empty($date->stockdat)){
																							 
																							if($order->qtystatus== 'onseaukarrival'){
																								echo '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																							}elseif($order->qtystatus == 'factorystock'){
														
																								echo '<label class="label label-primary"> FactoryStock</label>';
																									
																								 
																							}else{
																								echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																							}
																							
																						}else{
																							if($order->specialOrderID > 0){
																								$getSpeacialOrders=DB::table('special_order')->where('id','=',$order->specialOrderID)->first();
																								echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($getSpeacialOrders->inproduction_date)).' ) </small>';
																							}else{
																								if($order->qtystatus== 'onseaukarrival'){
																									echo '<small style="background-color: #029dff;" class="label label-success">On Sea - UK Arrival ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																								}elseif($order->qtystatus == 'factorystock'){
														
																								echo '<label class="label label-primary"> FactoryStock</label>';
																									
																								 
																							}else{
																									echo '<small class="label label-success">In Production ('.date('d-m-Y',strtotime($date->stockdate)).') </small>';
																								}
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
																		if(!empty($order->delivery_date)){
																			
																			echo date('d-m-Y',strtotime($order->delivery_date));
																		}else{
																			echo '---';
																		}
																		?>
																</td>
																
																<td>1</td>
																 
																<td>
																
																	<?php
																	if($order->orderStatus != ''){
																		 if($order->orderStatus=='finance new'){
																			echo '<label class="label label-warning" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																			 
																		 }else if($order->orderStatus=='finance link sent') {
																			echo '<label class="label label-danger" style="text-transform:capitalize;background-color:#72b733 !important;">'.$order->orderStatus.'</label>';
																			
																		 }else if($order->orderStatus=='finance accepted') {
																			echo '<label class="label label-info" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																			
																		 }else if($order->orderStatus=='finance verified'){
																			 echo '<label class="label label-primary" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																			 
																		 }else if($order->orderStatus=='finance awaiting delivery slip'){
																			 echo '<label class="label label-success" style="text-transform:capitalize;background-color:#F7609E;">'.$order->orderStatus.'</label>';
																			 
																		 }else if($order->orderStatus=='finance completed'){
																			 echo '<label class="label label-success" style="text-transform:capitalize;background-color:#1ab357;">'.$order->orderStatus.'</label>';
																			 
																		 }else{
																			 echo '<label class="label label-danger" style="text-transform:capitalize;">'.$order->orderStatus.'</label>';
																		 }
																	}else{
																		echo '--';
																	}
																	?>
																</td>
																
																
																<td>
																	<?php
																		$orderNoteTokenString=$order->orderNoteTokenString;
																		 
																			if(!empty($order->customer_name)){
																				echo $order->customer_name;
																			}else{
																				echo '---';
																			}
																			$addcutomeraction=URL::to('/admin/addadmincustomername/');
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
																							<input type="hidden" name="opentab" value="pending"/>
																								<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
																								<input type="hidden" name="productToken" value="<?php echo  base64_encode($order->product_id) ;?>'"/>
																								 <input type="hidden" name="otdertypepage" value="finance"/>
																								<input type="hidden" name="orderToken" value="<?php echo  base64_encode($order->orderID); ?>"/>
																								<input type="hidden" name="orderTokenString" value="<?php echo $uniqueQtyNumber; ?>"/>
																								 
																										
																							</div>
																							<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
																								<div class="form-group" style="width: 100%;">
																								<label class="control-label">Customer Name :</label><br/>
																								<input type="text" style="width:100%"  class="form-control" name="order_notes_title" required id="order_notes_title" placeholder="Customer Name"></div>
																								<div class="form-group" style="width: 100%;">
																								<label class="control-label">Notes:</label><br/>
																								<textarea style="width:100%"  required name="order_notes_descriptions" class="form-control" placehoder="Notes"></textarea></div>
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
                                                                    $invoiceurl = URL::to('admin/adminOrderInvoice', base64_encode($order->orderID));
                                                                    $delurl = URL::to('admin/deleteadminorder', base64_encode($order->orderID));
                                                                    $deatailurl = URL::to('admin/financeorderdetail', base64_encode($order->orderID.'&'.$orderNoteTokenString));
																	
																	
																	$printurl = URL::to('admin/orderPrint', base64_encode($order->orderID.'&'.$orderNoteTokenString.'&pending'));
																	
                                                                    $action=URL::to('/admin/addordernotes/');
                                                                    $actionInProductionOrder=URL::to('/admin/inproductionorder/');
																	 
                                                                    $notes=DB::table('admin_order_notes')->where('orderTokenString','=',$uniqueQtyNumber)->where('orderID','=',$order->orderID)->where('product_id','=',$order->product_id)->orderBy('admin_order_notesID','desc')->get();
																	//echo $uniqueQtyNumber.'<br/>';
																	$delaerName=DB::table('dealer')->where('id','=',$order->dealerID)->first();
																	 
																$sender="";
																 
																 if($sessionData['role']=='admin'){
																	$sender='admin';
																}else{
																	if($sessionData['role']=='staff' && isset($sessionData['first_name']) && !empty($sessionData['first_name'])){
																		$sender=$sessionData['first_name'].' (Staff)';
																	}
																} 
																//echo $sender;
																						 

																	 ?>
																	 	<!--<a href="javascript:void(0)"  data-toggle="modal" data-target="#notes<?php echo $order->orderID.$qtycount; ?>"  title="add notes" class="btn btn-xs btn-default"><i class="fa fa-file-text-o"></i></a>-->
																	 

																	  <a href="<?php echo $printurl; ?>"  data-toggle="tooltip" title="Print Order" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-print"></i></a>
																	  
																	
																	<div class="modal inmodal" id="inProductionMailNotes<?php echo $order->orderID.$qtycount; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content animated fadeIn">

                                                                                    <form action="<?php echo $actionInProductionOrder; ?>" method="POST" enctype="multipart/form-data" class="products" id="">
                                                                                        <input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
                                                                                        <input type="hidden" name="productToken" value="<?php echo  base64_encode($order->product_id) ;?>"/>
                                                                                        <input type="hidden" name="sender" value="admin"/>
																						<input type="hidden" name="opentab" value="pending"/>
                                                                                        <input type="hidden" name="orderToken" value="<?php echo  base64_encode($order->orderID); ?>"/>
                                                                                        <input type="hidden" name="orderTokenString" value="<?php echo $order->orderNoteTokenString; ?>"/>
																					
                                                                                        <div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                                            <h4 class="modal-title"><?php if(!empty($delaername->first_name) && !empty($delaername->last_name)){
																		$name= $delaername->first_name.'&nbsp;'.$delaername->last_name;
																	}
																	echo $name; ?></h4>
                                                                                        </div>
                                                                                        <div class="modal-body col-md-12" style=" max-height: 350px;overflow-y: scroll;">
                                                                                            <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
																							<!--<div class="form-group" style="width: 100%;">
                                                                                                <label class="control-label">Name:</label><br/>
                                                                                                <input type="text" style="width:100%" required name="name" class="form-control" placeholder="Name" value="<?php echo  $name ; ?>" ></div><br/><br/>-->
																								
                                                                                            </div> 
                                                                                            <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
                                                                                                <div class="form-group" style="width: 100%;">
                                                                                                <label class="control-label">Notes:</label><br/>
                                                                                                <textarea style="width:100%" required name="mailnotes" class="form-control" placehoder="Notes"></textarea></div>
                                                                                            </div>
																							<div class="clearfix"></div>
																							<hr/>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                                                            <input type="submit" class="btn btn-primary" value="Send Mail" />
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
																	 
																</td>
															</tr>
														<?php	 
																/* } */
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
		<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<script src="<?php echo e(asset('assets/js/plugins/pace/pace.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/plugins/iCheck/icheck.min.js')); ?>"></script>
	<script type="text/javascript">
	$(function() {
		 $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
		/* var customer_table = $('#order_table').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
						buttons: [
                             {extend: 'csv', title:'Product Details'},
                            {extend: 'excel', title:'Product Details'},
                            {extend: 'pdf', title:'Product Details'}, 
                        ],
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
							url: "<?php echo e(URL::to('admin/ajax/log/adminOrderList')); ?>",
                            data:function(d){  }
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
		function ordercheckbox(){
			$('.selectorder').each(function(){
				if(this.checked == true) {
					$(this).parent().parent().parent().parent().css('background','#F5F5F6');
					var ordertranztoken=$(this).attr('data-tranztoken');
					$(this).parent().parent().next().val(ordertranztoken);
					$(this).parent().parent().parent().find('.ordertockentrz').prop('disabled',false);
					$(this).parent().parent().parent().find('.dealertoken').prop('disabled',false);
					 
					//disableclass
				}
				if(this.checked == false) {
					$(this).parent().parent().parent().parent().removeAttr('style');
					$(this).parent().parent().parent().find('.ordertockentrz').prop('disabled',true);
					$(this).parent().parent().parent().find('.dealertoken').prop('disabled',true);
				}
			});
		} 
		$('.selectorder').click(function(){
			//ordercheckbox();
			var ordercheckboxid=$(this).attr('id');
			var ordertranztoken=$(this).attr('data-tranztoken');
			var dealerToken=$(this).attr('data-dealerToken');
			var orderToken=$(this).val();
			
			if(this.checked == true) {
			//alert(ordercheckboxid);
				var newmedDiv = $(document.createElement('div'))
				 .attr("id", 'invoicetxtbox' + ordertranztoken);
				 
				newmedDiv.after().html('<input type="hidden" name="orderTranzToken[]" class="ordertockentrz" value="'+ordertranztoken+'"/><input value="'+dealerToken+'" type="hidden" name="dealerToken[]" class="dealertoken" /><input type="hidden" name="orderToken[]" class="orderToken" value="'+orderToken+'"/>');
					
				newmedDiv.appendTo("#invoicegroup");
			 }
			 if(this.checked == false) {
				$('#invoicetxtbox'+ordertranztoken).remove();
				 
			 }
		});
		function removeData(valID,ordernotes){
			var order = valID;
			var orderstring = ordernotes;
			//console.log(order);
			swal({
				title: "Are you sure?",
				text: "This order Will be deleted?",
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
					 
						$.ajax
						({
							type: "POST",
							url: "<?php echo e(URL::to('admin/ajax/log/deleteaminorder')); ?>",
						data: {'order':order,'_token':_token,'orderstring':orderstring},
							success: function(msg)
							{ 	 
								//$('#product_name').html(msg);
								//alert(msg);
								//order_table.draw();
								swal("Deleted!", "Your Order Item has been deleted.", "success"); 
								location.reload();
								
							}
						});  
					}	 
				});
				
		}
		deleted_data = removeData;
		$('#bookedinfordeliverybtn').click(function(){
			$('#bookedinfordelivery').submit();
			
		});
		$('.notebtn').click(function(){
			var fromid=$(this).attr('data-formid');
			//alert(fromid);
			$(fromid).submit();
		});
		 
    });
	function removedata(valID,ordernotes)
	{
		if(valID != '' && valID != 0 && ordernotes !=''){
			deleted_data(valID,ordernotes);
		}
	}
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>