<?php

namespace App\Http\Controllers;

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
use App\Staff;

class TestingController extends Controller
{

    public function backup_tables($host,$user,$pass,$dbname,$tables = '*')
    {

        $connection = @mysqli_connect($host,$user,$pass);
        $selectdb = mysqli_select_db($connection,$dbname);
        if(!$selectdb)
        {
            return 'Selected Database attemted Failed';
        }

        //get all of the tables
        if($tables == '*')
        {
            $tables = array();
            $result = @mysqli_query($connection,'SHOW TABLES');
            while($row = @mysqli_fetch_row($result))
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        //cycle through
        $return = '';
        foreach($tables as $table)
        {
            $num_fields = 0;
            $result_a = @mysqli_query($connection,'SELECT * FROM '.$table);
            if(@mysqli_num_fields($result_a) != 0)
            {
                $num_fields = mysqli_num_fields($result_a);
            }else{
                $row = @mysqli_fetch_array($result_a, MYSQLI_NUM);
                $num_fields = count($row);
            }


            $return.= 'DROP TABLE '.$table.';';
            $row2 = @mysqli_fetch_row(@mysqli_query($connection,'SHOW CREATE TABLE '.$table));
            $return.= "\n\n".$row2[1].";\n\n";

            for ($i = 0; $i < $num_fields; $i++)
            {
                while($row = @mysqli_fetch_row($result_a))
                {
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j < $num_fields; $j++)
                    {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n","\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j < ($num_fields-1)) { $return.= ','; }
                    }
                    $return.= ");\n";
                }
            }
            $return.="\n\n\n";
        }
        //save file
        $file_name = 'db_backup/superior_spas_'.time().'.sql';
        $file_name = 'db_backup/superior_spas_jigs.sql';
        $handle = fopen($file_name,'w+');
        fwrite($handle,$return);
        fclose($handle);
        return $handle;
    }

    public function index()
    {
        echo 'Base Path : '.base_path(); echo '<br/>';
        echo 'Public Path : '.public_path(); echo '<br/>';
        echo 'Storage Path : '.storage_path(); echo '<br/>';
        echo 'App Path : '.app_path(); echo '<br/>';

        try{
            $now =  'super_'.time().'_backup.sql';
            /*$result = Artisan::call('laradump:mysqldump');
            dump($result);*/

            $schema = 'laravel_product_crm';
            $password = '';
            $path = 'db_backup/super_jig.sql';
            $file = 'super_jig.sql';
            $command = sprintf('mysqldump --opt -h localhost -u root -p  laravel_product_crm > '.$path);
            /*echo $command;echo '<br/>';
            exec($command);*/

            echo '<br/>';echo '<br/>';
            echo $this->backup_tables('localhost', 'root', '', 'laravel_product_crm', $tables = '*');

        }catch (Exception $e) {
            echo 'There are some issue while creating backup. Please contact developers as soon as possible.';
        }
    }
}
