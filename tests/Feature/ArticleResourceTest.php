<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create admin role if it doesn't exist
    $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
    
    // Create admin user
    $this->admin = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $this->admin->assignRole($adminRole);
    
    // Log in the admin user
    $this->actingAs($this->admin);
});

test('admin can view articles index page', function () {
    $response = $this->get('/admin/articles');
    $response->assertStatus(200);
});

test('admin can create an article', function () {
    $articleData = [
        'title' => 'Test Article',
        'slug' => 'test-article',
        'category' => 'technology',
        'tags' => ['laravel', 'filament'],
        'content' => 'This is a test article content.',
    ];

    $response = $this->post('/admin/articles', $articleData);
    $response->assertRedirect('/admin/articles');
    
    $this->assertDatabaseHas('articles', [
        'title' => 'Test Article',
        'slug' => 'test-article',
        'category' => 'technology',
    ]);
});

test('admin can view an article', function () {
    $article = Article::factory()->create();
    
    $response = $this->get("/admin/articles/{$article->id}");
    $response->assertStatus(200);
    $response->assertSee($article->title);
});

test('admin can update an article', function () {
    $article = Article::factory()->create();
    
    $updateData = [
        'title' => 'Updated Article Title',
        'slug' => 'updated-article',
        'category' => 'design',
        'content' => 'Updated content.',
    ];
    
    $response = $this->put("/admin/articles/{$article->id}", $updateData);
    $response->assertRedirect('/admin/articles');
    
    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'title' => 'Updated Article Title',
        'slug' => 'updated-article',
        'category' => 'design',
    ]);
});

test('admin can delete an article', function () {
    $article = Article::factory()->create();
    
    $response = $this->delete("/admin/articles/{$article->id}");
    $response->assertRedirect('/admin/articles');
    
    $this->assertSoftDeleted($article);
});

test('non-admin cannot access article management', function () {
    // Create a regular user without admin privileges
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $this->actingAs($user);
    
    $response = $this->get('/admin/articles');
    $response->assertStatus(403); // Forbidden
});
