@extends('admin/layouts/masteradmin')

@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Dealer list</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/admin')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/admin/dealerlist')}}">All Dealer</a>
                    </li>
                    <li class="active">
                        <strong>All Dealer list</strong>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>All Dealer List Data</h5>
                            <div class="ibox-tools">
                                <a class="btn btn-w-m btn-primary" href="{{URL::to('/admin/dealeradd')}}"  style="background-color: #18A689;">
                                    <i class="fa fa-users"></i>&nbsp;&nbsp;&nbsp;Add Dealer
                                </a>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
								<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>"/>
                                <table class="table table-striped table-bordered table-hover dataTables-example" id="dealer_table" >
                                    <thead>
                                    <tr>
                                        <th>Dealer</th>
                                        <th>Company Name</th>
                                        <th>Email</th>
                                        <th>Category</th>
                                        <th>Login</th>
                                        <th style="min-width: 108px;">Action</th>
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

                    var customer_table = $('#dealer_table').DataTable({
                        dom: '<"html5buttons"B>lTfgitp',
                        buttons: [
                            /* {extend: 'csv', title:'Customer Details'},
                            {extend: 'excel', title:'Customer Details'},
                            {extend: 'pdf', title:'Customer Details'}, */
                        ],
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: "{{ URL::to('admin/ajax/log/dealerdata') }}",
                            data:function(d){  }
                        },
                        "columns": [
                            {data: 'Dealer', name: 'first_name'},
                            {data: 'Company Name', name: 'company_name'},
                            {data: 'Email', name: 'emailID'},
                            {data: 'Category', name: 'categoryID'},
                            {data: 'Login', name: 'Login', orderable: false, searchable: false},
                            {data: 'Action', name: 'Action', orderable: false, searchable: false},
                        ]

                    });
					function removeData(valID){
						var order = valID;
						 
						//console.log(order);
						swal({
							title: "Are you sure?",
							text: "This Dealer Will be deleted?",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Yes",
							cancelButtonText: "No",
							closeOnConfirm: true,
							closeOnCancel: true
							},
							function(isConfirm){
								if (isConfirm) {
									var _token = $('#token').val();
								 
									$.ajax
									({
										type: "POST",
										url: "{{URL::to('admin/ajax/log/dealerdelete')}}",
										data: {'order':order,'_token':_token},
										success: function(msg)
										{ 	 
											//console.log(msg);
											//alert(msg);
											//order_table.draw();
											swal("Deleted!", "Your Dealer has been deleted.", "success"); 
											location.reload();
											
										}
									});  
								}	 
							});
							
					}
					deleted_data = removeData;
                });
				function removedata(valID)
				{
					if(valID != '' && valID != 0){
						deleted_data(valID);
					}
				}
            </script>
        @stop()