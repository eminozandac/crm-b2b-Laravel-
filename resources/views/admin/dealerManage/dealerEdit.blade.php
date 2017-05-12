@extends('admin/layouts/masteradmin')

@section('pagecss')
 <link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Edit Dealer</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/admin/dealerlist')}}">All Dealer</a>
                    </li>
                    <li class="active">
                        <strong>All Dealer list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Edit Dealer <small>Category, Company name etc...</small></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="dealer_add" name="dealer_add" method="POST" action="{{ action('admin\ManageDealerController@editData') }}" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">

                                <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>
                                <input name="id" type="hidden" id="id" value="{{  $dealer->id }}"/>
                                

 
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Assign Group</label>
                                    <div class="col-sm-10">
                                        <select data-placeholder="Choose a Group..." class="form-control "  style="width: 100%;" tabindex="3" name="groupID" id="groupID">
                                            <option value="">Select</option>
                                            <?php
                                            foreach($group as $key=> $value)
                                            { ?>
                                            <option value="{{ $key }}"   <?php  if($dealer->groupID == $key){ echo 'selected'; } ?> >
                                                {{ $value }}
                                            </option>
                                            <?php  } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Company name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="jack" value="{{ $dealer->company_name }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Assign Category</label>
                                    <div class="col-sm-10">
                                        <select data-placeholder="Choose a Category..." class="chosen-select" multiple style="width: 100%" required tabindex="4" name="categoryID[]" id="categoryID">
                                            <?php
                                            foreach($categories as $key=> $value)
                                            { ?>
                                                <option value="{{ $key }}" <?php  if(in_array($key, explode(',', $dealer->categoryID)) ) { echo 'selected'; }?> >
                                                    {{ $value }}
                                                </option>
                                            <?php  } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">First name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="jack" value="{{ $dealer->first_name }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Last name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="rich" value="{{ $dealer->last_name }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

								
								<div class="form-group">
                                    <label class="col-sm-2 control-label">Invoice Prefix</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="invoicePrefix" name="invoicePrefix" placeholder="jony" value="{{ $dealer->invoicePrefix }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email Address</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="emailID" name="emailID" placeholder="jack@mail.com" value="{{ $dealer->emailID }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								
								<div class="form-group">
									<label class="col-sm-2 control-label">Phone</label>
									<div class="col-sm-10">
										<input type="text" class="form-control" name="phone" id="phone" placeholder="Phone"  value="{{ $dealer->phone }}">
									</div>
								</div>
								<div class="hr-line-dashed"></div>

                               <div class="form-group">
                                    <label class="col-sm-2 control-label">Billing Address</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="address" style="height:100px;" name="address" placeholder="Billing Address" >{{ $dealer->address }}</textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

								<div class="form-group">
                                    <label class="col-sm-2 control-label">Shipping Address</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="shipping_address" name="shipping_address" style="height:100px;" placeholder="Shipping Address" >{{ $dealer->shipping_address }}</textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								
								<div class="form-group">
                                    <label class="col-sm-2 control-label">Contact person 1</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id=" " name="contactperson1" style="height:100px;" placeholder="Contact person 1" >{{ $dealer->contactperson1 }}</textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div> 

								<div class="form-group">
                                    <label class="col-sm-2 control-label">Contact person 2</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="contactperson2" name="contactperson2" style="height:100px;" placeholder="Contact person 2" >{{ $dealer->contactperson2 }}</textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div> 
								
								<div class="form-group">
                                    <label class="col-sm-2 control-label">Contact person 3</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="contactperson3" name="contactperson3" style="height:100px;" placeholder="Contact person 3" >{{ $dealer->contactperson3 }}</textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div> 

                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="submit" class="btn btn-primary" value="Update"/>
                                        <!--<button class="btn btn-white" type="button" id="btn_reset">Reset</button>-->
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
                    var config = {
                        '.chosen-select'           : {},
                        '.chosen-select-deselect'  : {allow_single_deselect:true},
                        '.chosen-select-no-single' : {disable_search_threshold:10},
                        '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                        '.chosen-select-width'     : {width:"95%"}
                    }
                    for (var selector in config) {
                        $(selector).chosen(config[selector]);
                    }

                    <?php  if(isset($categoryID) && !empty($categoryID))
                    {
                        foreach($categoryID as $key => $value){
                        ?>
                        console.log(<?php echo $value;?>);
                        /*$('.chosen-select').val(<?php echo $value;?>);*/
                        //$('.chosen-select').val(<?php echo $value;?>).end().trigger('chosen:updated');
                    <?php } } ?>



                    $('#btn_reset').click(function ()
                    {
                        $('input[type="text"]').each(function(){
                            $(this).val('');
                        });

                        $('input[type="email"]').each(function(){
                            $(this).val('');
                        });

                        $('input[type="password"]').each(function(){
                            $(this).val('');
                        });
                        window.location.reload();
                    });

                    $('#dealer_add').find('[name="categoryID"]')
                        .change(function(e) {
                            $('#dealer_add').formValidation('revalidateField', 'categoryID[]');
                        })
                        .end()
                        .formValidation(
                        {
                            framework: 'bootstrap',
                            excluded: ':disabled',
                            message: 'This value is not valid',
                            icon: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                                company_name: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Dealer Company Name'
                                        },
                                        stringLength: {
                                            min: 3,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                        
                                    }
                                },
                                'categoryID[]': {
                                    validators: {
                                        callback: {
                                            message: 'Please choose Brand you like most',
                                            callback: function(value, validator, $field) {
                                                /* Get the selected options */
                                                var options = validator.getFieldElements('categoryID[]').val();
                                                return (options != null);
                                            }
                                        }
                                    }
                                },
                                first_name: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Dealer First Name !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            message: 'The Field must be more than 3 characters long'
                                        }
									}
                                },
                                last_name: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Dealer Last Name !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                         
                                    }
                                },
								invoicePrefix: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Dealer Invoice Prefix !'
                                        },
                                        regexp: {
                                            regexp: /^[a-z\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                emailID: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Email Address !'
                                        },
                                        emailAddress: {
                                            message: 'Enter Valid Email Address !'
                                        }
                                    }
                                },
                                address: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Address !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                },
								pincode: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Post Code !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            message: 'The Field must be more than 3 characters long'
                                        }
                                    }
                                },
                                city: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter City !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            message: 'The Field must be more than 3 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-z\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                state: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter State !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            message: 'The Field must be more than 3 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-z\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                country: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Select Country !'
                                        }
                                    }
                                },
                                groupID: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Select GroupID !'
                                        }
                                    }
                                },
								phone: {
									validators: {
										notEmpty: {
											message: 'Enter Phone Number.'
										}, 
										 regexp: {
                                            regexp: /^[()\/0-9\s\/^+\/^-]+$/i ,
                                            message: 'This Field can consist of Numerical characters and spaces only'
                                        }
										 
									}
								},
                            }
                        });
                });
            </script>
        @stop()