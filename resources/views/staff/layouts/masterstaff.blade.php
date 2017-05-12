<?php
    $count_today_task = 0;
    $today_task = Session::get('staffTodaytask');
    $count_today_task = count($today_task);

    $today_task_list_data = Session::get('staffTodaytask_list');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory CRM | Staff </title>
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
			@include('staff.includes.sidebarmenu')
	    </nav>
		<div id="page-wrapper" class="gray-bg">
			<div class="row border-bottom">
				<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
					<div class="navbar-header">
						<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
					</div>

					<ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-tasks"></i>  <span class="label label-primary">{{ $count_today_task }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                @if($count_today_task != 0)

                                    <?php $no = 0; ?>
                                    @foreach($today_task as $key_today_task => $value_today_task)
                                        <li>
                                            <a href="javascript:void(0);" onclick="displayAssignTask('{{ 'notificationlist_'.$no }}')" id="notificationlist_{{ $no }}" data-title="{{ $today_task_list_data[$no]['title'] }}"  data-assigndate="{{ $today_task_list_data[$no]['assign_date'] }}"
                                               data-completiondate="{{ $today_task_list_data[$no]['completion_date'] }}" data-taskassign="{{ $today_task_list_data[$no]['task_staff'] }}"
                                               data-taskID="{{ $today_task_list_data[$no]['task_id'] }}" data-task_priority ={{ $today_task_list_data[$no]['task_priority']  }}>
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
							<a class="count-info" title="User Profile" href="{{action('staff\StaffController@profile')}}">
								<i class="fa fa-user" aria-hidden="true"></i>
							</a>
						</li>
						<li>
							<a title="Logout" href="{{action('staff\StaffController@logout')}}">
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
