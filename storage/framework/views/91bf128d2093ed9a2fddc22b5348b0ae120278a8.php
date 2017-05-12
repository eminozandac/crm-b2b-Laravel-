<?php
$message = '<!DOCTYPE html>
<html>
<body style="color: #0a0a0a;font-family:Arial,sans-serif;font-weight: 400;text-align: center;margin: 0;padding: 0;box-sizing: border-box;width: 100% !important;height: 100vh">
<table  data-made-with-foundation="" style="background: #f3f3f3 none repeat scroll 0 0; height: 100%; width: 100%;text-align: center;border-collapse: collapse;border-spacing: 0;">
<tr>
<td  align="center" valign="top">
<center data-parsed="" style="min-width: 580px; width: 100%;">
<table style="text-align: center;background: #fefefe none repeat scroll 0 0;width: 560px;height: 100%;border-collapse: collapse;border-spacing: 0;">
<tbody>
<tr>
<td  style="text-align:center;">
<br>
<span style="float:left;width:100%;background-color:#1AB394;padding:5% 0;">
<center data-parsed="" style="min-width: 580px; width: 100%;color:#fff;font-size: 24px;">
<img src="'.asset('assets/img/superiorspas.png').'" class="img-responsive"/>
</center>
</span>
<table class="rowA" style="display: table;padding: 0;position: relative; width: 100%;text-align: center;border-collapse: collapse;border-spacing: 0;">
<tbody>
<tr>
<th class="small-12 large-12 columns first last" style="padding: 16px;width: 564px;text-align: center">
<table style="text-align: center;position: relative;width: 100%;">
<tr>
<th>
<br />
<h4  style="text-align: center; font-size: 24px;color: inherit; font-family: Arial,sans-serif;font-weight: 400; margin-bottom: 10px;word-wrap: normal;margin: 0;">
Hello, <strong>'.$data_info["emailID"].'</strong><br/><br/> Your Task Details</h4>
</th>
<th class="expander" style="padding: 0 !important; visibility: hidden; width: 0;"></th>
</tr>
</table>
</th>
</tr>
</tbody>
</table>
<hr>
<table class="rowA" style="display: table;padding: 0;position: relative; width: 100%;text-align: center;">
<tbody>
<tr>
<th class="small-12 large-12 columns first last" style="padding: 16px;width: 564px;text-align: center">
<table style="position: relative;width: 100%;text-align: center;">
<tr>
<th>
<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<b>Task Priority : </b> '.$data_info["task_priority"].'
</p>
<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<b>Task User : </b> '.$data_info["assign_user"].'
</p>
<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<b>Title : </b> '.$data_info["title"].'
</p>
<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<b>Assign Date : </b> '.$data_info["assign_date"].'
</p>
<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<b>Completion Date : </b> '.$data_info["completion_date"].'
</p>
<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<b>Description : </b> '.$data_info["description"].'
</p>
<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<b>Status : </b> '.$data_info["task_status"].'
</p>

<p  style="text-align: center;font-size: 14px;line-height: 19px;font-weight: normal; ">
<a href="'.$data_info["loginUrl"].'"><b>Login</b></a>
</p>
<hr />
<br />

</th>
<th style="padding: 0 !important; visibility: hidden; width: 0;"></th>
</tr>
</table>
</th>
</tr>
</tbody>
</table>
<table style="position: relative;width: 100%;text-align: center;border-collapse: collapse;border-spacing: 0;background-color: #212121;color: #ffffff;">
<tbody>
<tr>
<th>
<table style="position: relative;width: 100%;text-align: center;border-collapse: collapse;border-spacing: 0;">
<tr>
<th>
<br>
<p style="position: relative;width: 100%;text-align: center;color: #ffffff;">CRM Management System.</p>
</th>
<th  style="padding: 0 !important; visibility: hidden; width: 0;"></th>
</tr>
</table>
</th>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</center>
</td>
</tr>
</table>
</body>
</html>';

echo $message;
