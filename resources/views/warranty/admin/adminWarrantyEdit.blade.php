@extends('admin.layouts.masteradmin')
@section('pagecss')
    <link href="{{ asset('assets/css/plugins/chosen/chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dropzone/basic.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dropzone/dropzone.css') }}" rel="stylesheet">
    <style>
        .dropzone, .dropzone *, .dropzone-previews, .dropzone-previews *{
            z-index: 999;
        }
        .dropzone .dz-default.dz-message{
            z-index: 1;
        }
        .dz-preview img{
            text-indent: -9999px;
            display: none;
        }
    </style>
@stop

@section('contentPages')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Edit Warranty</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{URL::to('/admin/dashboard')}}">Home</a>
                </li>
                <li class="active">
                    <strong>Warranty</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Edit Warranty</h5>
                        <div class="ibox-tools">
                            <a class="btn btn-w-m btn-primary" href="{{URL::to('/admin/warranty')}}"  style="background-color: #18A689;">
                                <i class="fa fa-list">&nbsp;</i>List Product Warranty
                            </a>
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form id="warranty_form" name="warranty_form" method="POST" action="{{ action('admin\WarrantyAdminController@warrantySaveData') }}" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">

                            <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Assign User</label>
                                <div class="col-sm-10">
                                    <?php
                                        $role = '';
                                        $role = $warrantyData->assign_role;
                                        $checked = '';
                                        if(($role == 'staff') || ($role == ''))
                                        {
                                            $checked = 'checked';
                                        }
                                    ?>
                                    <div>
                                        <label>
                                            <input type="radio" value="staff" id="rbtn_staff" name="assign_role" {{ $checked }}> Staff
                                        </label>
                                    </div>

                                    <?php
                                        $checked = '';
                                        if($role == 'employee')
                                        {
                                            $checked = 'checked';
                                        }
                                    ?>
                                    <div>
                                        <label>
                                            <input type="radio" value="employee" id="rbtn_employee" name="assign_role" {{ $checked }}> Employee
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Assign Warranty</label>
                                <div class="col-sm-10">
                                    <select data-placeholder="Choose a User..."  class="chosen-select"  style="width: 100%;" tabindex="2" name="warranty_assign" id="warranty_assign">

                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Role</label>
                                <div class="col-sm-10">
                                    <label class="control-label">{{ strtoupper($warrantyData->role) }}</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <?php
                            $name = '';
                            if($warrantyData->role == 'dealer')
                            {
                                $result = DB::table('dealer')->where('id',$warrantyData->user_id)->first();
                                $name = $result->first_name.' '.$result->last_name;
                            ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Dealer name</label>
                                    <div class="col-sm-10">
                                        <label class="control-label">{{ $name }}</label>
                                    </div>
                                </div>
                            <div class="hr-line-dashed"></div>
                            <?php } ?>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Status</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="warranty_status" id="warranty_status">
                                        @if(!empty($warranty_status)))
                                        @foreach($warranty_status as $key => $value)
                                            <?php
                                            $selected = '';
                                            if($key == $warrantyData->warranty_status)
                                            {
                                                $selected = "selected";
                                            }
                                            ?>
                                            <option value="{{ $key }}" {{ $selected }} >
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group" id="div_customer_name">
                                <label class="col-sm-2 control-label">Customer's name</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required name="name" id="name" value="{{ $warrantyData->name }}" placeholder="john"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Customer's Address</label>
                                <div class="col-sm-10">
                                    <textarea  class="form-control" id="address" name="address">{{ $warrantyData->address }}</textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Customer's postcode</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="postcode" name="postcode" placeholder="" value="{{ $warrantyData->postcode }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    Customer's EmailID
                                    @if($warrantyData->role == 'customer')
                                        <span class="help-block m-b-none span_help" style="color:#f8ac59;">(login-username)</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    <input type="email"  class="form-control contactMethod" id="emailID" name="emailID" placeholder="john@mail.com" value="{{ $warrantyData->emailID }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    Customer's Telephone
                                    @if($warrantyData->role == 'customer')
                                        <span class="help-block m-b-none span_help" style="color:#f8ac59;">(login-username)</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control contactMethod" id="phone" name="phone" placeholder="1234" value="{{ $warrantyData->phone }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Brand</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Hot tub" value="{{ $warrantyData->product_name }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Model</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="product_model" name="product_model" placeholder="Hot tub" value="{{ $warrantyData->product_model }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group" id="date_purchase">
                                <label class="col-sm-2 control-label">Date of Delivery</label>
                                <div class="col-sm-10 input-group date" style="padding-left:15px; padding-right:15px; ">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <?php
                                        $purchase_date = '';
                                        if($warrantyData->purchase_date != '0000-00-00'){
                                            $purchase_date = date('d-m-Y',strtotime($warrantyData->purchase_date));
                                        }
                                    ?>
                                    <input type="text" class="form-control" name="purchase_date" id="purchase_date" value="{{ $purchase_date }}" />
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Serial Number</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="product_serial_number" name="product_serial_number" placeholder="1234" value="{{ $warrantyData->product_serial_number }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Fault</label>
                                <div class="col-sm-10">
                                    <textarea  class="form-control" id="product_fault" name="product_fault">{{ $warrantyData->product_fault }}</textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Part Required</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="part_require" name="part_require" placeholder="" value="{{ $warrantyData->part_require }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Additional Comments</label>
                                <div class="col-sm-10">
                                    <textarea  class="form-control" id="note" name="note">{{ $warrantyData->note }}</textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Upload Images</label>
                                <div class="col-sm-10 dropzone" id="file_note">
                                    <div class="dropzone-previews"></div>
                                </div>
                                <input type="hidden" name="hidden_file_images" id="hidden_file_images" value="{{ $warrantyData->file_images }}" />

                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">Send</button>
                                    <button class="btn btn-white" type="button" id="btn_reset">Reset</button>
                                    <input type="hidden" name="warranty_uniqueID" id="warranty_uniqueID" value="{{ $warrantyData->warranty_uniqueID }}"/>
                                    <input type="hidden" name="method_process" id="method_process" value="edit" />
                                    <input type="hidden" name="role" id="role" value="{{ $warrantyData->role }}" />
                                    <input type="hidden" name="user_id" id="user_id" value="{{ $warrantyData->user_id }}" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input name="_token" type="hidden" id="hidden_token_file" value="{{ csrf_token() }}"/>
@stop()

@section('pagescript')
    @include('admin.includes.commonscript')

    <script src="{{ asset('assets/js/plugins/chosen/chosen.jquery.js') }}"></script>

    <!-- DROPZONE -->
    <script src="{{ asset('assets/js/plugins/dropzone/dropzone.js') }}"></script>

    <!-- Data picker -->
    <script src="{{ asset('assets/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>

    <script type="text/javascript">
        $(function ()
        {
            $('#btn_reset').click(function ()
            {
                window.location.reload();
            });

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

            $('#date_purchase .date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: 'd-m-yyyy'
            });

            $('#warranty_form').find('[name="purchase_date"]')
            .change(function(e) {
                $('#warranty_form').formValidation('revalidateField', 'purchase_date');
            })
            .end()
            .formValidation({
                framework: 'bootstrap',
                excluded: ':disabled',
                message: 'This value is not valid',
                icon: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    warranty_assign: {
                        validators: {
                            notEmpty: {
                                message: 'Select Staff'
                            }
                        }
                    },
                    name: {
                        validators: {
                            notEmpty: {
                                message: 'Enter Customer name'
                            },
                            stringLength: {
                                min: 3,
                                max: 30,
                                message: 'The Field must be more than 3 characters long'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z0-9\s]+$/i,
                                message: 'This Field can consist of alphabetical characters and spaces only'
                            }
                        }
                    },
                    emailID: {
                        validators: {
                            emailAddress: {
                                message: 'Enter Valid Email Address !'
                            }
                        }
                    },
                    phone: {
                        row: '.col-xs-3',
                        validators: {
                            phone: {
                                country: 'country',
                                message: 'The value is not valid %s phone number'
                            }
                        }
                    },
                    contact : {
                        selector: '.contactMethod',
                        validators: {
                            callback: {
                                message: 'You must enter at least one emailID/phone',
                                callback: function(value, validator, $field)
                                {
                                    var isEmpty = true,
                                    // Get the list of fields
                                            $fields = validator.getFieldElements('contact');
                                    console.log($fields);
                                    for (var i = 0; i < $fields.length; i++) {
                                        if ($fields.eq(i).val() !== '') {
                                            isEmpty = false;
                                            break;
                                        }
                                    }

                                    if (!isEmpty) {
                                        // Update the status of callback validator for all fields
                                        validator.updateStatus('contact', validator.STATUS_VALID, 'callback');
                                        return true;
                                    }

                                    return false;
                                }
                            }
                        }
                    }
                }
            });

        });
    </script>

    <script>
        $(function()
        {
            var warranty_uniqueID = $('#warranty_uniqueID').val();

            Dropzone.autoDiscover = false;
            var file_note_names = [];
            var hidden_file_note = [];
            var uniqueNames = [];
            var i = 0;
            var n = 0;
            var jk = 0;

            var myDropzone = new Dropzone("div#file_note",
                    {
                        url: "{{ URL::to('/admin/warrantyfile/') }}",
                        method : "post",
                        paramName: "note_file",
                        params :{ _token: $('#hidden_token_file').val() },
                        previewsContainer: ".dropzone-previews",
                        uploadMultiple: true,
                        parallelUploads: 10,
                        maxFiles: 10,
                        dictMaxFilesExceeded: "You can only upload upto 10 images",
                        addRemoveLinks: true,
                        dictRemoveFile: "Delete",
                        dictCancelUploadConfirmation: "Are you sure to cancel upload?",
                        acceptedFiles: ".jpg, .jpeg, .png, .pdf",
                        init: function()
                        {
                            // Hack: Add the dropzone class to the element
                            $(this.element).addClass("dropzone");
                            this.on("success", function (file, serverFileName)
                            {
                                var json_obj_file = jQuery.parseJSON(serverFileName);
                                var no = 0;
                                for (var i in json_obj_file)
                                {
                                    hidden_file_note.push(json_obj_file[i].newname);
                                    file_note_names[n] = {"serverFileName": json_obj_file[i].newname, "fileName": json_obj_file[i].oldname, "fileId": n};
                                    n++;

                                    no++;
                                    if(i == jk) {
                                        var downloadurl = json_obj_file[i].downloadurl;
                                        var id_a = "id=bt-down_" + no;
                                        file._downloadLink = Dropzone.createElement("<a download class=\"btn\" " + id_a + " style=\"width:100%; cursor:pointer;\" href=\"" + downloadurl + "\" title=\"Download\" data-dz-download><i class=\"fa fa-download\"></i></a>");
                                        file.previewElement.appendChild(file._downloadLink);
                                        file.previewElement.classList.add("dz-success");
                                    }
                                }
                                jk++;

                                var uniqueNames = [];
                                $.each(hidden_file_note, function(i, el){
                                    if($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
                                });
                                hidden_file_note = uniqueNames;

                                var file_name_string = hidden_file_note.join(",");
                                $('#hidden_file_images').val(file_name_string);
                            });

                            this.on("removedfile", function(file)
                            {
                                var rmvFile = "";
                                if(file_note_names.length != 0)
                                {
                                    for(var f=0; f<file_note_names.length; f++)
                                    {

                                        if(file_note_names[f].fileName == file.name)
                                        {
                                            rmvFile = file_note_names[f].serverFileName;
                                        }else{
                                            rmvFile = file.name;
                                        }
                                    }
                                }else{
                                    rmvFile = file.name;
                                }
                                if (rmvFile)
                                {
                                    hidden_file_note = jQuery.grep(hidden_file_note, function( a ) {
                                        return a !== rmvFile;
                                    });
                                    var file_name_string = hidden_file_note.join(",");
                                    $('#hidden_file_images').val(file_name_string);
                                    $.post("{{ URL::to('/admin/warrantyremove/') }}", {
                                        file: rmvFile, _token:$('#hidden_token_file').val()
                                    });
                                }
                            });
                        }
                    });

            var hidden_token_file = $("#hidden_token_file").val();
            $.post("{{ URL::to('/admin/warrantygetimages/') }}", { data_warranty_uniqueID : warranty_uniqueID, _token: hidden_token_file },
                    function(data)
                    {
                        var json_obj_file = jQuery.parseJSON(data);
                        var no = 0;
                        var dwn = 0;
                        for (var i in json_obj_file)
                        {
                            no++;

                            var mockFile = { name: json_obj_file[i].name, size: json_obj_file[i].size };

                            hidden_file_note.push(json_obj_file[i].name);

                            myDropzone.options.addedfile.call(myDropzone, mockFile);

                            myDropzone.options.thumbnail.call(myDropzone, mockFile, json_obj_file[i].url);

                            if(dwn == i)
                            {
                                var a = '';
                                var ab = '';
                                var id_a = "id=bt-down_"+no;
                                a = "<a download class=\"btn\" "+id_a+" style=\"margin-left:5%; width:auto; cursor:pointer;\" href=\""+json_obj_file[i].url+"\" title=\"Download\" data-dz-download><i class=\"fa fa-download\"></i></a>"
                                ab = "<a target=\"_blank\" class=\"btn\" "+id_a+" style=\"margin-left:5%; width:auto; cursor:pointer;\" href=\""+json_obj_file[i].url+"\" title=\"Download\" data-dz-download><i class=\"fa fa-eye\"></i></a>"
                                var data = $('.dropzone-previews div.dz-image-preview:nth-child('+no+')').attr("data-download");
                                if(data == undefined)
                                {
                                    $('.dropzone-previews div.dz-image-preview:nth-child('+no+')').attr("data-download", no);
                                    $('.dropzone-previews div.dz-image-preview:nth-child('+no+')').append(ab);
                                    $('.dropzone-previews div.dz-image-preview:nth-child('+no+')').append(a);

                                }
                                a = '';
                                dwn++;
                            }



                        }
                        // If you use the maxFiles option, make sure you adjust it to the
                        // correct amount:
                        var existingFileCount = no; // The number of files already uploaded
                        myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
                    });
        });
    </script>
    <script type="text/javascript">
        //task_assign
        $(function ()
        {
            var new_token = $('#hidden_token_file').val();

            function getAssignUser()
            {
                $('#warranty_assign').html('');
                $('#warranty_assign').trigger("chosen:updated");
                var assign_role = $('input[name="assign_role"]:checked').val();
                var selected_user = '{{ $warrantyData->warranty_assign }}';

                $.ajax
                ({
                    type: "POST",
                    url: "{{ URL::to('admin/ajax/log/getassignuser') }}",
                    data: {	data_assign_role : assign_role, data_selected_user:selected_user,  _token: new_token },
                    success: function (result)
                    {
                        $('#warranty_assign').html(result);
                        $('#warranty_assign').trigger("chosen:updated");
                    }
                });
            }

            $('input[name="assign_role"]').change(function ()
            {
                getAssignUser();
            });
            getAssignUser();
        });
    </script>
@stop()