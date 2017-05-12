

<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
    <style>
        .btn_status{
            border: none;
            color: #ffffff;
        }

        .btn_status:hover{
            opacity: 0.7;
            color: #ffffff;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Task List</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/employee')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/employee/task')); ?>">All Task</a>
                    </li>
                    <li class="active">
                        <strong>All Task list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Task Info</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="<?php echo e(URL::to('/employee/taskadd')); ?>"  style="background-color: #18A689;">
                                    <i class="fa fa-users"></i> Add Task
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="tabs-container">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab_pending">Pending (<?php echo e($total_pending_task); ?>)</a></li>
                                    <li class=""><a data-toggle="tab" href="#tab_complete">Complete (<?php echo e($total_complete_task); ?>)</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="tab_pending" class="tab-pane active">
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <div class="col-md-12" style="padding-left: 0;">
                                                    <button id="btn_allcomplete" class="btn btn-w-m btn-info">All Complete</button>
                                                </div>

                                                <table class="table table-striped table-bordered table-hover dataTables-example" id="task_pending" >
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 30px;" class="text-center"><input type="checkbox"></th>
                                                        <th style="min-width: 130px;">Assigned By</th>
                                                        <th>Priority</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Assign Date</th>
                                                        <th>Completion Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if(isset($task_list_pending) && !empty($task_list_pending)): ?>
                                                        <?php foreach($task_list_pending as $key => $value): ?>
                                                            <?php if($value->task_status != 'COMPLETE'): ?>
                                                            <?php
                                                            $taks_assigned_by = '';
                                                            $role_create = $role_create_class = '';
                                                            if($value->create_role == 'staff'){
                                                                $role_create = 'S';
                                                                $role_create_class = 'badge-warning';
                                                                $select = "`staff` WHERE `id` = '".$value->staff_id."' LIMIT 1";
                                                            }
                                                            if($value->create_role == 'employee'){
                                                                $role_create = 'E';
                                                                $role_create_class = 'badge-info';
                                                                $select = "`employee` WHERE `id` = '".$value->staff_id."' LIMIT 1";
                                                            }

                                                            $result_create = DB::table(DB::raw($select))->get();
                                                            $create_name = '';
                                                            if(!empty($result_create))
                                                            {
                                                                $create_name = $result_create[0]->first_name.' '.$result_create[0]->last_name;
                                                                $taks_assigned_by = $create_name;
                                                            }else{
                                                                $taks_assigned_by =  '---';
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="checkbox" value="<?php echo e($value->task_id); ?>" name="row_checkbox">
                                                                </td>
                                                                <td><?php if($taks_assigned_by != '---' || $taks_assigned_by != ''): ?><span class="badge <?php echo e($role_create_class); ?>"><?php echo e($role_create); ?></span><?php endif; ?> <?php echo e($taks_assigned_by); ?></td>
                                                                <td>
                                                                    <?php if($value->task_priority != ''): ?>
                                                                        <label class="label" style="color: #fff;background-color:<?php echo e($priority_status_color[$value->task_priority]); ?> ">
                                                                            <?php echo e($value->task_priority); ?>

                                                                        </label>
                                                                    <?php else: ?>
                                                                        ---
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo e($value->title); ?></td>
                                                                <td><?php echo e(\Illuminate\Support\Str::limit($value->description, $limit = 25, $end = '...')); ?></td>
                                                                <td><?php echo e(date('d-m-Y',strtotime($value->assign_date))); ?></td>
                                                                <td><?php echo e(date('d-m-Y',strtotime($value->completion_date))); ?></td>
                                                                <td>
                                                                    <?php if($value->task_status != ''): ?>
                                                                        <?php
                                                                        $status = $value->task_status;
                                                                        $task_status_name = $task_status[$status];
                                                                        $task_status_bg = $task_status_color[$status];

                                                                        $style = 'background-color:'.$task_status_bg.';';
                                                                        ?>
                                                                        <button class="btn btn-w-m btn-xs btn_status" type="button" style="<?php echo e($style); ?>">
                                                                            <?php echo e($task_status_name); ?>

                                                                        </button>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $url = URL::to('employee/edit-task', $value->task_id);
                                                                    $html = "";
                                                                    $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";
                                                                    $html.= " ";
                                                                    $deleted = (string)$value->task_id;
                                                                    $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('$deleted')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash-o\"></i></a>";
                                                                    echo $html;
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab_complete" class="tab-pane">
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover dataTables-example" id="task_complete" >
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 130px;">Assigned By</th>
                                                        <th>Priority</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Assign Date</th>
                                                        <th>Completion Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if(isset($task_list_complete) && !empty($task_list_complete)): ?>
                                                        <?php foreach($task_list_complete as $key => $value): ?>
                                                            <?php if($value->task_status == 'COMPLETE'): ?>
                                                            <?php
                                                            $taks_assigned_by = '';
                                                            $role_create = $role_create_class = '';
                                                            if($value->create_role == 'staff'){
                                                                $role_create = 'S';
                                                                $role_create_class = 'badge-warning';
                                                                $select = "`staff` WHERE `id` = '".$value->staff_id."' LIMIT 1";
                                                            }
                                                            if($value->create_role == 'employee'){
                                                                $role_create = 'E';
                                                                $role_create_class = 'badge-info';
                                                                $select = "`employee` WHERE `id` = '".$value->staff_id."' LIMIT 1";
                                                            }

                                                            $result_create = DB::table(DB::raw($select))->get();
                                                            $create_name = '';
                                                            if(!empty($result_create))
                                                            {
                                                                $create_name = $result_create[0]->first_name.' '.$result_create[0]->last_name;
                                                                $taks_assigned_by = $create_name;
                                                            }else{
                                                                $taks_assigned_by =  '---';
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><?php if($taks_assigned_by != '---' || $taks_assigned_by != ''): ?><span class="badge <?php echo e($role_create_class); ?>"><?php echo e($role_create); ?></span><?php endif; ?> <?php echo e($taks_assigned_by); ?></td>
                                                                <td>
                                                                    <?php if($value->task_priority != ''): ?>
                                                                        <label class="label" style="color: #fff;background-color:<?php echo e($priority_status_color[$value->task_priority]); ?> ">
                                                                            <?php echo e($value->task_priority); ?>

                                                                        </label>
                                                                    <?php else: ?>
                                                                        ---
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo e($value->title); ?></td>
                                                                <td><?php echo e(\Illuminate\Support\Str::limit($value->description, $limit = 25, $end = '...')); ?></td>
                                                                <td><?php echo e(date('d-m-Y',strtotime($value->assign_date))); ?></td>
                                                                <td><?php echo e(date('d-m-Y',strtotime($value->completion_date))); ?></td>
                                                                <td>
                                                                    <?php if($value->task_status != ''): ?>
                                                                        <?php
                                                                        $status = $value->task_status;
                                                                        $task_status_name = $task_status[$status];
                                                                        $task_status_bg = $task_status_color[$status];

                                                                        $style = 'background-color:'.$task_status_bg.';';
                                                                        ?>
                                                                        <button class="btn btn-w-m btn-xs btn_status" type="button" style="<?php echo e($style); ?>">
                                                                            <?php echo e($task_status_name); ?>

                                                                        </button>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $url = URL::to('employee/edit-task', $value->task_id);
                                                                    $html = "";
                                                                    $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";
                                                                    $html.= " ";
                                                                    $deleted = (string)$value->task_id;
                                                                    $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('$deleted')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash-o\"></i></a>";
                                                                    echo $html;
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <?php endif; ?>
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
                    </div>
                </div>
            </div>
        </div>
        <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>
		<?php $__env->stopSection(); ?>

        <?php $__env->startSection('pagescript'); ?>
            <?php echo $__env->make('employee.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>
            <script  src="<?php echo e(asset('assets/js/plugins/iCheck/icheck.min.js')); ?>"></script>

            <script type="text/javascript">
                deletedata = null;
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

                    var task_table = $('.table').DataTable({
                        "aoColumnDefs": [ { "bSortable": true, "aTargets": [ 1,5 ] } ],
                        "iDisplayLength": 20,
                        "aLengthMenu": [[10, 20, 30, 50, 100, 200, 500, -1], [10, 20, 30, 50, 100, 200, 500, "All"]],
                    });


                    var chk_Array = [];

                    $('input:checkbox').removeAttr('checked');

                    $('#task_pending thead input:checkbox').click(function() {
                        var checkedStatus   = $(this).prop('checked');
                        var table           = $(this).closest('table');

                        if(this.checked){
                            $('tbody input[name="row_checkbox"]:checkbox', table).each(function() {
                                var val = $('tbody input:checkbox').val();
                                chk_Array.push(val);
                                //$(this).prop('checked', checkedStatus);
                                this.checked = true;
                            });
                        }else {
                            chk_Array = [];
                            $('tbody input[name="row_checkbox"]:checkbox', table).each(function () {
                                this.checked = false;
                            })
                        }
                    });

                    $('#task_pending tbody input[name="row_checkbox"]:checkbox').on('click',function(){
                        if($('tbody input[name="row_checkbox"]:checkbox:checked').length == $('tbody input[name="row_checkbox"]:checkbox').length){
                            var val = $('tbody input:checkbox').val();
                            chk_Array.push(val);
                            $('thead input:checkbox').prop('checked',true);
                            //alert(chk_Array);
                        }else{
                            chk_Array = [];
                            $('thead input:checkbox').prop('checked',false);
                        }
                    });


                    function allApprove(val_status)
                    {
                        var tokendata = $('#hidden_token').val();
                        chk_Array = [];
                        $('#task_pending tbody input[name="row_checkbox"]:checkbox:checked').each(function(){
                            chk_Array.push($(this).val());
                        });

                        if(chk_Array.length != 0){
                            $.ajax({
                                type:'POST',
                                url: "<?php echo e(URL::to('employee/ajax/log/taskstatuschange')); ?>",
                                data: { _token:tokendata, data_ID: chk_Array, data_status:val_status },
                                success: function (result)
                                {
                                    swal("Selected Pending Task is Completed");
                                    setTimeout(function (){
                                        window.location.reload();
                                    },2000);
                                }
                            });
                        }else{
                            swal("Select", "Please Check Checkbox :)", "error");
                        }
                    }


                    $('#btn_allcomplete').click(function (){
                        allApprove('COMPLETE');
                    });

                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                                    title: "Are you sure?",
                                    text: "You will not be able to recover this Task related data",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Yes, delete task!",
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
                                            url: "<?php echo e(URL::to('employee/ajax/log/taskdelete')); ?>",
                                            data: {	task_id:id, _token:tokendata },
                                            success: function (result) {
                                                swal("Deleted!", "Your Task has been deleted.", "success");
                                                setTimeout(function (){
                                                    window.location.reload();
                                                },3000);
                                            }
                                        });
                                    } else {
                                        swal("Cancelled", "Your Task is safe :)", "error");
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
<?php echo $__env->make('employee.layouts.masteremployee', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>