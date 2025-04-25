<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

     /**  
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'customer_id',
        'shipment_id',
        'coupon_id',
        'destination',
        'total_price',
        'discount',
        'final_price',
        'payment',
        'barcode',
        'note',
        'invoice_code',
        'status',
    ];

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            $day = date('d');
            $month = date('m');
            $idSequence = Invoice::get()->count();
            $model->invoice_code = 'INV/'.($idSequence+1).'/'.$day.'/'.$month;
        });

        return;
    }

    /**
     * The coupon that belongs to the invoice.
     */
    protected function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    
    /**
     * The customer that belongs to the invoice.
     */
    protected function customer()
    {
        return $this->belongsTo(User::class);
    } 

    /**
     * The shipment that belongs to the invoice.
     */
    protected function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * The items that belong to the invoice.
     */
    protected function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity');
    }

}
