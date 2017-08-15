<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountTest extends TestCase
{
    public function testBasicTest()
    {
        $response = $this->get('/account');
        $response->assertRedirect('/login');
    }

    public function testLoggedIn()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user)->post('/account/update');
        $response->assertRedirect('/');
    }
}
