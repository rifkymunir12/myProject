<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\Coupon;

class ShippingCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Shipment::create([
            'name'          => 'JJJ',
            'price'         => 10000,
            'description'   => 'Ini adalah descriptionnya',
        ]);

        Shipment::create([
            'name'          => 'VXZ',
            'price'         => 20000,
            'description'   => 'Adalah description',
        ]);

        Coupon::create([
            'name'          => 'coupon palsu 1',
            'code'          => 'alalalal',
            'discount'      =>  10000,
            'description'    => 'sebuah description',
        ]);

        Coupon::create([
            'name'          => 'coupon palsu 2',
            'code'          => 'asdfghjkl',
            'discount'      =>  20000,
            'description'    => 'sebuah tulisan',
        ]);

    }

}