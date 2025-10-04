<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    Date::setTestNow(now());
});

test('user can login with valid credentials', function () {
    $password = 'password123';
    $user = User::factory()->create([
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'token',
            'expiresIn',
        ],
    ]);

    $this->assertNotNull($user->fresh()->last_login);
});

test('login fails with invalid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertUnauthorized();
    $response->assertJson([
        'success' => false,
        'message' => 'Unauthorized',
        'data' => null,
    ]);
});

test('can get authenticated user with valid token', function () {
    $user = User::factory()->create();

    $token = Auth::login($user);
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/me');

    $response->assertOk();
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'id',
            'email',
            'last_login',
            'created_at',
        ],
    ]);
});

test('unauthenticated user cannot access me endpoint', function () {
    $response = $this->getJson('/api/me');

    $response->assertUnauthorized();
});

test('can logout with valid token', function () {
    $user = User::factory()->create();
    $token = Auth::login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/logout');

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'Success logout',
    ]);
});

test('can refresh JWT token', function () {
    $user = User::factory()->create();
    $token = Auth::login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/refresh');

    $response->assertOk();
    $response->assertJsonStructure([
        'success',
        'data' => [
            'token',
            'expiresIn',
        ],
    ]);
});
