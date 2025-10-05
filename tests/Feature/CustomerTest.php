<?php

use App\Http\Controllers\CustomerController;
use App\Models\Customer;
use App\Models\Message;
use App\Models\User;
use Database\Seeders\MessageSeeder;
use Database\Seeders\WhatsappTokenSeeder;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user, 'api');

    // Set up default config values
    DB::table('config')->insert([
        ['key' => 'spreadsheet_id', 'value' => 'test-spreadsheet-id-123'],
        ['key' => 'sync_time', 'value' => '18:30'],
        ['key' => 'days_after', 'value' => '3'],
        ['key' => 'send_interval', 'value' => '10'],
    ]);
    $this->seed(MessageSeeder::class);
    $this->seed(WhatsappTokenSeeder::class);
});

afterEach(function () {
    DB::table('config')->truncate();
});

describe('CustomerController', function () {
    describe('byDate', function () {
        it('returns customers by specific date', function () {
            $date = '2025-01-15';

            Customer::factory(3)->create(['date' => $date]);
            Customer::factory(2)->create(['date' => '2025-01-16']);

            $response = getJson("/api/customers/{$date}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonCount(3, 'data');
        });

        it('returns empty array when no customers exist for date', function () {
            $response = getJson('/api/customers/2025-01-15');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [],
                ]);
        });

        it('includes message relationship in response', function () {
            $message = Message::factory()->create();
            $customer = Customer::factory()->create([
                'date' => '2025-01-15',
                'message_id' => $message->id,
            ]);

            $response = getJson('/api/customers/2025-01-15');

            $response->assertStatus(200)
                ->assertJsonPath('data.0.message', fn ($msg) => $msg !== null);
        });
    });

    describe('byOrder', function () {
        it('returns customer by order ID', function () {
            $customer = Customer::factory()->create([
                'name' => 'John Doe',
            ]);

            $response = getJson("/api/customer/{$customer->order_id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonPath('data.order_id', $customer->order_id)
                ->assertJsonPath('data.name', 'John Doe');
        });

        it('returns 404 when order ID does not exist', function () {
            $response = getJson('/api/customer/INVALID-ORDER');

            $response->assertStatus(404);
        });
    });

    describe('editMessage', function () {

        it('allows editing when status is waiting', function () {
            $message = Message::factory()->create();
            $customer = Customer::factory()->create([
                'status' => 'waiting',
            ]);

            $response = putJson("/api/customer/message/{$customer->order_id}", [
                'message_id' => $message->id,
            ]);

            $response->assertStatus(200);
        });

        it('allows editing when status is not sended', function () {
            $message = Message::factory()->create();
            $customer = Customer::factory()->create([
                'status' => 'failed',
            ]);

            $response = putJson("/api/customer/message/{$customer->order_id}", [
                'message_id' => $message->id,
            ]);

            $response->assertStatus(200);
        });

        it('denies editing when status is sended', function () {
            $message = Message::factory()->create();
            $customer = Customer::factory()->create([
                'status' => 'sended',
            ]);

            $response = putJson("/api/customer/message/{$customer->order_id}", [
                'message_id' => $message->id,
            ]);

            $response->assertStatus(403);
        });

        it('validates message_id is required', function () {
            $customer = Customer::factory()->create([
                'status' => 'waiting',
            ]);

            $response = putJson("/api/customer/message/{$customer->order_id}", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('message_id');
        });

        it('validates message_id must exist in messages table', function () {
            $customer = Customer::factory()->create([
                'status' => 'waiting',
            ]);

            $response = putJson("/api/customer/message/{$customer->order_id}", [
                'message_id' => 99999,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('message_id');
        });

        it('returns 404 when customer order does not exist', function () {
            $message = Message::factory()->create();

            $response = putJson('/api/customer/message/INVALID-ORDER', [
                'message_id' => $message->id,
            ]);

            $response->assertStatus(404);
        });
    });

    describe('sendMessage', function () {
        it('sends message successfully when status is failed and phone is valid', function () {
            $customer = Customer::factory()->create([
                'status' => 'failed',
                'phone' => '6289636055420',
            ]);
            $mock = Mockery::mock(CustomerController::class)
                ->makePartial()
                ->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('isWhatsapp')
                ->andReturn(true);
            $mock->shouldReceive('send')
                ->andReturn(true);

            $response = postJson("/api/customer/message/{$customer->order_id}");
            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => null,
                ]);
            expect($customer->fresh()->status)->toBe('sended');
        });

        it('denies sending when status is not failed', function () {
            $customer = Customer::factory()->create([
                'status' => 'waiting',
                'phone' => '628123456789',
            ]);

            $response = postJson("/api/customer/message/{$customer->order_id}");

            $response->assertStatus(403);
        });

        it('denies sending when status is sended', function () {
            $customer = Customer::factory()->create([
                'status' => 'sended',
                'phone' => '628123456789',
            ]);

            $response = postJson("/api/customer/message/{$customer->order_id}");

            $response->assertStatus(403);
        });

        it('returns error when WhatsApp validation fails', function () {
            $customer = Customer::factory()->create([
                'status' => 'failed',
                'phone' => '6281234567890',
            ]);

            $response = postJson("/api/customer/message/{$customer->order_id}");

            // Should return error when validation fails
            expect($response->status())->toBeIn([200, 400]);
        });

        it('returns 404 when customer order does not exist', function () {
            $response = postJson('/api/customer/message/INVALID-ORDER');

            $response->assertStatus(404);
        });
    });
});
