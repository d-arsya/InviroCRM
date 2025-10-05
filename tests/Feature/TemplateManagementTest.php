<?php

use App\Models\Message;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user, 'api');
});

describe('MessageController', function () {
    describe('index', function () {
        it('returns all message templates', function () {
            $templates = Message::factory()->count(3)->create();

            $response = getJson('/api/templates');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonCount(3, 'data');
        });

        it('returns empty array when no templates exist', function () {
            $response = getJson('/api/templates');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                    'data' => [],
                ]);
        });
    });

    describe('store', function () {
        it('creates a new message template', function () {
            $data = [
                'title' => 'Welcome Message',
                'text' => 'Hello, welcome to our service!',
                'default' => false,
            ];

            $response = postJson('/api/templates', $data);

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonPath('data.title', 'Welcome Message')
                ->assertJsonPath('data.text', 'Hello, welcome to our service!');

            $this->assertDatabaseHas('messages', [
                'title' => 'Welcome Message',
                'text' => 'Hello, welcome to our service!',
            ]);
        });

        it('validates required fields', function () {
            $response = postJson('/api/templates', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'text']);
        });

        it('validates title field', function () {
            $response = postJson('/api/templates', [
                'title' => '',
                'text' => 'Some text',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('title');
        });

        it('validates text field', function () {
            $response = postJson('/api/templates', [
                'title' => 'Test Template',
                'text' => '',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('text');
        });
    });

    describe('show', function () {
        it('returns a specific message template', function () {
            $template = Message::factory()->create([
                'title' => 'Test Template',
                'text' => 'Test text',
            ]);

            $response = getJson("/api/templates/{$template->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonPath('data.id', $template->id)
                ->assertJsonPath('data.title', 'Test Template');
        });

        it('returns 404 when template does not exist', function () {
            $response = getJson('/api/templates/999');

            $response->assertStatus(404);
        });
    });

    describe('update', function () {
        it('updates a message template', function () {
            $template = Message::factory()->create([
                'title' => 'Old title',
                'text' => 'Old text',
            ]);

            $response = putJson("/api/templates/{$template->id}", [
                'title' => 'Updated title',
                'text' => 'Updated text',
                'default' => false,
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success',
                ])
                ->assertJsonPath('data.title', 'Updated title')
                ->assertJsonPath('data.text', 'Updated text');

            expect($template->fresh())
                ->title->toBe('Updated title')
                ->text->toBe('Updated text');
        });

        it('validates required fields on update', function () {
            $template = Message::factory()->create();

            $response = putJson("/api/templates/{$template->id}", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'text']);
        });

        it('returns 404 when updating non-existent template', function () {
            $response = putJson('/api/templates/999', [
                'title' => 'Test',
                'text' => 'Test text',
            ]);

            $response->assertStatus(404);
        });
    });

    describe('destroy', function () {
        it('deletes a non-default message template', function () {
            $template = Message::factory()->create([
                'default' => false,
            ]);

            $response = deleteJson("/api/templates/{$template->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Success delete item',
                ]);

            $this->assertDatabaseMissing('messages', [
                'id' => $template->id,
            ]);
        });

        it('cannot delete a default message template', function () {
            $template = Message::factory()->create([
                'default' => true,
            ]);

            $response = deleteJson("/api/templates/{$template->id}");

            $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Cannot delete a default message.',
                ]);

            $this->assertDatabaseHas('messages', [
                'id' => $template->id,
            ]);
        });

        it('returns 404 when deleting non-existent template', function () {
            $response = deleteJson('/api/templates/999');

            $response->assertStatus(404);
        });

        it('handles deletion errors gracefully', function () {
            $template = Message::factory()->create();

            // Mock the delete method to throw an exception
            Message::deleting(function () {
                throw new \Exception('Database error');
            });

            $response = deleteJson("/api/templates/{$template->id}");

            $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                ]);
        });
    });
});
