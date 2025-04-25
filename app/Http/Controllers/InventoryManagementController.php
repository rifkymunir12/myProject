<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Contracts\Response;
use App\Http\Requests\EditItemStockRequest;
use App\Http\Requests\ItemAmountRequest;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;


class InventoryManagementController extends Controller
{
    public function update_item_amount(ItemAmountRequest $request){
        if (auth()->user()->hasRole('User')){
            return Response::abortForbidden();
        }

        $data = $request->validated();

        $item = Item::find($data['item_id']);
    
        $item_in = $data['item_in'];
    
        //karena item_in dibuat required, maka ga disingkat fungsi update nya
        $item->update([
            'stock_in'    =>  $item->stock_in + $item_in,
            'amount'      =>  $item->amount + $item_in,
        ]);  

        return Response::json(new ItemResource($item));
    }

    public function edit_item_stock(EditItemStockRequest $request){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }

        $data = $request->validated();

        $item = Item::find($data['item_id']);

        $item->update([
            'stock_in'  => $data['stock_in'] ?? $item->stock_in,
            'stock_out' => $data['stock_out'] ?? $item->stock_out, 
        ]); 

        $item->update([
            'amount'  => $item->stock_in + $item->stock_out, 
        ]);

        return Response::json(new ItemResource($item));
    }
}

//coba cek lagi benar kak kalkulasinya