
                <li class="nav-header">
                    <div class="dropdown profile-element"> 
						<span>
						<?php
						$sessionData=Session::get('adminLog');
						if(isset($sessionData) && !empty($sessionData['adminID'])){ 
						$userdata=DB::table('admin')->where('id','=',$sessionData['adminID'])->first();
						if(!empty($userdata->adminAvatar)){
							$cavatar='uploads/admin/'.$userdata->adminAvatar;
						} else{
							$cavatar='assets/img/placeholder300x300.png';
						}
						?>
                            <img alt="image" class="img-circle sidebar-admin-avatar" src="{{asset($cavatar)}}" />
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php 	
						echo $userdata->name; } ?></strong>
                             </span> <span class="text-muted text-xs block">Settings <b class="caret"></b></span> </span> 
						</a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="{{action('admin\ProfileController@profile')}}">Profile</a></li>
                            <li><a href="{{action('admin\LoginController@logout')}}">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        CRM
                    </div>
                </li>

                <li class="<?php if (Request::is('admin/dashboard')){echo 'active';} ?>">
                    <a href="{{action('admin\LoginController@dashboard')}}"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span>
                    </a>
                </li>


