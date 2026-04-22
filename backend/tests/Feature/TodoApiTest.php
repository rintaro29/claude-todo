<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_todos(): void
    {
        Todo::factory()->count(3)->create();

        $response = $this->getJson('/api/todos');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function test_store_creates_todo(): void
    {
        $response = $this->postJson('/api/todos', ['title' => 'Buy milk']);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'Buy milk', 'is_completed' => false]);

        $this->assertDatabaseHas('todos', ['title' => 'Buy milk']);
    }

    public function test_store_requires_title(): void
    {
        $response = $this->postJson('/api/todos', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_update_toggles_is_completed(): void
    {
        $todo = Todo::factory()->create(['is_completed' => false]);

        $response = $this->patchJson("/api/todos/{$todo->id}");

        $response->assertOk()
            ->assertJsonFragment(['is_completed' => true]);

        $this->patchJson("/api/todos/{$todo->id}")
            ->assertJsonFragment(['is_completed' => false]);
    }

    public function test_destroy_deletes_todo(): void
    {
        $todo = Todo::factory()->create();

        $this->deleteJson("/api/todos/{$todo->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_returns_404_for_unknown_id(): void
    {
        $this->patchJson('/api/todos/999')->assertNotFound();
        $this->deleteJson('/api/todos/999')->assertNotFound();
    }
}
