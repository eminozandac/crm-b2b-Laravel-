

<?php $__env->startSection('pagecss'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Edit Lead Report</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/dealer')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/dealer/leadsreport')); ?>">All Leads Report</a>
                    </li>
                    <li class="active">
                        <strong>All Leads Report list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Edit Leads Reports</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="leadreport_edit" name="leadreport_edit" method="POST" action="<?php echo e(action('dealer\DealerLeadsController@editData')); ?>" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">

                                <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>
                                <input name="id" type="hidden" id="id" value="<?php echo e($leads->id); ?>"/>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Title</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="title" name="title" placeholder="jack" value="<?php echo e($leads->title); ?>">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Customer Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="rich" value="<?php echo e($leads->name); ?>">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								
								<div class="form-group">
                                    <label class="col-sm-2 control-label">Status</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="lead_status"  id="lead_status" >
											<option value="">Select Status</option>
											<option <?php if($leads->lead_status=='Open – Not contacted'){echo 'selected=selected';} ?> value="Open – Not contacted">Open – Not contacted</option>
											<option <?php if($leads->lead_status=='Open – Attempted Contact'){echo 'selected=selected';} ?> value="Open – Attempted Contact">Open – Attempted Contact</option>
											<option <?php if($leads->lead_status=='Open – Contacted'){echo 'selected=selected';} ?> value="Open – Contacted">Open – Attempted Contact</option>
											<option <?php if($leads->lead_status=='Closed – Sale'){echo 'selected=selected';} ?> value="Closed – Sale">Closed – Sale</option>
											<option <?php if($leads->lead_status=='Closed – Not Interested'){echo 'selected=selected';} ?> value="Closed – Not Interested">Closed – Not Interested</option>
											 
										</select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email Address</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="emailID" name="emailID" placeholder="jack@mail.com" value="<?php echo e($leads->emailID); ?>">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Phone</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="098756423" value="<?php echo e($leads->phone); ?>">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Description</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="description" name="description" rows="8"><?php echo e($leads->description); ?></textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="submit" class="btn btn-primary" value="Update"/>
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
            <?php echo $__env->make('dealer.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <script type="text/javascript">
                $(function ()
                {
                    $('#btn_reset').click(function ()
                    {
                        window.location.reload();
                    });

                    $('#leadreport_edit').formValidation({
                        framework: 'bootstrap',
                        excluded: ':disabled',
                        message: 'This value is not valid',
                        icon: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: 'Enter Title'
                                    },
                                    stringLength: {
                                        min: 3,
                                        max: 30,
                                        message: 'The Field must be more than 3 characters long'
                                    }
                                }
                            },
                            name: {
                                validators: {
                                    notEmpty: {
                                        message: 'Enter Customer name !'
                                    },
                                    stringLength: {
                                        min: 3,
                                        max: 30,
                                        message: 'The Field must be more than 3 characters long'
                                    }
                                }
                            },
                            emailID: {
                                validators: {
                                    
                                    emailAddress: {
                                        message: 'Enter Valid Email Address !'
                                    }
                                }
                            },
                            phone: {
                                validators: {
                                    
                                    stringLength: {
                                        min: 6,
                                        max: 100,
                                        message: 'The Field must be more than 6 characters long'
                                    },
                                    regexp: {
                                        regexp: /^[0-9\s]+$/i,
                                        message: 'This Field can consist of numeric value'
                                    }
                                }
                            },
                            description: {
                                validators: {
                                    notEmpty: {
                                        message: 'Enter Description !'
                                    },
                                    stringLength: {
                                        min: 3,
                                        max: 200,
                                        message: 'The Field must be more than 3 characters long'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('dealer.layouts.masterdealer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>