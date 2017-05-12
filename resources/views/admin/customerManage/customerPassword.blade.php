@extends('admin/layouts/masteradmin')

@section('pagecss')
 <link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Change Customer Password</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/admin/customerlist')}}">All Customer</a>
                    </li>
                    <li class="active">
                        <strong>All Customer list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Change Customer Password</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="change_password" name="change_password" method="POST" action="{{ action('admin\ManageCustomerController@updatePassworddata') }}" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">

                                <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>
                                <input name="id" type="hidden" id="id" value="{{  $customer->id }}"/>
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer->customer_id }}"/>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Customer Name</label>
                                    <div class="col-sm-10 text-left">
                                        <label class="col-sm-2 control-label">{{ $customer->first_name.' '.$customer->last_name }}</label>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email Address</label>
                                    <div class="col-sm-10">
                                        <label class="col-sm-2 control-label">{{ $customer->emailID }}</label>
                                        <input type="hidden" name="emailID" value="{{ $customer->emailID }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">New Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="******">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Confirm Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="txt_confirmpassword" name="txt_confirmpassword" placeholder="******">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>


                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="submit" class="btn btn-primary" value="Update"/>
                                       <!-- <button class="btn btn-white" type="button" id="btn_reset">Reset</button>-->
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		@stop()

        @section('pagescript')
            @include('admin.includes.commonscript')

            <script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>

            <script type="text/javascript">
                $(function ()
                {
                    $('#btn_reset').click(function ()
                    {
                        $('input[type="password"]').each(function(){
                            $(this).val('');
                        });
                        window.location.reload();
                    });

                    $('#change_password').formValidation({
                            framework: 'bootstrap',
                            excluded: ':disabled',
                            message: 'This value is not valid',
                            icon: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                                password: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Password'
                                        },
                                        different: {
                                            field: 'emailID',
                                            message: 'The password cannot be the same as username'
                                        },
                                        stringLength: {
                                            min: 6,
                                            max: 12,
                                            message: 'The password must be more than 6 characters long'
                                        }
                                    }
                                },
                                txt_confirmpassword: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Confirm Password'
                                        },
                                        different: {
                                            field: 'emailID',
                                            message: 'The password cannot be the same as username'
                                        },
                                        stringLength: {
                                            min: 6,
                                            max: 12,
                                            message: 'The password must be more than 6 characters long'
                                        },
                                        identical: {
                                            field: 'password',
                                            message: 'Password Does not Match !'
                                        }
                                    }
                                }
                            }
                        });
                });
            </script>
        @stop()