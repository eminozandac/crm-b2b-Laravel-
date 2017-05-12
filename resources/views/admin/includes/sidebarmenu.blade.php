        <?php
            $currentPage = Route::getCurrentRoute()->getPath();

            $sessionData = Session::get('adminLog');
            $role = $sessionData['role'];
        ?>

        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">

            @if($role == 'admin')
                @include('admin.includes.adminmenu')
            @elseif($role == 'staff')
                @include('staff.includes.staffmenu')
            @endif

                @include('admin.includes.listmenu')

            </ul>
        </div>