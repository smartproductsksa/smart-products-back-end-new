<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixStoragePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix file permissions for publicly uploaded images';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Fixing file permissions for public storage...');
        
        $directories = [
            'articles',
            'news',
            'pages/hero',
            'pages/gallery',
            'pages/detailed-gallery',
            'pages/sections',
            'article-attachments',
            'news-attachments',
        ];
        
        $disk = Storage::disk('public');
        $fixedCount = 0;
        
        foreach ($directories as $directory) {
            if (!$disk->exists($directory)) {
                $this->line("Creating directory: {$directory}");
                $disk->makeDirectory($directory);
                continue;
            }
            
            $files = $disk->allFiles($directory);
            
            foreach ($files as $file) {
                try {
                    // Set visibility to public
                    $disk->setVisibility($file, 'public');
                    $fixedCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to fix: {$file} - " . $e->getMessage());
                }
            }
            
            $this->line("Processed {$directory}: " . count($files) . " files");
        }
        
        $this->info("Fixed {$fixedCount} files successfully!");
        
        return Command::SUCCESS;
    }
}
