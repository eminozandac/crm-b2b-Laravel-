@extends('dealer.layouts.masterdealer')

@section('pagecss')
    <style>
        .btn_status{
            border: none;
            color: #ffffff;
        }

        .btn_status:hover{
            opacity: 0.7;
            color: #ffffff;
        }
    </style>
@stop

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Lead Report list</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/dealer')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/dealer/leadsreport')}}">All Lead Report</a>
                    </li>
                    <li class="active">
                        <strong>All Lead Report list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Lead Report Info</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="{{URL::to('/dealer/leadsreportadd')}}"  style="background-color: #18A689;">
                                    <i class="fa fa-users">&nbsp;</i>Add Lead Report
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive1">
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="leadreport_table" >
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Customer Name</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>EmailID</th>
                                        <th>Phone</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($leadreport))
                                        @foreach($leadreport as $key => $value)
                                            <?php
                                                $url = URL::to('dealer/edit-leadreport', $value->leadreport_id);
                                                $deleted = (string)$value->leadreport_id;
                                            ?>
                                            <tr>
                                                <td>{{ $value->title }}</td>
                                                <td>{{ $value->name }}</td>
                                                <td>
                                                    @if($value->lead_status != '')
                                                        <?php
                                                            $status = $value->lead_status;
                                                            $lead_status_report_name = $lead_status_report[$status];
                                                            $lead_status_report_bg = $lead_status_report_color[$status];

                                                            $style = 'background-color:'.$lead_status_report_bg.';';
                                                        ?>
                                                        <button class="btn btn-w-m btn-xs btn_status" type="button" style="{{ $style }}">
                                                            {{ $lead_status_report_name }}
                                                        </button>
                                                    @endif
                                                </td>
                                                <td><?php echo date('d-m-Y',strtotime($value->date_create)) ; ?></td>
                                                <td>{{ $value->emailID }}</td>
                                                <td>{{ $value->phone }}</td>
                                                <td>{{ $value->description }}</td>
                                                <td>
                                                    <a href="{{ $url }}"  data-toggle="tooltip" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>&nbsp;
                                                    <a href="javascript:void(0)"  data-toggle="tooltip" title="Delete" onclick="deleted('{{ $deleted }}')" class="btn btn-xs btn-default"><i class="fa fa-trash-o"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
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
            @include('dealer.includes.commonscript')

            <script src="{{asset('assets/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
            <script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>

            <script type="text/javascript">
                deletedata = null;
                $(function (){

                    var leadreport_table = $('#leadreport_table').DataTable({
                        "aoColumnDefs": [ { "bSortable": true, "aTargets": [ 1,5 ] } ],
                        "iDisplayLength": 20,
                        "aLengthMenu": [[10, 20, 30, 50, 100, 200, 500, -1], [10, 20, 30, 50, 100, 200, 500, "All"]],
                    });


                    function deleted(id)
                    {
                        var tokendata = $('#hidden_token').val();
                        swal({
                                    title: "Are you sure?",
                                    text: "You will not be able to recover this Leads Report related data",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Yes, delete leads report!",
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
                                            url: "{{ URL::to('dealer/ajax/log/leadsdelete') }}",
                                            data: {	leadreport_id:id, _token:tokendata },
                                            success: function (result) {
                                                swal("Deleted!", "Your Leads Report has been deleted.", "success");
                                                setTimeout(function (){
                                                    window.location.reload();
                                                },3000);
                                            }
                                        });
                                    } else {
                                        swal("Cancelled", "Your Leads Report is safe :)", "error");
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