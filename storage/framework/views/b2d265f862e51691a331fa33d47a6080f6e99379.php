        <?php
            $currentPage = Route::getCurrentRoute()->getPath();

            $sessionData = Session::get('adminLog');
            $role = $sessionData['role'];
        ?>

        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">

            <?php if($role == 'admin'): ?>
                <?php echo $__env->make('admin.includes.adminmenu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php elseif($role == 'staff'): ?>
                <?php echo $__env->make('staff.includes.staffmenu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>

                <?php echo $__env->make('admin.includes.listmenu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            </ul>
        </div>