@extends('dealer.layouts.masterdealer')
@section('pagecss')
    <link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
	<style>
		.defuladd{
			padding: 5px;
			border: 1px solid #ccc;
			border-radius: 5px;
		}
	</style>
@stop
@section('contentPages')

			<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Checkout</h2>
                    <ol class="breadcrumb">
                       <li>
							<a href="{{ URL::to('/dealer') }}">Home</a>
						</li>
                        <li>
							<a href="{{ URL::to('/dealer/product') }}">Main Product</a>
						</li>
                        <li class="active">
                            <strong>Checkout</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
			<div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
			<form class="m-t form-horizontal"  role="form" method="post" action="{{action('dealer\OrderController@placeOrderCheckout')}}" id="customer_billing" >
                <div class="col-md-9">
					<div class="ibox">
					 
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                        <div class="ibox-title">
                            
                            <h5>Your Order Notes</h5>
                        </div>
                        <div class="ibox-content col-md-12">
						<?php
							//print_r($orderdata);
						?>
						<input type="hidden" name="_token"  id="_token" value="<?php echo csrf_token() ;?>"/>
						<input type="hidden" name="productToken" value="<?php echo  $orderdata['productToken'] ;?>"/>
						<input type="hidden" name="varaintToken" id="varaintToken" value="<?php echo $orderdata['varaintToken']; ?>"/>
						<input type="hidden" name="qtystatus" id="qtystatus" value="<?php echo $orderdata['qtystatus'];?>"/>
						<input type="hidden" name="qty" id="qty" value="<?php echo $orderdata['qty'];?>"/>
						<?php //print_r($orderdata);
							for($i=0;$i<$orderdata['qty'];$i++){
						?> 
							<div class="form-group">
								<label class="col-sm-3 control-label">Finance</label>
								 <div class="col-sm-9">
									<div class="i-checks"><label> <input type="checkbox"  name="finance[]" value="qry_<?php echo $i; ?>"> <i></i>   </label></div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Customer Name <?php echo $i+1;?></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="order_notes_title[]" id="order_notes_title" placeholder="Customer Name">
								</div>
							</div>
							 
							<div class="form-group">
								<label class="col-sm-3 control-label">Note  </label>
								<div class="col-sm-9">
									<textarea  class="form-control" name="order_notes_descriptions[]" id="order_notes_descriptions" placeholder="Notes"></textarea>
									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Delivery Information</label>
								<div class="col-sm-9">
									<!--<select class="form-control addressType" name="addressType" id="addressType_{{ $i }}" 
									onchange="loadAddressType('{{ $i }}')" >
										<option data-box="addressbox_<?php echo $i; ?>" value="default">Default</option>
										<option data-box="addressbox_<?php echo $i; ?>" value="other">Other</option>
									</select>-->
									<div class="col-sm-12">
										<div class="form-group">
										 <label class="control-label">&nbsp;</label>
										     <div class="i-checks"><label> <input type="radio" id="addressType_{{ $i }}" onclick="loadAddressType('{{ $i }}')" data-box="addressbox_<?php echo $i; ?>" data-def="defaultAddress_<?php echo $i; ?>"   value="default" checked="checked" name="addressType_{{ $i }}[]"> <i></i> Deliver to this address</label></div>
											<p id="defaultAddress_<?php echo $i; ?>" class="defuladd"><?php 
												$sessionData=Session::get('dealerLog');
												$id = $sessionData['dealerID'];
												$getUserdata=DB::table('dealer')->where('id','=',$id)->first();
												echo $getUserdata->address;
											?></p>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
										     <div class="i-checks"><label> <input type="radio" id="addressType_{{ $i }}" onclick="loadAddressType('{{ $i }}')"  data-box="addressbox_<?php echo $i; ?>" data-def="defaultAddress_<?php echo $i; ?>" value="other" name="addressType_{{ $i }}[]"> <i></i> Deliver to diffrent address </label></div>
                                    
										</div>
									</div>
								</div>
							</div>
							<div class="form-group" id="addressbox_<?php echo $i; ?>"  style="display:none;">
								<label class="col-sm-3 control-label">Enter New Address  </label>
								<div class="col-sm-9">
									<textarea class="form-control" name="address[]" placeholder="Address"></textarea>
								</div>
							</div>
							<div class="hr-line-dashed"></div>
						<?php
							}
						?> 
                        </div>
						<div class="ibox">
							<div class="ibox-title">
								
								<h5>Add Accessories</h5>
							</div>
							<div class="ibox-content col-md-12">
								<div class="col-sm-8">
									<div class="form-group">
										<label class="control-label">Accessory Name:</label>
									  
										<select data-placeholder="Select Accessories..." class="chosen-select"  style="width: 100%"  tabindex="4" name="newaccessory" id="newaccessory">
										 <option value="" selected>Select Accessory</option>
											<?php 
												$getProducts=DB::table('product_accessories')->where('deleted_at','=',NULL)->get();
												foreach($getProducts as $getProduct){
												?>
													<option value="<?php echo base64_encode($getProduct->accessoryID); ?>" >{{$getProduct->accessory_name}}</option>
											<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label class="control-label">Qty:</label>
										<input class="touchspin" type="text" value="1" id="newaccessoryqty" name="newaccessoryqty">
										  
									</div>
								</div>
								<div class="col-md-12">
									<table class="table table-striped table-bordered table-hover dataTables-example" id="order_table" style="width:100%;" >
										<thead>
											<tr>
												<th>#</th> 
												<th>Accessory Name</th>
												<th>Qty</th>
												<th style="min-width: 55px;">Action</th>
											</tr>
										</thead>
										<tbody id="accessoryorderdata">
										<?php
										 $sessionDataAccessory = Session::get('accessoriesPlaceorder');
										 if(!empty($sessionDataAccessory))
										 {
											$newacc=0;
											foreach($sessionDataAccessory as $new_k => $new_v)
											{
												$newacc++;
												$acessoryName=DB::table('product_accessories')->where('accessoryID','=',$new_k)->first();
												echo'
													<tr>
														<td>'.$newacc.'</td>
														<td>'.$acessoryName->accessory_name.'</td>
														<td>
															<div class="form-group" style="max-width:150px;margin-bottom:0px;">
																<input type="hidden" name="accesoryedit[]" value="'.base64_encode($new_k).'"/>
																<input class="touchspin" style="max-width:150px;" name="accesoryqtyedit[]" type="text" value="'.$new_v.'">
															</div>
														</td>';
														?>
														<td><a href="javascript:void(0);" title="Delete order" 
														onclick="removeaccessory('<?php echo base64_encode($new_k); ?>')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a></td>
														
													</tr>
												<?php 
											} 
										 }else{
											 echo '<tr><td colspan="4">No Accessories Added.</td></tr>';
										 }
										?>
										</tbody>
									</table>
								</div>
							</div>
                        </div>
                        <div class="ibox-content">
							<input type="button" id="accesorybtn" value="Add Accessory" class="btn btn-primary" />
                            <a href="{{URL::to('/dealer/product')}}" class="btn btn-white"><i class="fa fa-arrow-left"></i> Continue shopping</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Order Summary</h5>
                        </div>
                        <div class="ibox-content">
								<?php 
								$sessionData=Session::get('dealerLog');
								$id = $sessionData['dealerID'];
								$dealerGroup=DB::table('dealer')->where('id','=',$id)->first();
								$variationData=DB::table('variation')->where('variationID','=',base64_decode($orderdata['varaintToken']))->first();
								$productData=DB::table('products')->where('product_id','=', base64_decode($orderdata['productToken']))->first();
								$discountGroup=DB::table('discount')->where('product_id','=', base64_decode($orderdata['productToken']))->where('groupID','=',$dealerGroup->groupID)->first();
								if(!empty($productData->real_price)){
								/* 	if(!empty($productData->sale_price)){
										
										$totalprice=$productData->sale_price * $orderdata['qty'];
										$orginPrice=$productData->sale_price;
										if(!empty($discountGroup->discountPer)){
											$discount= '- '. $discountGroup->discountPer.'%<hr/>&pound;'.round($totalprice*$orderdata['qty']);
										}else{$discount='';}
										
										$pricePrint='&pound;'.$productData->sale_price.'
										<s class="small text-muted">&pound;'.$productData->real_price.'</s>';
										$itemTotal=$productData->sale_price*$orderdata['qty'].'<br/>'.$discount;
										
								//	}else{ */
										$orginPrice=$productData->real_price;
										$totalprice=$productData->real_price * $orderdata['qty'];
										
										if(!empty($discountGroup->discountPer)){
											$discount= '- '. $discountGroup->discountPer.'%<hr/>&pound;'.round($totalprice*$orderdata['qty']);
										}else{$discount='';}
										
										$pricePrint='&pound;'.$productData->real_price;
										$itemTotal=$productData->real_price*$orderdata['qty'].'<br/>'.$discount;;
								//	}
									// echo '<br/>&pound;'.$orginPrice.'<br/> &nbsp;&nbsp;&nbsp;x'.$orderdata['qty'].'<br/>_________';
									echo 'Price : &pound;'.$orginPrice.'<br/>';
									echo 'Qty :  &nbsp;&nbsp;&nbsp;x'.$orderdata['qty'].'<br/>';
									echo 'VAT : &nbsp;&nbsp;&nbsp;20%<br/>_________________<br/>';
									$vat=($orginPrice * $orderdata['qty'] * 20 / 100);
									if(!empty($discountGroup->discountPer)){
										echo 'Subtotal :  &pound;'.$orginPrice * $orderdata['qty'].'<br/>';
										echo 'Discount :  -'.$discountGroup->discountPer.'<br/>_________________<br/>';
										echo '<strong>Total :  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &pound;'.round($orginPrice - ($orginPrice * $orderdata['qty'] * $discountGroup->discountPer / 100 ) + $vat).'</strong>';
									}else{
										echo '<strong>Total :  &pound;'.round(($orginPrice * $orderdata['qty']) + $vat).'<br/></strong>';
									}
								}
									
								?>
                           
                            <h2 class="font-bold">
                               
                            </h2>

                            <hr/>
                            <span class="text-muted small">
                                
                            </span>
                            <div class="m-t-sm">
                                <div class="btn-group">
                                <input type="submit" class="btn btn-primary btn-sm" value="Place Order"> 
                                <a href="{{ URL::to('/dealer/product') }}" class="btn btn-white btn-sm"> Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				</form>
            </div>
        </div>
	@stop()
@section('pagescript')
	@include('admin.includes.commonscript')
	<script src="{{ asset('assets/js/plugins/slick/slick.min.js')}}"></script>
	<script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
	   <!-- TouchSpin -->
    <script src="{{asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
	<script  src="{{asset('assets/js/plugins/iCheck/icheck.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
	<script type="text/javascript">
	$(function() {
		 var config = {
						'.chosen-select'           : {},
						'.chosen-select-deselect'  : {allow_single_deselect:true},
						'.chosen-select-no-single' : {disable_search_threshold:10},
						'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
						'.chosen-select-width'     : {width:"95%"}
					} 
					for (var selector in config) {
						$(selector).chosen(config[selector]);
					}
				 function tuchspinintcustom(){
					 
					var touchspain = '.touchspin';
					$(touchspain).TouchSpin({
						buttondown_class: 'btn btn-white',
						buttonup_class: 'btn btn-white',
						min: 1, 
						max:$(touchspain).attr('max')
					});
				 }
						tuchspinintcustom();
					$(".select2_demo_1").select2();
					$(".select2_demo_2").select2();
					$(".select2_demo_3").select2({
						placeholder: "Select a Product",
						allowClear: true
					});
			$('.i-checks').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green',
			});
			$('.product-images').slick({
				dots: true
			});
			 
		 
				$('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                }).on('ifToggled', function() {

                    if( $(this).val() == 'other'){
                         // alert('yes');
						var addressType = $(this).attr('data-box');
						$('#'+addressType).css({'display':'block'});
						
						var defadd = $(this).attr('data-def');
						$('#'+defadd).css({'display':'none'});		  
						//alert(addressType);
						 
                    }else{
                        var addressType = $(this).attr('data-box');
						$('#'+addressType).css({'display':'none'});		
						
						var defadd = $(this).attr('data-def');
						$('#'+defadd).css({'display':'block'});		
                    }

                });
                $('#form_updatepassword').formValidation({
                    framework: 'bootstrap',
                    excluded: ':disabled',
                    message: 'This value is not valid',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        opassword: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Password'
                                },
                                different: {
                                    field: 'emailID',
                                    message: 'The password cannot be the same as Email Address'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 12,
                                    message: 'The password must be more than 6 characters long'
                                }
                            }
                        },
                        password: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Password'
                                },
                                different: {
                                    field: 'emailID',
                                    message: 'The password cannot be the same as Email Address'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 12,
                                    message: 'The password must be more than 6 characters long'
                                }
                            }
                        },
                        cpassword: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Confirm Password'
                                },
                                different: {
                                    field: 'emailID',
                                    message: 'The password cannot be the same as Email Address'
                                },
                                stringLength: {
                                    min: 6,
                                    max: 12,
                                    message: 'The password must be more than 6 characters long'
                                }
                            }
                        }
                    }
                });


                $('#customer_billing').formValidation({
                    framework: 'bootstrap',
                    excluded: ':disabled',
                    message: 'This value is not valid',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        order_notes_title: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Customer Name !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 30,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                 
                            }
                        }, 
						order_notes_descriptionss: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Order notes Description !'
                                },
                                stringLength: {
                                    min: 3,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                 
                            }
                        }, 
						billing_firstname: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Dealer First Name !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 30,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                regexp: {
                                    regexp: /^[a-z\s]+$/i,
                                    message: 'This Field can consist of alphabetical characters and spaces only'
                                }
                            }
                        },
                        billing_lastname: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Dealer Last Name !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 30,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                regexp: {
                                    regexp: /^[a-z\s]+$/i,
                                    message: 'This Field can consist of alphabetical characters and spaces only'
                                }
                            }
                        },
                        billing_emailID: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Email Address !'
                                },
                                emailAddress: {
                                    message: 'Enter Valid Email Address !'
                                }
                            }
                        },
                        billing_address: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Address !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 200,
                                    message: 'The Field must be more than 3 characters long'
                                }
                            }
                        },
                        billing_city: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter City !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 100,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                regexp: {
                                    regexp: /^[a-z\s]+$/i,
                                    message: 'This Field can consist of alphabetical characters and spaces only'
                                }
                            }
                        },
                        billing_state: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter State !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 100,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                regexp: {
                                    regexp: /^[a-z\s]+$/i,
                                    message: 'This Field can consist of alphabetical characters and spaces only'
                                }
                            }
                        },
                        billing_zipcode: {
                            validators: {
                                notEmpty: {
                                    message: 'Enter Zip Code !'
                                },
                                stringLength: {
                                    min: 3,
                                    max: 100,
                                    message: 'The Field must be more than 3 characters long'
                                },
                                 
                            }
                        },
                        billing_country: {
                            validators: {
                                notEmpty: {
                                    message: 'Select Country !'
                                }
                            }
                        }
                    }
                });
				
			function accessoryaddplaceordertime()
			{
				var _token = $('#_token').val();
				//alert('call');
				var newaccessory = $('#newaccessory').val();
				var newaccessoryqty = $('#newaccessoryqty').val();
				
				var accesoryold='';
				var accesoryoldqty='';
				
				var accesoryedit_ar = [];
				accesoryedit_ar = $('input[name="accesoryedit[]"]').map(function(){return $(this).val();}).get();
				//console.log(accesoryedit_ar);
				
				var accesoryqtyedit_ar = [];
				accesoryqtyedit_ar = $('input[name="accesoryqtyedit[]"]').map(function(){return $(this).val();}).get();
				//console.log(accesoryqtyedit_ar);
								
				if(newaccessory.length > 0 || accesoryedit_ar.length > 0)
				{
					$.ajax
					({
						type: "POST",
						url: "{{URL::to('dealer/ajax/log/placeorderaccessories')}}",
						data: {'_token':_token, 'newaccessory':newaccessory, 'newaccessoryqty':newaccessoryqty , 
						'accesoryold':accesoryedit_ar, 'accesoryoldqty':accesoryqtyedit_ar
						},
						success: function(msg)
						{ 	 
							//console.log(msg);
							//alert(msg);
							
							$('#accessoryorderdata').html(msg);		
							$("#newaccessory option:first").attr('selected','selected');
							//location.reload();
							tuchspinintcustom();
							toastr.options = {closeButton:true,preventDuplicates:true}
							toastr.success('Accessories Updated Successfuly')
						}
					});
				}  
			}
			
			$('#accesorybtn').click(function()
			{
				accessoryaddplaceordertime();
				//setTimeout(function(){ window.location.reload(); }, 2000);
				 //window.location.reload();
			});	
			//accessoryaddplaceordertime();
			
			function removeaccessory(valid){
				var _token = $('#_token').val();
				var accssory= valid;
				if( accssory != ''){
				//console.log(accssory);
					 swal({
						title: "Are you sure?",
						text: "This accessory Will be deleted?",
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
							 
							$.ajax
							({
								type: "POST",
								url: "{{URL::to('dealer/ajax/log/placeorderaccessoriesremove')}}",
								data: {'_token':_token, 'accssory':accssory},
								success: function(msg)
								{ 	 
									//console.log(msg);
									//order_table.draw();
									swal("Deleted!", "Your accessory Item has been deleted.", "success"); 
									//location.reload();
									$('#accessoryorderdata').html(msg);		
									$("#newaccessory option:first").attr('selected','selected');
									//location.reload();
									tuchspinintcustom();
									 toastr.options = {closeButton:true,preventDuplicates:true}
									toastr.success('Accessories Deleted Successfuly')
									
								}
							});
							 
						}	 
					}); 
				}
			}
			
			removeaccessory_data=removeaccessory;
    });
	function removeaccessory(valid)
	{
		if(valid != '' && valid != 0){
			removeaccessory_data(valid);
		}
	}
	</script>
	
	<script type="text/javascript">
		new_loadAddressType = null;
		$(function ()
		{
			function loadAddressType(id)
			{
				var addressType = '#addressType_'+id;
				if($(addressType).val() == 'other')
				{
					var show_div = '#addressbox_'+id;
					$(show_div).css({'display':'block'});		    
				}else
				{
					var show_div = '#addressbox_'+id;
					$(show_div).css({'display':'none'});
				}
			}
			new_loadAddressType = loadAddressType; 
			loadAddressType(0);
		});
		function loadAddressType(id)
		{
			new_loadAddressType(id);
		}
	</script>
@stop()