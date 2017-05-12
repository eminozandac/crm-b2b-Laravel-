<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'discount';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required'
    ];

}
