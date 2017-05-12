<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/switchery/switchery.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/dropzone/basic.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/dropzone/dropzone.css')); ?>" rel="stylesheet">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Add Media Category File</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/admin/mediacategorylist')); ?>">All Media Category List</a>
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
                                <a class="btn btn-w-m btn-primary" href="<?php echo e(URL::to('/admin/mediacategoryfile')); ?>" style="background-color: #18A689;">
                                    <i class="fa fa-medium"></i> All Media File
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="mediacategory_add" name="mediacategory_add" method="POST" action="<?php echo e(action('admin\MediaCategoryFileController@saveData')); ?>" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">
                                <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>

                                <?php
                                function nestedTree($id,$nested='')
                                {
                                    $dash = '';
                                    $no = 0;
                                    $html = '';
                                    $select_query = "SELECT t1.id AS level1_ID, t1.name AS level1, t2.id AS level2_ID, t2.media_uniqueID AS level2_media_uniqueID, t2.name as level2
                                        FROM media_category AS t1  LEFT JOIN media_category AS t2 ON t2.parent_id = t1.id  WHERE t1.id = ".$id." ORDER BY t1.id ASC ";

                                    $result_mediacategory_tree = DB::select($select_query);
                                    if(!empty($result_mediacategory_tree))
                                    {
                                        foreach($result_mediacategory_tree as $key_tree => $value_tree)
                                        {
                                            if(($value_tree->level2_ID != '') && ($value_tree->level2_ID != null))
                                            {
                                                $nbsp = '&nbsp;';
                                                if($nested == '')
                                                {
                                                    $dash = $nbsp.'-';
                                                    $nested = $dash;
                                                }else
                                                {
                                                    $nested = $nested.'-';
                                                    $dash = $nbsp.$nested.$nbsp;
                                                }

                                                $html.= '<option value="'.$value_tree->level2_media_uniqueID.'">';
                                                    $html.= $dash.$value_tree->level2;
                                                $html.= '</option>';

                                                $nested_tree = nestedTree($value_tree->level2_ID,$nested);
                                                if($nested_tree != 'false' || $nested_tree != false)
                                                {
                                                    $html.= $nested_tree;
                                                    $dash = '';
                                                    $nested = '';
                                                }
                                            }
                                        }

                                        if($html != '')
                                        {
                                            return $html;
                                        }
                                    }else{
                                        $html = '';
                                        return 'false';
                                    }
                                }
                                ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Category name</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="media_uniqueID" name="media_uniqueID" required>
                                            <option value="">---</option>
                                            <?php if(!empty($parent_category)): ?>
                                                <?php foreach($parent_category as $key => $value): ?>
                                                    <option value="<?php echo e($value->media_uniqueID); ?>"><?php echo e($value->name); ?></option>
                                                    <?php
                                                    if($value->id != '')
                                                    {
                                                        $tree = nestedTree($value->id);
                                                        if(($tree != 'false') || ($tree != false))
                                                        {
                                                            echo $tree;
                                                        }
                                                    }
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
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
                                        <input type="hidden" name="media_file_uniqueID" id="media_file_uniqueID" value="<?php echo e($media_file_uniqueID); ?>" />
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input name="_token" type="hidden" id="hidden_token_file" value="<?php echo e(csrf_token()); ?>"/>
		<?php $__env->stopSection(); ?>

        <?php $__env->startSection('pagescript'); ?>
            <?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <!-- Switchery -->
            <script src="<?php echo e(asset('assets/js/plugins/switchery/switchery.js')); ?>"></script>

            <script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>

            <!-- DROPZONE -->
            <script src="<?php echo e(asset('assets/js/plugins/dropzone/dropzone.js')); ?>"></script>

            <!-- Data picker -->
            <script src="<?php echo e(asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')); ?>"></script>


            <script type="text/javascript">
                $(function ()
                {

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
                            media_uniqueID: {
                                validators: {
                                    notEmpty: {
                                        message: 'Select Media Category Name'
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
                    url: "<?php echo e(URL::to('/admin/mediafileadd/')); ?>",
                    method : "post",
                    paramName: "note_file",
                    params :{ _token: $('#hidden_token_file').val() },
                    previewsContainer: ".dropzone-previews",
                    uploadMultiple: true,
                    parallelUploads: 50,
                    maxFiles: 50,
                    maxFilesize: 3072, // MB
                    dictMaxFilesExceeded: "You can only upload upto 50 images",
                    addRemoveLinks: true,
                    dictRemoveFile: "Delete",
                    acceptedFiles: ".jpg, .jpeg, .png, .pdf, .txt, .doc, .docx, .xl, .xlc, .xls, .xlsx, .ppt, .pptx, .potx, .gif, .mp3, .mp4, .m4p, .m4v, .webm, .wmv, .mkv, .mov, .qt, .3gp, .flv, .avi, .asf, .mpeg",
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
                                $.post("<?php echo e(URL::to('/admin/mediafileremove/')); ?>", {
                                    file: rmvFile, _token:$('#hidden_token_file').val()
                                });
                            }
                        });
                    }
                });
            });
        </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin/layouts/masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>