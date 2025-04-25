<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function print_invoice($id)
    {    
        $invoice = Invoice::where('id', $id)->first();

        $invoice_date = $invoice->updated_at->locale('id');
        $invoice_date = $invoice_date->settings(['formatFunction' => 'translatedFormat'])->format('j F Y');

        return Pdf::loadview('invoice',[
            'invoice' => $invoice,
            'date'    => $invoice_date,
            'customer'=> $invoice->customer,
            'shipment'=> $invoice->shipment,
            'barcode' => base_path('storage/app/public/qr-codes/'.$invoice->barcode)
        ])->download('myInvoice.pdf');
    }
}