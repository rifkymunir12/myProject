<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Contracts\Response;
use App\Http\Requests\ShipmentRequest;
use App\Http\Resources\ShipmentCollection;
use App\Http\Resources\ShipmentResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ShipmentController extends Controller
{
    public function index(Request $request){
        
        $shipments = QueryBuilder::for(Shipment::class)
                ->allowedFilters([
                    AllowedFilter::exact('price'),
                ])
                ->paginate($request->show);

        //$shipments = Shipment::paginate($request->show);

        return Response::json(new ShipmentCollection($shipments));
    }

    public function show(Shipment $shipment){

        return Response::json(new ShipmentResource($shipment));
    }

    public function store(ShipmentRequest $request){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
        $data = $request->validated();
            
        $newShipment = Shipment::create($data);//belum diganti

        return Response::json(new ShipmentResource($newShipment));
    
    }

    public function update(ShipmentRequest $request, Shipment $shipment){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
        $data = $request->validated();
        $updatedShipment = $shipment;

        $updatedShipment->update($data);

        //$updatedShipment->update($data); 

        return Response::json(new ShipmentResource($updatedShipment));
    }
   

    public function destroy(Shipment $shipment){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }

        $shipment->delete();
        return Response::noContent();
    }
   
}