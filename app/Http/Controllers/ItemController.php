<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use App\Models\Item;
use App\Contracts\Response;
use App\Http\Requests\ItemUpdateRequest;
use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ItemController extends Controller
{
    public function index(Request $request){
    
        $items = Item::paginate($request->show);

        $items = QueryBuilder::for(Item::class)
                ->allowedFilters([
                    'unit',
                    AllowedFilter::exact('price'),
                ])
                ->paginate($request->show);

        return Response::json(new ItemCollection($items));
    }

    public function show(Item $item){

        return Response::json(new ItemResource($item));
    }

    public function store(ItemRequest $request){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }

        $data = $request->validated();
        

        $data['item_image'] = $request->file('image')?->hashName() != NULL ?  $request->file('image')->hashName() : '';
        $data['stock_out'] = 0;
        $data['stock_in'] = $data['amount'];

        $newItem = Item::create($data);

        $request->file('image')?->store('public');

        return Response::json(new ItemResource($newItem));
    }

    public function update(ItemUpdateRequest $request, Item $item){
        //yg bisa add item dan update cuman admin, mod bisa melakukan apa saja di inventory management, 
        //yaitu update amount, dan update jlh item jika salah
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
            $data = $request->validated();
            $updatedItem = $item;

            $data['item_image'] = $request->file('image')?->hashName() != NULL ?  $request->file('image')->hashName() : '';

            $updatedItem->update($data);        

            $request->file('image')?->store('public');

            return Response::json(new ItemResource($updatedItem));
        
        //yang di cek terakhir
   }

    public function destroy(Item $item){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }

        $item->delete();
        return Response::noContent();
    }

}