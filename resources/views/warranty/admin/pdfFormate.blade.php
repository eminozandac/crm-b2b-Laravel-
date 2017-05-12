<?php
$dealer_name = $name = $telephone = $address = $pdf_name = '';
$customer_name = '';

$role = $warrantyData->role;
$userID = $warrantyData->user_id;
$customer_name = $warrantyData->name;
$emailID = $warrantyData->emailID;
$address = $warrantyData->address;
$postcode = $warrantyData->postcode;
$telephone = $warrantyData->phone;

$band = $warrantyData->product_name;
$model = $warrantyData->product_model;
$serial_number = $warrantyData->product_serial_number;
$purchase_date = $warrantyData->purchase_date;
$fault = $warrantyData->product_fault;
$part_require = $warrantyData->part_require;
$additional_note = $warrantyData->note;

if($role == 'dealer')
{
    $result = DB::table('dealer')->where('id',$userID)->first();
    $dealer_name = $result->first_name.' '.$result->last_name;
}
if($role == 'customer')
{
    $result = DB::table('customer')->where('id',$userID)->first();
    $name = $result->first_name.' '.$result->last_name;
}
$name = str_replace(' ','-',$name);
$pdf_name = $customer_name.'_warranty'.'.pdf';
?>
        <!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Product Warranty</title>
    <style>

        body
        {
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .page
        {
            width: 205mm;
            min-height: 297mm;
            padding: 0.5mm;
            margin: 0.3mm auto;
            background-color: white;
        }

        .container
        {
            border: 2px black solid;
            height: 287mm;
            padding: 1mm;
            outline: 1cm #FFFFFF solid;
        }

        @page
        {
            size: A4;
            margin: 0;
        }

        @media print
        {
            html, body
            {
                width: 210mm;
                height: 297mm;
            }
        }

        .clear:before,.clear:after {
            content: " ";
            display: table;
        }

        .clear:after {
            clear: both;
        }

        h1,h2,h3,h4,h5,h6,label
        {
            font-size: 16px;
            color: #000000;
            font-weight: bold;
            margin: 2px;
            line-height: 18px;
            margin-left: 3px;
            margin-bottom: 10px;
            text-align: left;
        }

        label{
            font-weight:500;
        }

        span,p{
            color: #000000;
            font-size: 14px;
            font-weight: normal;
            line-height: 16px;
            margin: 2px;
        }

        span{
            text-align: left;
        }

        p{
            float: left;
            text-align: justify;
        }

        table{
            vertical-align: top;
        }

        table , tbody, tr, td
        {
            vertical-align: top;
        }

        .tr_7 label{
            font-size: 12px;
            font-weight: 600;
        }
    </style>

</head>
<body>
<div class="page">
    <div class="container">

        <table cellspacing="2" cellpadding="0" border="0" style="width: 100%;">
            <col width="64" span="14" />
            <tr>
                <td colspan="7" rowspan="4" width="50%" style="padding-bottom: 3mm;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Customer Details</h3></td>
                        </tr>
                        <tr>
                            <td><label>Dealer Name :</label></td>
                            <td>{{ $dealer_name }}</td>
                        </tr>
                        <tr>
                            <td><label>Customer Name :</label></td>
                            <td>{{ $customer_name }}</td>
                        </tr>
                        <tr>
                            <td><label>Customer EmailID :</label></td>
                            <td>{{ $emailID }}</td>
                        </tr>
                        <tr>
                            <td><label>Customer Telephone :</label></td>
                            <td>{{ $telephone }}</td>
                        </tr>
                        <tr>
                            <td><label>Customer Post Code :</label></td>
                            <td>{{ $postcode }}</td>
                        </tr>
                        <tr>
                            <td><label>Customer Address :</label></td>
                            <td>{{ $address }}</td>
                        </tr>
                    </table>
                </td>
                <td colspan="7" rowspan="2" style="border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Spa Details</h3></td>
                        </tr>
                        <tr>
                            <td><label>Brand :</label></td>
                            <td>{{ $band }}</td>
                        </tr>
                        <tr>
                            <td><label>Model :</label></td>
                            <td>{{ $model }}</td>
                        </tr>
                        <tr>
                            <td><label>Serial Number :</label></td>
                            <td>{{ $serial_number }}</td>
                        </tr>
                    </table>
                </td>
            </tr>


            <tr> </tr>

            <tr>
                <td colspan="7" rowspan="2" style="padding-bottom: 3mm; border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Job Type</h3></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> </tr>

            <tr>
                <td colspan="7" rowspan="3" width="50%" style="height:60px;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Booked in By</h3></td>
                        </tr>
                    </table>
                </td>
                <td colspan="7" rowspan="3" width="50%" style="border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Date of Job :</h3></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> </tr>
            <tr> </tr>

            <tr>
                <td colspan="14" rowspan="3" width="100%" style="height:85px;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Fault :</h3></td>
                            <td>{{ $fault }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> </tr>
            <tr> </tr>

            <tr>
                <td colspan="14" rowspan="3" width="100%" style="height:85px;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Part Required :</h3></td>
                            <td>{{ $part_require }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> </tr>
            <tr> </tr>

            <tr>
                <td colspan="14" rowspan="3" width="100%" style="height:85px;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Details of Work Carried Out :</h3></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> </tr>
            <tr> </tr>

            <tr>
                <td colspan="14" rowspan="3" width="100%" style="height:85px;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Parts Used :</h3></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> </tr>
            <tr> </tr>

            <tr>
                <td colspan="14" rowspan="3" width="100%" style="height:85px;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Additional Comments :</h3></td>
                            <td>{{ $additional_note }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr> </tr>
            <tr> </tr>

            <tr class="tr_7">
                <td colspan="2" rowspan="4" width="14.2%" style="height:85px;border: 1px solid #000;">
                    <label>Time on site :</label>
                </td>
                <td colspan="2" rowspan="4" width="14.2%" style="border: 1px solid #000;">
                    <label>Time off site :</label>
                </td>
                <td colspan="2" rowspan="4" width="14.2%" style="border: 1px solid #000;">
                    <label>Total Hours :</label>
                </td>
                <td colspan="2" rowspan="4" width="14.2%" style="border: 1px solid #000;">
                    <label>Mileage :</label>
                </td>
                <td colspan="2" rowspan="4" width="14.2%" style="border: 1px solid #000;">
                    <label>Cost of parts used :</label>
                </td>
                <td colspan="2" rowspan="4" width="14.2%" style="border: 1px solid #000;">
                    <label>Total cost :</label>
                </td>
                <td colspan="2" rowspan="4" width="14.2%" style="border: 1px solid #000;">
                    <label>Fixed price :</label>
                </td>
            </tr>

            <tr> </tr>
            <tr> </tr>
            <tr> </tr>

            <tr>
                <td colspan="7" rowspan="3" width="50%" style="height:175px;border: 1px solid #000;">
                    <table>
                        <tr>
                            <td><h3>Customer Declaration :</h3></td>
                        </tr>
                        <tr>
                            <td>
                                <span>I/ We confirm that the above work has been carried out to our satisfaction and that the spa has been left in full working order and in good working condition with no damage or outstanding issues noted by me/ us above. i/ we agree to pay for the above work if applicable and have been informed of this fact accordingly. i/we agree for the above amount to be charged to the card details</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="7" rowspan="3" width="50%" style="border: 1px solid #000;">
                    <table>
                        <tr>
                            <td style="height:85px;">
                                <h3>Customer signature :</h3>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <h3>Customer name :</h3>
                            </td>
                            <td>
                                <span>{{ $customer_name }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr></tr>
            <tr></tr>
        </table>

        @if(isset($data_print) && ($data_print == 'pass'))
            <script type="text/javascript">
                window.print();
            </script>
        @endif

    </div>
</div>
</body>
</html>