@extends('admin.layouts.masteradmin')
@section('contentPages')
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Admin Profile</h2>
			<ol class="breadcrumb">
				<li>
					<a href="{{URL::to('/admin')}}">Home</a>
				</li>
				<li>
					<a>Admin</a>
				</li>
				<li class="active">
					<strong>Profile</strong>
				</li>
			</ol>
		</div>
		<div class="col-lg-2">

		</div>
	</div>
	<div class="wrapper wrapper-content">
		<div class="row animated fadeInRight">
			<div class="col-md-4">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Profile Detail</h5>
					</div>
					<div>
						<div class="ibox-content no-padding border-left-right">
						<?php 
                        $sessionData=Session::get('adminLog');
                        if(isset($sessionData) && !empty($sessionData['adminID']))
                        {
						    $userdata=DB::table('admin')->where('id','=',$sessionData['adminID'])->first();
						    if(!empty($userdata->adminAvatar)){
							    $cavatar='uploads/admin/'.$userdata->adminAvatar;
						    } else{
							    $cavatar='assets/img/placeholder300x300.png';
						    }
						?>
							<img alt="image" style="margin: 0 auto;" class="img-responsive" src="{{asset($cavatar)}}">
						</div>
						<div class="ibox-content profile-content">
							<h4><strong><?php echo $userdata->name; } ?></strong></h4>
							<p>
                                <strong>
                                <i class="fa fa-envelope">&nbsp;</i>
                                <?php
                                    $sessionData=Session::get('adminLog');
						            if(isset($sessionData) && !empty($sessionData['adminID']))
                                    { echo $sessionData['email']; }
                                ?>
                                </strong>
                            </p>
						<div class="row">
							<form action="{{action('admin\ProfileController@updateAdminAvatar')}}" method="post" enctype="multipart/form-data" class="form-horizontal">
									<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
									 
									<div class="col-sm-12">
										<div class="btn-group">
										<label class="control-label" for="order_id">&nbsp;</label><br/>
											<label title="Upload image file" for="inputImage" class="btn btn-primary">
												<input type="file" accept="image/*" name="adminAvatar" required id="inputImage" class="hide">
												Upload new image
											</label>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="order_id">&nbsp;</label><br/>
											<input type="submit" class="btn btn-primary" value="Save" >
										</div>
									</div>
							</form>
						</div>
					</div>
				</div>
                </div>
			</div>
			<div class="col-md-8">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Other Detail</h5>
					</div>
					<div class="tabs-container">
						<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#tab-1"> Personal Detail</a></li>
							<li class=""><a data-toggle="tab" href="#tab-2">Change Password</a></li>
						</ul>
						<div class="tab-content">
							<div id="tab-1" class="tab-pane active">
								<div class="panel-body">
									<div class="ibox-content col-md-12">
										<div class="col-md-12">
											<form class="m-t form-horizontal" role="form" method="post" action="{{action('admin\ProfileController@profileUpdate')}}" id="form-login" >
												<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
												<div class="form-group">
													<input type="text" class="form-control" name="name" id="name" placeholder="Full Name" value="<?php if(isset($sessionData) && !empty($sessionData['adminID'])){ echo $userdata->name;} ?>">
												</div>
												<div class="col-md-2 pl0">
													<button type="submit" class="btn btn-primary block full-width m-b">Update</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<div id="tab-2" class="tab-pane">
								<div class="panel-body">
									<div class="col-md-12">
										<form class="m-t form-horizontal" role="form" method="post" action="{{action('admin\ProfileController@passwordUpdate')}}" id="form-login" >
											<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
											<div class="form-group">
												<input type="password" class="form-control" name="opassword" id="opassword" placeholder="Old Password">
											</div>
											<div class="form-group">
												<input type="password" class="form-control" name="password" id="password" placeholder="New Password">
											</div>
											<div class="form-group">
												<input type="password" class="form-control" name="cpassword" id="cpassword" placeholder="Confirm Password">
											</div>
											<div class="col-md-2 pl0">
												<button type="submit" class="btn btn-primary block full-width m-b">Update</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop()
@section('pagescript')
		@include('admin.includes.commonscript')
		<script type="text/javascript" src="{{asset('assets/js/jquery-form-validation.js')}}"></script>
@stop()