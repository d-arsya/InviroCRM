<?php

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
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
});

afterEach(function () {
    DB::table('config')->truncate();
});

describe('ConfigController', function () {
    describe('spreadsheet', function () {
        it('returns spreadsheet link and sync time', function () {
            $response = getJson('/api/config/spreadsheet');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [
                        'spreadsheet' => 'https://docs.google.com/spreadsheets/d/test-spreadsheet-id-123',
                        'hour' => 18,
                        'minute' => 30,
                    ],
                ]);
        });

        it('parses sync time correctly with single digit minutes', function () {
            DB::table('config')->whereKey('sync_time')->update(['value' => '20:05']);

            $response = getJson('/api/config/spreadsheet');

            $response->assertStatus(200)
                ->assertJsonPath('data.hour', 20)
                ->assertJsonPath('data.minute', 5);
        });
    });

    describe('getSending', function () {
        it('returns sending configuration', function () {
            $response = getJson('/api/config/sending');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [
                        'days' => 3,
                        'interval' => 10,
                    ],
                ]);
        });

        it('returns integer values for days and interval', function () {
            $response = getJson('/api/config/sending');

            $response->assertStatus(200);

            expect($response->json('data.days'))->toBeInt();
            expect($response->json('data.interval'))->toBeInt();
        });
    });

    describe('sending', function () {
        it('updates sending configuration', function () {
            $response = putJson('/api/config/sending', [
                'days' => 5,
                'interval' => 15,
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [
                        'days' => 5,
                        'interval' => 15,
                    ],
                ]);

            expect(DB::table('config')->whereKey('days_after')->first()->value)->toBe('5');
            expect(DB::table('config')->whereKey('send_interval')->first()->value)->toBe('15');
        });

        it('validates days is required', function () {
            $response = putJson('/api/config/sending', [
                'interval' => 15,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('days');
        });

        it('validates days must be integer', function () {
            $response = putJson('/api/config/sending', [
                'days' => 'invalid',
                'interval' => 15,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('days');
        });

        it('validates days minimum value is 1', function () {
            $response = putJson('/api/config/sending', [
                'days' => 0,
                'interval' => 15,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('days');
        });

        it('validates days maximum value is 10', function () {
            $response = putJson('/api/config/sending', [
                'days' => 11,
                'interval' => 15,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('days');
        });

        it('validates interval is required', function () {
            $response = putJson('/api/config/sending', [
                'days' => 5,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('interval');
        });

        it('validates interval must be integer', function () {
            $response = putJson('/api/config/sending', [
                'days' => 5,
                'interval' => 'invalid',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('interval');
        });

        it('validates interval minimum value is 5', function () {
            $response = putJson('/api/config/sending', [
                'days' => 5,
                'interval' => 4,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('interval');
        });

        it('validates interval maximum value is 60', function () {
            $response = putJson('/api/config/sending', [
                'days' => 5,
                'interval' => 61,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('interval');
        });
    });

    describe('link', function () {
        it('updates spreadsheet link and extracts ID', function () {
            $response = putJson('/api/config/link', [
                'link' => 'https://docs.google.com/spreadsheets/d/new-spreadsheet-id-456/edit',
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [
                        'link' => 'https://docs.google.com/spreadsheets/d/new-spreadsheet-id-456',
                    ],
                ]);

            expect(DB::table('config')->whereKey('spreadsheet_id')->first()->value)
                ->toBe('new-spreadsheet-id-456');
        });

        it('validates link is required', function () {
            $response = putJson('/api/config/link', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('link');
        });

        it('validates link must be string', function () {
            $response = putJson('/api/config/link', [
                'link' => 12345,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('link');
        });

        it('extracts ID from different Google Sheets URL formats', function () {
            $response = putJson('/api/config/link', [
                'link' => 'https://docs.google.com/spreadsheets/d/abc123xyz/edit#gid=0',
            ]);

            $response->assertStatus(200)
                ->assertJsonPath('data.link', 'https://docs.google.com/spreadsheets/d/abc123xyz');

            expect(DB::table('config')->whereKey('spreadsheet_id')->first()->value)
                ->toBe('abc123xyz');
        });
    });

    describe('sync', function () {
        it('updates sync time configuration', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 20,
                'minute' => 45,
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [
                        'hour' => 20,
                        'minute' => 45,
                    ],
                ]);

            expect(DB::table('config')->whereKey('sync_time')->first()->value)
                ->toBe('20:45');
        });

        it('validates hour is required', function () {
            $response = putJson('/api/config/sync', [
                'minute' => 30,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('hour');
        });

        it('validates hour must be integer', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 'invalid',
                'minute' => 30,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('hour');
        });

        it('validates hour minimum value is 16', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 15,
                'minute' => 30,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('hour');
        });

        it('validates hour maximum value is 23', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 24,
                'minute' => 30,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('hour');
        });

        it('validates minute is required', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 20,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('minute');
        });

        it('validates minute must be integer', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 20,
                'minute' => 'invalid',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('minute');
        });

        it('validates minute maximum value is 59', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 20,
                'minute' => 60,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('minute');
        });

        it('accepts minute value of 0', function () {
            $response = putJson('/api/config/sync', [
                'hour' => 18,
                'minute' => 0,
            ]);

            $response->assertStatus(200)
                ->assertJsonPath('data.minute', 0);

            expect(DB::table('config')->whereKey('sync_time')->first()->value)
                ->toBe('18:0');
        });
    });

    describe('default', function () {
        it('returns default message title and days configuration', function () {
            $defaultMessage = Message::factory()->create([
                'title' => 'Default Welcome Message',
                'default' => true,
            ]);

            $response = getJson('/api/default-config');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [
                        'days' => 3,
                        'title' => 'Default Welcome Message',
                    ],
                ]);
        });

        it('returns integer value for days', function () {
            Message::factory()->create([
                'title' => 'Test Message',
                'default' => true,
            ]);

            $response = getJson('/api/default-config');

            $response->assertStatus(200);

            expect($response->json('data.days'))->toBeInt();
        });
    });
});
