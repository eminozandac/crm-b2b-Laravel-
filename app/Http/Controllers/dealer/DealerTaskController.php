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
use App\DealerTask;
use XeroLaravel;
use Cart;
use Datatables;
use URL;
use App\Http\Controllers\logdata\LogController;
use App\Http\Controllers\dealer\DealerCalenderController;

class DealerTaskController extends Controller
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
            $priority_status = Config('constants.TASK_PRIORITY');
            $data['priority_status'] = $priority_status;

            $priority_status_color = Config('constants.TASK_PRIORITY_COLOR');
            $data['priority_status_color'] = $priority_status_color;

            $task_status = Config('constants.TASK_STATUS');
            $data['task_status'] = $task_status;

            $task_status_color = Config('constants.TASK_STATUS_COLOR');
            $data['task_status_color'] = $task_status_color;

            (new DealerCalenderController)->today_task();
			
			$data['task_list'] = DB::table('dealer_task')->where('dealer_id','=',$id)->where('deleted_at','=',NULL)->get();

            $data['task_list_pending'] = DB::table('dealer_task')->where('dealer_id','=',$id)->where('task_status','!=','COMPLETE')->where('deleted_at','=',NULL)->get();
            $data['task_list_complete'] = DB::table('dealer_task')->where('dealer_id','=',$id)->where('task_status','=','COMPLETE')->where('deleted_at','=',NULL)->get();
			
			return View::make('dealer/task/taskList',$data);
        }else{
            return View::make('dealer/index');
        }
    }



    public function dataTask()
    {

    }

    public function addTask()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $dealer = new DealerTask();
        if(isset($id) && ($id != 0))
        {
            $priority_status = Config('constants.TASK_PRIORITY');
            $data['priority_status'] = $priority_status;

            $priority_status_color = Config('constants.TASK_PRIORITY_COLOR');
            $data['priority_status_color'] = $priority_status_color;

            (new DealerCalenderController)->today_task();
            $data['dealer_id'] = $id;

            $Unique='';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }

            $data['task_id'] = $Unique;
            $data['title'] = '';
            $data['assign_date'] = '';
            $data['completion_date'] = '';
            $data['description'] = '';
            $data['task_status_selected'] = 'PENDING';
            $data['task_priority_selected'] = '';

            $data['task_status'] = Config::get('constants.TASK_STATUS');

            if(is_array(Session::get('session_data')) && !empty(Session::get('session_data')))
            {
                $session_data = Session::get('session_data');
                $data['task_id'] = $session_data['task_id'];
                $data['title'] = $session_data['title'];
                $data['assign_date'] = $session_data['assign_date'];
                $data['completion_date'] = $session_data['completion_date'];
                $data['description'] = $session_data['description'];
                $data['task_status_selected'] = $session_data['task_status'];
                $data['task_priority_selected'] = $session_data['task_priority'];
            }
            return View::make('dealer.task.taskAdd',$data);
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
            $task = new DealerTask();

            $data_task = Input::all();
            $rules = array(
                'title'=>'required|not_in:0',
                'assign_date'=>'required|not_in:0',
                'completion_date'=>'required|not_in:0',
                'description'=>'required|not_in:0',
                'task_status'=>'required|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {
                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$messages);
                return Redirect::to('/dealer/taskadd');
            }else
            {
                $task_assigndate = $task_completiondate = '';

                $task_assigndate = date('Y-m-d',strtotime($data_task['assign_date']));
                $task_assigndate = $task_assigndate.'T'.date('H:i:s');

                $task_completiondate = date('Y-m-d',strtotime($data_task['completion_date']));
                $task_completiondate = $task_completiondate.'T'.date('H:i:s');


                $task->task_id = $data_task['task_id'];
                $task->dealer_id = $id;

                $task->title = $data_task['title'];
                $task->description = $data_task['description'];
                $task->task_status = strtoupper($data_task['task_status']);
                $task->assign_date = $task_assigndate;
                $task->completion_date = $task_completiondate;

                $task->task_priority = $data_task['task_priority'];
                $task->save();

                $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Add Task Details';
                $logdata = array();
                $logdata['role'] = 'dealer';
                $logdata['role_id'] = $id;
                $logdata['operation'] = 'Add Task Details';
                $logdata['description'] = $description;
                $logdata['role_date'] = date('Y-m-d');

                $result_logdata = (new LogController)->index($logdata);

                Session::forget('session_data');
                Session::save();

                Session::flash('operationSucess','Successfully Add task');
                return Redirect::to('/dealer/task');
                exit;
            }

        }else{
            return Redirect::to('/dealer/dashboard');
        }
    }

    public function editTask($taskid)
    {
        $result_data = array();
        $data = array();

        $task = new DealerTask();

        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $dealer_id = $id;

        if(isset($id) && ($id != 0))
        {
            $priority_status = Config('constants.TASK_PRIORITY');
            $data['priority_status'] = $priority_status;

            $priority_status_color = Config('constants.TASK_PRIORITY_COLOR');
            $data['priority_status_color'] = $priority_status_color;

            (new DealerCalenderController)->today_task();
            $data['title'] = '';
            $data['assign_date'] = '';
            $data['completion_date'] = '';
            $data['description'] = '';
            $data['task_status'] = '';

            //$lead_ar = array('dealer_id' => $dealer_id,'task_id'=>$taskid);
			$lead_ar = array('task_id'=>$taskid);
            $result_data = DB::table('dealer_task')->where($lead_ar)->first();

            Session::forget('session_data');
            Session::save();

            $data['task_status'] = Config::get('constants.TASK_STATUS');

            if(!empty($result_data)){
                $data['task'] = $result_data;
				return View::make('dealer.task.taskEdit',$data);
            }else{
               return Redirect::to('/dealer/taskadd');
            }
                
        }else{
            return Redirect::to('/dealer/dashboard');
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
            $task = new DealerTask();
            $data_task = Input::all();

            $rules = array(
                'title'=>'required|not_in:0',
                'assign_date'=>'required|not_in:0',
                'completion_date'=>'required|not_in:0',
                'description'=>'required|not_in:0',
                'task_status'=>'required|not_in:0',
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {
                $messages = $validator->messages();
                Session::flash('operationFaild','Some thing Went wrong');
                Session::set('session_data',$data_task);
                return Redirect::to('/dealer/edit-task/'.$data_task['task_id']);
            }else
            {
                $update_array = array();

                $task_assigndate = $task_completiondate = '';

                $task_assigndate = date('Y-m-d',strtotime($data_task['assign_date']));
                $task_assigndate = $task_assigndate.'T'.date('H:i:s');

                $task_completiondate = date('Y-m-d',strtotime($data_task['completion_date']));
                $task_completiondate = $task_completiondate.'T'.date('H:i:s');

                $update_array['dealer_id'] = $id;


                $update_array['title'] = $data_task['title'];
                $update_array['description'] = $data_task['description'];
                $update_array['task_status'] = $data_task['task_status'];
                $update_array['assign_date'] =  $task_assigndate;
                $update_array['completion_date'] =  $task_completiondate;

                $update_array['task_priority'] = $data_task['task_priority'];

                Session::forget('session_data');
                Session::save();

                $response = $task->where('id', $data_task['id'])->first();
                if (!empty($response))
                {
                    $task->where('id', $data_task['id'])->update($update_array);
                    Session::flash('operationSucess','Successfully Edit Task');

                    $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Update Task Details';
                    $logdata = array();
                    $logdata['role'] = 'dealer';
                    $logdata['role_id'] = $id;
                    $logdata['operation'] = 'Update Task Details';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    return Redirect::to('/dealer/task');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/dealer/task');
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
            $task = new DealerTask();
            $data_delete = Input::all();
            $task_id = $data_delete['task_id'];
            $task->where('task_id', '=', $task_id)->delete();
        }
    }

    public  function changeStatusData()
    {
        $data_post = Input::all();
        $id = 0;
        $sessionData=Session::get('adminLog');
        $id = $sessionData['adminID'];
        if(isset($id) && ($id != 0))
        {
            $task = new DealerTask();
            $task_id = $data_post['data_ID'];
            $status = strtoupper($data_post['data_status']);
            if($status == 'COMPLETE'){
                if(count($task_id) > 0)
                {
                    foreach($task_id as $key => $value)
                    {
                        $task->where('task_id', '=', $value)->update(array('task_status' => 'COMPLETE'));
                    }
                }
            }
        }
    }
}
