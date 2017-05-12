<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Database Backup List</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/admin/databasebackup')); ?>">All Group</a>
                    </li>
                    <li class="active">
                        <strong>All Database Backup list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Database Backup</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="<?php echo e(URL::to('creatbackup')); ?>"  style="background-color: #18A689;">
                                    <i class="fa fa-hdd-o"></i> Create Database Backup
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="database_table" >
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                           $no = 0;
                                        ?>
                                        <?php if(!empty($backup_fileNm)): ?>
                                            <?php foreach($backup_fileNm as $key => $value): ?>
                                                <?php
                                                    $no++;
                                                    $created_date = '';
                                                    $filename = 'db_backup/'.$value;
                                                    if (file_exists($filename))
                                                    {
                                                        $created_date = date ("d-m-Y h:i:s", filemtime($filename));
                                                    }
                                                    $import = 'importbackup/'.$value;
                                                ?>
                                                <tr>
                                                    <td><?php echo e($value); ?></td>
                                                    <td><?php echo e($created_date); ?></td>
                                                    <td>
                                                        <?php /*<a href="<?php echo e($filename); ?>"  data-toggle="tooltip" title="Download" class="btn btn-xs btn-success" download="">
                                                            <i class="fa fa-download"></i> Download
                                                        </a>*/ ?>
                                                        <?php /*<a href="<?php echo e(URL::to($import)); ?>" style="display: none;" id="import_<?php echo e($no); ?>"></a>*/ ?>
                                                        &nbsp;&nbsp;
                                                        <?php /*<a href="javascript:void(0)"  data-toggle="tooltip" title="Import" onclick="importdatabase('<?php echo e($no); ?>')" class="btn btn-xs btn-warning">
                                                            <i class="fa fa-recycle"></i> Import
                                                        </a>*/ ?>
                                                        &nbsp;&nbsp;
                                                        <a href="javascript:void(0)"  data-toggle="tooltip" title="Delete" onclick="deletedatabase('<?php echo e($filename); ?>')" class="btn btn-xs btn-danger">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
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
                importdata = null;
                $(function (){

                    var database_table = $('#database_table').DataTable();


                    function importdatabase(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover last data in database",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, Import Database This!",
                            cancelButtonText: "No, cancel!",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function(isConfirm){
                            if (isConfirm)
                            {
                                var idValue = '#import_'+id;
                                var urlValue = $(idValue).attr('href');
                                console.log(urlValue);
                                window.location = urlValue;
                                //$(idValue).trigger('click');

                            } else {
                                swal("Cancelled", "Your Database is safe :)", "error");
                            }
                        });
                    }
                    importdata = importdatabase;

                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this database",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete This!",
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
                                    url: "<?php echo e(URL::to('deletebackup')); ?>",
                                    data: {	data_filename:id, _token:tokendata },
                                    success: function (result) {
                                        swal("Deleted!", "Your Back Database has been deleted.", "success");
                                        setTimeout(function (){
                                            window.location.reload();
                                        },3000);
                                    }
                                });
                            } else {
                                swal("Cancelled", "Your Database is safe :)", "error");
                            }
                        });
                    }
                    deletedata = deleted;

                });

                function deletedatabase(id){
                    if(id != 0 && id != ''){
                        deletedata(id);
                    }
                }

                function importdatabase(id){
                    if(id != 0 && id != ''){
                        importdata(id);
                    }
                }

            </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin/layouts/masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>