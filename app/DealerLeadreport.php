<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealerLeadreport extends Model
{
    public $timestamps=true;
    protected $dates = ['deleted_at'];
    protected $table = 'dealer_leadreport';

    protected $hidden = array('password', 'remember_token');

    protected $rules=[
        'id'=>'required',
        'leadreport_id'=>'required'
    ];
}
