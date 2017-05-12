


<?php $__env->startSection('pagecss'); ?>

    <style>
        a,a:hover, a:active, a:focus, a.class-refresh{
            outline: 0;
        }
        .fc-event{
            line-height: 1.4em;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
	<?php  $sessionData=Session::get('employeeLog');?>
            <div class="wrapper wrapper-content">
				<div class="row">

                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span class="label label-info pull-right"></span>
                                <h5>Today Task</h5>
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
                                <h5>Assign Warranty</h5>
                            </div>
                            <div class="ibox-content">
                                <h1 class="no-margins"><?php echo e($totalWarranty); ?></h1>
                                <div class="stat-percent font-bold text-info"> </div>
                                <small>Warranty : <?php echo e($totalWarranty); ?></small>
                            </div>
                        </div>
                    </div>

				</div>

                <?php echo $__env->make('employee.task.taskCalenderData', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>New Warranty Note</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="feed-activity-list" id="dashboard_warranty_note">
                                    <?php
                                    $result_warranty_note = DB::table('warrantyproduct_note')->select('*')
                                            ->where('role', '!=', 'employee')
                                            ->orderBy('id', 'DESC')->get();

                                    $result_warranty_note = DB::table('warrantyproduct_note')->join('warrantyproduct', 'warrantyproduct_note.warranty_uniqueID', '=', 'warrantyproduct.warranty_uniqueID')
                                            ->select('warrantyproduct_note.*')
                                            ->where('warrantyproduct_note.role', '!=', 'employee')
                                            ->where('warrantyproduct.warranty_assign', '=', $sessionData['unique_ID'])
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
                                                        <a href="<?php echo e(URL::to('employee/warranty')); ?>"><?php echo e($model_name); ?></a>
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
        </div>

	<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
    <script src="<?php echo e(asset('assets/js/plugins/fullcalendar/moment.min.js')); ?>"></script>
	<?php echo $__env->make('employee.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.tooltip.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.spline.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.resize.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.pie.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.symbol.js')); ?>"></script>
    <script  type="text/javascript" src="<?php echo e(asset('assets/js/plugins/flot/jquery.flot.time.js')); ?>"></script>

    <!-- Full Calendar -->
    <script src="<?php echo e(asset('assets/js/plugins/fullcalendar/fullcalendar.min.js')); ?>"></script>

    <?php echo $__env->make('employee.task.taskCalenderScript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <script type="text/javascript">
        $(function ()
        {
            $('#dashboard_warranty_note').slimScroll({
                height: '200px'
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('employee.layouts.masteremployee', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>