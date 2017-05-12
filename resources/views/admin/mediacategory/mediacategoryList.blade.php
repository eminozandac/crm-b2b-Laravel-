@extends('admin/layouts/masteradmin')

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Media Category list</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/admin/mediacategorylist')}}">All Media Category</a>
                    </li>
                    <li class="active">
                        <strong>All Media Category list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Media Category List Data</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="{{URL::to('/admin/mediacategoryadd')}}"  style="background-color: #18A689;">
                                    <i class="fa fa-medium"></i> Add Media Category
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="data_result_table" >
                                    <thead>
                                    <tr>
                                        <th>Media Category</th>
                                        <th>Dealer Name</th>
                                        <th>Display</th>
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
        <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>
		@stop()

        @section('pagescript')
            @include('admin.includes.commonscript')

            <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
            <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>

            <script type="text/javascript">
                deletedata = null;
                $(function (){

                    var data_result_table = $('#data_result_table').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "{{ URL::to('admin/ajax/log/mediacategorydata') }}",
                            data: function (d) {
                            }
                        },
                        "columns": [
                            {data: 'Media Category', name: 'name'},
                            {data: 'Dealer Name', name: '', searchable: false},
                            {data: 'Display', name: 'status', orderable: false, searchable: false},
                            {data: 'Action', name: 'Action', orderable: false, searchable: false},
                        ]

                    });

                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this media category related data",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Delete",
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
                                    url: "{{ URL::to('admin/ajax/log/mediacategorydelete') }}",
                                    data: {	media_uniqueID:id, _token:tokendata },
                                    success: function (result) {
                                        swal("Deleted!", "Your Media Category has been deleted.", "success");
                                        setTimeout(function (){
                                            window.location.reload();
                                        },3000);
                                    }
                                });
                            } else {
                                swal("Cancelled", "Your Media Category Name is safe :)", "error");
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
        @stop()