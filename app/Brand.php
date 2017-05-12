<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'brand';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required'
    ];

}
