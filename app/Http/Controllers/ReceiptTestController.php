<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Item;
use App\Models\Receipt;
use App\Contracts\Response;
use App\Http\Requests\ReceiptRequest;
use App\Http\Resources\ReceiptCollection;
use App\Http\Resources\ReceiptResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptTestController extends Controller
{
    public function index(Request $request){
        $receipts = Receipt::paginate($request->show);

        return Response::json(new ReceiptCollection($receipts));
    }
    public function show($id){
        //kalau mau get receiptnya harus pakai id, gabisa pakai nama model (mungkin nama model sama controller harus sama)
        return Response::json(new ReceiptResource(Receipt::find($id)));
    }

    public function store(ReceiptRequest $request){
      
        //coba buat db:transaction yang ga dan pakai begin and rollback (auto and manual)
        $newReceipt = DB::transaction(function () use ($request){

            $data = $request->validated();      

            $items = json_decode($request['items']);
    
            $newReceipt = Receipt::create([
                'customer_id'   => $request->user()->id,
                'shipment_id'   => $data['shipment_id'],
                'destination'   => $data['destination'],
                'coupon_id'     => Coupon::where('code', $data['coupon_code'] ?? '')?->first()?->id,
                'payment'       => floatval($data['payment']),
                'note'          => $data['note'],
            ]);
            $totalPrice = 0;
        
            foreach($items as $item){
                DB::table('item_receipt')->insert([
                    'item_id'      =>  $item->item_id,
                    'receipt_id'   =>  $newReceipt->id,
                    'quantity'     =>  $item->quantity,
                ]); 
    
                $totalPrice += (Item::where('id' , '=', $item->item_id)->first()->price * $item->quantity);
            }
    

            $finalPrice = $totalPrice - ($newReceipt?->coupon?->discount ?? 0) + $newReceipt->shipment->price;
    
    
            if($finalPrice > floatval($data['payment'])){
                return Response::abortFailed('Uang pembayaran tidak mencukupi!');
            };
    
            $newReceipt->update([
                'total_price'   => $totalPrice,
                'final_price'   => $finalPrice,
            ]);


            return $newReceipt;
        }); 

        return Response::json(new ReceiptResource($newReceipt));
    }

    public function update(ReceiptRequest $request, $id){
        if (auth()->user()->hasAnyRole('Mod', 'Admin')){
            try{
                DB::beginTransaction();
                $data = $request->validated();
                $updatedReceipt = Receipt::find($id);

                $updatedReceipt->update([
                    'customer_id'   => $data['customer_id'] ??  $updatedReceipt->customer->id,
                    'shipment_id'   => $data['shipment_id'] ?? $updatedReceipt->shipment->id, 
                    'destination'   => $data['destination'] ?? $updatedReceipt->destination,
                    'coupon_id'     => Coupon::where('code', $data['coupon_code'] ?? '')?->first()?->id,
                    'payment'       => floatval($data['payment']) ?? $updatedReceipt->payment,
                    'note'          => $data['note'],
                ]); 

                $totalPrice = 0;   
                $items = json_decode($request['items']);

                DB::table('item_receipt')->where('receipt_id', $updatedReceipt->id)->delete();

                    foreach($items as $item){
                    DB::table('item_receipt')->insert([
                        'item_id'      =>  $item->item_id,
                        'receipt_id'   =>  $updatedReceipt->id,
                        'quantity'     =>  $item->quantity,
                    ]); 
                    
                    $totalPrice += Item::where('id' , '=', $item->item_id)->first()->price * $item->quantity;
                }

                $finalPrice = $totalPrice - ($updatedReceipt->coupon?->discount ?? 0)  + $updatedReceipt->shipment->price;

                if($finalPrice > floatval($data['payment'])){
                    return Response::abortFailed('Uang pembayaran tidak mencukupi!');
                };

                $updatedReceipt->update([
                    'total_price'   => $totalPrice,
                    'final_price'   => $finalPrice,
                ]);

                DB::commit();

                return Response::json(new ReceiptResource($updatedReceipt));
            }
            catch (Exception $e){

                DB::rollback();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

    return Response::abortForbidden();
   }

    public function destroy(Receipt $receipt){
        if (auth()->user()->hasAnyRole('Mod', 'Admin')){
            $receipt->delete();
        }

        return Response::noContent();
   }
}


