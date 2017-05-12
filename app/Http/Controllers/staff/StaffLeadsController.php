<?php

namespace App\Http\Controllers\staff;

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
use Config;
use App\Staff;
use App\Leadreport;
use XeroLaravel;
use Cart;
use Datatables;
use URL;
use App\Http\Controllers\logdata\LogController;

class StaffLeadsController extends Controller
{
    function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    function getTokenProduct(){
        $length=4;
        $token = "";
        $codeAlphabet= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
        }
        return $token.rand(111111,999999);
    }

    public function index()
    {
        $dt = Carbon::now();

        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];

        if(isset($id) && ($id != 0))
        {
            $lead_status = Config('constants.LEAD_STATUS');
            $data['lead_status_report'] = $lead_status;

            $lead_status_color = Config('constants.LEAD_STATUS_COLOR');
            $data['lead_status_report_color'] = $lead_status_color;

            //$data['leadreport'] = Leadreport::select('*')->where('staff_id','=',$id)->orderBy('id','desc')->get();
            $data['leadreport'] = Leadreport::select('*')->orderBy('id','desc')->get();

			return View::make('staff/leads/leadsList',$data);
        }else{
            return View::make('staff/index');
        }
    }

    public function dataLeadsReport()
    {

    }

    public function addLeadreport()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $data['staff_id'] = $id;

            $Unique='';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }
            $data['leadreport_id'] = $Unique;

            $data['title'] = '';
            $data['name'] = '';
            $data['emailID'] = '';
            $data['phone'] = '';
            $data['description'] = '';

            if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['leadreport_id'] = $session_data['leadreport_id'];
                $data['title'] = $session_data['title'];
                $data['name'] = $session_data['name'];
                $data['emailID'] = $session_data['emailID'];
                $data['phone'] = $session_data['phone'];
                $data['description'] = $session_data['description'];
            }

            $lead_status = Config('constants.LEAD_STATUS');
            $data['lead_status_report'] = $lead_status;

            $lead_status_color = Config('constants.LEAD_STATUS_COLOR');
            $data['lead_status_report_color'] = $lead_status_color;

            $staff = new Staff();
            $data['staff_assign'] = Staff::select('staff_id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))->orderBy('first_name')->lists('full_name', 'staff_id');

            return View::make('staff.leads.leadsAdd',$data);
        }else{
            return Redirect::to('/staff');
        }
    }

    public function  saveData()
    {
        $dt = Carbon::now();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $leadreport = new Leadreport;
 
            $data_leadreport = Input::all();
            $rules = array(
                'assign_id'=>'required|min:1|max:1000|not_in:0',
                'title'=>'required|min:3|max:30|not_in:0',
                'name'=>'required|min:3|max:30|not_in:0',
                'description'=>'required|min:1|max:5000|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                if(isset($messages->get('emailID')[0])){
                    Session::flash('operationFaild','The email address has already been taken.');
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                }
                Session::set('session_data',$data_leadreport);
                return Redirect::to('/staff/leadsreportadd');
            }else
            {
                $leadreport->create_role = 'staff';
                $leadreport->assign_role = 'staff';

                if(isset($data_leadreport['assign_id']) && !empty($data_leadreport['assign_id']) && is_array($data_leadreport['assign_id'])){
                    $leadreport->assign_id = implode(',',$data_leadreport['assign_id']);
                }else{
                    $leadreport->assign_id = $data_leadreport['assign_id'];
                }


                $leadreport->leadreport_id = $data_leadreport['leadreport_id'];
                $leadreport->staff_id = $id;
                $leadreport->title = $data_leadreport['title'];
                $leadreport->name = $data_leadreport['name'];
                $leadreport->lead_status = $data_leadreport['lead_status'];
                $leadreport->emailID = $data_leadreport['emailID'];
                $leadreport->phone = $data_leadreport['phone'];
                $leadreport->description = $data_leadreport['description'];
                $leadreport->date_create = $dt->toDateString();
                $leadreport->save();

                $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Add Lead Report Details';
                $logdata = array();
                $logdata['role'] = 'staff';
                $logdata['role_id'] = $id;
                $logdata['operation'] = 'Add Lead Report Details';
                $logdata['description'] = $description;
                $logdata['role_date'] = date('Y-m-d');

                $result_logdata = (new LogController)->index($logdata);

                Session::forget('session_data');
                Session::save();

                Session::flash('operationSucess','Successfully Add LeadReport');
                return Redirect::to('/staff/leadsreport');
                exit;
            }

        }else{
            return Redirect::to('/staff');
        }
    }

    public function editLeadreport($leadsid)
    {
        $result_data = array();
        $data = array();

        $leadreport = new Leadreport;
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];

        $staff_id = $id;

        if(isset($id) && ($id != 0))
        {
            $data['title'] = '';
            $data['name'] = '';
            $data['emailID'] = '';
            $data['phone'] = '';
            $data['description'] = '';

            //$lead_ar = array('staff_id' => $staff_id,'leadreport_id'=>$leadsid);
            $lead_ar = array('leadreport_id' => $leadsid);
            $result_data = DB::table('staff_leadreport')->where($lead_ar)->first();

            Session::forget('session_data');
            Session::save();

            $lead_status = Config('constants.LEAD_STATUS');
            $data['lead_status_report'] = $lead_status;

            $lead_status_color = Config('constants.LEAD_STATUS_COLOR');
            $data['lead_status_report_color'] = $lead_status_color;

            $staff = new Staff();
            $data['staff_assign'] = Staff::select('staff_id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))->orderBy('first_name')->lists('full_name', 'staff_id');

            if(!empty($result_data)){
                $data['leads'] = $result_data;
                return View::make('staff.leads.leadsEdit',$data);
            }else{
                return Redirect::to('/staff/leadsreportadd');
            }
        }else{
            return Redirect::to('/staff');
        }
    }


    public function  editData()
    {
        $dt = Carbon::now();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $leadreport = new Leadreport();

            $data_leadreport = Input::all();
            $rules = array(
                'title'=>'required|min:3|max:30|not_in:0',
                'name'=>'required|min:3|max:30|not_in:0',
                'description'=>'required|min:1|max:5000|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {

                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$data_leadreport);
                return Redirect::to('/staff/leadsreportadd');
            }else
            {
				$update_array = array();

                $update_array['create_role'] = 'staff';
                $update_array['assign_role'] = 'staff';

                if(isset($data_leadreport['assign_id']) && !empty($data_leadreport['assign_id']) && is_array($data_leadreport['assign_id'])){
                    $update_array['assign_id'] = implode(',',$data_leadreport['assign_id']);
                }else{
                    $update_array['assign_id'] = $data_leadreport['assign_id'];
                }

                $update_array['staff_id'] = $id;
                $update_array['title'] = $data_leadreport['title'];
                $update_array['name'] = $data_leadreport['name'];
                $update_array['lead_status'] = $data_leadreport['lead_status'];
                $update_array['emailID'] = $data_leadreport['emailID'];
                $update_array['phone'] = $data_leadreport['phone'];
                $update_array['description'] = $data_leadreport['description'];
                $update_array['date_create'] =  $dt->toDateString();;

                Session::forget('session_data');
                Session::save();

                $response = $leadreport->where('id', $data_leadreport['id'])->first();
				
				if (!empty($response)) 
				{
                    $leadreport->where('id', $data_leadreport['id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Edit Leads Report');

                    $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Update Lead Report Details';
                    $logdata = array();
                    $logdata['role'] = 'staff';
                    $logdata['role_id'] = $id;
                    $logdata['operation'] = 'Update Lead Report Details';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    $result_logdata = (new LogController)->index($logdata);

                    return Redirect::to('/staff/leadsreport');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/staff/leadsreport');
                    exit;
                }
            }

        }else{
            return Redirect::to('/staff');
        }
    }


    public  function  deleteData()
    {
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $leadreport = new Leadreport;
            $data_delete = Input::all();
            $leadreport_id = $data_delete['leadreport_id'];
            $leadreport->where('leadreport_id', '=', $leadreport_id)->delete();
        }
    }
}
