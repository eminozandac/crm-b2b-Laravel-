    <script type="text/javascript" src="{{asset('assets/js/jquery-2.1.1.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/bootstrap.min.js')}}"></script>
	
	<script type="text/javascript" src="{{asset('assets/js/formValidation.js')}}"></script>
	<script type="text/javascript" src="{{asset('assets/js/framework/bootstrap.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('assets/js/plugins/toastr/toastr.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('assets/js/plugins/slick/slick.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/select2/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/iCheck/icheck.min.js')}}"></script>

    <!-- Custom and plugin javascript -->
    <script type="text/javascript" src="{{asset('assets/js/inspinia.js')}}"></script>

    <!-- Peity -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/peity/jquery.peity.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/demo/peity-demo.js')}}"></script>

   

    <!-- jQuery UI -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>

    <!-- Jvectormap -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js')}}"></script>
    <script  type="text/javascript" src="{{asset('assets/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>

    <!-- EayPIE -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/easypiechart/jquery.easypiechart.js')}}"></script>

    <!-- Sparkline -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/sparkline/jquery.sparkline.min.js')}}"></script>

    <!-- Sparkline demo data  -->
    <script type="text/javascript" src="{{asset('assets/js/demo/sparkline-demo.js')}}"></script>
	<script type="text/javascript" src="{{asset('assets/js/plugins/footable/footable.all.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('assets/js/plugins/summernote/summernote.min.js')}}"></script>

    <!-- Sweet alert -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/sweetalert/sweetalert.min.js')}}"></script>

    <script type="text/javascript">
        $(function() {
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
        });
    </script>
