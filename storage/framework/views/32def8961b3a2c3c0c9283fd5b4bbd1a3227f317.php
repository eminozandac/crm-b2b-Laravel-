<?php $__env->startSection('pagecss'); ?>
    <style>
        .nested_ul {
            padding-left: 10px;
        }
        .nested_ul li{
            border: none;
        }
        .nested_ul li ul{
            padding-left: 15px;
        }
        .nested_ul li ul li{
            border-bottom: 1px solid #e7eaec;
            display: block;
        }

        *{
            outline: none;
        }
        .file .icon, .file .image{
            height: 135px;
        }

        .lightBoxGallery img{
            margin: 0;
        }
        .span_images_height{
            line-height: 5.5rem;
        }
    </style>
    <link href="<?php echo e(asset('assets/css/plugins/blueimp/css/blueimp-gallery.min.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>
                    Media Category list
                    <div class="col-md-2 pull-right">
                        <a href="<?php echo e(URL::to('admin/mediacategoryfileadd')); ?>" class="btn btn-primary btn-block">Upload Files</a>
                    </div>
                </h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/admin/mediacategoryfile')); ?>">All Media Category</a>
                    </li>
                    <?php
                        function parentName($id)
                        {
                            $result_name = DB::table('media_category')->select('id','media_uniqueID','parent_id','name')->where('id','=',$id)->first();
                            $html = '';
                            if(!empty($result_name))
                            {
                                $name = $result_name->name;
                                $action_url = '/admin/mediacategoryfilelist/'.$result_name->id;
                                $parent_id = $result_name->parent_id;
                                if($parent_id != 0){
                                    $parent_tree= parentName($parent_id);
                                    echo $parent_tree;
                                }
                                $html.= '<li>';
                                    $html.= '<a href="'.URL::to($action_url).'">';
                                        $html.= '<strong>'.$name.'</strong>';
                                    $html.= '</a>';
                                $html.= '</li>';
                            }
                            return $html;
                        }
                    ?>
                    <?php if(isset($categoryID) && !empty($categoryID)): ?>
                        <?php
                            $name = '';
                            $action_url = '#';
                            $result_name = DB::table('media_category')->select('id','media_uniqueID','parent_id','name')->where('id','=',$categoryID)->first();
                            if(!empty($result_name))
                            {
                                $name = $result_name->name;
                                $action_url = '/admin/mediacategoryfilelist/'.$result_name->id;
                                $parent_id = $result_name->parent_id;
                                if($parent_id != 0){
                                  $parent_tree= parentName($parent_id);
                                    echo $parent_tree;
                                }
                            }
                        ?>
                        <li>
                            <a href="<?php echo e(URL::to($action_url)); ?>"><strong><?php echo e($name); ?></strong></a>
                        </li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
        <?php
            function countChildFile($media_id,$storecount=0)
            {
                $total_count = 0;
                $select_query = "SELECT t1.id AS level1_ID, t1.name AS level1, t2.id AS level2_ID, t2.media_uniqueID AS level2_media_uniqueID, t2.name as level2
                FROM media_category AS t1  LEFT JOIN media_category AS t2 ON t2.parent_id = t1.id  WHERE t1.media_uniqueID = '".$media_id."' ORDER BY t1.id ASC ";

                $result_mediacategory_tree = DB::select($select_query);
                if(!empty($result_mediacategory_tree))
                {
                    foreach($result_mediacategory_tree as $key_tree => $value_tree)
                    {
                        if(($value_tree->level2_ID != '') && ($value_tree->level2_ID != null))
                        {
                            $count = DB::table('media_category_file')->where('media_uniqueID','=',$value_tree->level2_media_uniqueID)->count();
                            if($count != 0)
                            {
                                $storecount = $storecount + $count;
                            }
                            $count_child = countChildFile($value_tree->level2_media_uniqueID,$storecount);
                            $storecount =  $count_child;
                        }
                    }
                }
                return $storecount;
            }
        ?>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12 animated fadeInRight">
                    <div class="row">
                        <div class="col-lg-12 lightBoxGallery">
                            <?php  $no = 0; ?>
                            <?php if(isset($parent_category) && !empty($parent_category) ): ?>
                                <?php foreach($parent_category as $key => $value): ?>
                                    <?php
                                        $no++;
                                        $action_url = '/admin/mediacategoryfilelist/'.$value->id;
                                        $count = 0;
                                        $count = DB::table('media_category_file')->where('media_uniqueID','=',$value->media_uniqueID)->count();
                                        $childcount = countChildFile($value->media_uniqueID,0);
                                        $count = $count + $childcount;
                                    ?>
                                    <div class="file-box" style="height: 260px;">
                                        <div class="file" >
                                            <a href="<?php echo e(URL::to($action_url)); ?>">
                                                <span class="corner"></span>
                                                <div class="icon">
                                                    <?php if($value->file_name != ''): ?>
                                                        <?php
                                                            $show_url = 'uploads/mediafile/thumb/'.$value->file_name;
                                                        ?>
                                                        <img alt="<?php echo e($value->name); ?>" class="img-responsive" src="<?php echo e(URL::to($show_url)); ?>">
                                                    <?php else: ?>
                                                        <i class="fa fa-file"></i>
                                                  <?php endif; ?>
                                                </div>
                                                <div class="file-name" style="padding: 5px;">
                                                    <span style="text-align: left;"><?php echo e($value->name); ?></span>
                                                    <br/>
                                                    <b class="pull-right">( <?php echo e($count); ?> )</b>
                                                    <br/>
                                                    <span class="span_images_height">&nbsp;</span>
                                                </div>

                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php

                                $image_file_type_ar = array('jpg','jpeg','png','JPG','JPEG','PNG');
                                $audio_file_type_ar = array('mp3');
                                $video_file_type_ar = array('mp4','3gp');
                                $document_file_type_ar = array('pdf','docs','txt');
                            ?>
                            <?php if(isset($medialFile) && !empty($medialFile) ): ?>
                                <?php foreach($medialFile as $key => $value): ?>
                                    <?php
                                        $no++;
                                        $show_url = 'uploads/mediafile/thumb/'.$value->file_name;
                                        $download_url = 'uploads/mediafile/'.$value->file_name;
                                        $original_filename = $value->old_file_name;
                                    ?>
                                    <div class="file-box">
                                        <div class="file">
                                            <span class="corner"></span>
                                             <?php if( (in_array($value->file_type,$video_file_type_ar)) || (in_array($value->file_type,$document_file_type_ar)) ): ?>
                                                <?php
                                                    $file_class = 'fa fa-file-text-o';
                                                    if($value->file_type == 'pdf')
                                                    {
                                                        $file_class = 'fa fa-file-pdf-o';
                                                    }else if(in_array($value->file_type,$audio_file_type_ar))
                                                    {
                                                        $file_class = 'fa fa-music';

                                                    }else if(in_array($value->file_type,$video_file_type_ar))
                                                    {
                                                        $file_class = 'fa fa-film';
                                                    }else{
                                                        $file_class = 'fa fa-file-text-o';
                                                    }
                                                ?>
                                                <div class="icon">
                                                    <i class="<?php echo e($file_class); ?>"></i>
                                                </div>
                                            <?php endif; ?>

                                            <?php if(in_array($value->file_type,$image_file_type_ar)): ?>
                                                <div class="icon ">
                                                    <a href="<?php echo e(URL::to($download_url)); ?>" title="<?php echo e($original_filename); ?>" data-gallery="">
                                                        <img alt="<?php echo e($original_filename); ?>" class="img-responsive" src="<?php echo e(URL::to($show_url)); ?>">
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <div class="file-name text-center">
                                                <?php echo e($original_filename); ?><br/><br/>
                                                <a download="" href="<?php echo e(URL::to($download_url)); ?>" class="btn btn-primary waves-effect waves-light" style="padding: 0.255rem 0.675rem;" data-toggle="tooltip" data-placement="top" title="DOWNLOAD" >
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <?php if(in_array($value->file_type,$image_file_type_ar)): ?>
                                                    <a href="<?php echo e(URL::to($download_url)); ?>" class="btn btn-info waves-effect waves-light"  style="padding: 0.255rem 0.675rem;" title="<?php echo e($original_filename); ?>" data-gallery="">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                  <?php else: ?>
                                                    <a target="_blank" href="<?php echo e(URL::to($download_url)); ?>" class="btn btn-info waves-effect waves-light" style="padding: 0.255rem 0.675rem;" data-toggle="tooltip" data-placement="top" title="VIEW" >
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <a class="btn btn-danger waves-effect waves-light" style="padding: 0.255rem 0.675rem;" data-toggle="tooltip" data-placement="top" title="DELETE" onclick="deleted('<?php echo e($value->media_file_uniqueID); ?>')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php endif; ?>

                            <?php if($no == 0): ?>
                                <div class="file-box">
                                    <div class="file">
                                        <a href="#">
                                            <span class="corner"></span>
                                            <div class="icon">
                                                <i class="fa fa-file"></i>
                                            </div>
                                            <div class="file-name">
                                                Not Any Media File Upload
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="blueimp-gallery" class="blueimp-gallery">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
            <a class="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
            <a class="close"><i class="fa fa-times" aria-hidden="true"></i></a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
        </div>

        <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>
		<?php $__env->stopSection(); ?>

        <?php $__env->startSection('pagescript'); ?>
            <?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>

            <!-- blueimp gallery -->
            <script src="<?php echo e(asset('assets/js/plugins/blueimp/jquery.blueimp-gallery.min.js')); ?>"></script>

            <script type="text/javascript">
                deletedata = null;
                $(function ()
                {
                    $('.file-box').each(function() {
                        animationHover(this, 'pulse');
                    });


                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this media file",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete this!",
                            cancelButtonText: "No, cancel!",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function(isConfirm){
                            if (isConfirm)
                            {
                                $.ajax
                                ({
                                    type: "POST",
                                    url: "<?php echo e(URL::to('admin/ajax/log/mediafiledelete')); ?>",
                                    data: {	media_file_uniqueID:id, _token:tokendata },
                                    success: function (result) {
                                        swal("Deleted!", "Your Sure medial file has been deleted.", "success");
                                        setTimeout(function (){
                                            window.location.reload();
                                        },3000);
                                    }
                                });
                            } else {
                                swal("Cancelled", "Your media file is safe :)", "error");
                            }
                        });
                    }
                    deletedata = deleted;
                });

                function deleted(id)
                {
                    if(id != 0 && id != ''){
                        deletedata(id);
                    }
                }
            </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin/layouts/masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>