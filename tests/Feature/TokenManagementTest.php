<?php

use App\Models\User;
use App\Models\WhatsappToken;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('WhatsappTokenController', function () {
    describe('index', function () {
        it('returns all whatsapp tokens', function () {
            actingAs($this->user, 'api');

            $tokens = WhatsappToken::factory()->count(3)->create();

            $response = getJson('/api/tokens');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonCount(3, 'data');
        });

        it('requires authentication', function () {
            $response = getJson('/api/tokens');

            $response->assertStatus(401);
        });

        it('returns empty array when no tokens exist', function () {
            actingAs($this->user, 'api');

            $response = getJson('/api/tokens');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [],
                ]);
        });
    });

    describe('show', function () {
        it('returns a specific whatsapp token', function () {
            actingAs($this->user, 'api');

            $token = WhatsappToken::factory()->create();

            $response = getJson("/api/tokens/{$token->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonPath('data.token', $token->token);
        });

        it('returns 404 when token does not exist', function () {
            actingAs($this->user, 'api');

            $response = getJson('/api/tokens/999');

            $response->assertStatus(404);
        });
    });

    describe('update', function () {
        it('updates a whatsapp token', function () {
            actingAs($this->user, 'api');

            $token = WhatsappToken::factory()->create([
                'token' => 'old-token-value',
            ]);

            $response = putJson("/api/tokens/{$token->id}", [
                'token' => 'new-token-value',
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonPath('data.token', 'new-token-value');

            expect($token->fresh()->token)->toBe('new-token-value');
        });

        it('validates token is required', function () {
            actingAs($this->user, 'api');

            $token = WhatsappToken::factory()->create();

            $response = putJson("/api/tokens/{$token->id}", [
                'token' => '',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('token');
        });

        it('validates token must be a string', function () {
            actingAs($this->user, 'api');

            $token = WhatsappToken::factory()->create();

            $response = putJson("/api/tokens/{$token->id}", [
                'token' => 12345,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('token');
        });

        it('returns 404 when token does not exist', function () {
            actingAs($this->user, 'api');

            $response = putJson('/api/tokens/999', [
                'token' => 'new-token-value',
            ]);

            $response->assertStatus(404);
        });
    });
});
