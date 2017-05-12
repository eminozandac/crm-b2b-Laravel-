<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'category';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required'
    ];

}
