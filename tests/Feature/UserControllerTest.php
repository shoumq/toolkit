<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_delete_client() : void
    {
        $admin = User::factory()->create(['is_admin' => true]); 
        $this->actingAs($admin);

        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        $response = $this->deleteJson(route('user.delete', $user->id));
        $response->assertStatus(200)
            ->assertJson(['message' => 'Client deleted successfully.']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        $this->assertNull(Cache::get("user:{$user->id}"));
    }

    public function test_update_client() : void
    {
        $admin = User::factory()->create(['is_admin' => true]); 
        $this->actingAs($admin);
        
        $user = User::factory()->create();

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated14@example.com',
        ];

        $response = $this->patchJson(route('user.update', $user->id), $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Client updated successfully.']);

        $this->assertDatabaseHas('users', array_merge(['id' => $user->id], $updatedData));

        $this->assertTrue(Cache::has("user:{$user->id}"));
    }
}
