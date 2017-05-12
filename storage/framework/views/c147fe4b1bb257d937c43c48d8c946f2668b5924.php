

<?php $__env->startSection('pagecss'); ?>
    <style>
        .my_col{
            width: 20%;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>


    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-success pull-right"></span>
                        <h5><a href="<?php echo e(URL::to('/admin/productList')); ?>">Products</a></h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <?php
                            $produtsCount = DB:: table('products')->where('deleted_at', '=', NULL)->count();
                            echo $produtsCount;
                            ?>
                        </h1>

                        <div class="stat-percent font-bold text-success"></div>
                        <small>Total Products</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-info pull-right"></span>
                        <h5><a href="<?php echo e(URL::to('/admin/orderList')); ?>">Orders</a></h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php
                            $orderCount = DB:: table('order_transaction')->where('deleted_at', '=', NULL)->count();
                            echo $orderCount;
                            ?></h1>

                        <div class="stat-percent font-bold text-info"></div>
                        <small>New orders</small>
                    </div>
                </div>
            </div>
			<div class="col-lg-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-info pull-right"></span>
                        <h5><a href="<?php echo e(URL::to('/admin/orderList')); ?>">Special Orders</a></h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins"><?php
                            $special_order = DB:: table('special_order')->where('deleted_at', '=', NULL)->count();
                            echo $special_order;
                            ?></h1>

                        <div class="stat-percent font-bold text-info"></div>
                        <small>Total Special orders</small>
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
                        <span class="label label-primary pull-right"></span>
                        <h5><a href="<?php echo e(URL::to('/admin/productCategoriesList')); ?>">Categories</a></h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <?php
                            $categoriesCount = DB:: table('category')->count();
                            echo $categoriesCount;
                            ?>
                        </h1>

                        <div class="stat-percent font-bold text-navy"></div>
                        <small>Total categories</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-success pull-right"></span>
                        <h5><a href="<?php echo e(URL::to('/admin/dealerlist')); ?>">Dealers</a></h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <?php
                            $costomerCount = DB:: table('dealer')->where('deleted_at', '=', NULL)->count();
                            echo $costomerCount;
                            ?>
                        </h1>

                        <div class="stat-percent font-bold text-success"></div>
                        <small>Total Dealer</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-success pull-right"></span>
                        <h5><a href="<?php echo e(URL::to('/admin/warranty')); ?>">Warranty</a></h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <?php
                            $warrantynewclaimCount = DB:: table('warrantyproduct')->where('warranty_status', '=', 'new_claim')->where('deleted_at', '=', NULL)->count();
                            echo $warrantynewclaimCount;
                            ?>
                        </h1>

                        <div class="stat-percent font-bold text-success"></div>
                        <small>New Warranty Claim</small>
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
                    <div class="ibox-content" id="oder_message" style="    max-height: 335px; overflow-x: scroll;">
                        <div class="feed-activity-list">
                            <?php
                            $note = DB::table("admin_order_notes")->where('orderID','!=','0')->where('deleted_at','=',NULL)->where('role', '!=', 'admin')->where('role', '!=', 'staff')->orderBy('admin_order_notesID', 'DESC')->get();
                           
							?>
                            <?php if(isset($note) && (!empty($note))): ?>

                                <?php foreach($note as $key_note => $value_note): ?>

                                    <?php
                                    $today = date('Y-m-d H:i:s');
                                    $a = new DateTime($today);
                                    $b = new DateTime($value_note->created_at);
                                    $difference_time = $a->diff($b);

                                    $time_text = '';
                                    if (($difference_time->format("%d") != 0)) {
                                        $time_text .= $difference_time->format("%d") . 'days';
                                        $time_text .= ' ';

                                    } else if (($difference_time->format("%d") == 0) && ($difference_time->format("%h") != 0)) {
                                        $time_text .= $difference_time->format("%h") . 'h';
                                        $time_text .= ' ';
                                    }
                                    $time_text .= $difference_time->format("%i") . 'm ago';

                                    $result_product = DB::table('products')->select('productName')->where('product_id', '=', $value_note->product_id)->first();
                                    $dealerCompany=DB::table('dealer')->where('id','=',$value_note->dealerID)->first();
									$getOrderTranzDetail=DB::table('order_transaction')->where('orderID','=',$value_note->orderID)->where('deleted_at','=',NULL)->first();	
									?>

                                    <div class="feed-element">
                                        <div>
                                            <small class="pull-right text-navy"><?php echo e($time_text); ?></small>
                                            <strong class="label label-primary"  style="margin-bottom: 10px;"><?php echo e($dealerCompany->company_name); ?> (<?php echo $dealerCompany->role; ?>) </strong><br/>
                                            Product : <strong  style="margin-bottom: 10px;"><?php echo e($result_product->productName); ?></strong><br/>

                                            <div style="margin-bottom: 10px;"> Description
                                                : <?php echo e(\Illuminate\Support\Str::limit($value_note->description, $limit = 100, $end = '...')); ?></div>
                                            <small class="text-muted">
                                                Time : <?php echo e(date('H:i A',strtotime($value_note->created_at))); ?>

                                                - <?php echo e(date('d-m-Y',strtotime($value_note->created_at))); ?>

                                            </small>

                                            <?php
                                            $action = URL::to('/admin/addordernotes/');
											//echo $value_note->orderTokenString;
                                            $getTran = DB::table('order_transaction')->where('orderNoteTokenString', '=',$value_note->orderTokenString)->where('deleted_at','=',NULL)->first();
                                           
                                            // print_r($getTran); exit;
											  $dealerCompany=DB::table('dealer')->where('id','=',$getTran->dealerID)->first();
                                                $productName = DB::table('products')->where('product_id', '=', $getTran->product_id)->where('deleted_at','=',NULL)->first();
                                                $categoryName = DB::table('category')->where('id', '=', $productName->category_id)->where('deleted_at','=',NULL)->first();
                                                $barndName = DB::table('brand')->where('id', '=', $productName->brand_id)->where('deleted_at','=',NULL)->first();
                                            ?>

                                           <a href="javascript:void(0)" data-toggle="modal" data-target="#notesssss<?php echo $value_note->orderTokenString; ?>"
                                               title="add notes" class="btn btn-xs btn btn-primary"
                                               style="float: right;">Show Message</a>

                                            <div class="modal inmodal" id="notesssss<?php echo $value_note->orderTokenString; ?>" tabindex="-1" role="dialog"
                                                 aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content animated fadeIn">

                                                        <form action="<?php echo $action; ?>" method="POST"
                                                              enctype="multipart/form-data" class="products"
                                                              id="noteform<?php echo $value_note->orderTokenString; ?>">
                                                            <input type="hidden" name="_token"
                                                                   value="<?php echo csrf_token();?>"/>
                                                            <input type="hidden" name="productToken"
                                                                   value="<?php echo base64_encode($getTran->product_id);?>"/>
                                                            <?php
                                                            $sender = "";

                                                            $sessionData = Session::get('adminLog');

                                                            $sender = 'admin';

                                                            //echo $sender;
                                                            ?>
                                                            <input type="hidden" name="sender"   value="<?php echo $sender; ?>"/>
                                                            <input type="hidden" name="sendertype"  value="<?php echo $sessionData['role']; ?>"/>
                                                            <input type="hidden" name="dealerID"  value="<?php echo $getTran->dealerID; ?>"/>
                                                            <input type="hidden" name="opentab" value="pending"/>
                                                            <input type="hidden" name="ordertype"  value="admindashboard"/>
                                                            <input type="hidden" name="orderToken" value="<?php echo base64_encode($getTran->orderID); ?>"/>
                                                            <input type="hidden" name="orderTokenString"   value="<?php echo $value_note->orderTokenString; ?>"/>

                                                            <div class="modal-header">
                                                                <button type="button" class="close"  data-dismiss="modal">
                                                                    <span  aria-hidden="true">&times;</span>
                                                                    <span class="sr-only">Close</span>
                                                                </button>
                                                                <h4 class="modal-title"><?php echo $productName->productName; ?></h4>
                                                            </div>
                                                            <div class="modal-body col-md-12"   style=" max-height: 350px;overflow-y: scroll;">
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
                                                                        <textarea style="width:100%" required
                                                                                  name="description"
                                                                                  class="form-control"
                                                                                  placehoder="Notes"></textarea></div>
                                                                    <div class="form-group" style="width: 100%;">
                                                                        <label class="control-label">Send
                                                                            Mail:</label><br/>
                                                                        <input type="checkbox" name="sendMail"
                                                                               value="1"></div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <hr/>
                                                                <?php
                                                                $notes = DB::table('admin_order_notes')->where('deleted_at','=',NULL)->where('orderID', '=', $getTran->orderID)->where('product_id', '=', $getTran->product_id)->orderBy('admin_order_notesID', 'desc')->get();
                                                                foreach ($notes as $note) {
                                                                    if (!empty($note->name) || !empty($note->description)) {
                                                                        $today = date('Y-m-d H:i:s');
                                                                        $a = new DateTime($today);
                                                                        $b = new DateTime($note->created_at);
                                                                        $difference_time = $a->diff($b);

                                                                        $time_text = '';
                                                                        if (($difference_time->format("%d") != 0)) {
                                                                            $time_text .= $difference_time->format("%d") . 'days';
                                                                            $time_text .= ' ';

                                                                        } else if (($difference_time->format("%d") == 0) && ($difference_time->format("%h") != 0)) {
                                                                            $time_text .= $difference_time->format("%h") . 'h';
                                                                            $time_text .= ' ';
                                                                        }
                                                                        $time_text .= $difference_time->format("%i") . 'm ago';

                                                                        $sender = '<small class="label label-success">' . $dealerCompany->company_name .' ('.$dealerCompany->role.')'. '</small>';
                                                                        echo '<h3>' . $sender . '<small class="pull-right text-navy">' . $time_text . '</small></h3>';
                                                                        echo '<p>' . $note->description . '</p>
                                                                        <small class="text-muted">
                                                                        Time : ' . date('H:i A', strtotime($note->created_at)) . ' - ' . date('Y-m-d', strtotime($note->created_at)) . '
                                                                        </small>
                                                                        <hr/>';

                                                                    } else {
                                                                        $notestitle = 'No Notes Available';
                                                                        $datanotes = '<i class="fa fa-frown-o" aria-hidden="true" style="font-size:85px;color:#ccc;"></i>';
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-white"
                                                                        data-dismiss="modal">Close
                                                                </button>
                                                                <input type="submit" data-formid="noteform<?php echo $value_note->orderTokenString; ?>" class="btn btn-primary notebtn"  value="Save changes"/>
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
                                    ->where('role', '!=', 'admin')
                                    ->orderBy('id', 'DESC')->get();
                            ?>
                            <?php if(!empty($result_warranty_note)): ?>
                                <?php foreach($result_warranty_note as $key => $value): ?>
                                    <?php
                                    $date = date('d-m-Y', strtotime($value->created_at));
                                    $time = date('h:i A', strtotime($value->created_at));

                                    $name = '';
                                    if ($value->role == 'admin') {
                                        $result_name = DB::table('admin')->select('name')->first();
                                        if (!empty($result_name)) {
                                            $name = $result_name->name;
                                        }
                                    } else {
                                        $result_name = DB::table($value->role)
                                                ->select('first_name', 'last_name')
                                                ->where('id', '=', $value->user_id)
                                                ->first();
                                        if (!empty($result_name)) {
                                            $name = $result_name->first_name . ' ' . $result_name->last_name;
                                        }
                                    }

                                    $model_name = '';
                                    $result_warranty_model = DB::table('warrantyproduct')->select('*')->where('warranty_uniqueID', '=', $value->warranty_uniqueID)->first();
                                    if (!empty($result_warranty_model)) {
                                        $model_name = $result_warranty_model->product_model;
                                    }
                                    ?>
                                    <div class="feed-element">
                                        <div>
                                            <strong><?php echo e($value->role); ?></strong> - <?php echo e($name); ?>

                                            <strong class="pull-right" style="margin-right: 15px;">
                                                <a href="<?php echo e(URL::to('admin/warranty')); ?>"><?php echo e($model_name); ?></a>
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
    </div>

    <div class="footer">
        <div class="pull-right">

        </div>
        <div>
            <strong>Copyright</strong> Superior Spas &copy; <?php echo date('Y'); ?>
        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
    <?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.tooltip.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.spline.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.resize.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.pie.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.symbol.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.time.js')); ?>"></script>
    <script type="text/javascript">
        new_showOrderMsg = null;
        new_divscroll = null;
        $(document).ready(function () {
            $('#dashboard_warranty_note').slimScroll({
                height: '300px'
            });

            $('#oder_message').slimScroll({
                height: '335px'
            });


            function newshowOrderMsg(valueID) {
                var produt = '#hidden_oder_msg_product_' + valueID;
                var name = '#hidden_oder_msg_name_' + valueID;
                var description = '#hidden_oder_msg_description_' + valueID;

                $('#oder_msg_product, #oder_msg_name, #oder_msg_description').text('');

                $('#oder_msg_product').text($(produt).val());
                $('#oder_msg_name').text($(name).val());
                $('#oder_msg_description').text($(description).val());

                $('#order_msg_model').trigger('click');
            }

            new_showOrderMsg = newshowOrderMsg;

            function divscrolle() {
                setTimeout(function () {
                    $('html,body').scrollTop(0);
                }, 1100);
            }

            new_divscroll = divscrolle;
        });

        function showOrderMsg(valueID) {

            if (valueID != 0) {
                new_showOrderMsg(valueID);
            }
        }

        function divscroll() {
            new_divscroll();
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>