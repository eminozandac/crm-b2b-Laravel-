<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use View;
use Validator;
use Hash;
use Carbon\Carbon;
use Input;
use Mail;
use Form;
use Auth;
use File;
 

class CalendarController extends Controller
{
	public function eventCalender(){
		$sessionData=Session::get('adminLog');
		if(isset($sessionData) && !empty($sessionData['adminID'])){
			return view('admin.calender.eventCalender')->with('sessionData',$sessionData);
		}else{
			 return Redirect::to('/');
		}
    }
}
