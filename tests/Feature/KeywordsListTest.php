<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeywordsListTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_keywords_list_requires_authentication(): void
    {
        $response = $this->get('/keywords');
        $response->assertStatus(302);
    }

    public function test_keywords_list_loads_authenticated(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/keywords');
        $response->assertStatus(200);
    }
}