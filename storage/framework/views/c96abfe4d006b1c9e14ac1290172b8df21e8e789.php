<?php $__env->startSection('pagecss'); ?>
 <link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
 <link href="<?php echo e(asset('assets/css/plugins/switchery/switchery.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Add Customer</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/admin/customerlist')); ?>">All Customer</a>
                    </li>
                    <li class="active">
                        <strong>All Customer list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Add Customer</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="customer_add" name="customer_add" method="POST" action="<?php echo e(action('admin\ManageCustomerController@saveData')); ?>" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">
                                <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">First name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="jack">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Last name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="rich">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email Address</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="emailID" name="emailID" placeholder="jack@mail.com">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="******">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Confirm Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="txt_confirmpassword" name="txt_confirmpassword" placeholder="******">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								
								<div class="form-group">
                                    <label class="col-sm-2 control-label">Phone</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="phone">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Address</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="address" name="address" placeholder="address" ></textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">City</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">State</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="state" name="state" placeholder="State">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Country</label>
                                    <div class="col-sm-10">
                                        <select data-placeholder="Choose a Country..." class="chosen-select"  style="width: 100%;" tabindex="2" name="country" id="country">
                                            <option value="">Select</option>
                                            <?php
                                            foreach($country as $key=> $value)
                                            { ?>
                                            <option value="<?php echo e($key); ?>" >
                                                <?php echo e($value); ?>

                                            </option>
                                            <?php  } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Active</label>
                                    <div class="col-sm-10">
                                        <input type="checkbox" class="js-switch_2" name="status" id="status"  value="0" />
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="submit" class="btn btn-primary" value="Add"/>
                                        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo e($customer_id); ?>"/>
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

            <script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>

            <!-- Switchery -->
            <script src="<?php echo e(asset('assets/js/plugins/switchery/switchery.js')); ?>"></script>

            <script type="text/javascript">
                $(function ()
                {

                    var status = document.querySelector('#status');
                    var switchery_2 = new Switchery(status, { color: '#1AB394' });

                    status.onchange = function() {
                        if((status.checked == 'true') || (status.checked == true)){
                            $('#status').val(1);
                        }else{
                            $('#status').val(0);
                        }
                    };


                    $("#groupID").select2({
                        placeholder: "Select a state",
                        allowClear: true
                    });

                    var config = {
                        '.chosen-select'           : {},
                        '.chosen-select-deselect'  : {allow_single_deselect:true},
                        '.chosen-select-no-single' : {disable_search_threshold:10},
                        '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                        '.chosen-select-width'     : {width:"95%"}
                    }
                    for (var selector in config) {
                        $(selector).chosen(config[selector]);
                    }

                    <?php  if(isset($categoryID) && !empty($categoryID))
                    {
                        foreach($categoryID as $key => $value){
                        ?>
                        console.log(<?php echo $value;?>);
                        /*$('.chosen-select').val(<?php echo $value;?>);*/
                        $('.chosen-select').val(<?php echo $value;?>).end().trigger('chosen:updated');
                    <?php } } ?>



                    $('#btn_reset').click(function ()
                    {
                        $('input[type="text"]').each(function(){
                            $(this).val('');
                        });

                        $('input[type="email"]').each(function(){
                            $(this).val('');
                        });

                        $('input[type="password"]').each(function(){
                            $(this).val('');
                        });
                        window.location.reload();
                    });

                    $('#customer_add').formValidation(
                        {
                            framework: 'bootstrap',
                            excluded: ':disabled',
                            message: 'This value is not valid',
                            icon: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                                 first_name: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Customer First Name !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 30,
                                            message: 'The Field must be more than 3 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-z\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                last_name: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Customer Last Name !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 30,
                                            message: 'The Field must be more than 3 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-z\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                emailID: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Email Address !'
                                        },
                                        emailAddress: {
                                            message: 'Enter Valid Email Address !'
                                        }
                                    }
                                },
                                password: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Password'
                                        },
                                        different: {
                                            field: 'emailID',
                                            message: 'The password cannot be the same as username'
                                        },
                                        stringLength: {
                                            min: 6,
                                            max: 12,
                                            message: 'The password must be more than 6 characters long'
                                        }
                                    }
                                },
                                txt_confirmpassword: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Confirm Password'
                                        },
                                        different: {
                                            field: 'emailID',
                                            message: 'The password cannot be the same as username'
                                        },
                                        stringLength: {
                                            min: 6,
                                            max: 12,
                                            message: 'The password must be more than 6 characters long'
                                        },
                                        identical: {
                                            field: 'password',
                                            message: 'Password Does not Match !'
                                        }
                                    }
                                },
                                address: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Address !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 200,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                },
								phone: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Phone !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 12,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                },
                                city: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter City !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 100,
                                            message: 'The Field must be more than 3 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-z\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                state: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter State !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 100,
                                            message: 'The Field must be more than 3 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-z\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                country: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Select Country !'
                                        }
                                    }
                                }
                            }
                        });
                });
            </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin/layouts/masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>