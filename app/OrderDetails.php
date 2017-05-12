<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'product_order_details';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'orderDetailID'=>'required'
    ];

}
