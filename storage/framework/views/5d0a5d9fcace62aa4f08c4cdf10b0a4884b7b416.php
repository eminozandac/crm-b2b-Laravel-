    <a href="javascript:void(0);" data-toggle="modal" data-target="#taskinfoModel" id="task_info" style="display: none;visibility: hidden">Task Information</a>

    <div class="modal inmodal fade" id="taskinfoModel" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Task Information</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="text-left">
                        <h4>
                            <span style="float:left; width: 23%;">Priority</span> : <strong><span id="info_task_priority" style="color: #fff;"></span></strong>
                        </h4>
                    </div>
                    <br/>

                    <div class="text-left">
                        <h4>
                            <span style="float:left; width: 23%;">Title</span> : <strong><span id="info_taks_title"></span></strong>
                        </h4>
                    </div>
                    <br/>

                    <div class="text-left">
                        <h4>
                            <span style="float:left; width: 23%;">Assign Date</span> : <strong><span id="info_taks_start"></span></strong>
                        </h4>
                    </div>
                    <br/>

                    <div class="text-left">
                        <h4>
                            <span style="float:left; width: 23%;">Completion Date</span> : <strong><span id="info_taks_end"></span></strong>
                        </h4>
                    </div>
                    <br/>

                    <div class="text-left">
                        <h4>
                            <span style="float:left; width: 23%;">Assign Task </span> : <strong><span id="info_taks_staff"></span></strong>
                        </h4>
                    </div>
                    <br/>
                </div>
                <div class="modal-footer" style="margin-top: 0">
                    <input type="hidden" id="info_task_id" name="info_task_id" value="">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-2.1.1.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/bootstrap.min.js')); ?>"></script>

    <script type="text/javascript" src="<?php echo e(asset('assets/js/formValidation.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/framework/bootstrap.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/metisMenu/jquery.metisMenu.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/toastr/toastr.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/slick/slick.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/select2/select2.full.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/iCheck/icheck.min.js')); ?>"></script>
    <!-- Custom and plugin javascript -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/inspinia.js')); ?>"></script>
    <!-- Peity -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/peity/jquery.peity.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/demo/peity-demo.js')); ?>"></script>

    <!-- jQuery UI -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/jquery-ui/jquery-ui.min.js')); ?>"></script>

    <!-- Jvectormap -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js')); ?>"></script>
    <script  type="text/javascript" src="<?php echo e(asset('assets/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')); ?>"></script>

    <!-- EayPIE -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/easypiechart/jquery.easypiechart.js')); ?>"></script>

    <!-- Sparkline -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/sparkline/jquery.sparkline.min.js')); ?>"></script>

    <!-- Sparkline demo data  -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/demo/sparkline-demo.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/footable/footable.all.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/summernote/summernote.min.js')); ?>"></script>

    <!-- Sweet alert -->
    <script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/sweetalert/sweetalert.min.js')); ?>"></script>

    <script type="text/javascript">
        new_displayAssignTask = null;
        $(function()
        {
            <?php
                if(Session::get('operationSucess')){
            ?>
                toastr.options = {closeButton:true,preventDuplicates:true}
                toastr.success('<?php echo Session::get('operationSucess'); ?>')
            <?php
            }
            if(Session::get('operationFaild')){
            ?>
                toastr.options = {closeButton:true,preventDuplicates:true}
                toastr.error('<?php echo Session::get('operationFaild'); ?>')
            <?php }?>

            $('.footable').footable();

            function displayAssignTask(value)
            {
                var valueID = '#'+value;

                var task_id = $(valueID).attr('data-taskID');
                $('#info_task_id').val('');
                $('#info_task_id').val(task_id);

                var task_title = $(valueID).attr('data-title');
                $('#info_taks_title').text('');
                $('#info_taks_title').text(task_title);

                var task_start = $(valueID).attr('data-assigndate');
                $('#info_taks_start').text('');
                $('#info_taks_start').text(task_start);

                var task_ends = $(valueID).attr('data-completiondate');
                $('#info_taks_end').text('');
                $('#info_taks_end').text(task_ends);

                var task_assignuser = $(valueID).attr('data-taskassign');
                $('#info_taks_staff').text('');
                $('#info_taks_staff').text(task_assignuser);

                var task_priority = $(valueID).attr('data-task_priority');
                $('#info_task_priority').text('');
                $('#info_task_priority').text(task_priority);

                if(task_priority == 'HIGH'){
                    $('#info_task_priority').css({'background-color':'#CF000F'});
                }if(task_priority == 'MEDIUM'){
                    $('#info_task_priority').css({'background-color':'#1AB394'});
                }if(task_priority == 'LOW'){
                    $('#info_task_priority').css({'background-color':'#F8AC59'});
                }

                $('#task_info').trigger('click');

                var tokendata = $('#hidden_token').val();
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo e(URL::to('staff/ajax/log/readtaks')); ?>",
                    data: {_token: tokendata, data_taskID: task_id },
                    success: function (result)
                    {
                        setTimeout(function (){
                            window.location.reload();
                        },3500);
                    }
                });
            }
            new_displayAssignTask = displayAssignTask;

        });

        function displayAssignTask(value)
        {
            new_displayAssignTask(value);
        }
    </script>
