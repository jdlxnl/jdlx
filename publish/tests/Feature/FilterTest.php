<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Authenticatable
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['name' => "___"]);
        Permission::create(['name'=>'*']);
        $this->user->givePermissionTo("*");

        Sanctum::actingAs($this->user);
    }

    public function testEqualsFilter(): void
    {
        User::factory()->count(2)->create(['name' => "i_like_a_good_search_result"]);
        User::factory()->count(100)->create();

        $response = $this->json('GET', '/api/user?filter[name]=i_like_a_good_search_result');

        $response->assertStatus(200);
        $response->assertJsonCount(2, "data.items");
    }

    public function testLikeFilter(): void
    {
        User::factory()->count(2)->create(['name' => "i_like_a_good_search_result"]);
        User::factory()->count(100)->create();

        $response = $this->json('GET', '/api/user?filter[name]=like:i_like_a_good_%');
        $response->assertStatus(200);
        $response->assertJsonCount(2, "data.items");
    }

    public function testGtFilter(): void
    {
        User::factory()->count(2)->create(['name' => "aaa"]);
        User::factory()->count(2)->create(['name' => "bbb"]);
        User::factory()->count(2)->create(['name' => "ccc"]);

        $response = $this->json('GET', '/api/user?filter[name]=gt:aab');

        $response->assertStatus(200);
        $response->assertJsonCount(4, "data.items");
    }
}
