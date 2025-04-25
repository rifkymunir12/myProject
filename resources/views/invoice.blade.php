<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 10px 20px 10px 20px !important;
            padding: 10px 20px 10px 20px !important;
        }

        body {
            position: relative;
            width: 18.9cm;
            /* height: 26.73cm; */
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-family: Arial, sans-serif;
            text-align: left;

        }

        #header {
           margin-left: 61%;
        }

        #header th tr{
            text-align: right ;
        }

        #header tr{
            text-align: right ;
        }
        #inline{
            display: inline-table;
        }

        .invoiceItem, .invoiceItem th , .invoiceItem td{
            border: 1px solid; 
            border-collapse: collapse;
            height: 20px;
        }
        
        .paymentDetail, .paymentDetail td{ 
            border: 1px solid; 
            border-collapse: collapse;
            margin-left: auto;
            text-align: right;
            font-weight: bold;
            height: 20px;
            color : 
        }
        .status{
            float: right; 
            margin-right: 5%;
            border-style: solid;
            text-transform: uppercase;
        }

    </style>
</head>

<body>
    <?php
        $i = 1;
        $total = 0;
        $status = $invoice->status;
        
        $color = "";

        if($status == 'Unpaid'){
            $color = 'color:gray;';
        }
        elseif($status == 'Waiting'){
            $color = 'color:yellow;';
        }
        elseif($status == 'Cancelled'){
            $color = 'color:red;';
        }
        elseif($status == 'Paid'){
            $color = 'color:green;';
        }
        //unpaid ga ada warna, cancelled merah, waiting kuning, paid hijau
    ?>  

    <h1>INVOICE</h1>

    <table style="float: left">
        <tr style="font-weight: bold;">
            <td>Invoice Code</td>
            <td>:</td>
            <td>{{$invoice->invoice_code}}</td>
        </tr>
        <tr>
            <td>Name</td>
            <td>:</td>\\\\\\\\\\\\\\\\
            <td>{{$customer->name}}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>  
            <td>{{$customer->email}}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>:</td>  
            <td>{{$date}}</td>
        </tr>
        <tr>
            <td>Destination</td>
            <td>:</td>  
            <td>{{$invoice->destination}}</td>
        </tr>
        <tr>
            <td>Shipment</td>
            <td>:</td>  
            <td>{{$shipment->name ?? '_'}}</td>
        </table>
        </tr>
       
    <!--<h1 class="status" style={{$color;}}>{{$status}}</h1>-->

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <table class="invoiceItem" style="width: 100%; " cellpadding="8">
        <tr style="border :1px solid; background-color:#DDDDDD;">
            <th style="width: 4%" >NO</th>
            <th style="width: 40%">ITEM DESCRIPTION</th>
            <th style="width: 22%">PRICE</th>
            <th style="width: 18%">QUANTITAS</th>
            <th style="width: 16%">AMOUNT</th>
        </tr>
    @foreach ($invoice->items as $item)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$item->name}}</td>
            <td style="text-align: right;">Rp. {{$item->price}}</td>
            <td style="text-align: center;">{{$item->pivot->quantity}} {{$item->unit->name}}</td>
            <td style="text-align: right;">Rp.{{$item->price * $item->pivot->quantity}}</td>
        </tr>    
    @endforeach
    </table>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <table class="paymentDetail" width="35%" cellpadding="8">
    <?php
        $return = $invoice->payment > $invoice->final_price ? $invoice->payment - $invoice->final_price  : 0;
    ?>
        <tr>
            <td style="width:45%">TOTAL PRICE</td>
            <td style="width:55%">Rp. {{$invoice->total_price}}</td>
        </tr>
        <tr>
            <td>DISCOUNT KOUPON</td>
            <td>RP.{{$invoice->coupon->discount ?? 0}}</td>
        </tr>
        <tr>
            <td>SHIPMENT PRICE</td>
            <td>Rp.{{$shipment->price}}</td>
        </tr>
        <tr>
            <td>FINAL PRICE</td>
            <td>Rp.{{$invoice->final_price}}</td>
        </tr>
    </table>

    <div style="position:relative; bottom: 4.7cm; left: 0.5cm;">
        <h4>NOTE :</h4n>
        <p>{{$invoice->note}}</p>
        <img src="{{$barcode}}" style="width: 3cm; height: 3cm;"/>
    </div>
    

</body>
</html>