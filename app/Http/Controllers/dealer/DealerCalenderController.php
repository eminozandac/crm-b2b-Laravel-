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
 

class DealerCalenderController extends Controller
{
    function today_task()
    {
        $task = new DealerTask();
        $dealer = new Dealer();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];


        $today_task_ar = array();
        $list_today_task_ar = array();

        $today_date = date('Y-m-d');
        $taks_ar = array('dealer_id' => $id);

        //Today Task
        $result_today = DealerTask::select('*')
            ->where(DB::raw(" month(assign_date)" ) , '=',  date('m'))
            ->where(DB::raw(" year(assign_date)" ) , '=',  date('Y'))
            ->where(DB::raw(" day(assign_date)" ) ,  '=', date('d'))
            ->where('read_status','=',0)
            ->where('dealer_id','=',$id)
            ->orWhere('created_at','like',date('Y-m-d').'%')
            ->get();

        if(!empty($result_today))
        {
            $no = 0;
            foreach ($result_today as $key => $value)
            {
                $today_task_ar[] = $value['title'];
                $list_today_task_ar[$no]['task_id'] = $value['task_id'];
                $list_today_task_ar[$no]['title'] = $value['title'];
                $list_today_task_ar[$no]['assign_date'] = date('m/d/Y',strtotime($value['assign_date']));
                $list_today_task_ar[$no]['completion_date'] = date('m/d/Y',strtotime($value['completion_date']));

                $no++;
            }
            \Session::put('dealerTodaytask' , $today_task_ar);
            \Session::save();

            \Session::put('dealerTodaytask_list' , $list_today_task_ar);
            \Session::save();
        }

        //Update OverDue Task Details
        $result_task = DealerTask::select('task_id','task_status')
            ->where('completion_date','<',date('Y-m-d').'T%')
            ->where('task_status','=','PENDING')
            ->where('dealer_id','=',$id)
            ->get();

        if(!empty($result_task))
        {
            $update_array['task_status'] = 'OVERDUE';
            foreach($result_task as $key => $value)
            {
                $task->where('task_id', $value->task_id)->update($update_array);
            }
        }
    }

    function calenderData()
    {
        $dealer = new Dealer();
        $status_math = array('PENDING' => '#F9690E', 'WAITING' => '#F7CA18', 'OVERDUE' => '#6C7A89', 'COMPLETE' => '#26A65B', 'CANCEL' => '#F22613');

        $dt = Carbon::now();

        $data = array();
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $event = array();
        $data_ar = array();
        $today_task_ar = array();

        $today_date = date('Y-m-d');
        $taks_ar = array('dealer_id' => $id);


        //All Task ID
        $result_task_data = DealerTask::select('*')->where('dealer_id','=',$id)->orderBy('assign_date','desc')->get();
        if(!empty($result_task_data))
        {
            $event = array('pass' => 1);
            $no=0;
            foreach($result_task_data as $key => $value)
            {
                $data_ar = array();
                unset($value['id']);
                unset($value['created']);
                unset($value['updated_at']);
                unset($value['deleted_at']);

                $data_ar = $value;
                $data_ar['color'] = '';
                $data_ar['task_user'] = '';

                if (array_key_exists($value->task_status, $status_math)) {
                    $data_ar['color'] = $status_math[$value->task_status];
                }
                $event['task_data'][] = $data_ar;
                $no++;
            }
        }else{
            $event = array('pass' => 0);
        }

        $event = json_encode($event,true);

        //Today Task
        $this->today_task();

        return $event;
    }

    function calenderDragData()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $today = date('Y-m-d');

        $result_task = DealerTask::select('*')
            ->where('dealer_id','=',$id)
            ->where(DB::raw(" month(assign_date)" ) , '=',  date('m'))
            ->where(DB::raw(" year(assign_date)" ) , '=',  date('Y'))
            ->where(DB::raw(" day(assign_date)" ) ,  '=', date('d'))
            ->orderBy('assign_date','desc')->get();

        return $result_task;
    }

    public function index()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        if(isset($id) && ($id != 0))
        {
            $event = $this->calenderData();
            $data['event'] = $event;

            $data['task_data'] = $this->calenderDragData();

			return View::make('dealer/task/taskCalender',$data);
        }else{
            return View::make('dealer/index');
        }
    }

    public function changeTask()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $data = Input::all();
        $task = new DealerTask();
        if(!empty($data))
        {
            $task_assigndate = date('Y-m-d',strtotime($data['data_assign_date']));
            $task_assigndate = $task_assigndate.'T'.date('H:i:s');
            $update_array['assign_date'] = $task_assigndate;

            $task_completiondate = date('Y-m-d',strtotime($data['data_completion_date']));
            $task_completiondate = $task_completiondate.'T'.date('H:i:s');
            $update_array['completion_date'] = $task_completiondate;

            $task->where('task_id', $data['data_taskID'])->update($update_array);

            $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Update Task Details';
            $logdata = array();
            $logdata['role'] = 'dealer';
            $logdata['role_id'] = $id;
            $logdata['operation'] = 'Update Task Details';
            $logdata['description'] = $description;
            $logdata['role_date'] = date('Y-m-d');

            $result_logdata = (new LogController)->index($logdata);
        }
    }


    public function readTask()
    {
        $id = 0;
        $sessionData=Session::get('dealerLog');
        $id = $sessionData['dealerID'];

        $data = Input::all();
        $task = new DealerTask();
        if(!empty($data))
        {
            $update_array['read_status'] = 1;
            $task->where('task_id', $data['data_taskID'])->update($update_array);

            Session::forget('dealerTodaytask');
            Session::forget('dealerTodaytask_list');

            $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Read Task Details';
            $logdata = array();
            $logdata['role'] = 'dealer';
            $logdata['role_id'] = $id;
            $logdata['operation'] = 'Read Task Details';
            $logdata['description'] = $description;
            $logdata['role_date'] = date('Y-m-d');

            $result_logdata = (new LogController)->index($logdata);
        }
    }

}
