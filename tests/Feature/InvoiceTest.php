<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Coupon;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
class InvoiceTest extends TestCase
{
    //TAMBAHKA/LENGKAPI TESTING UNTUK INVOICE
    /**
     * @test
     */
    public function invoice_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/invoice',
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function invoice_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/invoice',
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                    '*' => [ 
                        'id',
                        'invoice_code',
                        'customer' =>[
                            'id',
                            'customer',
                        ],
                        'destination',
                        'status',
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
                ],
            ]
        ]);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function invoice_get_resource_unauthenticated() : void
    {
        $invoice= Invoice::factory()->create();

        $response = $this->get(
                    '/api/invoice/'.$invoice->id,
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function invoice_get_resource_authenticated() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/invoice/'.$invoice->id,
                    ['Accept' => 'application/json']
        );

        $response->assertJsonStructure([
            'data' => [
                        'id',
                        'invoice_code',
                        'customer' =>[
                            'id',
                            'customer',
                        ],
                        'destination',
                        'status',
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

        $invoice->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function invoice_get_pdf_unauthenticated() : void
    {
        $invoice= Invoice::factory()->create();

        $response = $this->get(
                    '/api/print_invoice/'.$invoice->id,
                    ['Accept' => 'application/json']
        );
        
        $invoice->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function invoice_get_pdf_authenticated() : void
    {
        $invoice= Invoice::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/print_invoice/'.$invoice->id,
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function invoice_get_excel_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/export-invoice',
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function invoice_get_excel_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/export-invoice',
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(200);
    }

    //tambah test untuk post method utk semua error code

    /**
     * @test
     */
    public function invoice_post_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/invoice',
                    [
                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'note'  => 'catatatn',
                    ],
                    ['Accept' => 'application/json']
        );


        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function invoice_post_resource_wrong_input() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/invoice',
                    [
                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9db37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'note'  => 'catatatn',
                        'destination' => 'thissss',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        //ga masuk

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_post_resource_wrong_coupon_code() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/invoice',
                    [
                        'coupon_code' => 'asdsfgh',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'note'  => 'catatatn',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The selected coupon code is invalid.',
        ]);
        
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_post_resource_wrong_shipment() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/invoice',
                    [
                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea21',
                        'destination' => 'thissss',
                        'note'  => 'catatatn',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The selected shipment id is invalid.',
        ]);

        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function invoice_post_resource_used_coupon() : void
    {
        
        $user = User::factory()->create();
        DB::table('coupon_user')->insert([
            'coupon_code'   => 'asdfghjkl',
            'user_id'       =>  $user->id,
        ]);

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/invoice',
                    [
                        // 'coupon_id' => '9c98f20b-64e0-46ac-9f0d-7636dbc0f156',
                        // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                        //             {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                        //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                        // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                        // 'destination' => 'thissss',
                        // 'note'  => 'catatatn',

                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'payment' => 500000,
                        'note'  => 'catatatn',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        DB::table('coupon_user')->where('user_id', $user->id)->delete();


        $response->assertJsonFragment([
            'message' => 'Coupon telah digunakan!',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_post_resource_quantity_passed_the_amount() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson( //setelah dd, di DB:commit dia ngelag lama kali, di postman ada lock wait timeout error
                    '/api/invoice',
                    [
                        // 'coupon_id' => '9c98f20b-64e0-46ac-9f0d-7636dbc0f156',
                        // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                        //             {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                        //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                        // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                        // 'destination' => 'thissss',
                        // 'note'  => 'catatatn',

                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4000000000}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'note'  => 'catatatn',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'Kursi palsu tidak mencukupi permintaan!',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_post_resource_quantity_not_a_multiple() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                '/api/invoice',
                [
                    // asli
                    // 'coupon_code' => 'qwerttyupp',
                    // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                    //              {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                    //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                    // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                    // 'destination' => 'thissss',
                    // 'payment' => 500000,
                    // 'note'  => 'catatatn',

                    'coupon_code' => 'alalalal',
                    'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1011},
                                {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                    'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                    'destination' => 'thissss',
                    'note'  => 'catatatn',
                ],
                
                
                ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'Bukan kelipatan 100!',
        ]);

        $response->assertStatus(422);
    }
    

     /**
     * @test
     */
    public function invoice_post_resource_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/invoice',
                    [
                        // asli
                        // 'coupon_code' => 'qwerttyupp',
                        // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                        //              {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                        //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                        // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                        // 'destination' => 'thissss',
                        // 'payment' => 500000,
                        // 'note'  => 'catatatn',

                        'coupon_code' => 'alalalal',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'note'  => 'catatatn',
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('invoices', [
            'id'        => $response->json('data.id'),
            // 'coupon_id' => '9c9695d2-6827-456c-820c-7dd1fdfc0c6a',
            // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
            'coupon_id' => '9d0b37c8-9795-44ec-be89-116530a98eb6',
            'shipment_id' => $response->json('data.shipment.id'),
            'destination' => 'thissss',
            'payment' => 0,
            'note'  => 'catatatn',
        ]);

        $this->assertDatabaseHas('invoice_item', [
            // 'item_id'      =>  '9c96979c-c6c8-4537-a72c-506679292fb5',
            'item_id'   => '9d0b37c8-2a68-4f6b-90c4-adc718ceca9c',
            'invoice_id'=>  $response->json('data.id'),
            'quantity'  => 4,
        ]);

        $this->assertDatabaseHas('invoice_item', [
            // 'item_id'      =>  '9c9697ca-e579-46f5-9bcc-0fc558c0c668'
            'item_id'   => '9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84',
            'invoice_id'=>  $response->json('data.id'),
            'quantity'  => 1000,
        ]);

        $this->assertDatabaseHas('invoice_item', [
            // 'item_id'      =>  '9c969d13-627a-4fab-9eac-33711b662ab2',
            'item_id'   => '9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf',
            'invoice_id'=>  $response->json('data.id'),
            'quantity'  => 2,
        ]); 

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
        $user->forceDelete();// ini juga akan ngapus data customer di coupon_user
    }

    /**
     * @test
     */
    public function invoice_update_resource_unauthentificated() : void
    {
        $invoice = Invoice::factory()->create();
      
        $response = $this->putJson(
                    '/api/invoice/'.$invoice->id,
                    [
                        // asli
                        // 'coupon_id' => '9c98f20b-64e0-46ac-9f0d-7636dbc0f156',
                        // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                        //              {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                        //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                        // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                        // 'destination' => 'thissss',
                        // 'payment' => 500000,
                        // 'note'  => 'catatatn',

                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'payment' => 500000,
                        'note'  => 'catatatn',
                        'status' => 'Unpaid',
                    ],
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
      
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function invoice_update_resource_unauthorized() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/invoice/'.$invoice->id,
                    [
                        // 'coupon_id' => '9c98f20b-64e0-46ac-9f0d-7636dbc0f156',
                        // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                        //              {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                        //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                        // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                        // 'destination' => 'thissss',
                        // 'payment' => 500000,
                        // 'note'  => 'catatatn ',

                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'payment' => 500000,
                        'note'  => 'catatatn',
                        'status' => 'Unpaid',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $invoice->forceDelete();
        $user->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function invoice_update_resource_wrong_input() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/invoice/'.$invoice->id,
                    [
                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'payment' => -5,
                        'note'  => 'catatatn',
                        'status' => 'Unpaid',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $invoice->forceDelete();
        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_update_resource_wrong_coupon_code() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/invoice/'.$invoice->id,
                    [
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'coupon_code' => 'asdffffff',
                        'destination' => 'thissss',
                        'payment' => 500000,
                        'note'  => 'catatatn',
                        'status' => 'Unpaid',
                    ],
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The selected coupon code is invalid.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_update_resource_wrong_shipment() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/invoice/'.$invoice->id,
                    [
                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea21',
                        'destination' => 'thissss',
                        'payment' => 500000,
                        'note'  => 'catatatn',
                        'status' => 'Unpaid',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $invoice->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The selected shipment id is invalid.',
        ]);
       

        //$response->dd(); (ga ada terinsert di database)

        $response->assertStatus(422);
    }


    /**
     * @test
     */
    // public function invoice_update_resource_not_enough_payment() : void
    // {
    //     $invoice = Invoice::factory()->create();
    //     $user = User::factory()->create();
    //     $user->assignRole(Role::where('name' ,'Admin')->first());
    //     Passport::actingAs($user);

    //     $response = $this->putJson(
    //                 '/api/invoice/'.$invoice->id,
    //                 [
    //                     // 'coupon_id' => '9c98f20b-64e0-46ac-9f0d-7636dbc0f156',
    //                     // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
    //                     //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
    //                     //             {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
    //                     // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
    //                     // 'payment' => 10000,
    //                     // 'destination' => 'thissss',
    //                     // 'note'  => 'catatatn',

    //                     'coupon_code' => 'asdfghjkl',
    //                     'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
    //                                 {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
    //                                 {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
    //                     'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
    //                     'destination' => 'thissss',
    //                     'note'  => 'catatatn',
    //                 ],
    //                 ['Accept' => 'application/json']
    //     );

    //     $invoice->forceDelete();
    //     $user->forceDelete();
        
    //     $response->assertJsonFragment([
    //         'message' => 'Uang pembayaran tidak mencukupi!',
    //     ]);

    //     $response->assertStatus(422);
    // }

    /**
     * @test
     */
    public function invoice_update_resource_quantity_passed_the_amount() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson( //setelah dd, di DB:commit dia ngelag lama kali, di postman ada lock wait timeout error
                    '/api/invoice/'.$invoice->id,
                    [
                        // 'coupon_id' => '9c98f20b-64e0-46ac-9f0d-7636dbc0f156',
                        // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                        //             {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                        //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                        // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                        // 'destination' => 'thissss',
                        // 'note'  => 'catatatn',
                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 40000000000}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'note'  => 'catatatn',
                        'payment' => 500000,
                        'status' => 'Unpaid',
                        
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
    
        $response->assertJsonFragment([
            'message' => 'Kursi palsu tidak mencukupi permintaan!',
        ]);



        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function invoice_update_resource_used_coupon() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        DB::table('coupon_user')->insert([
            'coupon_code'   => 'asdfghjkl',
            'user_id'       =>  $invoice->customer_id,
        ]);

        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/invoice/'.$invoice->id,
                    [
                        'coupon_code' => 'asdfghjkl',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'payment' => 500000,
                        'note'  => 'catatatn',
                        'status' => 'Unpaid',
                    ],
                    ['Accept' => 'application/json']
        );
        
        DB::table('coupon_user')->where('user_id', $invoice->customer_id)->delete();

        $invoice->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'Coupon telah digunakan!',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_update_resource_quantity_not_a_multiple() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->putJson(
                '/api/invoice/'.$invoice->id,
                [
                    // asli
                    // 'coupon_code' => 'qwerttyupp',
                    // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                    //              {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                    //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                    // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                    // 'destination' => 'thissss',
                    // 'payment' => 500000,
                    // 'note'  => 'catatatn',

                    'coupon_code' => 'alalalal',
                    'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1011},
                                {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                    'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                    'destination' => 'thissss',
                    'note'  => 'catatatn',
                    'payment' => 500000,
                    'status' => 'Unpaid',
                ],
                
                
                ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'Bukan kelipatan 100!',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function invoice_update_resource_authorized() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/invoice/'.$invoice->id,
                    [
                        // asli
                        // 'coupon_code' => 'qwerttyupp',
                        // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                        //             {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c0c668", "quantity": 1000},
                        //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                        // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                        // 'destination' => 'thissss',
                        // 'payment' => 500000,
                        // 'note'  => 'catatatn',

                        'coupon_code' => 'alalalal',
                        'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                                    {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                                    {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                        'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                        'destination' => 'thissss',
                        'note'  => 'catatatn',
                        'payment' => 500000,
                        'status' => 'Unpaid',
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('invoices', [
            'id'    => $invoice->id,
            // 'coupon_id' => '9c9695d2-6827-456c-820c-7dd1fdfc0c6a',
            // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
            'coupon_id' => '9d0b37c8-9795-44ec-be89-116530a98eb6',
            'shipment_id' => $response->json('data.shipment.id'),
            'destination' => 'thissss',
            'payment' => 500000,
            'note'  => 'catatatn',
        ]);
       
        $this->assertDatabaseHas('invoice_item', [
            // 'item_id'      =>  '9c96979c-c6c8-4537-a72c-506679292fb5',
            'item_id'   => '9d0b37c8-2a68-4f6b-90c4-adc718ceca9c',
            'invoice_id'=> $invoice->id,
            'quantity'  => 4,
        ]);

        $this->assertDatabaseHas('invoice_item', [
            // 'item_id'      =>  '9c9697ca-e579-46f5-9bcc-0fc558c0c668'
            'item_id'   => '9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84',
            'invoice_id'=>  $invoice->id,
            'quantity'  => 1000,
        ]);

        $this->assertDatabaseHas('invoice_item', [
            // 'item_id'      =>  '9c969d13-627a-4fab-9eac-33711b662ab2',
            'item_id'   => '9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf',
            'invoice_id'=>  $invoice->id,
            'quantity'  => 2,
        ]); 

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

        $invoice->forceDelete();
        $user->forceDelete();
        DB::table('coupon_user')
            ->where('user_id', $invoice->customer_id)
            ->where('coupon_code', 'alalalal')->delete();
    }

    /**
     * @test
     */
    public function invoice_update_resource_can_change_coupon() : void
    {
        $invoice = Invoice::create([
            'customer_id'          => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',
            'destination'          => 'a place',
            'shipment_id'          => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
            'payment'              => 0,
            'coupon_id'            => '9d0b37c8-9795-44ec-be89-116530a98eb6',//alalalal
            'status'               => 'Unpaid',
            'note'                 => 'this is note',
        ]); 

        DB::table('invoice_item')->insert([
            'item_id'      =>  '9d0b37c8-2a68-4f6b-90c4-adc718ceca9c',
            'invoice_id'   =>  $invoice->id,
            'quantity'     =>  4,
        ]); 
        
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
            '/api/invoice/'.$invoice->id,
            [
                // asli
                // 'coupon_code' => 'qwerttyupp',
                // 'items' => '[{"item_id": "9c96979c-c6c8-4537-a72c-506679292fb5", "quantity": 4}, 
                //             {"item_id": "9c9697ca-e579-46f5-9bcc-0fc558c 0c668", "quantity": 1000},
                //             {"item_id" : "9c969d13-627a-4fab-9eac-33711b662ab2", "quantity" : 2}]',
                // 'shipment_id' => '9c969a08-36c9-4426-9d53-7d5ce5a79ba4',
                // 'destination' => 'thissss',
                // 'payment' => 500000,
                // 'note'  => 'catatatn',

                'coupon_code' => 'asdfghjkl',//
                'items' => '[{"item_id": "9d0b37c8-2a68-4f6b-90c4-adc718ceca9c", "quantity": 4}, 
                            {"item_id": "9d0b37c8-58ae-4b71-af7c-d29cfb4d9f84", "quantity": 1000},
                            {"item_id" : "9d0b37c8-4ab5-46b9-a4cf-5adc58fc9caf", "quantity" : 2}]',
                'shipment_id' => '9d0b37c8-895f-4e72-896b-9e77a52bea22',
                'destination' => 'thissss',
                'note'  => 'catatatn',
                'payment' => 500000,
                'status' => 'Paid',
            ],
            ['Accept' => 'application/json']
            );
        
            $this->assertDatabaseMissing('coupon_user', [
                'coupon_code'   => 'alalalal',
                'user_id'       =>  $invoice->customer_id,
            ]); 
    
            DB::table('coupon_user')
            ->where('user_id', $invoice->customer_id)
            ->where('coupon_code', 'asdfghjkl')->delete();
    
            $response->assertStatus(200);
    
            $invoice->forceDelete();
            $user->forceDelete();
    }

    /**
     * @test
     */
    public function invoice_delete_resource_unauthentificated() : void
    {
        $invoice = Invoice::factory()->create();
      
        $response = $this->delete(
                    '/api/invoice/'.$invoice->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function invoice_delete_resource_unauthorized() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first()); 
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/invoice/'.$invoice->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $invoice->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(403);
    }
        
    

    /**
     * @test
     */
    function invoice_delete_resource_authorized() : void
    {
        $invoice = Invoice::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/invoice/'.$invoice->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(204);
        $response->assertNoContent();
        $this->assertSoftDeleted($invoice);

        $invoice->forceDelete();
        $user->forceDelete();    
    }

    //lengkapi test berdasarkan fungsi2 yang telah ditambah/edit, dan buat test file baru untuk payment controller
}
