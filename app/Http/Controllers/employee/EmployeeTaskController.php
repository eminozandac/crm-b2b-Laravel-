<?php

namespace App\Http\Controllers\employee;

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
use App\Employee;
use App\Task;
use XeroLaravel;
use Cart;
use Datatables;
use URL;
use App\Http\Controllers\logdata\LogController;
use App\Http\Controllers\employee\EmployeeCalenderController;
use App\Http\Controllers\staff\StaffCalenderController;

class EmployeeTaskController extends Controller
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
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        $employee_uniqueID = $sessionData['unique_ID'];

        $data['task_assign'] = '';
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


            $employee = new Employee();
            $data['task_assign'] = Employee::select('employee_id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))->orderBy('first_name')->lists('full_name', 'employee_id');
            (new EmployeeCalenderController)->today_task();

            //Pending
            $total_pending_ar = array();
            $select1_pending = "SELECT count('id') as total_pending FROM `staff_task` WHERE task_status != 'COMPLETE' AND `role` = 'employee' AND `task_assign`='".$employee_uniqueID."'  ";
            $result1 = DB::select($select1_pending);
            if(!empty($result1))
            {
                foreach($result1 as $key => $value) {
                    array_push($total_pending_ar,$value->total_pending);
                }
            }

            $select2_pending = "SELECT count('id') as total_pending FROM `staff_task` WHERE task_status != 'COMPLETE' AND `create_role` = 'employee' AND `staff_id`='".$id."'  ";
            $result2 = DB::select($select2_pending);
            if(!empty($result2))
            {
                foreach($result2 as $key => $value) {
                    array_push($total_pending_ar,$value->total_pending);
                }
            }
            $data['total_pending_task'] = array_sum($total_pending_ar);

            //Complete
            $total_complete_ar = array();
            $select1_complete = "SELECT count('id') as total_complete FROM `staff_task` WHERE `task_status` = 'COMPLETE' AND `role` = 'employee' AND `task_assign`='".$employee_uniqueID."'  ";
            $result1 = DB::select($select1_complete);
            if(!empty($result1))
            {
                foreach($result1 as $key => $value) {
                    array_push($total_complete_ar,$value->total_complete);
                }
            }

            $select2_complete = "SELECT count('id') as total_complete FROM `staff_task` WHERE `task_status` = 'COMPLETE' AND `create_role` = 'employee' AND `staff_id`='".$id."'  ";
            $result2 = DB::select($select2_complete);
            if(!empty($result2))
            {
                foreach($result2 as $key => $value) {
                    array_push($total_complete_ar,$value->total_complete);
                }
            }
            $data['total_complete_task'] = array_sum($total_complete_ar);

            /*$data['task_list'] = DB::table('staff_task')
                ->where('role','=','employee')
                ->where('task_assign','=',$employee_uniqueID)
                ->orWhere('staff_id','=',$id)
                ->orwhere('create_role','=','employee')
                ->where('deleted_at','=',NULL)->get();*/

            $data['task_list_pending'] = DB::select("SELECT * FROM `staff_task` WHERE `task_status` != 'COMPLETE' AND (`role` = 'employee' AND `task_assign`='".$employee_uniqueID."') OR (`create_role` = 'employee' AND `staff_id`='".$id."') ");

            $data['task_list_complete'] = DB::select("SELECT * FROM `staff_task` WHERE `task_status` = 'COMPLETE' AND (`role` = 'employee' AND `task_assign`='".$employee_uniqueID."') OR (`create_role` = 'employee' AND `staff_id`='".$id."') ");
			
			return View::make('employee/task/taskList',$data);
        }else{
            return View::make('employee/index');
        }
    }



    public function dataTask()
    {

    }

    public function readTask()
    {
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];

        $data = Input::all();
        $task = new Task();
        if(!empty($data))
        {
            $update_array['read_status'] = 1;
            $task->where('task_id', $data['data_taskID'])->update($update_array);

            $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Read Task Details';
            $logdata = array();
            $logdata['role'] = 'staff';
            $logdata['role_id'] = $id;
            $logdata['operation'] = 'Read Task Details';
            $logdata['description'] = $description;
            $logdata['role_date'] = date('Y-m-d');

            $result_logdata = (new LogController)->index($logdata);
        }
    }

    public function addTask()
    {
        $data = array();
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];

        $staff = new Staff();
        if(isset($id) && ($id != 0))
        {
            $priority_status = Config('constants.TASK_PRIORITY');
            $data['priority_status'] = $priority_status;

            $priority_status_color = Config('constants.TASK_PRIORITY_COLOR');
            $data['priority_status_color'] = $priority_status_color;

            (new StaffCalenderController)->today_task();
            $data['staff_id'] = $id;

            $Unique='';
            for($j=0;$j < 4;$j++)
            {
                if($j!=3){$dash='-';}else{$dash='';}
                $Unique .= $this->getTokenProduct().$dash;
            }
            $data['task_id'] = $Unique;
            $data['task_assign'] = '';

            $data['title'] = '';
            $data['assign_date'] = '';
            $data['completion_date'] = '';
            $data['description'] = '';
            $data['task_status_selected'] = 'PENDING';
            $data['task_priority_selected'] = '';

            /*$data['task_assign'] =  DB::table('staff')->where('status','=',1)->lists('emailID','staff_id');*/

            $data['task_assign'] = Staff::select('staff_id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))->orderBy('first_name')->lists('full_name', 'staff_id');

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
                $data['task_assign_selected'] = $session_data['task_assign'];
                $data['task_priority_selected'] = $session_data['task_priority'];
            }
            return View::make('employee.task.taskAdd',$data);
        }else{
            return Redirect::to('/employee');
        }
    }

    public function  saveData()
    {
        $dt = Carbon::now();
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        if(isset($id) && ($id != 0))
        {
            $task = new Task();
            $staff = new Staff();
            $employee = new Employee();

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
                return Redirect::to('/employee/taskadd');
            }else
            {
                $task_assigndate = $task_completiondate = '';

                $task_assigndate = date('Y-m-d',strtotime($data_task['assign_date']));
                $task_assigndate = $task_assigndate.'T'.date('H:i:s');

                $task_completiondate = date('Y-m-d',strtotime($data_task['completion_date']));
                $task_completiondate = $task_completiondate.'T'.date('H:i:s');


                $task->task_id = $data_task['task_id'];
                $task->staff_id = $id;
                $task->create_role = 'employee';

                $data_task['role'] = 'staff';
                $task->role = $data_task['role'];
                $task->task_assign = $data_task['task_assign'];

                $task->title = $data_task['title'];
                $task->description = $data_task['description'];
                $task->task_status = strtoupper($data_task['task_status']);
                $task->assign_date = $task_assigndate;
                $task->completion_date = $task_completiondate;

                $task->task_priority = $data_task['task_priority'];
                $task->save();

                $staff_emailID_ar = array();
                $staff_fullname_ar = array();

                if(isset($data_task['send_mailMessage']) && ($data_task['send_mailMessage'] == 1) )
                {
                    if(isset($data_task['task_assign']) && (!empty($data_task['task_assign'])))
                    {
                        $task_assign = $data_task['task_assign'];
                        if(is_array($task_assign))
                        {
                            foreach($task_assign as $key_staff_assign => $value_staff_assign)
                            {
                                $staff_emailID_result = '';

                                if($data_task['role'] == 'staff'){
                                    $staff_emailID_result = $staff->select('emailID','first_name','last_name')->where('staff_id','=',$value_staff_assign)->first();
                                }
                                if($data_task['role'] == 'employee')
                                {
                                    $staff_emailID_result = $employee->select('emailID','first_name','last_name')->where('employee_id','=',$value_staff_assign)->first();
                                }

                                $staff_emailID = $staff_emailID_result->emailID;
                                array_push($staff_emailID_ar,$staff_emailID);

                                $staff_name = $staff_emailID_result->first_name.' '.$staff_emailID_result->last_name;
                                array_push($staff_fullname_ar,$staff_name);
                            }
                        }else{
                            $staff_emailID_result = '';

                            if($data_task['role'] == 'staff'){
                                $staff_emailID_result = $staff->select('emailID','first_name','last_name')->where('staff_id','=',$task_assign)->first();
                            }
                            if($data_task['role'] == 'employee')
                            {
                                $staff_emailID_result = $employee->select('emailID','first_name','last_name')->where('employee_id','=',$task_assign)->first();
                            }

                            $staff_emailID = $staff_emailID_result->emailID;
                            array_push($staff_emailID_ar,$staff_emailID);

                            $staff_name = $staff_emailID_result->first_name.' '.$staff_emailID_result->last_name;
                            array_push($staff_fullname_ar,$staff_name);
                        }
                    }
                }

                $info = array(
                    'title' => $data_task['title'],
                    'assign_date' => $data_task['assign_date'],
                    'completion_date' => $data_task['completion_date'],
                    'description' => $data_task['description'],
                    'task_status' => $data_task['task_status'],
                    'task_priority' => $data_task['task_priority'],
                    'assign_user' => implode(',',$staff_fullname_ar),
                    "loginUrl" => URL::to('/')
                );

                if(!empty($staff_emailID_ar)){

                    foreach($staff_emailID_ar as $key_email => $value_email)
                    {
                        $email = $value_email;
                        $info['emailID']= $email;

                        Mail::send('email_templates.staffTaskmessage',['data_info' => $info], function($message) use ($email)
                        {
                            $message->to($email)->subject('CRM - Assign Task Details');
                        });
                    }
                }

                $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Add Task Details';
                $logdata = array();
                $logdata['role'] = 'employee';
                $logdata['role_id'] = $id;
                $logdata['operation'] = 'Add Task Details';
                $logdata['description'] = $description;
                $logdata['role_date'] = date('Y-m-d');

                $result_logdata = (new LogController)->index($logdata);

                Session::forget('session_data');
                Session::save();

                Session::flash('operationSucess','Successfully Add task');
                return Redirect::to('/employee/');
                exit;
            }

        }else{
            return Redirect::to('/employee/dashboard');
        }
    }


    public function editTask($taskid)
    {
        $result_data = array();
        $data = array();

        $task = new Task();
        $staff = new Staff();
        $employee = new Employee();

        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];

        $staff_id = $id;

        if(isset($id) && ($id != 0))
        {
            $priority_status = Config('constants.TASK_PRIORITY');
            $data['priority_status'] = $priority_status;

            $priority_status_color = Config('constants.TASK_PRIORITY_COLOR');
            $data['priority_status_color'] = $priority_status_color;

            (new StaffCalenderController)->today_task();
            $data['title'] = '';
            $data['assign_date'] = '';
            $data['completion_date'] = '';
            $data['description'] = '';
            $data['task_status'] = '';

            //$lead_ar = array('staff_id' => $staff_id,'task_id'=>$taskid);
            $lead_ar = array('task_id'=>$taskid);
            $result_data = DB::table('staff_task')->where($lead_ar)->first();

            Session::forget('session_data');
            Session::save();

            /* $data['task_assign'] =  DB::table('staff')->where('status','=',1)->lists('emailID','staff_id');*/

            $data['task_assign'] = Staff::select('staff_id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))->orderBy('first_name')->lists('full_name', 'staff_id');

            $data['task_status'] = Config::get('constants.TASK_STATUS');


            if(!empty($result_data)){
                $data['task'] = $result_data;
                return View::make('employee.task.taskEdit',$data);
            }else{
                return Redirect::to('/employee/taskadd');
            }

        }else{
            return Redirect::to('/employee/dashboard');
        }
    }


    public function  editData()
    {
        $dt = Carbon::now();
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        if(isset($id) && ($id != 0))
        {
            $task = new Task();
            $staff = new Staff();
            $employee = new Employee();

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
                return Redirect::to('/employee/edit-task/'.$data_task['task_id']);
            }else
            {
                $update_array = array();

                $task_assigndate = $task_completiondate = '';

                $task_assigndate = date('Y-m-d',strtotime($data_task['assign_date']));
                $task_assigndate = $task_assigndate.'T'.date('H:i:s');

                $task_completiondate = date('Y-m-d',strtotime($data_task['completion_date']));
                $task_completiondate = $task_completiondate.'T'.date('H:i:s');

                $update_array['staff_id'] = $id;
                $update_array['create_role'] = 'employee';

                $data_task['role'] = 'staff';
                $update_array['role'] = $data_task['role'];
                $update_array['task_assign'] = $data_task['task_assign'];

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

                    $staff_emailID_ar = array();
                    $staff_fullname_ar = array();
                    if(isset($data_task['send_mailMessage']) && ($data_task['send_mailMessage'] == 1) )
                    {
                        if(isset($data_task['task_assign']) && (!empty($data_task['task_assign'])))
                        {
                            $task_assign = $data_task['task_assign'];
                            foreach($task_assign as $key_staff_assign => $value_staff_assign)
                            {
                                $staff_emailID_result = '';

                                if($data_task['role'] == 'staff'){
                                    $staff_emailID_result = $staff->select('emailID','first_name','last_name')->where('staff_id','=',$value_staff_assign)->first();
                                }
                                if($data_task['role'] == 'employee')
                                {
                                    $staff_emailID_result = $employee->select('emailID','first_name','last_name')->where('employee_id','=',$value_staff_assign)->first();
                                }

                                $staff_emailID = $staff_emailID_result->emailID;
                                array_push($staff_emailID_ar,$staff_emailID);

                                $staff_name = $staff_emailID_result->first_name.' '.$staff_emailID_result->last_name;
                                array_push($staff_fullname_ar,$staff_name);
                            }
                        }
                    }

                    $info = array(
                        'title' => $data_task['title'],
                        'assign_date' => $data_task['assign_date'],
                        'completion_date' => $data_task['completion_date'],
                        'description' => $data_task['description'],
                        'task_status' => $data_task['task_status'],
                        'task_priority' => $data_task['task_priority'],
                        'assign_user' => implode(',',$staff_fullname_ar),
                        "loginUrl" => URL::to('/')
                    );

                    if(!empty($staff_emailID_ar)){

                        foreach($staff_emailID_ar as $key_email => $value_email)
                        {
                            $email = $value_email;
                            $info['emailID']= $email;

                            Mail::send('email_templates.staffTaskmessage',['data_info' => $info], function($message) use ($email)
                            {
                                $message->to($email)->subject('CRM - Assign Task Details');
                            });
                        }
                    }

                    $description = $sessionData['first_name'].' '.$sessionData['last_name'].' was Update Task Details';
                    $logdata = array();
                    $logdata['role'] = 'employee';
                    $logdata['role_id'] = $id;
                    $logdata['operation'] = 'Update Task Details';
                    $logdata['description'] = $description;
                    $logdata['role_date'] = date('Y-m-d');

                    return Redirect::to('/employee/');
                    exit;
                } else {
                    Session::flash('operationFaild','Some thing Went wrong');
                    return Redirect::to('/employee/task');
                    exit;
                }
            }
        }else{
            return Redirect::to('/employee');
        }
    }


    public  function  deleteData()
    {
        $id = 0;
        $sessionData=Session::get('employeeLog');
        $id = $sessionData['employeeID'];
        if(isset($id) && ($id != 0))
        {
            $task = new Task;
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
            $task = new Task();
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
