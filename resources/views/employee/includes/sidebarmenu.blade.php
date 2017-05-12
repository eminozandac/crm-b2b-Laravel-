        <?php
            $currentPage = Route::getCurrentRoute()->getPath();

            $sessionData = Session::get('employeeLog');
            $role = $sessionData['role'];
        ?>

        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">

                <li class="nav-header">
                    <div class="dropdown profile-element">
						<span>
						<?php
                            $sessionData = Session::get('employeeLog');
                            if(isset($sessionData) && !empty($sessionData['employeeID'])){
                            $userdata = DB::table('employee')->where('id','=',$sessionData['employeeID'])->first();
                            if(!empty($userdata->employeeAvatar)){
                                $cavatar='uploads/employee/'.$userdata->employeeAvatar;
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
                            <li><a href="{{action('employee\EmployeeController@profile')}}">Profile</a></li>
                            <li><a href="{{action('employee\EmployeeController@logout')}}">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        CRM
                    </div>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'employee/') || ($currentPage == 'employee/dashboard')){
                    $activeClass = 'active';
                }
                ?>
                <li class="{{ $activeClass }}">
                    <a href="{{action('employee\EmployeeController@index')}}">
                        <i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span>
                    </a>
                </li>


                <?php
                $activeClass = '';
                if(($currentPage == 'employee/task') || ($currentPage == 'employee/taskadd')){
                    $activeClass = 'active';
                }
                ?>
                <li class="{{ $activeClass }}">
                    <a href="{{action('employee\EmployeeTaskController@index')}}">
                        <i class="fa fa-tasks"></i> <span class="nav-label">Task Manage</span>
                    </a>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'employee/taskcalender')){
                    $activeClass = 'active';
                }
                ?>
                <li class="{{ $activeClass }}">
                    <a href="{{action('employee\EmployeeCalenderController@index')}}">
                        <i class="fa fa-calendar"></i> <span class="nav-label">Task Calender</span>
                    </a>
                </li>

                <?php
                $activeClass = '';
                if(($currentPage == 'employee/warranty') || ($currentPage == 'employee/warrantyadd'))
                {
                    $activeClass = 'active';
                }
                ?>
                <li class="<?php echo $activeClass; ?>">
                    <a href="{{ URL::to('employee/warranty') }}"><i class="fa fa-recycle" aria-hidden="true"></i> <span class="nav-label">Warranty</span></a>
                </li>

            </ul>
        </div>