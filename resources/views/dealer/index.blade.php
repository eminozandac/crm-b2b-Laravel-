<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Inventory CRM | Login</title>
		<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
		<link href="{{asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/animate.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
	</head>
	<body class="gray-bg">
		<div class="middle-box text-center loginscreen animated fadeInDown">
			<div class="col-md-12">
				<div>
					<img src="{{ asset('assets/img/superiorspas.png') }}" class="img-responsive"/><br/>
				</div>
				<h3>Welcome to Inventory</h3>
				<?php echo  Session::get('login'); ?>
				<form class="m-t form-horizontal" role="form" method="post" action="{{action('admin\LoginController@loginform')}}" id="formLogin" >
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<div class="form-group">
						<input type="email" class="form-control" name="email" id="email" placeholder="Email" required="">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="password" id="password" placeholder="Password" required="">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">Login</button>
					<a href="#" id="forgotPassword"><small>Forgot password?</small></a>
				</form>
				<form class="m-t form-horizontal" role="form" method="post" action="{{action('admin\LoginController@forgotPasswordAdmin')}}" id="formForgot" style="display:none;">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
					<div class="form-group">
						<input type="email" class="form-control" name="email" id="email" placeholder="Email" required="">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">Recover Password</button>
					<a href="#" id="knowPassword"><small>Do you know your password?</small></a>
				</form>
				<p class="m-t"> <small>&copy; 2016 Superior Spas</small> </p>
			</div>
		</div>
		<!-- Mainly scripts -->
		<script src="{{asset('assets/js/jquery-2.1.1.js')}}"></script>
		<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
		<script src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
		<script src="{{asset('assets/js/formValidation.js')}}"></script>
		<script src="{{asset('assets/js/framework/bootstrap.js')}}"></script>
		<script src="{{asset('assets/js/plugins/toastr/toastr.min.js')}}"></script>
		  <script>
			$(document).ready(function() {
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