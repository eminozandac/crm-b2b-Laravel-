<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {
 	 
	public $timestamps=true;

    protected $table = 'group';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'groupID'=>'required'
    ];

}
