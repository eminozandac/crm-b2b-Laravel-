        <?php $currentPage = Route::getCurrentRoute()->getPath(); ?>

        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> 
						<span>
						<?php
                            $sessionData = Session::get('dealerLog');
                            if(isset($sessionData) && !empty($sessionData['dealerID'])){
                            $userdata = DB::table('dealer')->where('id','=',$sessionData['dealerID'])->first();
                            if(!empty($userdata->dealerAvatar)){
                                $cavatar='uploads/dealer/'.$userdata->dealerAvatar;
                            } else{
                                $cavatar='assets/img/placeholder300x300.png';
                            }
						?>
                            <img alt="image" class="img-circle sidebar-admin-avatar" src="<?php echo e(asset($cavatar)); ?>" />
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold"><?php echo $userdata->company_name; } ?></strong>
                                </span>
                                <span class="text-muted text-xs block">Settings <b class="caret"></b></span>
                            </span>
						</a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="<?php echo e(action('dealer\DealerController@profile')); ?>">Profile</a></li>
                            <li><a href="<?php echo e(action('dealer\DealerController@logout')); ?>">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        CRM
                    </div>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'dealer/') || ($currentPage == 'dealer/dashboard')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo e($activeClass); ?>">
                    <a href="<?php echo e(action('dealer\DealerController@dashboard')); ?>">
                        <i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span>
					</a>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'dealer/product') || ($currentPage == 'dealer/productdetail')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo e($activeClass); ?>">
                    <a href="<?php echo e(action('dealer\DealerProductController@index')); ?>">
                        <i class="fa fa-bath"></i> <span class="nav-label">Products</span>
                    </a>
                </li>
				 <?php
                    $activeClass = '';
                    if(($currentPage == 'dealer/accessorylist')){
                        $activeClass = 'active';
                    }
                ?>
				<li class="<?php echo $activeClass; ?>">
                    <a href="<?php echo e(action('dealer\DealerAccessoryController@accessoryList')); ?>"> <i class="fa fa-cubes"></i><span class="nav-label">Accessories</span></a>
                </li>
				 <?php
                    $activeClass = '';
                    if($currentPage == 'dealer/dealerorders' || $currentPage == 'dealer/delaerfinanceorders' || $currentPage == 'dealer/specialorders' || $currentPage == 'dealer/accessoryorder'){
                        $activeClass = 'active';
                    }
                ?>
				<li class="<?php echo e($activeClass); ?>">
				  <a href="#"> <i class="fa fa-shopping-cart"></i><span class="nav-label">Orders</span><span class="fa arrow"></span></a>
                    
					<ul class="nav nav-second-level">
						 <?php
							$activeClass = '';
							if(($currentPage == 'dealer/dealerorders')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php echo e($activeClass); ?>">
							<a href="<?php echo e(action('dealer\OrderController@index')); ?>">
							   Product Orders
							</a>
						</li>
						<?php
							$activeClass = '';
							if(($currentPage == 'dealer/delaerfinanceorders')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php echo e($activeClass); ?>">
							<a href="<?php echo e(action('dealer\OrderController@delaerFinanceOrders')); ?>">
								Finance Orders
							</a>
						</li>
						<?php
							$activeClass = '';
							if(($currentPage == 'dealer/specialorders')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php echo e($activeClass); ?>">
							<a href="<?php echo e(action('dealer\SpecialOrderController@index')); ?>">
								Special Orders
							</a>
						</li>
						<?php
							$activeClass = '';
							if(($currentPage == 'dealer/accessoryorder')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php echo e($activeClass); ?>">
							<a href="<?php echo e(action('dealer\DealerAccessoryController@dealerAccessoryOrder')); ?>">
								Accessory Orders
							</a>
						</li>
					</ul>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'dealer/dealerordersinvoicelist')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo e($activeClass); ?>">
                    <a href="<?php echo e(action('dealer\OrderController@dealerOrdersInvoiceList')); ?>">
                        <i class="fa fa-file"></i> <span class="nav-label">My Invoice</span>
                    </a>
                </li>
				<?php
                $activeClass = '';
                if(($currentPage == 'dealer/task') || ($currentPage == 'dealer/taskadd')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo e($activeClass); ?>">
                    <a href="<?php echo e(action('dealer\DealerTaskController@index')); ?>">
                        <i class="fa fa-tasks"></i> <span class="nav-label">Task Manage</span>
                    </a>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'dealer/taskcalender')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo e($activeClass); ?>">
                    <a href="<?php echo e(action('dealer\DealerCalenderController@index')); ?>">
                        <i class="fa fa-calendar"></i> <span class="nav-label">Task Calender</span>
                    </a>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'dealer/leadsreport') || ($currentPage == 'dealer/leadsreportadd')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo e($activeClass); ?>">
                    <a href="<?php echo e(action('dealer\DealerLeadsController@index')); ?>">
                        <i class="fa fa-flag-o"></i> <span class="nav-label">Lead Report</span>
                    </a>
                </li>


                <?php
                    $activeClass = '';
                    if(($currentPage == 'dealer/warranty') || ($currentPage == 'dealer/warrantyadd')){
                        $activeClass = 'active';
                    }
                ?>
                <li class="<?php echo e($activeClass); ?>">
                    <a href="<?php echo e(URL::to('dealer/warranty')); ?>">
                        <i class="fa fa-recycle" aria-hidden="true"></i><span class="nav-label">Warranty</span>
                    </a>
                </li>
				
				<?php
                $activeClass = '';
                if(($currentPage == 'dealer/mediacategorylist') || ($currentPage == 'dealer/mediacategoryfile'))
                {
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="<?php echo e(URL('/dealer/mediacategoryfile' )); ?>"><i class="fa fa-medium"></i> <span class="nav-label">Media File</span></a>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'dealer/newslatter'))
                {
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="<?php echo e(URL('/dealer/newslatter' )); ?>"><i class="fa fa-envelope-o"></i> <span class="nav-label">Newsletter</span></a>
                </li>

            </ul>
        </div>