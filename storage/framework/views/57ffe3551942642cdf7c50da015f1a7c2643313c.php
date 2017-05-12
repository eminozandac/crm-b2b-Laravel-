

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Media Sub Category list</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/admin/mediacategorysublist')); ?>">All Media Sub Category</a>
                    </li>
                    <li class="active">
                        <strong>All Media Sub Category list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Media Sub Category List Data</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="<?php echo e(URL::to('/admin/mediasubcategoryadd')); ?>"  style="background-color: #18A689;">
                                    <i class="fa fa-medium"></i> Add Media Sub Category
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="data_result_table" >
                                    <thead>
                                    <tr>
                                        <th>Parent Name</th>
                                        <th>Name</th>
                                        <th>Total File</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($category_list)): ?>
                                            <?php foreach($category_list as $key => $value): ?>
                                                <?php
                                                    $count = DB::table('media_category_file')->where('media_uniqueID','=',$value->media_uniqueID)->count();
                                                    $url_edit = URL::to('admin/edit-mediasubcategory', $value->media_uniqueID);
                                                    $deleted = $value->media_uniqueID;

                                                    $parent_name = '';
                                                    $result = DB::table('media_category')->where('id','=',$value->parent_id)->first();
                                                    if(!empty($result)){
                                                        $parent_name = $result->name;
                                                    }
                                                ?>
                                                <tr>
                                                    <td><?php echo e($parent_name); ?></td>
                                                    <td><?php echo e($value->name); ?></td>
                                                    <td><?php echo e($count); ?></td>
                                                    <td>
                                                        <a href="<?php echo e($url_edit); ?>"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                                                        &nbsp;
                                                        <a href="javascript:void(0);"  data-toggle="tooltip" title="Delete" onclick="deleted('<?php echo e($deleted); ?>')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>
		<?php $__env->stopSection(); ?>

        <?php $__env->startSection('pagescript'); ?>
            <?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>

            <script type="text/javascript">
                deletedata = null;
                $(function (){

                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this media category related data",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Delete",
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
                                    url: "<?php echo e(URL::to('admin/ajax/log/mediasubcategorydelete')); ?>",
                                    data: {	media_uniqueID:id, _token:tokendata },
                                    success: function (result) {
                                        swal("Deleted!", "Your Media Category has been deleted.", "success");
                                        setTimeout(function (){
                                            window.location.reload();
                                        },3000);
                                    }
                                });
                            } else {
                                swal("Cancelled", "Your Media Category Name is safe :)", "error");
                            }
                        });
                    }
                    deletedata = deleted;

                });

                function deleted(id){
                    if(id != 0 && id != ''){
                        deletedata(id);
                    }
                }
            </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin/layouts/masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>