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
    <link href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/font-awesome/css/font-awesome.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/fullcalendar/fullcalendar.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/fullcalendar/fullcalendar.print.css')); ?>" rel='stylesheet' media='print'>
	<link href="<?php echo e(asset('assets/css/animate.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/style.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/toastr/toastr.min.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/footable/footable.core.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/datapicker/datepicker3.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/slick/slick.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/slick/slick-theme.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/select2/select2.min.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/iCheck/custom.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/summernote/summernote.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/summernote/summernote-bs3.css')); ?>" rel="stylesheet">
	<link href="<?php echo e(asset('assets/css/plugins/dataTables/datatables.min.css')); ?>" rel="stylesheet">
	 <link href="<?php echo e(asset('assets/css/plugins/sweetalert/sweetalert.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldContent('pagecss'); ?>
</head>
<body>
    <div id="wrapper">
		<nav class="navbar-default navbar-static-side" role="navigation">
			<?php echo $__env->make('dealer.includes.sidebarmenu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	    </nav>
		<div id="page-wrapper" class="gray-bg">
			<div class="row border-bottom">
				<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
					<div class="navbar-header">
						<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
					</div>
					<ul class="nav navbar-top-links navbar-right">
						<li>
							<a class="count-info" title="View Cart" href="<?php echo e(action('dealer\CartController@viewcart')); ?>">
								<i class="fa fa-shopping-cart"></i>  <span class="label label-warning"><?php $sessionCartData=Session::get('cartcount'); if(isset($sessionCartData)){echo $sessionCartData;} ?></span>
							</a>
						</li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-tasks"></i>  <span class="label label-primary"><?php echo e($count_today_task); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                <?php if($count_today_task != 0): ?>

                                    <?php $no = 0; ?>
                                    <?php foreach($today_task as $key_today_task => $value_today_task): ?>
                                        <li>
                                            <a href="javascript:void(0);" onclick="displayAssignTask('<?php echo e('notificationlist_'.$no); ?>')" id="notificationlist_<?php echo e($no); ?>"
                                               data-title="<?php echo e($today_task_list_data[$no]['title']); ?>"  data-assigndate="<?php echo e($today_task_list_data[$no]['assign_date']); ?>"
                                               data-completiondate="<?php echo e($today_task_list_data[$no]['completion_date']); ?>" data-taskassign=""
                                               data-taskID="<?php echo e($today_task_list_data[$no]['task_id']); ?>">
                                                <div>
                                                    <i class="fa fa-list fa-fw"></i> <?php echo e($value_today_task); ?>

                                                </div>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <?php $no++; ?>
                                    <?php endforeach; ?>

                                <?php else: ?>
                                    <li>Today Not Any Task</li>
                                <?php endif; ?>
                            </ul>
                        </li>
						 
						<li>
							<a class="count-info" title="User Profile" href="<?php echo e(action('dealer\DealerController@profile')); ?>">
								<i class="fa fa-user" aria-hidden="true"></i>
							</a>
						</li>
						<li>
							<a title="Logout" href="<?php echo e(action('dealer\DealerController@logout')); ?>">
								<i class="fa fa-sign-out"></i> Log out
							</a>
						</li>
					</ul>
				</nav>
				<?php echo $__env->yieldContent('contentPages'); ?>
			</div>
		</div>
    </div>
   <?php echo $__env->yieldContent('pagescript'); ?>
   
	 
</body>
</html>
