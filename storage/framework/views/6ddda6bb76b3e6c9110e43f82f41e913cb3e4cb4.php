<?php $currentPage = Route::getCurrentRoute()->getPath(); ?>

<div class="sidebar-collapse">
    <ul class="nav metismenu" id="side-menu">
        <li class="nav-header">
            <div class="dropdown profile-element">
						<span>
						<?php
                            $sessionData = Session::get('customerLog');
                            if(isset($sessionData) && !empty($sessionData['customerID'])){
                            $userdata = DB::table('customer')->where('id','=',$sessionData['customerID'])->first();
                            if(!empty($userdata->customerAvatar)){
                                $cavatar='uploads/customer/'.$userdata->customerAvatar;
                            } else{
                                $cavatar='assets/img/placeholder300x300.png';
                            }
                            ?>
                            <img alt="image" class="img-circle sidebar-admin-avatar" src="<?php echo e(asset($cavatar)); ?>" />
                        </span>
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold"><?php echo $userdata->first_name; } ?></strong>
                                </span>
                                <span class="text-muted text-xs block">Settings <b class="caret"></b></span>
                            </span>
                </a>
                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                    <li><a href="<?php echo e(action('customer\CustomerController@profile')); ?>">Profile</a></li>
                    <li><a href="<?php echo e(action('customer\CustomerController@logout')); ?>">Logout</a></li>
                </ul>
            </div>
            <div class="logo-element">
                CRM
            </div>
        </li>

        <?php
        $activeClass = '';
        if(($currentPage == 'customer/dashboard')){
            $activeClass = 'active';
        }
        ?>
        <li class="<?php echo e($activeClass); ?>">
            <a href="<?php echo e(URL::to('customer/dashboard')); ?>">
                <i class="fa fa-th-large" aria-hidden="true"></i><span class="nav-label">Dashboard</span>
            </a>
        </li>

        <?php
        $activeClass = '';
        if(($currentPage == 'customer/warranty') || ($currentPage == 'customer/warrantyadd')){
            $activeClass = 'active';
        }
        ?>
        <li class="<?php echo e($activeClass); ?>">
            <a href="<?php echo e(URL::to('customer/warranty')); ?>">
                <i class="fa fa-recycle" aria-hidden="true"></i><span class="nav-label">Warranty</span>
            </a>
        </li>

    </ul>
</div>