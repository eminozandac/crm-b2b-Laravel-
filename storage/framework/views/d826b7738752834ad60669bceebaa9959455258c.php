

<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Employee list</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/admin')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/admin/employeelist')); ?>">All Employee</a>
                    </li>
                    <li class="active">
                        <strong>All Employee list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Employee List Data</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="<?php echo e(URL::to('/admin/employeeadd')); ?>"  style="background-color: #18A689;">
                                    <i class="fa fa-users">&nbsp;&nbsp;</i>Add Employee
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="employee_table" >
                                    <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Active</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input name="_token" type="hidden" id="hidden_token_new" value="<?php echo e(csrf_token()); ?>"/>
		<?php $__env->stopSection(); ?>

        <?php $__env->startSection('pagescript'); ?>
            <?php echo $__env->make('admin.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>

            <script type="text/javascript">
                employeedata_delete = null;
                $(function (){

                    var employee_table = $('#employee_table').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
                        buttons: [
                            /* {extend: 'csv', title:'Customer Details'},
                            {extend: 'excel', title:'Customer Details'},
                            {extend: 'pdf', title:'Customer Details'}, */
                        ],
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "<?php echo e(URL::to('admin/ajax/log/employeedata')); ?>",
                            data:function(d){  }
                        },
                        "columns": [
                            {data: 'Employee', name: 'first_name'},
                            {data: 'Email', name: 'emailID'},
                            {data: 'Phone', name: 'phone'},
                            {data: 'Active', name: 'status', orderable: false, searchable: false},
                            {data: 'Action', name: 'Action', orderable: false, searchable: false},
                        ]
                    });

                    function employeedatadelete(val)
                    {
                        var tokendata = $('#hidden_token_new').val();
                        var noID = '#no_'+val+ '  i';
                        $(noID).addClass('fa fa-spinner fa-spin');
                        console.log(val);

                        swal({
                            title: "Are you sure?",
                            text: "Delete This Employee User??",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm){
                            if (isConfirm)
                            {
                                $.ajax
                                ({
                                    type: "POST",
                                    url: "<?php echo e(URL::to('admin/ajax/log/employeedelete')); ?>",
                                    data: { from_employee_data:val, _token:tokendata },
                                    cache: false,
                                    success: function (result)
                                    {
                                        employee_table.draw();
                                    }
                                });
                            }
                        });
                        employee_table.draw();
                    }
                    employeedata_delete = employeedatadelete;

                });

                function deleteEmployee(val)
                {
                    //val for user_id
                    if(val != '') {
                        employeedata_delete(val);
                    }
                }
            </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('admin/layouts/masteradmin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>