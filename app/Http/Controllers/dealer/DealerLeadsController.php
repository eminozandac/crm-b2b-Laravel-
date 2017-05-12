<?php

namespace App\Http\Controllers\dealer;

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
use App\Dealer;
use App\DealerLeadreport;
use XeroLaravel;
use Cart;
use Datatables;
use URL;
use App\Http\Controllers\logdata\LogController;

class DealerLeadsController extends Controller
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
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        if(isset($id) && ($id != 0))
        {
            $lead_status = Config('constants.LEAD_STATUS');
            $data['lead_status_report'] = $lead_status;

            $lead_status_color = Config('constants.LEAD_STATUS_COLOR');
            $data['lead_status_report_color'] = $lead_status_color;

            $data['leadreport'] = DealerLeadreport::select('*')->where('dealer_id','=',$id)->orderBy('id','desc')->get();

			return View::make('dealer/leads/leadsList',$data);
        }else{
            return View::make('dealer/index');
        }
    }

    public function dataLeadsReport()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $leadreport = new DealerLeadreport();
        $data = DealerLeadreport::select('*')->where('dealer_id','=',$id)->orderBy('id','desc');

        return Datatables::of($data)

            ->addColumn('Title', function ($data) {
                if($data->title != ''){
                    return $data->title;
                }else{
                    return '---';
                }
            })

            ->addColumn('Customer Name', function ($data) {
                if($data->name != ''){
                    return $data->name;
                }else{
                    return '---';
                }
            })
			->addColumn('Status', function ($data) {
                if($data->lead_status != ''){
                    return $data->lead_status;
                }else{
                    return '---';
                }
            })

            ->addColumn('EmailID', function ($data) {
                if($data->emailID != ''){
                    return $data->emailID;
                }else{
                    return '---';
                }
            })

            ->addColumn('Phone', function ($data) {
                if($data->phone != ''){
                    return $data->phone;
                }else{
                    return '---';
                }
            })

            ->addColumn('Description', function ($data) {
                if($data->description != ''){
                    return $data->description;
                }else{
                    return '---';
                }
            })


            ->addColumn('Action', function ($data) {
                $url = URL::to('dealer/edit-leadreport', $data->leadreport_id);
                $html = "";
                $html.= "<a href=\"$url\"  data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-pencil\"></i></a>";
                $html.= " ";
                $deleted = (string)$data->leadreport_id;
                $html.= "<a href=\"javascript:void(0)\"  data-toggle=\"tooltip\" title=\"Delete\" onclick=\"deleted('$deleted')\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-trash-o\"></i></a>";
                return $html;
            })
            ->make(true);
    }

    public function addLeadreport()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $data['dealer_id'] = $id;

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

            return View::make('dealer.leads.leadsAdd',$data);
        }else{
            return Redirect::to('/dealer');
        }
    }

    public function  saveData()
    {
        $dt = Carbon::now();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $leadreport = new DealerLeadreport();
 
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
                if(isset($messages->get('emailID')[0])){
                    Session::flash('operationFaild','The email address has already been taken.');
                }else{
                    Session::flash('operationFaild','Some thing Went wrong');
                }
                Session::set('session_data',$data_leadreport);
                return Redirect::to('/dealer/leadsreportadd');
            }else
            {
                $leadreport->leadreport_id = $data_leadreport['leadreport_id'];
                $leadreport->dealer_id = $id;
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
                $logdata['role'] = 'dealer';
                $logdata['role_id'] = $id;
                $logdata['operation'] = 'Add Lead Report Details';
                $logdata['description'] = $description;
                $logdata['role_date'] = date('Y-m-d');

                $result_logdata = (new LogController)->index($logdata);

                Session::forget('session_data');
                Session::save();

                Session::flash('operationSucess','Successfully Add LeadReport');
                return Redirect::to('/dealer/leadsreport');
                exit;
            }

        }else{
            return Redirect::to('/dealer');
        }
    }

    public function editLeadreport($leadsid)
    {
        $result_data = array();
        $data = array();

        $leadreport = new DealerLeadreport();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $dealer_id = $id;

        if(isset($id) && ($id != 0))
        {
            $data['title'] = '';
            $data['name'] = '';
            $data['emailID'] = '';
            $data['phone'] = '';
            $data['description'] = '';

            $lead_ar = array('dealer_id' => $dealer_id,'leadreport_id'=>$leadsid);
            $result_data = DB::table('dealer_leadreport')->where($lead_ar)->first();

            Session::forget('session_data');
            Session::save();

            $lead_status = Config('constants.LEAD_STATUS');
            $data['lead_status_report'] = $lead_status;

            $lead_status_color = Config('constants.LEAD_STATUS_COLOR');
            $data['lead_status_report_color'] = $lead_status_color;

            if(!empty($result_data)){
                $data['leads'] = $result_data;
                return View::make('dealer.leads.leadsEdit',$data);
            }else{
                return Redirect::to('/dealer/leadsreportadd');
            }
        }else{
            return Redirect::to('/dealer');
        }
    }


    public function  editData()
    {
        $dt = Carbon::now();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $leadreport = new DealerLeadreport();

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
                return Redirect::to('/dealer/leadsreportadd');
            }else
            {
				$update_array = array();

                $update_array['dealer_id'] = $id;
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
                    $logdata['role'] = 'dealer';
                    $logdata['role_id'] = $id;
                    $logdata['operation'] = 'Update Lead Report Details';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    $result_logdata = (new LogController)->index($logdata);

                    return Redirect::to('/dealer/leadsreport');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/dealer/leadsreport');
                    exit;
                }
            }

        }else{
            return Redirect::to('/dealer');
        }
    }


    public  function  deleteData()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];
        if(isset($id) && ($id != 0))
        {
            $leadreport = new DealerLeadreport();
            $data_delete = Input::all();
            $leadreport_id = $data_delete['leadreport_id'];
            $leadreport->where('leadreport_id', '=', $leadreport_id)->delete();
        }
    }
}
