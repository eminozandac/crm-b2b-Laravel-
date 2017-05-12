<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title"><?php echo e($warrantyData->product_model); ?> - <?php echo e($warrantyData->product_serial_number); ?></h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Comment</label>
        <textarea class="form-control" placeholder="Message" id="txt_comment"/>
    </div>
    <div class="form-group">
        <div class="i-checks">
            <label>
                <input type="checkbox" value="0" name="send_mailMessage" id="send_mailMessage"> <i></i> This Message Mail to User
            </label>
        </div>
    </div>

    <div class="ibox-content" id="ibox-content">
        <div>
            <div class="chat-activity-list">

                <?php if(!empty($warrantyNote)): ?>
                    <?php foreach($warrantyNote as $key => $value): ?>
                        <?php
                            $role = $value->role;
                            $user_id = $value->user_id;

                            $time = date('h:i A',strtotime($value->created_at));
                            $date = date('d-m-Y',strtotime($value->created_at));

                            $right = $text_right = '';

                            if(($login_role == $role) && ($login_user_id == $user_id)){
                                $right = 'right';
                                $text_right = 'text-right';
                            }else{
                                $right = $text_right = '';
                            }

                            $name = $emailID = '';
                            $table_ar = array('admin','staff','employee','dealer','customer');
                            if(($user_id != '') && ($role != '') && in_array($role,$table_ar))
                            {
                                $result = DB::table($role)->where('id','=',$user_id)->first();
                                if(!empty($result))
                                {
                                    if($role == 'admin')
                                    {
                                        $name = $result->name;
                                    }else{
                                        $name = $result->first_name.' '.$result->last_name;
                                    }
                                    $emailID = $result->emailID;
                                }
                            }else{
                                $name = $emailID = '';
                            }

                        ?>
                        <div class="chat-element <?php echo e($right); ?>">
                            <div class="media-body <?php echo e($text_right); ?>">
                                <strong><?php echo e($role); ?> - <?php echo e($name); ?></strong>
                                <p class="m-b-xs ">
                                    <?php echo e($value->note); ?>

                                </p>
                                <small class="text-muted"><?php echo e($time); ?> - <?php echo e($date); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php else: ?>
                    <div class="chat-element">
                        <div class="media-body ">
                            <p class="m-b-xs">Not Any Comments</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="margin-top: 2px;">
    <button type="button" class="btn btn-white" data-dismiss="modal" id="btn_close">Close</button>
    <button type="button" class="btn btn-primary" id="btn_commant_send">Send</button>
</div>
<input name="_token" type="hidden" id="comment_hidden_token" value="<?php echo e(csrf_token()); ?>"/>
<script  src="<?php echo e(asset('assets/js/plugins/iCheck/icheck.min.js')); ?>"></script>
<script type="text/javascript">
    $(function ()
    {
        var comment_tokendata = $('#comment_hidden_token').val();

        $('#ibox-content').slimScroll({
            height: '230px'
        });

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        }).on('ifToggled', function() {

            if($('input[name="send_mailMessage"]').val() == 0){
                $('input[name="send_mailMessage"]').val(1);
            }else{
                $('input[name="send_mailMessage"]').val(0);
            }
        });

        $('#btn_commant_send').click(function ()
        {
            var id = '<?php echo e($warrantyData->warranty_uniqueID); ?>';
            var comment = $('#txt_comment').val();
            var send_mail = $('#send_mailMessage').val();
            if(comment != '' && comment.length > 0)
            {
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo e(URL::to('staff/ajax/log/addwarrantynote')); ?>",
                    data: {	_token:comment_tokendata, warranty_uniqueID:id, data_comment : comment, data_send_mail:send_mail },
                    success: function (result)
                    {
                        $('#txt_comment').val('');
                        $('#btn_close').trigger('click');
                    }
                });
            }

        });

    });
</script>