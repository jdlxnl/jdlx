<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use \App\Http\Resources\UserResource;

class UserCrudTest extends TestCase
{

    use RefreshDatabase;


    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var User
     */
    protected $model = User::class;

    /**
     * @var UserResource
     */
    protected $resource = UserResource::class;

    protected $path ="/api/user";

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Permission::create(['name'=>'*']);
        $this->user->givePermissionTo("*");
        Sanctum::actingAs($this->user);
    }

    public function testList(): void
    {
        $this->model::factory()->count(99)->create();
        $response = $this->json('GET', "{$this->path}");
        $response->assertStatus(200);
        $response->assertJsonCount(10, "data.items");
        $response->assertJsonPath('data.totalItems', 100);
        $response->assertJsonPath('data.pageIndex', 1);
        $response->assertJsonPath('data.currentItemCount', 10);
    }

    public function testListPage2(): void
    {
        $this->model::factory()->count(99)->create();

        $response = $this->json('GET', "{$this->path}?page=2");

        $response->assertStatus(200);
        $response->assertJsonCount(10, "data.items");
        $response->assertJsonPath('data.totalItems', 100);
        $response->assertJsonPath('data.pageIndex', 2);
        $response->assertJsonPath('data.currentItemCount', 10);
        $response->assertJsonPath('data.startIndex', 11);
    }

    public function testCreate(): void
    {
        $entity = $this->createData();
        $filters = $this->model::getWritableFields();
        $response = $this->json('POST', "{$this->path}", $this->toWriteArray($entity, $filters));
        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => $this->model::getReadableFields()]);
    }

    public function testRetrieve(): void
    {
        $toGet = $this->model::factory()->create();
        $expected = json_decode(json_encode(new $this->resource($toGet)), true);

        $response = $this->json('GET', "{$this->path}/{$toGet->id}");

        $response->assertStatus(200);
        $response->assertJson(['data' => $expected]);
    }

    public function testRetrieveNotFound(): void
    {
        $response = $this->json('GET', "{$this->path}/300");
        $response->assertNotFound();
    }

    public function testUpdate(): void
    {
        $toUpdate = $this->model::factory()->create();
        $entity = $this->createData();
        $filters = $this->model::getEditableFields();
        $fields = $this->toWriteArray($entity, $filters);
        $resource = $this->toResourceResponse($entity, $filters);

        $response = $this->json('PUT', "{$this->path}/{$toUpdate->id}", $fields);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => $this->model::getReadableFields()]);

        foreach ($resource as $key => $value) {
            $response->assertJsonFragment([$key => $value]);
        }
    }

    public function testUpdateNotFound(): void
    {
        $response = $this->json('PUT', "{$this->path}/300");
        $response->assertNotFound();
    }


    public function testDelete(): void
    {
        $toDelete = $this->model::factory()->create();

        $response = $this->json('DELETE', "{$this->path}/{$toDelete->id}");

        $response->assertStatus(200);
        $response->assertJson(['data' => [
            'deleted' => true,
            'id' => $toDelete->id,
        ]]);

        $this->assertDeleted($toDelete);
    }

    public function testDeleteNotFound(): void
    {
        $response = $this->json('DELETE', "{$this->path}/300");
        $response->assertNotFound();
    }

    protected function createData()
    {
        return $this->model::factory()->make();
    }

    protected function toWriteArray($entity, $filter = false)
    {
        $data = json_decode(json_encode($entity), true);
        if ($filter) {
            return array_intersect_key($data, array_fill_keys($filter, null));
        }
        return $data;
    }

    protected function toResourceResponse($entity, $filter = false)
    {
        $data = json_decode(json_encode(new $this->resource($entity)), true);

        if ($filter) {
            return array_intersect_key($data, array_fill_keys($filter, null));
        }
        return $data;
    }
}
