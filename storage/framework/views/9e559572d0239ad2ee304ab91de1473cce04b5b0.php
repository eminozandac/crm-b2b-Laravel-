


<?php $__env->startSection('pagecss'); ?>

    <style>
        a,a:hover, a:active, a:focus, a.class-refresh{
            outline: 0;
        }
        .fc-event{
            line-height: 1.4em;
        }
        .my_col{
            width: 20%;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
	<?php  $sessionData=Session::get('staffLog');?>
            <div class="wrapper wrapper-content">
				<div class="row">
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5><a href="<?php echo e(URL::to('staff/leadsreport')); ?>">Today Leads Report</a></h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php echo e($todayLeads); ?></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>Total Leads : <?php echo e($totalLeads); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5><a href="<?php echo e(URL::to('staff/task')); ?>">Today Task</a></h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php echo e($todayTask); ?></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>Total Task : <?php echo e($totalTask); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5><a href="<?php echo e(URL::to('admin/orderList')); ?>">New Orders</a></h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php echo e($todayOrder); ?></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>All orders :<?php  $orderCount = DB:: table('order_transaction')->where('deleted_at', '=', NULL)->count();
                            echo $orderCount;
                            ?> </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5><a href="<?php echo e(URL::to('admin/specialorderslist')); ?>">Special Order</a></h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php echo e($todaySpecialOrder); ?></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>All Special Order : <?php echo e($totalSpecialOrder); ?></small>
                            </div>
                        </div>
					</div>
					<div class="col-lg-3">
						<div class="ibox float-e-margins">
							<div class="ibox-title">
								<span class="label label-info pull-right"></span>
								<h5><a href="<?php echo e(URL::to('/admin/orderList')); ?>">Accessory Orders</a></h5>
							</div>
							<div class="ibox-content">
								<h1 class="no-margins"><?php
									$accessory_order = DB:: table('accessory_order')->where('deleted_at', '=', NULL)->count();
									echo $accessory_order;
									?></h1>

								<div class="stat-percent font-bold text-info"></div>
								<small>Total Accessory orders</small>
							</div>
						</div>
					</div>
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-success pull-right"></span>
                                <h5><a href="<?php echo e(URL::to('/staff/warranty')); ?>">Warranty</a></h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins">
                                    <?php
                                    $warrantynewclaimCount = DB:: table('warrantyproduct')->where('warranty_status', '=', 'new_claim')->where('deleted_at', '=', NULL)->count();
                                    echo $warrantynewclaimCount;
                                    ?>
                                </h1>

                                <div class="stat-percent font-bold text-success"></div>
                                <small>Total Warranty Claim :<?php  $orderCount = DB:: table('warrantyproduct')->where('deleted_at', '=', NULL)->count();
                            echo $orderCount;
                            ?></small>
                            </div>
                        </div>
                    </div>
				</div>

                <div class="row">

                    <div class="col-lg-6">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Order Message</h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
								
                            </div>
                            <div class="ibox-content" id="oder_message">
                                <div class="feed-activity-list">
                                    <?php
								
									$senderstff=$sessionData['first_name'].' (Staff)';
									$senderadmin='admin';
                                        $note=DB::table("admin_order_notes")->where('deleted_at','=',NULL)->where('orderID','!=','0')->where('role','!=','staff')->where('role','!=','admin')->orderBy('admin_order_notesID','DESC')->get();
                                    ?>
                                    <?php if(isset($note) && (!empty($note))): ?>

                                        <?php foreach($note as $key_note => $value_note): ?>

                                            <?php
											
											 
                                            $today = date('Y-m-d H:i:s');
                                            $a = new DateTime($today);
                                            $b = new DateTime($value_note->created_at);
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

                                            $result_product = DB::table('products')->select('productName')->where('product_id','=',$value_note->product_id)->first();
											$dealerCompany=DB::table('dealer')->where('id','=',$value_note->dealerID)->first();
                                            ?>

                                            <div class="feed-element">
                                                <div>
                                                    <small class="pull-right text-navy"><?php echo e($time_text); ?></small>
                                                    <strong class="label label-primary"  style="margin-bottom: 10px;"><?php echo e($dealerCompany->company_name); ?> (<?php echo $dealerCompany->role; ?>) </strong><br/>
                                                    Product : <strong style="margin-bottom: 10px;"><?php echo e($result_product->productName); ?></strong><br/>
                                                    <div style="margin-bottom: 10px;"> Description : <?php echo e(\Illuminate\Support\Str::limit($value_note->description, $limit = 100, $end = '...')); ?></div>
                                                    <small class="text-muted">
                                                        Time : <?php echo e(date('H:i A',strtotime($value_note->created_at))); ?> - <?php echo e(date('d-m-Y',strtotime($value_note->created_at))); ?>

                                                    </small>
                                                     <?php  $action=URL::to('/admin/addordernotes/');
$getTran=DB::table('order_transaction')->where('orderNoteTokenString','=',$value_note->orderTokenString)->first();
$productName=DB::table('products')->where('product_id','=',$getTran->product_id)->first();

													 ?>
													 <a href="javascript:void(0)"  data-toggle="modal" data-target="#notesssss<?php echo $value_note->orderTokenString; ?>"  title="add notes" class="btn btn-xs btn btn-primary" style="float: right;">Show Message</a>
<div class="modal inmodal" id="notesssss<?php echo $value_note->orderTokenString; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content animated fadeIn">
		
			<form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="products" id="noteform<?php echo $value_note->orderTokenString; ?>">
				<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
				<input type="hidden" name="productToken" value="<?php echo  base64_encode($getTran->product_id) ;?>"/>
				<?php 
				$sender="";
				 
				$sessionData=Session::get('adminLog');
				
				$sender=$sessionData['first_name'].' (Staff)';
					
				//echo $sender;
				  $dealerCompany=DB::table('dealer')->where('id','=',$getTran->dealerID)->first();
				$categoryName = DB::table('category')->where('id', '=', $productName->category_id)->where('deleted_at','=',NULL)->first();
				$barndName = DB::table('brand')->where('id', '=', $productName->brand_id)->where('deleted_at','=',NULL)->first();
					?>
					
				<input type="hidden" name="sender" value="<?php echo $sender; ?>"/>
				<input type="hidden" name="sendertype" value="<?php echo $sessionData['role']; ?>"/>
				<input type="hidden" name="dealerID" value="<?php echo $getTran->dealerID; ?>"/>
				<input type="hidden" name="opentab" value="pending"/>
				<input type="hidden" name="ordertype" value="staffdashboard"/>
				<input type="hidden" name="orderToken" value="<?php echo  base64_encode($getTran->orderID); ?>"/>
				<input type="hidden" name="orderTokenString" value="<?php echo $value_note->orderTokenString; ?>"/>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title"><?php echo  $productName->productName; ?></h4>
				</div>
				<div class="modal-body col-md-12" style=" max-height: 350px;overflow-y: scroll;">
					<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
					 
						
					</div> 
					<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1  col-xs-12">
						<p><strong>Product Name : </strong><?php echo $productName->productName; ?></p>
						<p><strong>Category : </strong><?php echo $categoryName->categoryName; ?></p>
						<p><strong>Brand : </strong><?php echo $barndName->brandName; ?></p>
						<p><strong>Color : </strong><?php echo $getTran->product_color; ?></p>
						<p><strong>Order Status : </strong><?php echo $getTran->orderStatus; ?></p>
						<p><strong>Order Type : </strong><?php echo $getTran->qtystatus; ?></p>
						<div class="hr-line-dashed"></div>
						<div class="form-group" style="width: 100%;">
						<label class="control-label">Notes:</label><br/>
						<textarea style="width:100%"  required name="description" class="form-control" placehoder="Notes"></textarea></div>
						<div class="form-group" style="width: 100%;">
						<label class="control-label">Send Mail:</label><br/>
						<input type="checkbox" name="sendMail" value="1"></div>
					</div>
					<div class="clearfix"></div>
					<hr/>
					<?php
					 $notes=DB::table('admin_order_notes')->where('deleted_at','=',NULL)->where('orderID','=',$getTran->orderID)->where('product_id','=',$getTran->product_id)->orderBy('admin_order_notesID','desc')->get();
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
						/* if($note->sender=='admin'){
							$sender='<small class="label label-info"> You</small>&nbsp;Admin';
						}else{
							$sender='<small class="label label-success"> Dealer</small>&nbsp;&nbsp;'.$delaerName->first_name.'&nbsp;'.$delaerName->last_name;
						} */
					 $sender = '<small class="label label-success">' . $dealerCompany->company_name .' ('.$dealerCompany->role.')'. '</small>';
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
					<input type="submit" data-formid="noteform<?php echo $value_note->orderTokenString; ?>" class="btn btn-primary notebtn" value="Save changes" />
				</div>
			</form>
		</div>
	</div>
</div>
                                                   
                                                </div>
                                            </div>

                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <div class="feed-element">
                                            <div>
                                                <small class="pull-right text-navy">0</small>
                                                <strong>Not Any Message</strong>
                                                <small class="text-muted">--</small>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">

                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>New Warranty Note</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="feed-activity-list" id="dashboard_warranty_note">
                                    <?php
                                    $result_warranty_note = DB::table('warrantyproduct_note')->select('*')
                                            ->where('role','!=','staff')
                                            ->orderBy('id','DESC')->get();
                                    ?>
                                    <?php if(!empty($result_warranty_note)): ?>
                                        <?php foreach($result_warranty_note as $key => $value): ?>
                                            <?php
                                            $date = date('d-m-Y',strtotime($value->created_at));
                                            $time = date('h:i A',strtotime($value->created_at));

                                            $name = '';
                                            if($value->role == 'admin')
                                            {
                                                $result_name = DB::table('admin')->select('name')->first();
                                                if(!empty($result_name))
                                                {
                                                    $name = $result_name->name;
                                                }
                                            }else{
                                                $result_name = DB::table($value->role)
                                                        ->select('first_name','last_name')
                                                        ->where('id','=',$value->user_id)
                                                        ->first();
                                                if(!empty($result_name)){
                                                    $name = $result_name->first_name.' '.$result_name->last_name;
                                                }
                                            }

                                            $model_name = '';
                                            $result_warranty_model =  DB::table('warrantyproduct')->select('*')->where('warranty_uniqueID','=',$value->warranty_uniqueID)->first();
                                            if(!empty($result_warranty_model)){
                                                $model_name = $result_warranty_model->product_model;
                                            }
                                            ?>
                                            <div class="feed-element">
                                                <div>
                                                    <strong><?php echo e($value->role); ?></strong> - <?php echo e($name); ?>

                                                    <strong class="pull-right" style="margin-right: 15px;">
                                                        <a href="<?php echo e(URL::to('staff/warranty')); ?>"><?php echo e($model_name); ?></a>
                                                    </strong>
                                                    <div><?php echo e($value->note); ?></div>
                                                    <small class="text-muted"><?php echo e($time); ?> - <?php echo e($date); ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="feed-element">
                                            <div>
                                                <div>Not Any Warranty Note</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <?php echo $__env->make('staff.task.taskCalenderData', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            </div>
        </div>

	<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
	<?php echo $__env->make('staff.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script src="<?php echo e(asset('assets/js/plugins/fullcalendar/moment.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.tooltip.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.spline.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.resize.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.pie.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.symbol.js')); ?>"></script>
    <script  type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.time.js')); ?>"></script>

    <!-- Full Calendar -->
    <script src="<?php echo e(asset('assets/js/plugins/fullcalendar/fullcalendar.min.js')); ?>"></script>

    <?php echo $__env->make('staff.task.taskCalenderScript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <script type="text/javascript">
        $(function ()
        {
            $('#dashboard_warranty_note').slimScroll({
                height: '300px'
            });

            $('#oder_message > .feed-activity-list').slimScroll({
                height: '300px'
            });

            $('#staff_task_list_assign').slimScroll({
                height: '620px'
            });

        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('staff.layouts.masterstaff', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>