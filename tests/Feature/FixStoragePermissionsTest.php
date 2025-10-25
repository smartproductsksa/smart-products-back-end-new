<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FixStoragePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_creates_missing_directories()
    {
        $directories = [
            'articles',
            'news',
            'pages/hero',
            'pages/gallery',
            'pages/sections',
            'article-attachments',
            'news-attachments',
        ];

        Artisan::call('storage:fix-permissions');

        foreach ($directories as $directory) {
            $this->assertTrue(Storage::disk('public')->exists($directory));
        }
    }

    /** @test */
    public function it_sets_files_to_public_visibility()
    {
        // Create a test file without public visibility
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('articles', 'public');

        // Initially set to private
        Storage::disk('public')->setVisibility($path, 'private');
        $this->assertEquals('private', Storage::disk('public')->getVisibility($path));

        // Run the fix command
        Artisan::call('storage:fix-permissions');

        // Should now be public
        $this->assertEquals('public', Storage::disk('public')->getVisibility($path));
    }

    /** @test */
    public function it_processes_multiple_files_in_directory()
    {
        $files = [
            UploadedFile::fake()->image('test1.jpg'),
            UploadedFile::fake()->image('test2.jpg'),
            UploadedFile::fake()->image('test3.jpg'),
        ];

        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store('articles', 'public');
            Storage::disk('public')->setVisibility(end($paths), 'private');
        }

        // All should be private initially
        foreach ($paths as $path) {
            $this->assertEquals('private', Storage::disk('public')->getVisibility($path));
        }

        // Run the fix command
        Artisan::call('storage:fix-permissions');

        // All should now be public
        foreach ($paths as $path) {
            $this->assertEquals('public', Storage::disk('public')->getVisibility($path));
        }
    }

    /** @test */
    public function it_processes_nested_directories()
    {
        $directories = ['pages/hero', 'pages/gallery', 'pages/sections'];
        
        foreach ($directories as $directory) {
            $file = UploadedFile::fake()->image('test.jpg');
            $path = $file->store($directory, 'public');
            Storage::disk('public')->setVisibility($path, 'private');
        }

        Artisan::call('storage:fix-permissions');

        // Check all files in nested directories are now public
        foreach ($directories as $directory) {
            $files = Storage::disk('public')->files($directory);
            foreach ($files as $file) {
                $this->assertEquals('public', Storage::disk('public')->getVisibility($file));
            }
        }
    }

    /** @test */
    public function it_returns_success_status()
    {
        $exitCode = Artisan::call('storage:fix-permissions');
        
        $this->assertEquals(0, $exitCode);
    }

    /** @test */
    public function it_handles_empty_directories_gracefully()
    {
        // Run on empty directories
        $exitCode = Artisan::call('storage:fix-permissions');
        
        $this->assertEquals(0, $exitCode);
    }

    /** @test */
    public function it_processes_different_image_formats()
    {
        $formats = ['jpg', 'png', 'webp', 'gif'];
        
        $paths = [];
        foreach ($formats as $format) {
            $file = UploadedFile::fake()->image("test.{$format}");
            $paths[] = $file->store('articles', 'public');
            Storage::disk('public')->setVisibility(end($paths), 'private');
        }

        Artisan::call('storage:fix-permissions');

        foreach ($paths as $path) {
            $this->assertEquals('public', Storage::disk('public')->getVisibility($path));
        }
    }
}
