

<?php $__env->startSection('pagecss'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Add Group Name</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/admin/grouplist')); ?>">All Group List</a>
                    </li>
                    <li class="active">
                        <strong>All Group list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Add Group <small>Group name</small></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="group_add" name="group_add" method="POST" action="<?php echo e(action('admin\GroupController@saveData')); ?>" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">
                                <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>
                                <input name="country" type="hidden" id="country" value="en_US"/>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Group name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="jack" value="<?php echo e($name); ?>">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								<div class="form-group">
                                    <label class="col-sm-2 control-label">Group Discount</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="discount" name="discount" placeholder="Discount" value="<?php echo e($discount); ?>">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="submit" class="btn btn-primary" value="Add"/>
                                        <button class="btn btn-white" type="button" id="btn_reset">Reset</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php $__env->stopSection(); ?>

        <?php $__env->startSection('pagescript'); ?>
            <?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


            <script type="text/javascript">
                $(function ()
                {

                    $('#btn_reset').click(function ()
                    {
                        $('input[type="text"]').each(function(){
                            $(this).val('');
                        });

                        window.location.reload();
                    });

                    $('#group_add').formValidation({
                        framework: 'bootstrap',
                        excluded: ':disabled',
                        message: 'This value is not valid',
                        icon: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            name: {
                                validators: {
                                    notEmpty: {
                                        message: 'Enter Group Name'
                                    },
                                    stringLength: {
                                        min: 3,
                                        max: 100,
                                        message: 'The Field must be more than 3 characters long'
                                    } 
                                }
                            },
							discount: {
								validators: {
									notEmpty: {
                                        message: 'Enter Group Discount'
                                    },
									numeric: {
										message: 'Enter Proper discount',
										// The default separators
										decimalSeparator: '.'
									}
								}
							}
                        }
                    });
                });
            </script>
			
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin/layouts/masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>