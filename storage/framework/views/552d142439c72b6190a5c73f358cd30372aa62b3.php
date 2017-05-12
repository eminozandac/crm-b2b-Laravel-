
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row animated fadeInDown">

                <div class="col-lg-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Today Task List</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="class-refresh"  href="javascript:void(0);" >
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div id='staff_task_list_assign'>
                                <p>All Today Task</p>
                                    <?php
                                        function limit_words($string, $word_limit)
                                        {
                                            $words = explode(" ",$string);
                                            return implode(" ", array_splice($words, 0, $word_limit));
                                        }
                                    ?>
                                    <?php if(!empty($task_data)): ?>
                                        <?php foreach($task_data as $task => $value): ?>
									<?php $url = URL::to('staff/edit-task', $value->task_id); ?>
                                           <a href="<?php echo e($url); ?>"/> 
												<div class='external-event navy-bg'>
													<p style="float: right"><?php echo e(date('d-m-Y',strtotime($value->assign_date))); ?></p>
                                                <?php echo e(\Illuminate\Support\Str::limit($value->title, $limit = 25, $end = '...')); ?>

												</div>
											</a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-9">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Task Calender</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="<?php echo e(URL::to('/staff/taskadd')); ?>"  style="background-color: #18A689;padding: 2px 12px;">
                                    <i class="fa fa-users"></i> Add Task
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="class-refresh"  href="javascript:void(0);" >
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div id="staff_task_calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>
        <div id="task_events" style="display: none;visibility: hidden"><?php echo e($event); ?></div>

        <a href="javascript:void(0);" data-toggle="modal" data-target="#taskassignDateModel" id="date_changeTask" style="display: none;visibility: hidden">Task DateChange</a>

        <div class="modal inmodal fade" id="taskassignDateModel" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Task Assign/Completion Date</h4>
                    </div>
                    <div class="modal-body form-horizontal">
                        <div class="text-center">
                            <h4>
                                Title : <strong><span id="taks_title"></span></strong>
                            </h4>
                        </div>
                        <br/>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Assign Date</label>
                            <div class="col-sm-8">
                                <input type="text" required id="assign_date"  class="form-control datetd date" placeholder="dd-mm-yyyy" name="assign_date" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Completion Date</label>
                            <div class="col-sm-8">
                                <input type="text" required id="completion_date"  class="form-control datetd date" placeholder="dd-mm-yyyy" name="completion_date" value="">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer" style="margin-top: 0">
                        <input type="hidden" id="task_id" name="task_id" value="">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btn_model_task">Save changes</button>
                    </div>
                </div>
            </div>
        </div>