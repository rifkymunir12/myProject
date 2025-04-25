<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'unit_id',
        'price',
        'item_image',
        'description',
        //tambahan
        'amount',
        'stock_in',
        'stock_out',
    ];    

     /**
     * The customer that belongs to the invoice.
     */
    protected function unit()
    {
        return $this->belongsTo(ItemUnit::class);
    } 
}
