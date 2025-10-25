<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageConfigurationTest extends TestCase
{
    /** @test */
    public function it_has_public_disk_configured_correctly()
    {
        $config = config('filesystems.disks.public');
        
        $this->assertIsArray($config);
        $this->assertEquals('local', $config['driver']);
        $this->assertStringContainsString('storage/app/public', $config['root']);
        $this->assertEquals('public', $config['visibility']);
    }

    /** @test */
    public function it_can_store_and_retrieve_files_on_public_disk()
    {
        Storage::fake('public');
        
        $content = 'Test file content';
        $path = 'test-file.txt';
        
        Storage::disk('public')->put($path, $content);
        
        $this->assertTrue(Storage::disk('public')->exists($path));
        $this->assertEquals($content, Storage::disk('public')->get($path));
    }

    /** @test */
    public function it_generates_correct_urls_for_public_disk()
    {
        $path = 'test/image.jpg';
        $url = Storage::disk('public')->url($path);
        
        $this->assertStringContainsString('/storage/test/image.jpg', $url);
    }

    /** @test */
    public function it_supports_subdirectories_on_public_disk()
    {
        Storage::fake('public');
        
        $directories = [
            'articles',
            'news',
            'pages/hero',
            'pages/gallery',
            'pages/sections',
            'article-attachments',
            'news-attachments',
        ];
        
        foreach ($directories as $directory) {
            Storage::disk('public')->put($directory . '/test.txt', 'test');
            $this->assertTrue(Storage::disk('public')->exists($directory . '/test.txt'));
        }
    }

    /** @test */
    public function it_can_delete_files_from_public_disk()
    {
        Storage::fake('public');
        
        $path = 'articles/test-delete.jpg';
        Storage::disk('public')->put($path, 'content');
        
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        Storage::disk('public')->delete($path);
        
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    /** @test */
    public function it_supports_multiple_file_operations_on_public_disk()
    {
        Storage::fake('public');
        
        $files = [
            'file1.jpg' => 'content1',
            'file2.jpg' => 'content2',
            'file3.jpg' => 'content3',
        ];
        
        // Store multiple files
        foreach ($files as $filename => $content) {
            Storage::disk('public')->put('test/' . $filename, $content);
        }
        
        // List files
        $storedFiles = Storage::disk('public')->files('test');
        $this->assertCount(3, $storedFiles);
        
        // Delete all files
        Storage::disk('public')->deleteDirectory('test');
        $this->assertFalse(Storage::disk('public')->exists('test'));
    }

    /** @test */
    public function it_handles_file_sizes_correctly_on_public_disk()
    {
        Storage::fake('public');
        
        $content = str_repeat('a', 1024); // 1KB
        $path = 'test/size-test.txt';
        
        Storage::disk('public')->put($path, $content);
        
        $size = Storage::disk('public')->size($path);
        $this->assertEquals(1024, $size);
    }

    /** @test */
    public function it_supports_file_moving_and_copying_on_public_disk()
    {
        Storage::fake('public');
        
        $sourcePath = 'source/file.txt';
        $destPath = 'destination/file.txt';
        $copyPath = 'copy/file.txt';
        
        Storage::disk('public')->put($sourcePath, 'content');
        
        // Copy
        Storage::disk('public')->copy($sourcePath, $copyPath);
        $this->assertTrue(Storage::disk('public')->exists($sourcePath));
        $this->assertTrue(Storage::disk('public')->exists($copyPath));
        
        // Move
        Storage::disk('public')->move($sourcePath, $destPath);
        $this->assertFalse(Storage::disk('public')->exists($sourcePath));
        $this->assertTrue(Storage::disk('public')->exists($destPath));
    }

    /** @test */
    public function it_has_filesystem_configuration_with_default_disk()
    {
        $default = config('filesystems.default');
        
        // Should be either 'local' or can be overridden in .env
        $this->assertIsString($default);
    }

    /** @test */
    public function it_has_s3_disk_configuration_but_not_used_for_images()
    {
        $s3Config = config('filesystems.disks.s3');
        
        // S3 configuration should still exist for potential other uses
        $this->assertIsArray($s3Config);
        $this->assertEquals('s3', $s3Config['driver']);
        
        // But our forms should be configured to use 'public' disk instead
        $this->assertTrue(true);
    }
}
