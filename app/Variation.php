<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'variation';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'variationID'=>'required'
    ];

}
