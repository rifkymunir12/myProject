<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Invoice;
use App\Contracts\Response;
use App\Http\Requests\PaymentConfirmationRequest;
use App\Http\Requests\StatusPaymentRequest;
use App\Http\Requests\CancelRequest;
use App\Http\Resources\InvoiceResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function update_status_payment(StatusPaymentRequest $request){
        if (auth()->user()->hasRole('User')){
            return Response::abortForbidden();
        }

        $data = $request->validated();

        //kayanya fungsi payment dan invoice udah, tinggal updatenya invoice
        $updatedInvoice = DB::transaction( function () use ($data)
        {
            $invoice = Invoice::find($data['invoice_id']);
            
            //payment bisa diedit karena manatau ada yang beli offline dia dapat kembalian
            if($data['status'] == 'Paid'){

                //dd($data['status']);

                // $data['payment'] = null;
                $payment = $data['payment'] ?? 0;
                // dd($payment);

                if (floatval($payment) < $invoice->final_price){
                    throw ValidationException::withMessages(['payment' => 'Uang pembayaran tidak mencukupi!']);   
                }
                
                $invoice->update([
                    'payment'    => $data['payment'],
                ]); 

                foreach($invoice->items as $item){
                    $item->update([               
                        'stock_out'   =>  $item->stock_out + floatval($item->pivot->quantity),
                        'amount'      =>  $item->amount - floatval($item->pivot->quantity),
                    ]);

                }            
            }
            elseif($data['status'] == 'Cancelled'){
                //delete coupon user
                DB::table('coupon_user')->
                where('user_id', '=', $invoice->customer_id)->
                where('coupon_code','=', $invoice->coupon?->code)->delete();
            }

            $invoice->update([
                'status'    => $data['status'],
            ]); 

            return $invoice;            
        });
        

        return Response::json(new InvoiceResource($updatedInvoice));
    }

    public function send_payment_confirmation(PaymentConfirmationRequest $request){

        $invoice = Invoice::find($request['invoice_id']);
            
        if(auth()->user()->id !== $invoice->customer_id){   
            return Response::abortForbidden();
        }

        DB::transaction( function () use ($invoice) 
        {
            
            if($invoice->status == 'Cancelled'){
                throw ValidationException::withMessages(['status' => 'Pembelian telah dicancelled']);
            }

            if($invoice->status == "Paid"){
                throw ValidationException::withMessages(['status' => 'Anda telah membayar pembelian!']);
            }

            if($invoice->status == 'Waiting'){
                throw ValidationException::withMessages(['status' => 'Telah melakukan pembayaran! Silahkan tunggu konfirmasi pembayaran!']);
            }
            
            $invoice->update([
                'status'                  => 'Waiting',
            ]);  

            dd($invoice);
            
        });

        //dd($invoice->status);

        return response()->json(['message' => 'Silahkan tunggu konfirmasi pembayaran!'], 202);
    }

    public function cancel_purchase(CancelRequest $request){
        $cancelledInvoice = DB::transaction( function () use ($request) 
        {
            $invoice = Invoice::find($request['invoice_id']);

            if(auth()->user()->id !== $invoice->customer_id){
                return Response::abortForbidden();
            }

            if($invoice->status == 'Cancelled'){
                return response()->json(['message' => 'Pembelian telah dicancel!'], 403);
            }

            $invoice->update([
                'status'                  => 'Cancelled',
            ]);
        
          
            return $invoice;
        });
        
        return Response::json(new InvoiceResource($cancelledInvoice));
        //lihat dirumah pas udah bayar, bisa kah cancel? ntah di video gitu
    }

    //biar ga ulang kerjaan, ga perlu cek store invoice
}