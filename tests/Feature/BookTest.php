<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $book = factory(App\Book::class)->create();
        $user = App\User::find($book->user_id);

        $response = $this->actingAs($user)
            ->get('/list', [ 'X-Requested-With' => 'XMLHttpRequest' ]);
        $response->assertJsonStructure([ 'data' => [] ]);
        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $headers = [ 'X-Requested-With' => 'XMLHttpRequest' ];
        $codes = [ '9784873115382', '4000801139', '4873115388', '0000000000000' ];
        $user = factory(App\User::class)->create();

        // success
        $this->actingAs($user)
            ->post('/create', [ 'code' => $codes[0] ], $headers)
            ->assertStatus(200);
        $this->assertDatabaseHas('books', [ 'isbn' => $codes[0] ]);

        // success (isbn10)
        $this->actingAs($user)
            ->post('/create', [ 'code' => $codes[1] ], $headers)
            ->assertStatus(200);
        $this->assertDatabaseHas('books', [ 'isbn' => '978'.substr($codes[1], 0, -1).'3' ]);

        // dups
        $this->actingAs($user)
            ->post('/create', [ 'code' => $codes[2] ], $headers)
            ->assertStatus(409);

        // not found
        $this->actingAs($user)
            ->post('/create', [ 'code' => $codes[3] ], $headers)
            ->assertStatus(404);

        // invalid
        $this->actingAs($user)
            ->post('/create', [ 'code' => '' ], $headers)
            ->assertStatus(422);
    }

    public function testDelete()
    {
        $headers = [ 'X-Requested-With' => 'XMLHttpRequest' ];
        $book = factory(App\Book::class)->create();
        $user = App\User::find($book->user_id);

        // success
        $this->actingAs($user)
            ->post('/delete', [ 'id' => '1' ], $headers)
            ->assertStatus(200);
        $this->assertDatabaseMissing('books', [ 'id' => '1' ]);

        // not found
        $this->actingAs($user)
            ->post('/delete', [ 'id' => '0' ], $headers)
            ->assertStatus(404);

        // invalid
        $this->actingAs($user)
            ->post('/delete', [ 'id' => '' ], $headers)
            ->assertStatus(422);
    }
}
