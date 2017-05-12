<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model {
 	 
	public $timestamps=true;

    protected $table = 'attribute';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'attributeID'=>'required'
    ];

}
