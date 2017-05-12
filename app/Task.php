<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $timestamps=true;
    protected $dates = ['deleted_at'];
    protected $table = 'staff_task';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required',
        'task_id'=>'required'
    ];
}
