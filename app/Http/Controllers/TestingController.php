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
        $numeric_type = array('INT','TINYINT','SMALLINT','MEDIUMINT','BIGINT','FLOAT','DOUBLE','DECIMAL');

        $connection = @mysqli_connect($host,$user,$pass);
        $selectdb = mysqli_select_db($connection,$dbname);
        if(!$selectdb)
        {
            return 'error';
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
        $return.= '/* Create By Tretanz Infotech        */';
        $return.="\n";
        $return.= '/* Date : '.date("Y-m-d H:i:s").'   */';
        $return.="\n";
        $return.= '/* ******************************** */';
        $return.="\n\n\n\n\n\n";

        $return = '';


        foreach($tables as $table)
        {
            $num_fields = 0;
            $num_records = 0;
            $result_a = @mysqli_query($connection,'SELECT * FROM  `'.$table.'` ');
            if(@mysqli_num_fields($result_a) != 0)
            {
                $num_fields = mysqli_num_fields($result_a);
            }else{
                $row = @mysqli_fetch_array($result_a, MYSQLI_NUM);
                $num_fields = count($row);
            }

            $num_records = @mysqli_num_rows($result_a);

            //$return.= 'DROP TABLE '.$table.';';
            //$row2 = @mysqli_fetch_row(@mysqli_query($connection,'SHOW CREATE TABLE '.$table));
            //$return.= "\n\n".$row2[1].";\n\n";

            //$return.= 'DROP TABLE '.$table.';';
            $row2 = @mysqli_fetch_row(@mysqli_query($connection,'SHOW CREATE TABLE `'.$table.'` '));
            $return.= "\n\n".str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]).";\n\n";

            $result_fieldname = @mysqli_query($connection,'SHOW COLUMNS FROM `'.$table.'` ');

            $field_ar = array();
            $field_set_null = array();
            $field_set_type = array();

            if(@mysqli_num_rows($result_fieldname) > 0)
            {
                while ($row = mysqli_fetch_assoc($result_fieldname))
                {
                    /*print_r($row);
                    echo '<br/>';*/
                    $field_name = '`'.$row['Field'].'`';
                    array_push($field_ar, $field_name);

                    array_push($field_set_null, $row['Null']);

                    $type = explode('(',$row['Type']);
                    $type_name = strtoupper($type[0]);
                    array_push($field_set_type, $type_name);
                }
            }



            if($num_records != 0)
            {
                $field_data = '('.implode(', ',$field_ar).')';
                $return.= 'INSERT INTO `'.$table.'` '.$field_data.'  VALUES';
                $return.="\n";
                for ($i = 0; $i < $num_fields; $i++)
                {
                    $ab = 0;
                    $num_records = 0;
                    while($row = @mysqli_fetch_row($result_a))
                    {
                        $ab++;
                        $num_records = @mysqli_num_rows($result_a);
                        $return.= '(';
                        for($j=0; $j < $num_fields; $j++)
                        {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace("\n","\\n",$row[$j]);
                            if(isset($row[$j]))
                            {
                                if($row[$j] == '0')
                                {
                                    $return.= 0;
                                }else if($row[$j] == '')
                                {
                                    if($field_set_null[$j] == 'NO'){
                                        $return.= "''";
                                    }
                                    if($field_set_null[$j] == 'YES'){
                                        $return.= "NULL";
                                    }
                                }else{
                                    if(in_array($field_set_type[$j],$numeric_type)){
                                        $return.= $row[$j];
                                    }else{
                                        $string_value = $row[$j];
                                        //$string_value = html_entity_decode($string_value);
                                        //$string_value = htmlentities($string_value);
                                        $string_value = str_replace("'","''",$string_value);
                                        //$string_value = preg_replace('/\r\n|\r|\n/', "\r\n", $string_value);

                                        $return.= "'".$string_value."'" ;
                                    }
                                }
                                //$return.= '"'.$row[$j].'"' ;
                            }else
                            {
                                $return.= '""';
                            }
                            if ($j < ($num_fields-1))
                            {
                                $return.= ', ';
                            }
                        }
                        if($num_records == $ab)
                        {
                            $return.= ");\n";
                        }else{
                            $return.= "),\n";
                        }
                    }
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

    public function import_tables($host,$user,$pass,$dbname,$filename)
    {
        $connection = @mysqli_connect($host, $user, $pass);
        $selectdb = mysqli_select_db($connection, $dbname);
        if (!$selectdb) {
            return 'error';
        }

        $tables = array();
        $result = @mysqli_query($connection,'SHOW TABLES');
        while($row = @mysqli_fetch_row($result))
        {
            $tables[] = $row[0];
        }
        foreach($tables as $table) {
            mysqli_query($connection, 'TRUNCATE TABLE '.$table);
        }

        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($filename);
        // Loop through each line
        foreach ($lines as $line)
        {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == ''){
                continue;
            }

            // Add this line to the current segment
            $templine.= $line;

            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';')
            {
                // Perform the query
                mysqli_query($connection,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');

                // Reset temp variable to empty
                $templine = '';
            }
        }
        return "Tables imported successfully";
    }

    public function index()
    {
        echo 'Base Path : '.base_path(); echo '<br/>';
        echo 'Public Path : '.public_path(); echo '<br/>';
        echo 'Storage Path : '.storage_path(); echo '<br/>';
        echo 'App Path : '.app_path(); echo '<br/>';

        if(!is_dir('db_backup')){
            mkdir('db_backup',0755, true);
        }

        try{

            $file_name = '';
            $file_name = 'db_backup/superior_spas_jigs.sql';
            $file_name = 'db_backup/laravel_product_crm.sql';

            $result = $this->backup_tables('localhost', 'root', '', 'laravel_product_crm', $tables = '*');

            //$result = $this->import_tables('localhost', 'root', '', 'laravel_product_crm', $file_name);

            if($result != 'error'){
                echo 'pass';
            }else{
                echo 'please check database name';
            }
        }catch (Exception $e) {
            echo 'There are some issue while creating backup. Please contact developers as soon as possible.';
        }
    }
}
