
<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentPages'); ?>

			<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Checkout</h2>
                    <ol class="breadcrumb">
                       <li>
							<a href="<?php echo e(URL::to('/dealer')); ?>">Home</a>
						</li>
                        <li>
							<a href="<?php echo e(URL::to('/dealer/accessorylist')); ?>">Main Product</a>
						</li>
                        <li class="active">
                            <strong>Checkout</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
			<div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
			<form class="m-t form-horizontal"  role="form" method="post" action="<?php echo e(action('dealer\CartController@placeOrder')); ?>" id="customer_billing" >
                <div class="col-md-9">
					<div class="ibox">
					 
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                        <div class="ibox-title">
                            
                            <h5>Your Order Notes</h5>
                        </div>
                        <div class="ibox-content col-md-12">
							
							<!--<div class="form-group">
								<label class="col-sm-3 control-label">Customer Name</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="order_notes_title" id="order_notes_title" placeholder="Customer Name">
								</div>
							</div>-->
							<div class="form-group">
								<label class="col-sm-3 control-label">Note</label>
								<div class="col-sm-9">
									<textarea  class="form-control" name="order_notes" id="order_notes_descriptions" placeholder="Notes"></textarea>
								</div>
							</div>
                        </div>
						 
                        <div class="ibox-content">
                            <a href="<?php echo e(URL::to('/dealer/product')); ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Continue shopping</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Cart Summary</h5>
                        </div>
                        <div class="ibox-content">
                            <span>
                                Total
                            </span>
                            <h2 class="font-bold">
                                $<?php echo e(Cart::total()); ?>

                            </h2>

                            <hr/>
                            <span class="text-muted small">
                                
                            </span>
                            <div class="m-t-sm">
                                <div class="btn-group">
                                <input type="submit" class="btn btn-primary btn-sm" value="Place Order"> 
                                <a href="<?php echo e(URL::to('/dealer/product')); ?>" class="btn btn-white btn-sm"> Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				</form>
            </div>
        </div>
	<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagescript'); ?>
	<?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	     <script src="<?php echo e(asset('assets/js/plugins/slick/slick.min.js')); ?>"></script>
	  <script src="<?php echo e(asset('assets/js/bootstrap-select.js')); ?>"></script>
	   <!-- TouchSpin -->
    <script src="<?php echo e(asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')); ?>"></script>
	<script  src="<?php echo e(asset('assets/js/plugins/iCheck/icheck.min.js')); ?>"></script>
	<script type="text/javascript">
	$(function() {
        $('.product-images').slick({
            dots: true
        });
		$(".touchspin1").TouchSpin({
                buttondown_class: 'btn btn-white',
                buttonup_class: 'btn btn-white',
				min: 1
        });
		 
                function loadSelectbox()
                {
                    $(".country").select2({
                        placeholder: "Select a Country",
                        allowClear: true
                    });

                    if($('input[name="chk_shipping"]:checked').val() == 1){
                        $('.shipping_div').css({'display':'block'});
                    }else{
                        $('.shipping_div').css({'display':'none'});
                    }
                }
                loadSelectbox();

                $('.nav-tabs > li > a').click(function (){
                    loadSelectbox();
                });

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                }).on('ifToggled', function() {

                    if($('input[name="chk_shipping"]:checked').val() == 1){
                         $('.shipping_div').css({'display':'block'});
                    }else{
                        $('.shipping_div').css({'display':'none'});
                    }

                });
                $('#form_updatepassword').formValidation({
                    framework: 'bootstrap',
                    excluded: ':disabled',
                    message: 'This value is not valid',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        opassword: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Password'
                                },
                                different: {
                                    field: 'emailID',
                                    message: 'The password cannot be the same as Email Address'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 12,
                                    message: 'The password must be more than 6 characters long'
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
                                    message: 'The password cannot be the same as Email Address'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 12,
                                    message: 'The password must be more than 6 characters long'
                                }
                            }
                        },
                        cpassword: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Confirm Password'
                                },
                                different: {
                                    field: 'emailID',
                                    message: 'The password cannot be the same as Email Address'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 12,
                                    message: 'The password must be more than 6 characters long'
                                }
                            }
                        }
                    }
                });


                $('#customer_billing').formValidation({
                    framework: 'bootstrap',
                    excluded: ':disabled',
                    message: 'This value is not valid',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        order_notes_title: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Customer Name !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 30,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                 
                            }
                        }, 
						order_notes_descriptions: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Order notes Description !'
                                },
                                stringLength: {
                                    min: 3,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                 
                            }
                        }, 
						billing_firstname: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Dealer First Name !'
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
                        billing_lastname: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Dealer Last Name !'
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
                        billing_emailID: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Email Address !'
                                },
                                emailAddress: {
                                    message: 'Enter Valid Email Address !'
                                }
                            }
                        },
                        billing_address: {
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
                        billing_city: {
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
                        billing_state: {
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
                        billing_zipcode: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Zip Code !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 100,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                 
                            }
                        },
                        billing_country: {
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
<?php echo $__env->make('dealer.layouts.masterdealer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>