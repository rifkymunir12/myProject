<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use App\Contracts\Response;
use App\Http\Requests\ItemUnitRequest;
use App\Http\Resources\ItemUnitCollection;
use App\Http\Resources\ItemUnitResource;
use Illuminate\Http\Request;
class ItemUnitController extends Controller
{
    public function index(Request $request){        
        $itemUnits = ItemUnit::paginate($request->show);

        return Response::json(new ItemUnitCollection($itemUnits));
    }

    public function show(ItemUnit $itemUnit){

        return Response::json(new ItemUnitResource($itemUnit));
    }

    public function store(ItemUnitRequest $request){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
            $data = $request->validated();

            $newItemUnit = ItemUnit::create($data);
          
            return Response::json(new ItemUnitResource($newItemUnit));
    }

    public function update(ItemUnitRequest $request, ItemUnit $itemUnit){
        //yg bisa add item dan update cuman admin, mod bisa melakukan apa saja di inventory management, 
        //yaitu update amount, dan update jlh item jika salah
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
            $data = $request->validated();
            $updatedItemUnit = $itemUnit;

            $updatedItemUnit->update($data);       

            return Response::json(new ItemUnitResource($updatedItemUnit));      

            //ini udah di cek
        }

    public function destroy(ItemUnit $itemUnit){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }

        $itemUnit->delete();
        return Response::noContent();
    }

}