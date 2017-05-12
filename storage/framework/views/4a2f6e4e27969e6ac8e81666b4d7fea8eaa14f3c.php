                <?php
                $activeClass = '';
                if(($currentPage == 'admin/grouplist') || ($currentPage == 'admin/groupadd')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0);"><i class="fa fa-users"></i> <span class="nav-label">Group</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">

                        <li class="<?php if($currentPage == 'admin/grouplist'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/grouplist')); ?>">Group List</a>
                        </li>
                    </ul>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'admin/productList') || ($currentPage == 'admin/brandList') || ($currentPage == 'admin/productCategoriesList')
                        || ($currentPage == 'admin/attributeList') || ($currentPage == 'admin/productDetail') || ($currentPage == 'admin/editProducts') || ($currentPage == 'admin/addProducts')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="#"> <i class="fa fa-bath"></i><span class="nav-label">Products</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
						<li class="<?php if (Request::is('admin/productList')){echo 'active';} ?>"><a href="<?php echo e(action('admin\ProductController@productList')); ?>">Product list</a></li>
						
						<li class="<?php if (Request::is('admin/attributeList')){echo 'active';} ?>"><a href="<?php echo e(action('admin\AttributeController@index')); ?>">Attribute list</a></li>
						
                        <li class="<?php if (Request::is('admin/brandList')){echo 'active';} ?>"><a href="<?php echo e(action('admin\ProductController@brandList')); ?>">Brand list</a></li>

						<li class="<?php if (Request::is('admin/productCategoriesList')){echo 'active';} ?>"><a href="<?php echo e(action('admin\ProductController@productCategoriesList')); ?>">Category list</a></li>
						
						<li class="<?php if (Request::is('admin/addvariation')){echo 'active';} ?>"><a href="<?php echo e(action('admin\ProductController@addVariation')); ?>">Add Variation</a></li>
                    </ul>
                </li>
				
				 <?php
                $activeClass = '';
                if(($currentPage == 'admin/accessorycategorieslist')  || ($currentPage == 'admin/accessorylist') || ($currentPage == 'admin/addaccessory')){
                    $activeClass = 'active';
                }
                ?>
				
				
                <li class="<?php echo $activeClass; ?>">
                    <a href="#"> <i class="fa fa-cubes"></i><span class="nav-label">Accessories</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
						<li class="<?php if (Request::is('admin/accessorylist')){echo 'active';} ?>"><a href="<?php echo e(action('admin\AccessoryController@accessoryList')); ?>">Accessory list</a></li>
						
						<li class="<?php if (Request::is('admin/accessorycategorieslist')){echo 'active';} ?>"><a href="<?php echo e(action('admin\AccessoryController@accessoryCategoriesList')); ?>">Category list</a></li>
						
                    </ul>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'admin/orderDetials') || ($currentPage == 'admin/orderList')  || ($currentPage == 'admin/specialorderslist')  || ($currentPage == 'admin/financeorder') || ($currentPage == 'admin/accessoryorderslist')){
                    $activeClass = 'active';
                }
                ?>

                <li class="<?php echo $activeClass; ?>">
                    <a href="#"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Orders</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                         <li class="<?php if (Request::is('admin/orderList')){echo 'active';} ?>"><a href="<?php echo e(action('admin\OrderController@orderList')); ?>">Product Order</a></li>
						  <?php
							$activeClass = '';
							if(($currentPage == 'admin/financeorder')){
								$activeClass = 'active';
							}
							?>
						 <li class="<?php  echo $activeClass;  ?>">
                            <a href="<?php echo e(URL('/admin/financeorder')); ?>">Finance Order</a>
                        </li> 
						<?php
							$activeClass = '';
							if(($currentPage == 'admin/specialorderslist')){
								$activeClass = 'active';
							}
							?>
						 <li class="<?php  echo $activeClass;  ?>">
                            <a href="<?php echo e(URL('/admin/specialorderslist')); ?>">Special Order</a>
                        </li>
						<?php
							$activeClass = '';
							if(($currentPage == 'admin/accessoryorderslist')){
								$activeClass = 'active';
							}
							?>
						 <li class="<?php  echo $activeClass;  ?>">
                            <a href="<?php echo e(URL('/admin/accessoryorderslist')); ?>">Accessory Order</a>
                        </li>
                    </ul>
                </li>


                <?php
                    $activeClass = '';
                    if(($currentPage == 'admin/invoicelist') || ($currentPage == 'admin/createinvoice')){
                        $activeClass = 'active';
                    }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="<?php echo e(URL('/admin/invoicelist')); ?>"><i class="fa fa-file"></i> <span class="nav-label">My Invoice</span></a>
                </li>
				
				<?php
                    $activeClass = '';
                    if(($currentPage == 'admin/stockManage') || ($currentPage == 'admin/inproductionstockmanage')  || ($currentPage == 'admin/inseaarrivalukstockmanage') || ($currentPage == 'admin/batchwisestockmanage') || ($currentPage == 'admin/factorystockmanage')){
                        $activeClass = 'active';
                    }
                ?> 
				<?php
                    $activeClass = '';
                    if(($currentPage == 'admin/dealerlist') || ($currentPage == 'admin/dealeradd')){
                        $activeClass = 'active';
                    }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0);"><i class="fa fa-users"></i> <span class="nav-label">Dealer</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">

                        <li class="<?php if($currentPage == 'admin/dealerlist'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/dealerlist')); ?>">Dealer List</a>
                        </li>
                    </ul>
                </li>
				
				<?php
                    $activeClass = '';
                    if(($currentPage == 'admin/stockManage') || ($currentPage == 'admin/inproductionstockmanage')  || ($currentPage == 'admin/inseaarrivalukstockmanage') || ($currentPage == 'admin/batchwisestockmanage') || ($currentPage == 'admin/factorystockmanage')){
                        $activeClass = 'active';
                    }
                ?>
				<li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0)"><i class="fa fa-bar-chart"></i> <span class="nav-label">Stock</span><span class="fa arrow"></span> </a>
                    <ul class="nav nav-second-level">

                        <li class="<?php if($currentPage == 'admin/stockManage'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/stockManage')); ?>">view Stock</a>
                        </li>
						<?php
							$activeClass = '';
							if(($currentPage == 'admin/inproductionstockmanage')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php if($currentPage == 'admin/inproductionstockmanage'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/inproductionstockmanage')); ?>">In Production Stock</a>
                        </li>
						<?php
							$activeClass = '';
							if(($currentPage == 'admin/inseaarrivalukstockmanage')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php if($currentPage == 'admin/inseaarrivalukstockmanage'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/inseaarrivalukstockmanage')); ?>">On Sea Arrival Stock</a>
                        </li>
						<?php
							$activeClass = '';
							if(($currentPage == 'admin/batchwisestockmanage')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php if($currentPage == 'admin/factorystockmanage'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/factorystockmanage')); ?>">Factory Stock</a>
                        </li>
						<?php
							$activeClass = '';
							if(($currentPage == 'admin/batchwisestockmanage')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php if($currentPage == 'admin/batchwisestockmanage'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/batchwisestockmanage')); ?>">View Batch's</a>
                        </li>
						<?php
							$activeClass = '';
							if(($currentPage == 'admin/completedorders')){
								$activeClass = 'active';
							}
						?>
						<li class="<?php if($currentPage == 'admin/completedorders'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/completedorders')); ?>">Completed Order</a>
                        </li>
                    </ul>
                </li>

                <?php if($role == 'admin'): ?>
                <?php
                    $activeClass = '';
                    if(($currentPage == 'admin/stafflist') || ($currentPage == 'admin/staffadd')){
                        $activeClass = 'active';
                    }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0);"><i class="fa fa-user-o"></i> <span class="nav-label">Staff</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">

                        <li class="<?php if($currentPage == 'admin/stafflist'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/stafflist')); ?>">Staff List</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php
                $activeClass = '';
                if(($currentPage == 'admin/employeelist') || ($currentPage == 'admin/employeeadd')){
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0);"><i class="fa fa-user-circle-o"></i> <span class="nav-label">Employee</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">

                        <li class="<?php if($currentPage == 'admin/employeelist'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/employeelist')); ?>">Employee List</a>
                        </li>
                    </ul>
                </li>


                <?php if($role == 'admin'): ?>
                <?php
                    $activeClass = '';
                    if(($currentPage == 'admin/warranty') || ($currentPage == 'admin/warrantyadd')
                    || ($currentPage == 'admin/customerlist') || ($currentPage == 'admin/customeradd')){
                        $activeClass = 'active';
                    }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0);"><i class="fa fa-recycle" aria-hidden="true"></i> <span class="nav-label">Warranty</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <?php
                            $activeClass = '';
                            if(($currentPage == 'admin/warranty') || ($currentPage == 'admin/warrantyadd')){
                                $activeClass = 'active';
                            }
                        ?>
                        <li class="<?php echo e($activeClass); ?>">
                            <a href="<?php echo e(URL::to('admin/warranty')); ?>">Warranty</a>
                        </li>

                        <?php
                            $activeClass = '';
                            if(($currentPage == 'admin/customerlist') || ($currentPage == 'admin/customeradd')){
                                $activeClass = 'active';
                            }
                        ?>
                        <li class="<?php echo e($activeClass); ?>">
                            <a href="<?php echo e(URL('/admin/customerlist')); ?>">Customer List</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php
                    $activeClass = '';
                    if(($currentPage == 'admin/mediacategorylist') || ($currentPage == 'admin/mediacategorysublist') || ($currentPage == 'admin/mediacategoryfile'))
                    {
                        $activeClass = 'active';
                    }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0);"><i class="fa fa-medium"></i> <span class="nav-label">Media</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="<?php if($currentPage == 'admin/mediacategorylist'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/mediacategorylist' )); ?>">Media Main Category</a>
                        </li>
                        <li class="<?php if($currentPage == 'admin/mediacategorysublist'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/mediacategorysublist' )); ?>">Media Sub Category</a>
                        </li>
                        <li class="<?php if($currentPage == 'admin/mediacategoryfile'){ echo 'active'; } ?>">
                            <a href="<?php echo e(URL('/admin/mediacategoryfile' )); ?>">Media File</a>
                        </li>
                    </ul>
                </li>


                <?php if($role == 'admin'): ?>

                <?php

                $activeClass = '';

                if(($currentPage == 'logdata/loglistdata')){

                    $activeClass = 'active';

                }

                ?>

                <li class="<?php echo $activeClass; ?>">

                    <a href="<?php echo e(URL('/logdata/loglistdata')); ?>"><i class="fa fa-info-circle"></i> <span class="nav-label">Log</span></a>

                </li>
				
				<?php
                    $activeClass = '';
                    if(($currentPage == 'databasebackup')){
                        $activeClass = 'active';
                    }
                ?>

                <li class="<?php echo $activeClass; ?>">
                    <a href="<?php echo e(URL('databasebackup')); ?>"><i class="fa fa-hdd-o"></i> <span class="nav-label">Backup</span></a>
                </li>
				

                <?php endif; ?>
				

