<?php
    $count_today_task = 0;
    $today_task = Session::get('dealerTodaytask');
    $count_today_task = count($today_task);

    $today_task_list_data = Session::get('dealerTodaytask_list');
?>
<!DOCTYPE html>
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Inventory CRM | Dealer </title>
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/fullcalendar/fullcalendar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/fullcalendar/fullcalendar.print.css') }}" rel='stylesheet' media='print'>
	<link href="{{asset('assets/css/animate.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/footable/footable.core.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/slick/slick.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/plugins/slick/slick-theme.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/plugins/select2/select2.min.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/summernote/summernote.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/plugins/summernote/summernote-bs3.css')}}" rel="stylesheet">
	<link href="{{asset('assets/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
	 <link href="{{asset('assets/css/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet">
    @yield('pagecss')
</head>
<body>
    <div id="wrapper">
		<nav class="navbar-default navbar-static-side" role="navigation">
			@include('dealer.includes.sidebarmenu')
	    </nav>
		<div id="page-wrapper" class="gray-bg">
			<div class="row border-bottom">
				<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
					<div class="navbar-header">
						<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
					</div>
					<ul class="nav navbar-top-links navbar-right">
						<li>
							<a class="count-info" title="View Cart" href="{{action('dealer\CartController@viewcart')}}">
								<i class="fa fa-shopping-cart"></i>  <span class="label label-warning"><?php $sessionCartData=Session::get('cartcount'); if(isset($sessionCartData)){echo $sessionCartData;} ?></span>
							</a>
						</li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-tasks"></i>  <span class="label label-primary">{{ $count_today_task }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                @if($count_today_task != 0)

                                    <?php $no = 0; ?>
                                    @foreach($today_task as $key_today_task => $value_today_task)
                                        <li>
                                            <a href="javascript:void(0);" onclick="displayAssignTask('{{ 'notificationlist_'.$no }}')" id="notificationlist_{{ $no }}"
                                               data-title="{{ $today_task_list_data[$no]['title'] }}"  data-assigndate="{{ $today_task_list_data[$no]['assign_date'] }}"
                                               data-completiondate="{{ $today_task_list_data[$no]['completion_date'] }}" data-taskassign=""
                                               data-taskID="{{ $today_task_list_data[$no]['task_id'] }}">
                                                <div>
                                                    <i class="fa fa-list fa-fw"></i> {{ $value_today_task }}
                                                </div>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <?php $no++; ?>
                                    @endforeach

                                @else
                                    <li>Today Not Any Task</li>
                                @endif
                            </ul>
                        </li>
						 
						<li>
							<a class="count-info" title="User Profile" href="{{action('dealer\DealerController@profile')}}">
								<i class="fa fa-user" aria-hidden="true"></i>
							</a>
						</li>
						<li>
							<a title="Logout" href="{{action('dealer\DealerController@logout')}}">
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
