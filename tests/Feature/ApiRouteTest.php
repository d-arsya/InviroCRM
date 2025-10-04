<?php

test('returns JSON ["text" => "OK"] from /api/', function () {
    $response = $this->get('/api');

    $response->assertStatus(200);
    $response->assertJson(['text' => 'OK']);
});

test('redirects / to /docs', function () {
    $response = $this->get('/');
    $response->assertRedirect('/docs');
});
