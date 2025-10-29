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
    public function admin_can_create_an_article_via_factory()
    {
        // Test that articles can be created (tests the model and factory)
        $article = Article::factory()->create([
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => 'This is a test article content.',
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
        ]);
        
        $this->assertNotNull($article->category_id);
        $this->assertEquals('test-article', $article->slug);
    }

    /** @test */
    public function article_has_relationship_with_category()
    {
        $article = Article::factory()->create();
        
        $this->assertInstanceOf(\App\Models\Category::class, $article->category);
        $this->assertNotNull($article->category->name);
    }

    /** @test */
    public function article_can_be_updated()
    {
        $article = Article::factory()->create();
        
        $article->update([
            'title' => 'Updated Article Title',
            'slug' => 'updated-article',
            'content' => 'Updated content.',
        ]);
        
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Article Title',
            'slug' => 'updated-article',
        ]);
    }

    /** @test */
    public function article_can_be_soft_deleted()
    {
        $article = Article::factory()->create();
        
        $article->delete();
        
        $this->assertSoftDeleted($article);
        
        // Verify it still exists in database but is soft deleted
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
        ]);
        
        $this->assertNotNull($article->fresh()->deleted_at);
    }
    
    /** @test */
    public function article_uses_slug_for_route_binding()
    {
        $article = Article::factory()->create(['slug' => 'test-slug']);
        
        $this->assertEquals('slug', $article->getRouteKeyName());
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
