<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Invoice;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    /**
     * @test
     */
    public function update_status_payment_unauthenticated() : void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Waiting',
                        'payment'      => 10000,
                    ],
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
      
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function update_status_payment_unauthorized() : void
    {
        $invoice = Invoice::factory()->create();

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Waiting',
                        'payment'      => 10000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();
      
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function update_status_payment_wrong_input() : void
    {

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));
        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => 'kakaakpoo',
                        'status'       => 'Waiting',
                        'payment'      => 10000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();
      
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function update_status_payment_negative_payment() : void
    {

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));
        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Paid',
                        'payment'      => -10000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();
      
        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' => 'The payment field must be at least 0.',
        ]);
    }

    /**
     * @test
     */
    public function update_status_payment_wrong_status() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));
        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Pain',
                        'payment'      => 10000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();
      
        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' => 'The selected status is invalid.',
        ]);
    }

    /**
     * @test
     */
    public function update_status_not_enough_payment() : void
    {

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));
        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Paid',
                        'payment'      => 3000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();
      
        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' => 'Uang pembayaran tidak mencukupi!',
        ]);
    }


    /**
     * @test
     */
    public function update_status_payment_authorized_paid() : void
    {

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));
        $invoice = Invoice::factory()->create();
        
        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Paid',
                        'payment'      => 150000,
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('invoices', [
            'id'    => $invoice->id,
            'status' => "Paid",
            'payment' => 150000,
        ]);

        $user->forceDelete();
        $invoice->forceDelete();

        $response->assertJsonStructure([
            'data' => [
                        'id',
                        'invoice_code',
                        'customer' =>[
                            'id',
                            'customer',
                        ],
                        'destination',
                        'items' =>[
                            '*' => [
                                'name',
                                'price',
                                'quantity',
                                'item_image',
                            ],
                        ],
                        'total_price',
                        'coupon' => [
                            'name',
                            'description',
                            'discount',
                        ],
                        'shipment' => [
                            'id',
                            'name',
                            'price',
                            'description',  
                        ],
                        'final_price',
                        'payment',
                        'barcode',
                        'note',
                        'created_at'
            ]
        ]);
      
        $response->assertStatus(200);

    }

    /**
     * @test
     */
    public function update_status_payment_authorized_cancelled() : void
    {

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));
        $invoice = Invoice::factory()->create();
        
        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Cancelled',
                        'payment'      => 0,
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('invoices', [
            'id'    => $invoice->id,
            'status' => "Cancelled",
        ]);

        $user->forceDelete();
        $invoice->forceDelete();
        
        $response->assertJsonStructure([
            'data' => [
                        'id',
                        'invoice_code',
                        'customer' =>[
                            'id',
                            'customer',
                        ],
                        'destination',
                        'items' =>[
                            '*' => [
                                'name',
                                'price',
                                'quantity',
                                'item_image',
                            ],
                        ],
                        'total_price',
                        'coupon' => [
                            'name',
                            'description',
                            'discount',
                        ],
                        'shipment' => [
                            'id',
                            'name',
                            'price',
                            'description',
                        ],
                        'final_price',
                        'payment',
                        'barcode',
                        'note',
                        'created_at'
            ]
        ]);
      
        $response->assertStatus(200);

    }

    /**
     * @test
     */
    public function update_status_payment_authorized_waiting() : void
    {

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));
        $invoice = Invoice::factory()->create();
        
        $response = $this->postJson(
                    '/api/update_status_payment',
            [
                        'invoice_id'   => $invoice->id,
                        'status'       => 'Waiting',
                        'payment'      => 0,
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('invoices', [
            'id'    => $invoice->id,
            'status' => "Waiting",
        ]);

        $user->forceDelete();
        $invoice->forceDelete();

        $response->assertJsonStructure([
            'data' => [
                        'id',
                        'invoice_code',
                        'customer' =>[
                            'id',
                            'customer',
                        ],
                        'destination',
                        'items' =>[
                            '*' => [
                                'name',
                                'price',
                                'quantity',
                                'item_image',
                            ],
                        ],
                        'total_price',
                        'coupon' => [
                            'name',
                            'description',
                            'discount',
                        ],
                        'shipment' => [
                            'id',
                            'name',
                            'price',
                            'description',
                        ],
                        'final_price',
                        'payment',
                        'barcode',
                        'note',
                        'created_at'
            ]
        ]);
      
        $response->assertStatus(200);

    }
 
    

     /**
     * @test
     */
    public function send_payment_confirmation_unauthenticated() : void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
      
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function send_payment_confirmation_not_owning_the_invoice() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();
        
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function send_payment_confirmation_owned_invoice_already_cancelled() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $invoice = Invoice::create([
            'customer_id'           => $user->id,
            'shipment_id'           => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
            'destination'           => 'thissss',
            'note'                  => 'catatatn',
            'status'                => 'Cancelled',
            'total_price'           => 150000,
            'final_price'           => 150000,
        ]);

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();

        $response->assertJsonFragment([
            'message' => 'Pembelian telah dicancelled',
        ]);
      
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function send_payment_confirmation_already_paid() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $invoice = Invoice::create([
            'customer_id'           => $user->id,
            'shipment_id'           => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
            'destination'           => 'thissss',
            'note'                  => 'catatatn',
            'status'                => 'Paid',
            'total_price'           => 150000,
            'final_price'           => 150000,
        ]);

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();

        $response->assertJsonFragment([
            'message' => 'Anda telah membayar pembelian!',
        ]);
      
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function send_payment_confirmation_already_send_confirmation() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $invoice = Invoice::create([
            'customer_id'           => $user->id,
            'shipment_id'           => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
            'destination'           => 'thissss',
            'note'                  => 'catatatn',
            'status'                => 'Waiting',
            'total_price'           => 150000,
            'final_price'           => 150000,
        ]);

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $invoice->forceDelete();

        $response->assertJsonFragment([
            'message' => 'Telah melakukan pembayaran! Silahkan tunggu konfirmasi pembayaran!',
        ]);
      
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function send_payment_confirmation_authenticated() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $invoice = Invoice::create([
            'customer_id'           => $user->id,
            'shipment_id'           => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
            'destination'           => 'thissss',
            'note'                  => 'catatatn',
            'status'                => 'Unpaid',
            'total_price'           => 150000,
            'final_price'           => 150000,
        ]);

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

    
        $user->forceDelete();
        $invoice->forceDelete();

        //dd($invoice);//dia masih unpaid

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'Waiting',
        ]);


        $response->assertJsonFragment([
            'message' => 'Silahkan tunggu konfirmasi pembayaran!',
        ]);
        
        $response->assertStatus(202);
    }

    /**
     * @test
     */
    public function cancel_purchase_unauthenticated() : void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );
       
        $invoice->forceDelete();

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function cancel_purchase_already_cancelled() : void
    {
        $invoice = Invoice::factory()->create();

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'Pembelian telah dicancel!',
        ]);
      
       
        $invoice->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function cancel_purchase_success() : void
    {
        $invoice = Invoice::factory()->create();

        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/payment_confirmation',
            [
                        'invoice_id'   => $invoice->id,
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'Pembelian telah dicancel!',
        ]);
      
       
        $invoice->forceDelete();

        $response->assertStatus(403);
    }
}