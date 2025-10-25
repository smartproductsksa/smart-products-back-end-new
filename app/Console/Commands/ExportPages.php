<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pages:export 
                            {--slug= : Export a specific page by slug}
                            {--all : Export all pages}
                            {--output= : Output directory (default: storage/exports/pages)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export pages to JSON files for migration between environments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $outputDir = $this->option('output') ?? storage_path('exports/pages');
        
        // Create output directory if it doesn't exist
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
            $this->info("Created output directory: {$outputDir}");
        }

        // Determine which pages to export
        if ($slug = $this->option('slug')) {
            $pages = Page::where('slug', $slug)->get();
            
            if ($pages->isEmpty()) {
                $this->error("Page with slug '{$slug}' not found.");
                return self::FAILURE;
            }
        } elseif ($this->option('all')) {
            $pages = Page::orderBy('order')->get();
        } else {
            $this->error('Please specify --slug=<slug> or --all');
            return self::FAILURE;
        }

        $exportedCount = 0;
        
        foreach ($pages as $page) {
            $filename = $this->exportPage($page, $outputDir);
            $this->info("✓ Exported: {$page->title} → {$filename}");
            $exportedCount++;
        }

        $this->newLine();
        $this->info("Successfully exported {$exportedCount} page(s) to: {$outputDir}");
        $this->newLine();
        $this->comment('To import these pages in another environment, run:');
        $this->comment('php artisan pages:import --file=<filename> or --directory=<directory>');
        
        return self::SUCCESS;
    }

    /**
     * Export a single page to JSON
     */
    protected function exportPage(Page $page, string $outputDir): string
    {
        $data = [
            'title' => $page->title,
            'slug' => $page->slug,
            'status' => $page->status,
            'order' => $page->order,
            'content' => $page->content,
            'created_at' => $page->created_at?->toISOString(),
            'updated_at' => $page->updated_at?->toISOString(),
            'exported_at' => now()->toISOString(),
            'export_version' => '1.0',
        ];

        $filename = $outputDir . '/' . $page->slug . '.json';
        
        File::put(
            $filename,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return basename($filename);
    }
}

