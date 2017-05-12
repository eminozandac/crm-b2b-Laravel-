<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory CRM | Customer </title>
    <link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/fullcalendar/fullcalendar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/fullcalendar/fullcalendar.print.css') }}" rel='stylesheet' media='print'>
	<link href="{{ asset('assets/css/animate.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/style.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/footable/footable.core.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/slick/slick.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/slick/slick-theme.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/summernote/summernote.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/summernote/summernote-bs3.css')}}" rel="stylesheet">
	<link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet">
    @yield('pagecss')
</head>
<body>
    <div id="wrapper">
		<nav class="navbar-default navbar-static-side" role="navigation">
			@include('customer.includes.sidebarmenu')
	    </nav>
		<div id="page-wrapper" class="gray-bg">
			<div class="row border-bottom">
				<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
					<div class="navbar-header">
						<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
					</div>

					<ul class="nav navbar-top-links navbar-right">
                        <li>
							<a class="count-info" title="User Profile" href="{{action('customer\CustomerController@profile')}}">
								<i class="fa fa-user" aria-hidden="true"></i>
							</a>
						</li>
						<li>
							<a title="Logout" href="{{action('customer\CustomerController@logout')}}">
								<i class="fa fa-sign-out"></i> Log out
							</a>
						</li>
					</ul>
				</nav>
				@yield('contentPages')
			</div>
		</div>
    </div>
   @yield('pagescript')
 
   
</body>
</html>
