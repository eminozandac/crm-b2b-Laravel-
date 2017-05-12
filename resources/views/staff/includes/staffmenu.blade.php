
                <li class="nav-header">
                    <div class="dropdown profile-element"> 
						<span>
						<?php
                            $sessionData = Session::get('adminLog');
                            if(isset($sessionData) && !empty($sessionData['adminID'])){
                            $userdata = DB::table('staff')->where('id','=',$sessionData['adminID'])->first();
                            if(!empty($userdata->staffAvatar)){
                                $cavatar='uploads/staff/'.$userdata->staffAvatar;
                            } else{
                                $cavatar='assets/img/placeholder300x300.png';
                            }
						?>
                            <img alt="image" class="img-circle sidebar-admin-avatar" src="{{asset($cavatar)}}" />
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
                            <li><a href="{{action('staff\StaffController@profile')}}">Profile</a></li>
                            <li><a href="{{action('staff\StaffController@logout')}}">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        CRM
                    </div>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'staff/') || ($currentPage == 'staff/dashboard')){
                    $activeClass = 'active';
                }
                ?>
                <li class="{{ $activeClass }}">
                    <a href="{{action('staff\StaffController@index')}}">
                        <i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span>
					</a>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'staff/leadsreport') || ($currentPage == 'staff/leadsreportadd')){
                    $activeClass = 'active';
                }
                ?>
                <li class="{{ $activeClass }}">
                    <a href="{{action('staff\StaffLeadsController@index')}}">
                        <i class="fa fa-flag-o"></i> <span class="nav-label">Lead Report</span>
                    </a>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'staff/task') || ($currentPage == 'staff/taskadd')){
                    $activeClass = 'active';
                }
                ?>
                <li class="{{ $activeClass }}">
                    <a href="{{action('staff\StaffTaskController@index')}}">
                        <i class="fa fa-tasks"></i> <span class="nav-label">Task Manage</span>
                    </a>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'staff/taskcalender')){
                    $activeClass = 'active';
                }
                ?>
                <li class="{{ $activeClass }}">
                    <a href="{{action('staff\StaffCalenderController@index')}}">
                        <i class="fa fa-calendar"></i> <span class="nav-label">Task Calender</span>
                    </a>
                </li>
				
				<?php
                    $activeClass = '';
                    if(($currentPage == 'staff/warranty') || ($currentPage == 'staff/warrantyadd')
                    || ($currentPage == 'admin/customerlist') || ($currentPage == 'admin/customeradd'))
                    {
                        $activeClass = 'active';
                    }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="javascript:void(0);"><i class="fa fa-recycle" aria-hidden="true"></i> <span class="nav-label">Warranty</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <?php
                        $activeClass = '';
                        if(($currentPage == 'staff/warranty') || ($currentPage == 'staff/warrantyadd')){
                            $activeClass = 'active';
                        }
                        ?>
                        <li class="{{ $activeClass }}">
                            <a href="{{ URL::to('staff/warranty') }}">Warranty</a>
                        </li>

                        <?php
                        $activeClass = '';
                        if(($currentPage == 'admin/customerlist') || ($currentPage == 'admin/customeradd')){
                            $activeClass = 'active';
                        }
                        ?>
                        <li class="{{ $activeClass }}">
                            <a href="{{URL('/admin/customerlist')}}">Customer List</a>
                        </li>
                    </ul>
                </li>

