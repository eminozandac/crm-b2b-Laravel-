<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Inventory CRM | Login - Customer</title>
		<link href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>" rel="stylesheet">
		<link href="<?php echo e(asset('assets/font-awesome/css/font-awesome.css')); ?>" rel="stylesheet">
		<link href="<?php echo e(asset('assets/css/animate.css')); ?>" rel="stylesheet">
		<link href="<?php echo e(asset('assets/css/style.css')); ?>" rel="stylesheet">
		<link href="<?php echo e(asset('assets/css/plugins/toastr/toastr.min.css')); ?>" rel="stylesheet">
	</head>
	<body class="gray-bg">
		<div class="middle-box text-center loginscreen animated fadeInDown">
			<div class="col-md-12">
				<div>
					<h1 class="logo-name text-center">CRM</h1>
				</div>
				<h3>Welcome to Inventory</h3>
				<form class="m-t form-horizontal" role="form" method="post" action="<?php echo e(action('customer\CustomerController@loginform')); ?>" id="formLogin" >
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<div class="form-group">
						<input type="text" class="form-control" name="emailID" id="emailID" placeholder="Email/Phone" required="">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="password" id="password" placeholder="Password" required="">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">Login</button>
					<a href="#" id="forgotPassword"><small>Forgot password?</small></a>
				</form>
				<form class="m-t form-horizontal" role="form" method="post" action="<?php echo e(action('customer\CustomerController@forgotPasswordCustomer')); ?>" id="formForgot" style="display:none;">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<div class="form-group">
						<input type="email" class="form-control" name="emailID" id="emailID" placeholder="Email" required="">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">Recover Password</button>
					<a href="#" id="knowPassword"><small>Do you know your password?</small></a>
				</form>
                <p class="text-muted text-center"><small>Do not have an account?</small></p>

                <a class="btn btn-sm btn-white btn-block" href="<?php echo e(URL::to('/customer/register')); ?>">Create an account</a>
				<p class="m-t"> <small>&copy; 2016 Superior Spas</small> </p>
			</div>
		</div>
		<!-- Mainly scripts -->
		<script src="<?php echo e(asset('assets/js/jquery-2.1.1.js')); ?>"></script>
		<script src="<?php echo e(asset('assets/js/bootstrap.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/formValidation.js')); ?>"></script>
		<script src="<?php echo e(asset('assets/js/framework/bootstrap.js')); ?>"></script>
		<script src="<?php echo e(asset('assets/js/plugins/toastr/toastr.min.js')); ?>"></script>
		  <script>
			$(document).ready(function()
            {
                $('.form-horizontal').formValidation({
                    message: 'This value is not valid',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        emailID: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Email Address/Phone Number !'
                                },
                            }
                        },
                        password: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Password !'
                                },
                                different: {
                                    field: 'emailID',
                                    message: 'The password cannot be the same as username'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 30,
                                    message: 'The password must be more than 6 characters long'
                                }
                            }
                        }
                    }
                });

				$('#forgotPassword').click(function(){
					$('#formLogin').hide('slide');
					$('#formForgot').show('slide');
				});
				$('#knowPassword').click(function(){
					$('#formLogin').show('slide');
					$('#formForgot').hide('slide');
				});

                <?php
                if(Session::get('operationSucess')){
                    ?>
                    toastr.options = {closeButton:true}
                     toastr.success('<?php echo Session::get('operationSucess'); ?>')
                <?php
                }
                if(Session::get('operationFaild')){
                ?>
                    toastr.options = {closeButton:true}
                     toastr.error('<?php echo Session::get('operationFaild'); ?>')
                <?php }?>

			});
		</script>
	</body>
</html>