<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\News;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ImageStorageIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Fake the public storage
        Storage::fake('public');
        
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
    public function it_stores_article_images_in_public_disk()
    {
        $image = UploadedFile::fake()->image('article-test.jpg', 800, 600);
        
        // Simulate storing the image as Filament would do
        $path = $image->store('articles', 'public');
        
        // Assert file was stored in public disk
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Assert the path contains the articles directory
        $this->assertStringContainsString('articles/', $path);
    }

    /** @test */
    public function it_stores_news_images_in_public_disk()
    {
        $image = UploadedFile::fake()->image('news-test.jpg', 800, 600);
        
        // Simulate storing the image as Filament would do
        $path = $image->store('news', 'public');
        
        // Assert file was stored in public disk
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Assert the path contains the news directory
        $this->assertStringContainsString('news/', $path);
    }

    /** @test */
    public function it_stores_page_hero_images_in_public_disk()
    {
        $image = UploadedFile::fake()->image('hero-test.jpg', 1920, 1080);
        
        // Simulate storing the image as Filament would do
        $path = $image->store('pages/hero', 'public');
        
        // Assert file was stored in public disk
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Assert the path contains the correct directory
        $this->assertStringContainsString('pages/hero/', $path);
    }

    /** @test */
    public function it_stores_page_gallery_images_in_public_disk()
    {
        $images = [
            UploadedFile::fake()->image('gallery1.jpg', 800, 600),
            UploadedFile::fake()->image('gallery2.jpg', 800, 600),
        ];
        
        $paths = [];
        foreach ($images as $image) {
            $paths[] = $image->store('pages/gallery', 'public');
        }
        
        // Assert all files were stored in public disk
        foreach ($paths as $path) {
            $this->assertTrue(Storage::disk('public')->exists($path));
            $this->assertStringContainsString('pages/gallery/', $path);
        }
    }

    /** @test */
    public function it_stores_page_section_images_in_public_disk()
    {
        $image = UploadedFile::fake()->image('section-test.jpg', 800, 600);
        
        // Simulate storing the image as Filament would do
        $path = $image->store('pages/sections', 'public');
        
        // Assert file was stored in public disk
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Assert the path contains the correct directory
        $this->assertStringContainsString('pages/sections/', $path);
    }

    /** @test */
    public function it_stores_article_attachments_in_public_disk()
    {
        $attachment = UploadedFile::fake()->image('attachment.jpg', 400, 400);
        
        // Simulate storing the attachment as RichEditor would do
        $path = $attachment->store('article-attachments', 'public');
        
        // Assert file was stored in public disk
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Assert the path contains the correct directory
        $this->assertStringContainsString('article-attachments/', $path);
    }

    /** @test */
    public function it_stores_news_attachments_in_public_disk()
    {
        $attachment = UploadedFile::fake()->image('news-attachment.jpg', 400, 400);
        
        // Simulate storing the attachment as RichEditor would do
        $path = $attachment->store('news-attachments', 'public');
        
        // Assert file was stored in public disk
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Assert the path contains the correct directory
        $this->assertStringContainsString('news-attachments/', $path);
    }

    /** @test */
    public function it_generates_accessible_urls_for_stored_images()
    {
        $image = UploadedFile::fake()->image('public-url-test.jpg', 800, 600);
        $path = $image->store('articles', 'public');
        
        // Get the URL
        $url = Storage::disk('public')->url($path);
        
        // Assert URL contains /storage/ which is the public symlink
        $this->assertStringContainsString('/storage/', $url);
    }

    /** @test */
    public function it_can_delete_stored_images()
    {
        $image = UploadedFile::fake()->image('delete-test.jpg', 800, 600);
        $path = $image->store('articles', 'public');
        
        // Verify file exists
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Delete the file
        Storage::disk('public')->delete($path);
        
        // Verify file no longer exists
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    /** @test */
    public function it_handles_multiple_image_uploads()
    {
        $images = [
            UploadedFile::fake()->image('multi1.jpg', 800, 600),
            UploadedFile::fake()->image('multi2.jpg', 800, 600),
            UploadedFile::fake()->image('multi3.jpg', 800, 600),
        ];
        
        $paths = [];
        foreach ($images as $image) {
            $paths[] = $image->store('articles', 'public');
        }
        
        // Assert all files were stored
        foreach ($paths as $path) {
            $this->assertTrue(Storage::disk('public')->exists($path));
        }
        
        // Assert we have 3 files in the articles directory
        $files = Storage::disk('public')->files('articles');
        $this->assertCount(3, $files);
    }
}
