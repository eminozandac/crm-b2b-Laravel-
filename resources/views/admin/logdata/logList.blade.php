@extends('admin/layouts/masteradmin')

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Log List</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/admin/loglistdata')}}">All Log</a>
                    </li>
                    <li class="active">
                        <strong>All LogData</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Log Data</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="log_table" >
                                    <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Info</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
		@stop()

        @section('pagescript')
            @include('admin.includes.commonscript')

            <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
            <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>

            <script type="text/javascript">
                $(function (){

                    var log_table = $('#log_table').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
                        buttons: [
                            /* {extend: 'csv', title:'Customer Details'},
                            {extend: 'excel', title:'Customer Details'},
                            {extend: 'pdf', title:'Customer Details'}, */
                        ],
                        "aoColumnDefs": [ { "bSortable": true, "aTargets": [ 1,3 ] } ],
                        "iDisplayLength": 20,
                        "aLengthMenu": [[10, 20, 30, 50, 100, 200, 500, -1], [10, 20, 30, 50, 100, 200, 500, "All"]],
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "{{ URL::to('logdata/ajax/log/loglistdata') }}",
                            data:function(d){  }
                        },
                        "columns": [
                            {data: 'User', name: 'role'},
                            {data: 'Info', name: 'operation'},
                            {data: 'Description', name: 'description'},
                            {data: 'Date', name: 'role_date'}
                        ]
                    });
                });
            </script>
        @stop()