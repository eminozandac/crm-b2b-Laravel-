@extends('admin/layouts/masteradmin')

@section('pagecss')
    <link href="{{ asset('assets/css/plugins/switchery/switchery.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/chosen/chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
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
                <h2>Add Media Category Name</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/admin/mediacategorylist')}}">All Media Category List</a>
                    </li>
                    <li class="active">
                        <strong>All Media Category list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Add Media <small>Category</small></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="mediacategory_add" name="mediacategory_add" method="POST" action="{{ action('admin\MediaCategoryController@saveData') }}" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">
                                <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Category name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="jack" value="{{ $name }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Assign Dealer</label>
                                    <div class="col-sm-10">
                                        <select data-placeholder="Choose a Dealer..." class="contactMethod chosen-select" multiple style="width: 100%" tabindex="4" name="dealerID[]" id="dealerID">
                                            @if( isset($dealer) && !empty($dealer) )
                                                @foreach($dealer as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->first_name.' '.$value->last_name }}
                                                </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Show All</label>
                                    <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks">
                                            <input type="checkbox" value="1" id="show_all" name="show_all"> Show All Dealer
                                        </label>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Display</label>
                                    <div class="col-sm-10">
                                        <input type="checkbox" class="js-switch_2" name="status" id="status"  value="1" checked="checked"/>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Upload Images</label>
                                    <div class="col-sm-10 dropzone" id="file_mediacategory">
                                        <div class="dropzone-previews"></div>
                                    </div>
                                    <input type="hidden" name="hidden_old_file_name" id="hidden_old_file_name" />
                                    <input type="hidden" name="hidden_file_name" id="hidden_file_name" />
                                    <input type="hidden" name="hidden_file_type" id="hidden_file_type" />
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="submit" class="btn btn-primary" value="Add"/>
                                        <button class="btn btn-white" type="button" id="btn_reset">Reset</button>
                                        <input name="media_uniqueID" type="hidden" id="media_uniqueID" value="{{  $media_uniqueID }}"/>
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

            <!-- Switchery -->
            <script src="{{ asset('assets/js/plugins/switchery/switchery.js') }}"></script>

            <!-- Chosen -->
            <script src="{{ asset('assets/js/plugins/chosen/chosen.jquery.js') }}"></script>

            <!-- iCheck -->
            <script src="{{ asset('assets/js/plugins/iCheck/icheck.min.js') }}"></script>

            <!-- DROPZONE -->
            <script src="{{ asset('assets/js/plugins/dropzone/dropzone.js') }}"></script>

            <script type="text/javascript">
                $(function ()
                {
                    $('.i-checks').iCheck({
                        checkboxClass: 'icheckbox_square-green',
                        radioClass: 'iradio_square-green',
                    });

                    var status = document.querySelector('#status');
                    var switchery_2 = new Switchery(status, { color: '#1AB394' });

                    status.onchange = function() {
                        if((status.checked == 'true') || (status.checked == true)){
                            $('#status').val(1);
                        }else{
                            $('#status').val(0);
                        }
                    };


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

                    <?php  if(isset($dealerID) && !empty($dealerID))
                    {
                        foreach($dealerID as $key => $value)
                        {
                            ?>
                                console.log(<?php echo $value;?>);
                                $('.chosen-select').val(<?php echo $value;?>).end().trigger('chosen:updated');
                    <?php } } ?>

                   {{-- $('#dealerID').change(function () {
                        console.log($('#dealerID').val());
                    });--}}

                    $("#dealerID").chosen().change(function ()
                    {
                        var total_option = $('select#dealerID option').length;
                        var all_option = total_option;
                        total_option = (total_option -1);

                        var total_selected  = 0;
                        var all = jQuery.inArray( "all", $(this).val() );
                        selected_ar = $(this).val();
                        if((selected_ar != undefined) || (selected_ar != null))
                        {
                            total_selected = selected_ar.length;
                        }else{
                            total_selected = 0;
                        }

                        if(all >= 0)
                        {
                            $('.chosen-select').val('all').end().trigger('chosen:updated');
                            $('option').prop('selected', true);
                            $("#dealerID").trigger("chosen:updated");
                            console.log($(this).val());

                        }
                    });

                    $('#btn_reset').click(function ()
                    {
                        $('input[type="text"]').each(function(){
                            $(this).val('');
                        });

                        window.location.reload();
                    });

                    $('#mediacategory_add').formValidation({
                        framework: 'bootstrap',
                        excluded: ':disabled',
                        message: 'This value is not valid',
                        icon: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                        },
                        fields: {
                            name: {
                                validators: {
                                    notEmpty: {
                                        message: 'Enter Media Category Name'
                                    },
                                    stringLength: {
                                        min: 3,
                                        max: 100,
                                        message: 'The Field must be more than 3 characters long'
                                    },
                                    regexp: {
                                        regexp: /^[a-z0-9\s]+$/i,
                                        message: 'This Field can consist of alphabetical characters and spaces only'
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
                    $('#hidden_file_name').val('');
                    Dropzone.autoDiscover = false;
                    var file_mediacategory_names = [];
                    var hidden_file_mediacategory_old = [];
                    var hidden_file_mediacategory = [];
                    var hidden_file_mediacategory_extension = [];
                    var uniqueNames = [];
                    var i = 0;
                    var n = 0;
                    var jk = 0;

                    var myDropzone = new Dropzone("div#file_mediacategory",
                    {
                        url: "{{ URL::to('/admin/mediafileadd/') }}",
                        method : "post",
                        paramName: "note_file",
                        params :{ _token: $('#hidden_token_file').val() },
                        previewsContainer: ".dropzone-previews",
                        uploadMultiple: true,
                        parallelUploads: 1,
                        maxFiles: 1,
                        dictMaxFilesExceeded: "You can only upload upto 1 images",
                        addRemoveLinks: true,
                        dictRemoveFile: "Delete",
                        acceptedFiles: ".jpg, .jpeg, .png",
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
                                    hidden_file_mediacategory_old.push(json_obj_file[i].oldname);
                                    hidden_file_mediacategory.push(json_obj_file[i].newname);
                                    hidden_file_mediacategory_extension.push(json_obj_file[i].file_type);

                                    file_mediacategory_names[n] = {"serverFileName": json_obj_file[i].newname, "fileName": json_obj_file[i].oldname, "fileId": n};
                                    n++;
                                    no++;
                                }
                                jk++;

                                //new file names
                                var uniqueNames = [];
                                $.each(hidden_file_mediacategory, function(i, el){
                                    if($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
                                });
                                hidden_file_mediacategory = uniqueNames;

                                var file_name_string = hidden_file_mediacategory.join(",");
                                $('#hidden_file_name').val(file_name_string);

                                //file extension
                                var uniqueNames_extension = [];
                                $.each(hidden_file_mediacategory_extension, function(i, el){
                                    if($.inArray(el, uniqueNames_extension) === -1) uniqueNames_extension.push(el);
                                });
                                hidden_file_mediacategory_extension = uniqueNames_extension;

                                var file_name_string_extension = hidden_file_mediacategory_extension.join(",");
                                $('#hidden_file_type').val(file_name_string_extension);

                                //old file name
                                var uniqueNamesOld = [];
                                $.each(hidden_file_mediacategory_old, function(i, el){
                                    if($.inArray(el, uniqueNamesOld) === -1) uniqueNamesOld.push(el);
                                });
                                hidden_file_mediacategory_old = uniqueNamesOld;

                                var file_name_string = hidden_file_mediacategory_old.join(",");
                                $('#hidden_old_file_name').val(file_name_string);
                            });

                            this.on("removedfile", function(file)
                            {
                                var rmvFile = "";
                                for(var f=0; f<file_mediacategory_names.length; f++)
                                {

                                    if(file_mediacategory_names[f].fileName == file.name)
                                    {
                                        rmvFile = file_mediacategory_names[f].serverFileName;
                                    }
                                }
                                if (rmvFile)
                                {
                                    hidden_file_mediacategory = jQuery.grep(hidden_file_mediacategory, function( a ) {
                                        return a !== rmvFile;
                                    });
                                    var file_name_string = hidden_file_mediacategory.join(",");
                                    $('#hidden_file_name').val(file_name_string);
                                    $('#hidden_old_file_name').val(file_name_string);
                                    $.post("{{ URL::to('/admin/mediafileremove/') }}", {
                                        file: rmvFile, _token:$('#hidden_token_file').val()
                                    });
                                }
                            });
                        }
                    });
                });
            </script>
        @stop()