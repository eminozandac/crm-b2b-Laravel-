<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'dealer';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required'
    ];

}
