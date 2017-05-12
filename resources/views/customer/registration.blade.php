<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory CRM | Registration - Customer</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>
        <div>

            <h1 class="logo-name">CRM</h1>

        </div>
        <h3>Register to CRM</h3>
        <p>Create account to see it in action.</p>
        <form class="m-t" role="form" name="form_registration" id="form_registration" method="post" action="{{ action('customer\CustomerController@registerData') }}">

            <div class="form-group">
                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First name"  required="">
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last name" required="">
            </div>

            <div class="form-group">
                <input type="email" class="form-control" name="emailID" id="emailID" placeholder="Email" required="">
            </div>

            <div class="form-group">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required="">
            </div>

            <div class="form-group">
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required="">
            </div>

            <div class="form-group">
                <div class="checkbox i-checkss"><label> <input name="chk_agree" id="chk_agree" type="checkbox" value="1"><i></i> Agree the terms and policy </label></div>
            </div>

            <button type="submit" class="btn btn-primary block full-width m-b">Register</button>

            <p class="text-muted text-center"><small>Already have an account?</small></p>
            <a class="btn btn-sm btn-white btn-block" href="{{ URL::to('/customer') }}">Login</a>
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
        </form>
        <p class="m-t"> <small>&copy; 2016 Superior Spas</small> </p>
    </div>
</div>

<!-- Mainly scripts -->
<script src="{{ asset('assets/js/jquery-2.1.1.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/formValidation.js') }}"></script>
<script src="{{ asset('assets/js/framework/bootstrap.js') }}"></script>
<script src="{{ asset('assets/js/plugins/toastr/toastr.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('assets/js/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    $(document).ready(function()
    {
		function icheckboxreload(){
			
			$('.i-checks').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green',
			});
		}

        $('#form_registration')
		.formValidation({
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
                            message: 'Please Enter First name'
                        },
                        stringLength: {
                            min: 3,
                            max: 30,
                            message: 'The text must be more than 3 characters long'
                        }
                    }
                },
                last_name: {
                    validators: {
                        notEmpty: {
                            message: 'Please Enter Last name'
                        },
                        stringLength: {
                            min: 3,
                            max: 30,
                            message: 'The text must be more than 3 characters long'
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
                },
                confirm_password: {
                    validators: {
                        notEmpty: {
                            message: 'Enter Password !'
                        },
                        identical: {
                            field: 'password',
                            message: 'Password Does not Match !'
                        }
                    }
                },
                chk_agree: {
                    validators: {
                        notEmpty: {
                            message: 'Please Select Term and Condition !'
                        }
                    }
                }
            }
        });


    <?php
    if(Session::get('operationSucess')){
        ?>
        toastr.options = {closeButton:true}
        toastr.success('<?php echo Session::get('operationSucess'); ?>')
    <?php
    }
    if(Session::get('operationFaild'))
    {
    ?>
        toastr.options = {closeButton:true}
        toastr.error('<?php echo Session::get('operationFaild'); ?>')
    <?php }?>
});
</script>
</body>
</html>
