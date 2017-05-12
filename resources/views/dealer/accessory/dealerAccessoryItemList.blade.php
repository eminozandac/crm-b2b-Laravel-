@extends('dealer.layouts.masterdealer')

@section('pagecss')
    <!-- Toastr style -->
    <link href="{{ asset('assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
@stop

<?php
    function limit_words($string, $word_limit)
    {
        $words = explode(" ",$string);
        return implode(" ", array_splice($words, 0, 5));
    }
?>

@section('contentPages')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>B2B CRM</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ URL::to('/dealer') }}">Home</a>
                </li>
                <li>
                    <a>B2B CRM</a>
                </li>
                <li class="active">
                    <strong>All Accessories</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
		
		
			<?php 
			$categoryID=base64_decode($catID);
			$catName=DB::table('accessory_category')->where('id','=',$categoryID)->where('deleted_at','=',NULL)->first();
				$getAccessoryData=DB::table('product_accessories')->where('category_id','=',$categoryID)->where('deleted_at','=',NULL)->get();
				$num=0;
				foreach($getAccessoryData as $accessory){
					
				if($num==0){
					echo '<div class="row">';
				}
			?>
	
			<div class="col-md-3">
				<div class="ibox">
					<div class="ibox-content product-box">

						<div class="product-imitation" style="min-height: 185px;">
							 <?php 
							if(!empty($accessory->accessory_image)){
								$cavatar='uploads/accessories/'.$accessory->accessory_image;
							} else{
								$cavatar='assets/img/placeholder300x300.png';
							}
							
							?>
							 <img alt="image" class="img-circles" src="{{asset($cavatar)}}" style="width: auto;max-width: 100%;">
						</div>
						<div class="product-desc">
							<span class="product-price">
                                    $<?php echo $accessory->price; ?>
                                </span>
							<small class="text-muted"><?php echo $catName->categoryName; ?></small>
							 <?php 
								if( $accessory->accessory_qty > 0){
								}else{
									echo '<small class="label label-danger pull-right">Out of stock</small>';
								}
									?>
							<a href="#" class="product-name"> <?php echo $accessory->accessory_name; ?></a>



							<div class="small m-t-xs">
								<?php
								$str='';
								if(!empty($accessory->accessory_description)){
									$string=$accessory->accessory_description;
									$string = strip_tags($string);

										if (strlen($string) > 150) {

											// truncate string
											$stringCut = substr($string, 0, 150);

											// make sure it ends in a word so assassinate doesn't become ass...
											$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... '; 
										}
										echo $string;
								}
									//echo $str;
								?>
							</div>
							<div class="m-t text-righ">
								<?php 
								if( $accessory->accessory_qty > 0){
									?>
									<a href="javascript:void(0);" data-toggle="modal" data-target="#orderpopup<?php echo $accessory->accessoriesToken; ?>"  class="btn btn-primary">Buy now <i class="fa fa-shopping-cart"></i> </a>
								<?php	
								}else{
									?>
									<a href="javascript:void(0);" disabled="disabled" class="btn btn-primary">Buy now <i class="fa fa-shopping-cart"></i> </a>
									<?php
								}
								$formurl="{{action('dealer\CartController@index')}}";
								?>
								
								<div class="modal inmodal" id="orderpopup<?php echo $accessory->accessoriesToken; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content animated fadeIn">

										<form action="{{action('dealer\CartController@index')}}" method="POST" enctype="multipart/form-data" class="products" id="">
												 
											
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
													<h4 class="modal-title"><?php echo $accessory->accessory_name; ?></h4>
												</div>
												<div class="modal-body col-md-12">
													<input type="hidden" name="_token" value="<?php echo csrf_token() ;?>"/>
													<input type="hidden" name="accessoryToken" value="<?php echo  base64_encode($accessory->accessoryID) ;?>"/>
													<label class="col-md-2">Qty</label>
													<div class="col-md-4" id="touchspin1qtydiv_<?php echo $accessory->accessoryID; ?>">
														<input class="touchspin_<?php echo $accessory->accessoryID; ?>" id="touchspin1qty_<?php echo $accessory->accessoryID; ?>" type="text" value="1" min="1" name="qty" max="<?php echo $accessory->accessory_qty;?>">
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
													<input type="submit" class="btn btn-primary" value="Save changes" />
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			$num++;
				if($num==4){
					echo '</div>';
					$num=0;
				}
				}
			?>

		




        </div>

@stop()

@section('pagescript')

	@include('admin.includes.commonscript')
    <script src="{{ asset('assets/js/plugins/pace/pace.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/dataTables/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
	<script type="text/javascript">
	$(function() {
			function datatblload(){
				 $('#stock_table').DataTable({
					dom: '<"html5buttons"B>lTfgitp',
					buttons: [
						/* {extend: 'csv', title:'Product Details'},
						{extend: 'excel', title:'Product Details'},
						{extend: 'pdf', title:'Product Details'}, */
					],
				   
				});
			}
		function orderqty()
		{
			var no = $('div.modal').length;
			//alert($('#productlist tr').length);
			for(var i=0; i<=no; i++)
			{
				var touchspain = '.touchspin_'+(i+1);
				
				$(touchspain).TouchSpin({
					buttondown_class: 'btn btn-white',
					buttonup_class: 'btn btn-white',
					min: 1, 
					max:$(touchspain).attr('max')
				});
			}
			 
		}
		orderqty();
		//orderqty();
    });
	</script>
@stop()