
<?php $__env->startSection('contentPages'); ?>


    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>New Warranty Note</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="feed-activity-list" id="dashboard_warranty_note">
                            <?php
                            /*$result_warranty_note = DB::table('warrantyproduct_note')->select('*')
                                    ->where('role','!=','customer')
                                    ->orderBy('id','DESC')->get();*/

                            $result_warranty_note = DB::table('warrantyproduct_note')
                                ->join('warrantyproduct', 'warrantyproduct_note.warranty_uniqueID', '=', 'warrantyproduct.warranty_uniqueID')
                                ->select('warrantyproduct_note.*')
                                ->where('warrantyproduct.user_id','=',$sessionData['customerID'])
                                ->where('warrantyproduct.role','=','customer')
                                ->where('warrantyproduct_note.role','!=','customer')
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
                                                <a href="<?php echo e(URL::to('customer/warranty')); ?>"><?php echo e($model_name); ?></a>
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
    <?php echo $__env->make('customer.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script>
        $(document).ready(function()
        {
            $('#dashboard_warranty_note').slimScroll({
                height: '300px'
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('customer.layouts.mastercustomer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>