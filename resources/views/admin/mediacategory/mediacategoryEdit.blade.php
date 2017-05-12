@extends('admin/layouts/masteradmin')

@section('pagecss')
    <link href="{{asset('assets/css/plugins/switchery/switchery.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
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
                <h2>Edit Media Category</h2>
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
                            <h5>Edit Media Category <small>Category</small></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="mediacategory_edit" name="mediacategory_edit" method="POST" action="{{ action('admin\MediaCategoryController@editData') }}" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">

                                <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>
                                <input name="media_uniqueID" type="hidden" id="media_uniqueID" value="{{  $mediacategory->media_uniqueID }}"/>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Category name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="jack" value="{{ $mediacategory->name }}">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Assign Dealer</label>
                                    <div class="col-sm-10">
                                        <select data-placeholder="Choose a Dealer..." class="chosen-select" multiple style="width: 100%" tabindex="4" name="dealerID[]" id="dealerID">
                                            @if( isset($dealer) && !empty($dealer) )
                                                @foreach($dealer as $key => $value)
                                                    <?php
                                                        $dealer_ID = $mediacategory->dealer_id;
                                                        $selected = '';
                                                        if(in_array($value->id, explode(',', $dealer_ID)) )
                                                        {
                                                            $selected = 'selected';
                                                        }
                                                    ?>
                                                    <option value="{{ $value->id }}" {{ $selected }}>
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
                                        <?php
                                            $dealer_ID = $mediacategory->dealer_id;
                                            $checked = '';
                                            if($dealer_ID == 'all'){
                                                $checked = 'checked';
                                            }
                                        ?>
                                        <label class="checkbox-inline i-checks">
                                            <input type="checkbox" value="1" id="show_all" name="show_all" {{ $checked }} > Show All Dealer
                                        </label>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Display</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $checked = '';
                                        if($mediacategory->status == 1){
                                            $checked = 'checked';
                                        }
                                        ?>
                                        <input type="checkbox" class="js-switch_2" name="status" id="status" value="{{ $mediacategory->status }}" <?php echo $checked; ?> />
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Upload Images</label>
                                    <div class="col-sm-10 dropzone" id="file_mediacategory">
                                        <div class="dropzone-previews"></div>
                                    </div>
                                    <input type="hidden" name="hidden_old_file_name" id="hidden_old_file_name" value="{{ $mediacategory->old_file_name }}"/>
                                    <input type="hidden" name="hidden_file_name" id="hidden_file_name" value="{{ $mediacategory->file_name }}"/>
                                    <input type="hidden" name="hidden_file_type" id="hidden_file_type" />
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="submit" class="btn btn-primary" value="Update"/>
                                        <button class="btn btn-white" type="button" id="btn_reset">Reset</button>
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

                    $("#dealerID").chosen().change(function ()
                    {
                        var total_option = $('select#dealerID option').length;
                        var all_option = total_option;
                        total_option = (total_option -1);

                        var total_selected  = 0;
                        var selected_ar = 0;
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
                            console.log('hii');
                            $('.chosen-select').val('all').end().trigger('chosen:updated');
                            $('option').prop('selected', true);
                            $("#dealerID").trigger("chosen:updated");
                            console.log($("#dealerID").val());
                        }

                    });


                    $('#btn_reset').click(function ()
                    {
                        $('input[type="text"]').each(function(){
                            $(this).val('');
                        });

                        window.location.reload();
                    });

                    $('#mediacategory_edit').formValidation(
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
                var media_uniqueID = $('#media_uniqueID').val();
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
                            if(file_mediacategory_names.length != 0)
                            {
                                for(var f=0; f<file_mediacategory_names.length; f++)
                                {

                                    if(file_mediacategory_names[f].fileName == file.name)
                                    {
                                        rmvFile = file_mediacategory_names[f].serverFileName;
                                    }
                                }
                            }else{
                                rmvFile = file.name;
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

                var hidden_token_file = $("#hidden_token_file").val();
                $.post("{{ URL::to('/admin/getcategoryimages/') }}", { data_media_uniqueID : media_uniqueID, _token: hidden_token_file },
                function(data)
                {
                    var json_obj_file = jQuery.parseJSON(data);
                    var no = 0;
                    var dwn = 0;
                    for (var i in json_obj_file)
                    {
                        no++;

                        var mockFile = { name: json_obj_file[i].name, size: json_obj_file[i].size };

                        hidden_file_mediacategory.push(json_obj_file[i].name);

                        myDropzone.options.addedfile.call(myDropzone, mockFile);

                        myDropzone.options.thumbnail.call(myDropzone, mockFile, json_obj_file[i].url);

                        if(dwn == i)
                        {
                            /*var a = '';
                            var id_a = "id=bt-down_"+no;
                            a = "<a download class=\"btn\" "+id_a+" style=\"width:100%; cursor:pointer;\" href=\""+json_obj_file[i].url+"\" title=\"Download\" data-dz-download><i class=\"fa fa-download\"></i></a>"
                            var data = $('.dropzone-previews div.dz-image-preview:nth-child('+no+')').attr("data-download");
                            if(data == undefined)
                            {
                                $('.dropzone-previews div.dz-image-preview:nth-child('+no+')').attr("data-download", no);
                                $('.dropzone-previews div.dz-image-preview:nth-child('+no+')').append(a);
                            }
                            a = '';*/
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
        @stop()