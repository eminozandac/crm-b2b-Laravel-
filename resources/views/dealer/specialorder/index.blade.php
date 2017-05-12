@extends('dealer.layouts.masterdealer')

@section('pagecss')
 <link href="{{asset('assets/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
 <link href="{{asset('assets/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
 <link href="{{asset('assets/css/bootstrap-select.css')}}" rel="stylesheet">
@stop
 
<?php 
 
$pathcolorpanel=URL::to('colorDataJson/panelcolor.json');
$coloesidejson= file_get_contents($pathcolorpanel);
	$coloesidejson = @json_decode($coloesidejson,true);
	?>
@section('contentPages')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Special Order</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{URL::to('/dealer')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{URL::to('/dealer/product')}}">Product</a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Special Order</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form id="special_order" name="special_order" method="POST" action="{{ action('dealer\SpecialOrderController@saveData') }}" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">

                                <input name="_token" type="hidden" id="hidden_token" value="{{ csrf_token() }}"/>
                                <input name="special_orderID" type="hidden" id="special_orderID" value="{{ $specialorderID }}"/>
                                <input name="OrderNumber" type="hidden" id="OrderNumber" value="{{ $OrderNumber }}"/>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Company Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="jack" value="">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
	<?php //print_r($coloesidejson); ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Product Name</label>
                                    <div class="col-sm-10">
                                        <select data-placeholder="Choose a Product..." class="form-control chosen-select"  style="width: 100%;" tabindex="3" name="product_id" id="product_id">
                                            <option value="">Select</option>
                                            <?php
											
											$sessionData=Session::get('dealerLog');
											$id = $sessionData['dealerID'];
											$getcategory=DB::table('dealer')->where('id',$id)->first();
											$category_ar =explode(',',$getcategory->categoryID);
											//	print_r($category_ar);
												$catidlist='';
												for($i=0;$i< count($category_ar);$i++){
													if($i==0){
														
														$catidlist .="'".$category_ar[$i]."'";
													}else{
														$catidlist .=",'".$category_ar[$i]."'";
													}
												} 
											 
											$qry = "SELECT  category.*, products.* FROM products INNER JOIN category ON products.category_id=category.id WHERE products.visibility='1'  AND  products.deleted_at IS NULL AND products.category_id IN (".$catidlist.") OR category.showforall = 1";
											//print_r();
											$datastoks=DB::select(DB::raw($qry));
											if(!empty($datastoks)){
                                            foreach($datastoks as $products)
                                            { ?>
                                            <option value="<?php echo $products->product_id; ?>"  >
                                                <?php echo $products->productName; ?>
                                            </option>
                                            <?php  } }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Product Color</label>
                                    <div class="col-sm-10">
                                        <?php 
											$variationColor=array(
												'0' => 'none',
												'1' => 'Tuscan Sun',
												'2' => 'Pearl White',
												'3' => 'Sterling Silver',
												'4' => 'Tranquility',
												'5' => 'Storm Clouds',
												'6' => 'Cinnabar',
												'7' => 'Midnight Canyon',
												'8' => 'Winter Solstice',
												'9' => 'Cameo',
												'10' => 'Blue',
												'11' => 'White with Grey Sides',
												'12' => 'White with Brown Sides',
												'13' => 'Black with Black Sides',
												'14' => 'Black with Grey Sides',
											);
											$variationColorThumb=array(
												'0' => 'none',
												'1' => 'tuscan',
												'2' => 'pearl',
												'3' => 'sterling',
												'4' => 'tranq',
												'5' => 'strom',
												'6' => 'cinnabar',
												'7' => 'midnight',
												'8' => 'winter',
												'9' => 'cameo',
												'10' => 'blue',
												'11' => 'sterling',
												'12' => 'sterling',
												'13' => 'black',
												'14' => 'black',
												 
											);
														 
											 
											 
											?>
										<select class="selectpicker form-control" required name="product_color" id="product_color_filter" >
											<option value="">Select Color</option>
											<?php
											$color=count($variationColor);
												for($i=0;$i<$color;$i++){
													if($variationColor[$i]=='none'){
														echo '<option  value="none">None</option>';
													}else{
														$thumb="assets/img/".$variationColorThumb[$i].".jpg";
														echo'<option  value="'.$variationColor[$i].'" data-thumbnail="'.URL::to($thumb).'">'.$variationColor[$i].'</option>';
													}
														
												}
											?>
										</select>
                                    </div>
                                </div>
								<div class="hr-line-dashed"></div>
								<div class="form-group">
                                    <label class="col-sm-2 control-label">Side Panel Color</label>
                                    <div class="col-sm-10">
                                        
										<select class="selectpicker form-control" required name="product_side_color" id="product_color_filter" >
											<option value="">Select Side Panel Color</option>
											<?php
												foreach($coloesidejson as $k=>$v){
													echo '<option value="'.$k.'">'.$v.'</option>';
												}
											?>
										</select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
								<div id="attributesx" class="form-group">
									<label class="col-sm-2 control-label">Attributes</label>
									<div class="col-sm-10" id="attributes">
										<?php 
										/* $attrname=DB::table('attribute')->get();
										$first=0;
										foreach($attrname as $attr){
										$first++;
										if($first == 1){
											$selected="checked";
										}else{
											$selected="";
										}
										echo '<div class="i-checks"><label> <input type="checkbox" '.$selected.' name="attributeid[]" value="'.$attr->attributeID.'" class="form-control" > <i></i>'.$attr->attributeName.'</label></div>';
									} */
										?>
									</div><div class="clearfix"></div><div class="hr-line-dashed"></div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Finance</label>
									 <div class="col-sm-10">
										<div class="i-checks"><label> <input type="checkbox"  name="finance" value="1"> <i></i>   </label></div>
									</div>
								</div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Comments</label>
                                    <div class="col-sm-10">
                                        <textarea name="comments" id="comments"  class="form-control" rows="10"></textarea>
                                    </div>
                                </div>
								 
                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <input type="hidden" name="color" id="color" value="">
                                        <input type="submit" class="btn btn-primary" value="Add"/>
                                        <button class="btn btn-white" type="button" id="btn_reset">Reset</button>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input name="_token" type="hidden" id="hidden_tokenID" value="{{ csrf_token() }}"/>
		@stop()

        @section('pagescript')
            @include('dealer.includes.commonscript')

            <script src="{{asset('assets/js/plugins/chosen/chosen.jquery.js')}}"></script>
			<script src="{{asset('assets/js/plugins/iCheck/icheck.min.js')}}"></script>
			<script src="{{asset('assets/js/bootstrap-select.js')}}"></script>
            <script type="text/javascript">
                $(function ()
                {
                   /* $("#product_id").select2({
                        placeholder: "Select a state",
                        allowClear: true
                    });*/
					function checkboxinit(){
					  $('.i-checks').iCheck({
							checkboxClass: 'icheckbox_square-green',
							radioClass: 'iradio_square-green',
						});
					}
					checkboxinit();
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

                    function getColor(productID)
                    {
                        var tokendata = $('#hidden_tokenID').val();
						
                        $.ajax
                        ({
                            type: "POST",
                            url: "{{ URL::to('dealer/ajax/log/colordata') }}",
                            data: {data_productID: productID, _token: tokendata},
                            cache: false,
                            success: function (result) {
                                $('#variation_id').html(result);
								
                            }
                        });
                    }
					function getattribute(productID){
						 var tokendata = $('#hidden_tokenID').val();
						if(productID !=''){
							$.ajax
							({
								type: "POST",
								url: "{{ URL::to('dealer/ajax/log/attributedata') }}",
								data: {data_productID: productID, _token: tokendata},
								cache: false,
								success: function (result) {
									$('#attributes').html(result);
									checkboxinit();
								}
							});
						}else{
							$('#attributes').html('');
						}
					} 
					 
                    $('#product_id').change(function ()
                    {
                        $('#color').val('');
                        var productID = $('#product_id').val();
                        if(productID != ''){
                            getColor(productID);
                            getattribute(productID);
                        }else{
							$('#attributes').html('');
						}
                    });

                    $('#variation_id').change(function ()
                    {
                        var variationColor = $('#variation_id option:selected').text();
                        $('#color').val(variationColor);
                    });
					
					 
                    $('#btn_reset').click(function ()
                    {
                        $('input[type="text"]').each(function(){
                            $(this).val('');
                        });

                        $('textarea').each(function(){
                            $(this).val('');
                        });

                        $('select').each(function(){
                            $(this).val('');
                        });

                        window.location.reload();
                    });

                    $('#special_order').formValidation({
                            framework: 'bootstrap',
                            excluded: ':disabled',
                            message: 'This value is not valid',
                            icon: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                                company_name: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Customer Company Name'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 30,
                                            message: 'The Field must be more than 3 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-z0-9\s]+$/i,
                                            message: 'This Field can consist of alphabetical characters and spaces only'
                                        }
                                    }
                                },
                                comments: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Enter Comment !'
                                        },
                                        stringLength: {
                                            min: 3,
                                            max: 100000,
                                            message: 'The Field must be more than 100000 characters long'
                                        }
                                    }
                                },
                                product_id: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Select Product !'
                                        }
                                    }
                                },
                                variation_id: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Select Color !'
                                        }
                                    }
                                }
                            }
                        });
                });
            </script>
        @stop()