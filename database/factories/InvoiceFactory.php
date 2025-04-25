<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // asli
        // return [
        //     'coupon_id' => '9c9695d2-6827-456c-820c-7dd1fdfc0c6a', 
        //     'shipment_id' => '9c9699d5-9ce5-4ad6-828d-bcb90b918f82',
        //     'destination' => 'thissss',
        //     'payment' => 500000,
        //     'note'  => 'catatatn',
        // ];

        return [
            'customer_id' => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',//this_is_mod11
            'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
            'destination' => 'thissss',
            'note'  => 'catatatn',
            'status'    => 'Unpaid',
            'total_price' => 150000,
            'final_price' => 150000,
        ];
    
    }

    public function configure(){    
        return $this->afterCreating(function (Invoice $invoice){
            DB::table('invoice_item')->insert([
                'item_id'      =>  '9d0b37c8-2a68-4f6b-90c4-adc718ceca9c',//kursi palsu, 10.000
                'invoice_id'   =>  $invoice->id,
                'quantity'     =>  4,//40000
            ]); 

            DB::table('invoice_item')->insert([
                'item_id'      =>  '9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84',//gula  10
                'invoice_id'   =>  $invoice->id,
                'quantity'     =>  1000,//10000
            ]); 

            DB::table('invoice_item')->insert([
                'item_id'      =>  '9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf',// buku palsu 50.000
                'invoice_id'   =>  $invoice->id,
                'quantity'     =>  2//100000
            ]); 

            $path = storage_path('app/public/qr-codes/');
            if (!file_exists($path)) mkdir($path, 0777, true);

            QrCode::format('png')->size(600)->generate($invoice->id, $path . $invoice->id. '.png');

            $invoice->update([
                'barcode'       => $invoice->id.'.png'
            ]);

            // asli
            // DB::table('invoice_item')->insert([
            //     'item_id'      =>  '9c96979c-c6c8-4537-a72c-506679292fb5',
            //     'invoice_id'   =>  $invoice->id,
            //     'quantity'     =>  4,
            // ]); 

            // DB::table('invoice_item')->insert([
            //     'item_id'      =>  '9c9697ca-e579-46f5-9bcc-0fc558c0c668',
            //     'invoice_id'   =>  $invoice->id,
            //     'quantity'     =>  1000,
            // ]); 

            // DB::table('invoice_item')->insert([
            //     'item_id'      =>  '9c969d13-627a-4fab-9eac-33711b662ab2',
            //     'invoice_id'   =>  $invoice->id,
            //     'quantity'     =>  2,
            // ]); 
        });
    }
}