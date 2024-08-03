<?php

namespace Tests\Feature;

use App\Models\Declaration;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DeclarationControllerTest extends TestCase
{
    public function test_create_declaration() : void
    {
        $data = ['title' => 'Test Declaration'];

        $response = $this->postJson(route('declaration.create'), $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('declarations', $data);

        $declaration = Declaration::latest()->first();

        if (!$declaration) {
            return;
        }

        $redisKey = 'declaration:' . $declaration->id;

        if (!Cache::has($redisKey)) {
            $this->fail("Cache key {$redisKey} does not exist.");
        }

        $cachedDeclaration = json_decode(Cache::get($redisKey), true);

        $this->assertEquals(
            [
                'id' => $declaration->id,
                'title' => $declaration->title,
            ],
            [
                'id' => $cachedDeclaration['id'],
                'title' => $cachedDeclaration['title'],
            ]
        );
    }



    public function test_delete_declaration() : void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $declaration = Declaration::create(['title' => 'Test Declaration']);

        $this->assertDatabaseHas('declarations', ['id' => $declaration->id]);   

        Cache::put("declaration:" . $declaration->id, json_encode($declaration));

        $this->assertTrue(Cache::has("declaration:" . $declaration->id));

        $response = $this->deleteJson(route('declaration.delete', $declaration->id));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Declaration deleted successfully.']);

        $this->assertDatabaseMissing('declarations', ['id' => $declaration->id]);

        $this->assertFalse(Cache::has("declaration:" . $declaration->id));
    }


    public function test_update_declaration() : void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $declaration = Declaration::create(['title' => 'Old Title']);

        $this->assertDatabaseHas('declarations', ['id' => $declaration->id, 'title' => 'Old Title']);

        $updateData = ['title' => 'New Title', 'some_other_field' => 'New Value'];

        $response = $this->patchJson(route('declaration.update', $declaration->id), $updateData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Declaration updated successfully.']);

        $this->assertDatabaseHas('declarations', ['id' => $declaration->id, 'title' => 'New Title']);

        $this->assertDatabaseMissing('declarations', ['id' => $declaration->id, 'title' => 'Old Title']);

        $cachedDeclaration = Cache::get("declaration:" . $declaration->id);
        $this->assertNotNull($cachedDeclaration);
        $this->assertEquals('New Title', json_decode($cachedDeclaration)->title);
    }
}
