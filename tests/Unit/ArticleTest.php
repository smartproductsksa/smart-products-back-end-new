<?php

namespace Tests\Unit;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'title',
            'slug',
            'category_id',
            'tags',
            'content',
            'image',
        ];

        $article = new Article();
        $this->assertEquals($fillable, $article->getFillable());
    }

    /** @test */
    public function it_has_casts_attributes()
    {
        $article = new Article();
        $this->assertEquals([
            'tags' => 'array',
            'id' => 'int',
            'deleted_at' => 'datetime',
        ], $article->getCasts());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $article = new Article();
        $this->assertContains('Illuminate\Database\Eloquent\SoftDeletes', class_uses($article));
    }

    /** @test */
    public function it_has_route_key_name_set_to_slug()
    {
        $article = new Article();
        $this->assertEquals('slug', $article->getRouteKeyName());
    }

    /** @test */
    public function it_can_be_created_with_factory()
    {
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
    }
}
