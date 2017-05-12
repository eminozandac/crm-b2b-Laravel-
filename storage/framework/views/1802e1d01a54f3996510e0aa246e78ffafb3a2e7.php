<?php $__env->startSection('contentPages'); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Manage Warranty</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo e(URL::to('/employee')); ?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo e(URL::to('/employee/warranty')); ?>">All Warranty Report</a>
                    </li>
                    <li class="active">
                        <strong>All Warranty Report list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Warranty Report Info</h5>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="warranty_table" >
                                    <thead>
                                    <tr>
                                        <th style="min-width: 130px;">Assigned to Staff Member</th>
                                        <th style="min-width: 125px;">Role</th>
                                        <th>Customer Name</th>
                                        <th>Product Model</th>
                                        <th>Part Required</th>
                                        <th style="min-width: 200px;">Fault</th>
                                        <th>Status</th>
                                        <th style="min-width: 50px;">Date</th>
                                        <th style="min-width: 135px;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($warrantyList)): ?>
                                            <?php foreach($warrantyList as $key => $value): ?>
                                                <?php
                                                    $status = $status_color = '';
                                                    $config_status = 'warranty.WARRANTY_STATUS.'.$value->warranty_status;
                                                    $status = Config::get($config_status);

                                                    $config_status_color = 'warranty.WARRANTY_STATUS_COLOR.'.$value->warranty_status;
                                                    $status_color = 'color:#fff; background-color :'.Config::get($config_status_color);

                                                    $url_edit = "";
                                                    $url_edit = "/employee/warrantyedit/".$value->warranty_uniqueID;

                                                    $url_pdf = "";
                                                    $url_pdf = "/employee/warrantypdf/".$value->warranty_uniqueID;

                                                    $url_print = "";
                                                    $url_print = "/employee/warrantyprint/".$value->warranty_uniqueID;

                                                    $url_note = "";
                                                    $url_note = $value->warranty_uniqueID;

                                                    $staff_name = $name = $role_a = $role_class = '';

                                                    $role_assign = '-';
                                                    $role_assign_class = 'badge-danger';
                                                    if($value->warranty_assign != '')
                                                    {
                                                        $staffname = '';
                                                        $staffname_ar = array();
                                                        $staff_ar = explode(',',$value->warranty_assign);
                                                        foreach($staff_ar as $key_staffname => $value_staffname)
                                                        {
                                                            if($value->assign_role == 'staff')
                                                            {
                                                                $role_assign = 'S';
                                                                $role_assign_class = 'badge-warning';
                                                                $select = "`staff` WHERE `staff_id` = '".$value_staffname."' LIMIT 1";
                                                            }
                                                            if($value->assign_role == 'employee')
                                                            {
                                                                $role_assign = 'E';
                                                                $role_assign_class = 'badge-info';
                                                                $select = "`employee` WHERE `employee_id` = '".$value_staffname."' LIMIT 1";
                                                            }
                                                            $result_staff = DB::table(DB::raw($select))->get();
                                                            if(!empty($result_staff)){
                                                                $staffname = $result_staff[0]->first_name.' '.$result_staff[0]->last_name;
                                                                $staffname_ar[] = $staffname;
                                                            }
                                                        }
                                                        $staff_name = implode(',',$staffname_ar);
                                                    }else{
                                                        $role_assign = '-';
                                                        $role_assign_class = 'badge-danger';
                                                        $staff_name = '---';
                                                    }

                                                    if($value->role == 'dealer'){

                                                        $result = DB::table('dealer')->where('id',$value->user_id)->first();
                                                        if(!empty($result)){
                                                            $name = $result->first_name.' '.$result->last_name;
                                                        }else{
                                                            $name = '---';
                                                        }
                                                        $role_a = 'D';
                                                        $role_class = 'badge-primary';
                                                    }

                                                    if($value->role == 'customer')
                                                    {
                                                        $result = DB::table('customer')->where('id',$value->user_id)->first();
                                                        if(!empty($result))
                                                        {
                                                            $name = $result->first_name.' '.$result->last_name;
                                                        }else{
                                                            $name = '---';
                                                        }
                                                        $role_a = 'C';
                                                        $role_class = 'badge-info';
                                                    }

                                                ?>
                                                <tr>
                                                    <td><span class="badge <?php echo e($role_assign_class); ?>"><?php echo e($role_assign); ?></span> <?php echo e($staff_name); ?></td>
                                                    <td><span class="badge <?php echo e($role_class); ?>"><?php echo e($role_a); ?></span> <?php echo e($name); ?></td>
                                                    <td><?php echo e($value->name); ?></td>
                                                    <td><?php echo e($value->product_model); ?></td>
                                                    <td><?php echo e($value->part_require); ?></td>
                                                    <td><?php echo e($value->product_fault); ?></td>
                                                    <td>
                                                        <span class="label" style="<?php echo e($status_color); ?>"><?php echo e($status); ?></span>
                                                    </td>
                                                    <td><?php echo e(date('d-m-Y',strtotime($value->created_at))); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(URL::to($url_pdf)); ?>" class="btn btn-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="PDF" style="padding: 0.255rem 0.675rem;">
                                                            <i class="fa fa-file-pdf-o"></i>
                                                        </a>

                                                        <a href="<?php echo e(URL::to($url_print)); ?>" class="btn btn-success waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="PRINT" target="_blank" style="padding: 0.255rem 0.675rem;">
                                                            <i class="fa fa-print"></i>
                                                        </a>

                                                        <a href="javascript:void(0);" onclick="datanoteWarranty('<?php echo e($url_note); ?>');" class="btn btn-warning waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Comment" style="padding: 0.255rem 0.675rem;">
                                                            <i class="fa fa-comment"></i>
                                                        </a>

                                                        <a href="<?php echo e(URL::to($url_edit)); ?>" class="btn btn-primary waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="EDIT" style="padding: 0.255rem 0.675rem;">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a class="btn btn-danger waves-effect waves-light" style="padding: 0.255rem 0.675rem;" data-toggle="tooltip" data-placement="top" title="DELETE" onclick="deleted('<?php echo e($value->warranty_uniqueID); ?>')">
                                                            <i class="fa fa-trash"></i>
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

        <a href="#" id="a_warrantyModel" style="display: none;visibility: hidden" data-toggle="modal" data-target="#warrantyModel">---</a>
        <div class="modal inmodal" id="warrantyModel" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="margin-top: 5px;">
                <div class="modal-content animated bounceInRight" id="modal_content">
                </div>
            </div>
        </div>

        <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>

		<?php $__env->stopSection(); ?>

        <?php $__env->startSection('pagescript'); ?>
            <?php echo $__env->make('employee.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <script src="<?php echo e(asset('assets/js/plugins/jeditable/jquery.jeditable.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/js/plugins/dataTables/datatables.min.js')); ?>"></script>

            <script type="text/javascript">

                deletedata = null;
                new_datanoteWarranty = null;

                $(function ()
                {
                    var warranty_table = $('#warranty_table').DataTable();


                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                                    title: "Are you sure?",
                                    text: "You will not be able to recover this Warranty Report related data",
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
                                            url: "<?php echo e(URL::to('employee/ajax/log/warrantydelete')); ?>",
                                            data: {	warranty_uniqueID:id, _token:tokendata },
                                            success: function (result) {
                                                swal("Deleted!", "Your Sure Warranty has been deleted.", "success");
                                                setTimeout(function (){
                                                    window.location.reload();
                                                },3000);
                                            }
                                        });
                                    } else {
                                        swal("Cancelled", "Your Warranty is safe :)", "error");
                                    }
                                });
                    }
                    deletedata = deleted;


                    /*function datanote_Warranty(uniqueID)
                    {

                    }
                    new_datanoteWarranty = datanote_Warranty;*/

                });

                function deleted(id){

                    if(id != 0 && id != ''){
                        deletedata(id);
                    }
                }

                function datanoteWarranty(uniqueID)
                {
                    if(uniqueID != 0 && uniqueID != '')
                    {
                        var tokendata = $('#hidden_token').val();
                        $.ajax
                        ({
                            type: "POST",
                            url: "<?php echo e(URL::to('employee/ajax/log/warrantynote')); ?>",
                            data: {	warranty_uniqueID:uniqueID, _token:tokendata },
                            success: function (result)
                            {
                                if(result == '404' || result == 404){
                                    window.location = '<?php echo e(URL::to('/')); ?>';

                                }else if(result == '444' || result == 444){
                                    window.location.reload();
                                }else
                                {
                                    $('#modal_content').html('');
                                    $('#modal_content').html(result);

                                    $('#a_warrantyModel').trigger('click');
                                }
                            }
                        });
                    }
                }
            </script>
        <?php $__env->stopSection(); ?>
<?php echo $__env->make('employee.layouts.masteremployee', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>