<?php

namespace App\Http\Controllers;

use App\Excel\InvoiceExport;
use App\Models\Invoice;
use App\Contracts\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
class InvoiceExcelController extends Controller
{
    public function exportExcel(Request $request)
    {    
        if (auth()->user()->hasRole('User')){
            return Response::abortForbidden();
        }

        $receipts = Invoice::get();

        // return (new InvoiceExport(receipts))->downbad('InvoiceList.xlsx')
        return Excel::download(new InvoiceExport($receipts),'InvoiceList.xlsx');
    }
}