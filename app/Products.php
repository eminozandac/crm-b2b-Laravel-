<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'products';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required'
    ];

}
