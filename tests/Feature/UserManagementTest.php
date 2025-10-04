<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('returns all users', function () {
    User::factory()->count(3)->create();

    $response = getJson('/api/users');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'email', 'last_login', 'created_at'], // adjust fields as per UserResource
            ],
        ]);
});

it('stores a new user', function () {
    $data = [
        'email' => 'test@example.com',
        'password' => 'passworA123',
    ];

    $response = postJson('/api/users', $data);
    expect($response['data'])->toHaveKey('email');
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
});

it('fails to create user with invalid data', function () {
    $response = postJson('/api/users', [
        'email' => 'invalid-email',
        'password' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('fails to update user with invalid data', function () {
    $user = User::factory()->create();

    $response = putJson("/api/users/{$user->id}", [
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
it('fails to update user with duplicate data', function () {
    $user = User::factory()->create();
    $dup = User::factory()->create();

    $response = putJson("/api/users/{$user->id}", [
        'email' => $dup->email,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('shows a single user', function () {
    $user = User::factory()->create();

    $response = getJson("/api/users/{$user->id}");

    $response->assertOk();
    expect($response['data'])->toHaveKey('email');
    expect($response['data']['email'])->toBe($user->email);
});

it('updates a user', function () {
    $user = User::factory()->create();

    $updateData = ['email' => 'newemail@mail.com', 'password' => 'passWord123'];

    $response = putJson("/api/users/{$user->id}", $updateData);

    $response->assertOk();
    expect($response['data'])->toHaveKey('email');
    expect($response['data']['email'])->toBe($updateData['email']);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $updateData['email']]);
});

it('deletes a user if more than one exists', function () {
    $users = User::factory()->count(2)->create();

    $response = deleteJson("/api/users/{$users[0]->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Success delete item',
        ]);

    $this->assertDatabaseMissing('users', ['id' => $users[0]->id]);
});

it('does not delete the only user in the system', function () {
    $user = Auth::getUser();
    $response = deleteJson("/api/users/{$user->id}");

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'At least system have one account',
        ]);

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});
