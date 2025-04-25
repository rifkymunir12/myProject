<?php

namespace App\Http\Controllers;
use App\Models\Coupon;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Invoice;
use App\Contracts\Response;
use App\Http\Requests\InvoiceCreateRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        //filter pertama untuk admin dan mod, dia sortnya dari confirmationnya yang 1, kemudian dari yang terbaru
        //kalau utk user individual difilter, kemudian urutannya dari yang terbaru 
        $invoices = Invoice::paginate($request->show);

        return Response::json(new InvoiceCollection($invoices));
    }

    public function show(Invoice $invoice)
    {
        return Response::json(new InvoiceResource($invoice));
    }

    public function store(InvoiceCreateRequest $request)
    {   
        if (DB::table('coupon_user')->where('user_id', $request->user()->id)
                                    ->where('coupon_code', $request['coupon_code'])->first() != NULL)
        {
            return response()->json(['message' => 'Coupon telah digunakan!'], 422);
        }
        
        $newInvoice = DB::transaction(function () use($request) {
            $data = $request->validated();

            $data['customer_id'] = $request->user()->id;
            $data['payment'] = 0;
            $data['coupon_id'] =  Coupon::where('code', $data['coupon_code'] ?? '')?->first()?->id;
            $data['status'] ='Unpaid';

            // $newInvoice = Invoice::create([
            //     'customer_id'           => $request->user()->id,
            //     'shipment_id'           => $data['shipment_id'],
            //     'destination'           => $data['destination'],
            //     'coupon_id'             => Coupon::where('code', $data['coupon_code'] ?? '')?->first()?->id,
            //     'payment'               => 0,
            //     'note'                  => $data['note'] ?? null,
            //     'status'                => 'Unpaid',
            // ]);

            $newInvoice = Invoice::create($data);

            if (isset($data['coupon_code'] )) {
                DB::table('coupon_user')->insert([
                    'user_id'      =>  $request->user()->id,
                    'coupon_code'  =>  $data['coupon_code'],
                ]);
            }

            //urus kolum payment, pdfnya, kemudian testing

            $totalPrice = $this->total_price(json_decode($data['items']), $newInvoice);
            
            $finalPrice = $totalPrice - ($newInvoice?->coupon?->discount ?? 0) + $newInvoice->shipment->price;
                
            // if ($finalPrice > floatval($data['payment'])) {
            //     throw ValidationException::withMessages(['payment' => 'Uang pembayaran tidak mencukupi!']);   
            // };

            $path = storage_path('app/public/qr-codes/');
            if (!file_exists($path)) mkdir($path, 0777, true);

            QrCode::format('png')->size(600)->generate($newInvoice->id, $path . $newInvoice->id. '.png');

            
            $newInvoice->update([
                'total_price'   => $totalPrice,
                'final_price'   => $finalPrice,
                'barcode'       => $newInvoice->id.'.png',
            ]);

            return $newInvoice;
        });
        
        return Response::json(new InvoiceResource($newInvoice));
    }

    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
        $data = $request->validated();
        //dd($data['customer_id']); ada isinya

        if (
            DB::table('coupon_user')->where('user_id', $invoice->customer_id)
            ->where('coupon_code', $data['coupon_code'] ?? null)->first() != NULL
        ) {
                return response()->json(['message' => 'Coupon telah digunakan!'], 422);
            }
            $updatedInvoice = DB::transaction(callback: function () use($data, $invoice) { 
                $updatedInvoice = $invoice; 
                $old_coupon_code = Coupon::where('id', $invoice->coupon_id)?->first()?->code;
                
                //$old_code = $updatedInvoice->coupon?->code; dd sebelum ini atribut coupon ga ada, setelah ini baru ada atribut couponnya
                // ini menyebabkan kalau ngakses relasi, akan ditambah ke atribut   
             
                //dd($updatedInvoice);
                // $updatedInvoice->update([
                //     'shipment_id'           => $data['shipment_id'] ?? $updatedInvoice->shipment?->id,
                //     'destination'           => $data['destination'] ?? $updatedInvoice?->destination,
                //     'coupon_id'             => Coupon::where('code', $data['coupon_code'] ?? '')?->first()?->id
                //                                 ?? $updatedInvoice?->coupon_id,
                //     'payment'               => floatval($data['payment']) ?? $updatedInvoice?->payment,
                //     'note'                  => $data['note'] ?? $updatedInvoice?->note,
                //     'status'                => $data['status'] ?? $updatedInvoice?->status,   
                // ]);
                //dd(Coupon::where('code', $data['coupon_code'] ?? '')?->first());
                //dd($updatedInvoice);

                $data['coupon_id'] = Coupon::where('code', $data['coupon_code'] ?? '')?->first()?->id;
                $data['payment']   =  floatval($data['payment']);

                $updatedInvoice->update($data);

                //buat fungsi untuk hapus koupon kodenya jika mau ganti kode/ remove kodenya
                // customer tidak diganti
                // update: cari tau kenapa pas output hasil nya, dia masih ikutin coupon yang lama bukan yang baru

                DB::table('coupon_user')->where('user_id', $updatedInvoice->customer_id)
                    ->where('coupon_code', $old_coupon_code)->delete();

                if (isset($data['coupon_code'] )) {
                    DB::table('coupon_user')->insert([
                        'user_id'      =>  $updatedInvoice->customer_id,
                        'coupon_code'  =>  $data['coupon_code'],
                    ]);
                }    

                DB::table('invoice_item')->where('invoice_id', $updatedInvoice->id)->delete();

                $totalPrice = $this->total_price(json_decode($data['items']), $updatedInvoice);    
                $finalPrice = $totalPrice - ($updatedInvoice->coupon?->discount ?? 0)  + $updatedInvoice->shipment->price;

                $path = storage_path('app/public/qr-codes/');
                if (!file_exists($path)) mkdir($path, 0777, true);

                QrCode::format('png')->size(600)->generate($updatedInvoice->id, $path . $updatedInvoice->id. '.png');

                $updatedInvoice->update(attributes: [
                    'total_price'   => $totalPrice,
                    'final_price'   => $finalPrice,
                    'barcode'       => $updatedInvoice->id.'.png',
                ]);

                return $updatedInvoice;
            
            });   

        return Response::json(new InvoiceResource($updatedInvoice));
    }

    public function destroy(Invoice $invoice)
    {
        if (auth()->user()->hasRole('User')) {
            return Response::abortForbidden();
        }
        
        $invoice->delete();
        return Response::noContent();
    }

    protected function total_price($items, Invoice $invoice){
        $totalPrice = 0;

        foreach ($items as $item) {
            $validator = Validator::make((array)$item, [
                'item_id'   => 'required|uuid|exists:items,id',
                'quantity'  => 'required|numeric|min:1|',
            ]);

            //(kalau ga ada (array) nya ga bisa jalan validatornya)

            if ($validator->fails()) {
                throw ValidationException::withMessages(['item_id or quantity' => 'Salah input item atau quantitasnya!']);
            }

            $myItem = Item::find($item->item_id);

            if ($item->quantity >= $myItem->amount) {
                throw ValidationException::withMessages(['quantity' => $myItem->name . ' tidak mencukupi permintaan!']);
            }

            //misal tidak sesuai dengan kelipatan multiplier
            $multiple = $myItem->unit->multiple;
            if($item->quantity % $multiple != 0){
                throw ValidationException::withMessages(['quantity' => 'Bukan kelipatan '.$multiple.'!']);
            }


            DB::table('invoice_item')->insert([
                'item_id'      =>  $item->item_id,
                'invoice_id'   =>  $invoice->id,
                'quantity'     =>  $item->quantity,
            ]);
            

            // $myItem->update([               
            //     'stock_out'   =>  $myItem->stock_out + $item->quantity,
            //     'amount'      =>  $myItem->amount - $item->quantity,
            // ]);

            //logic warning ke adminnya 

            //jika quantity minus, maka tolak secara keseluruhan
            //jika amount<stock in, maka tolak
            $totalPrice += $myItem->price * $item->quantity;
        }

        return $totalPrice; 
    }
}