<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'product_order';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'orderID'=>'required'
    ];

}
