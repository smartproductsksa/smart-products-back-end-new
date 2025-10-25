<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArticleResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
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
    }

    /** @test */
    public function admin_can_view_articles_index_page()
    {
        $response = $this->get('/admin/articles');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_an_article()
    {
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
    }

    /** @test */
    public function admin_can_view_an_article()
    {
        $article = Article::factory()->create();
        
        $response = $this->get("/admin/articles/{$article->id}");
        $response->assertStatus(200);
        $response->assertSee($article->title);
    }

    /** @test */
    public function admin_can_update_an_article()
    {
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
    }

    /** @test */
    public function admin_can_delete_an_article()
    {
        $article = Article::factory()->create();
        
        $response = $this->delete("/admin/articles/{$article->id}");
        $response->assertRedirect('/admin/articles');
        
        $this->assertSoftDeleted($article);
    }

    /** @test */
    public function non_admin_cannot_access_article_management()
    {
        // Create a regular user without admin privileges
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get('/admin/articles');
        $response->assertStatus(403); // Forbidden
    }
}
