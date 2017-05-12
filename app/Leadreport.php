<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leadreport extends Model
{
    public $timestamps=true;
    protected $dates = ['deleted_at'];
    protected $table = 'staff_leadreport';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required',
        'leadreport_id'=>'required'
    ];
}
