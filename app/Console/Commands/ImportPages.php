<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ImportPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pages:import 
                            {--file= : Import a specific JSON file}
                            {--directory= : Import all JSON files from a directory}
                            {--update : Update existing pages instead of skipping them}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import pages from JSON files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $files = $this->getFilesToImport();

        if (empty($files)) {
            $this->error('No files to import. Please specify --file=<path> or --directory=<path>');
            return self::FAILURE;
        }

        $this->info("Found " . count($files) . " file(s) to import.");
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with the import?', true)) {
                $this->info('Import cancelled.');
                return self::SUCCESS;
            }
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($files as $file) {
            $result = $this->importPage($file);
            
            match ($result) {
                'imported' => $imported++,
                'updated' => $updated++,
                'skipped' => $skipped++,
                'error' => $errors++,
            };
        }

        $this->newLine();
        $this->info("Import completed:");
        $this->info("  ✓ Imported: {$imported}");
        
        if ($updated > 0) {
            $this->info("  ✓ Updated: {$updated}");
        }
        
        if ($skipped > 0) {
            $this->comment("  ⊗ Skipped: {$skipped}");
        }
        
        if ($errors > 0) {
            $this->error("  ✗ Errors: {$errors}");
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Get the list of files to import
     */
    protected function getFilesToImport(): array
    {
        if ($file = $this->option('file')) {
            if (!File::exists($file)) {
                $this->error("File not found: {$file}");
                return [];
            }
            return [$file];
        }

        if ($directory = $this->option('directory')) {
            if (!File::isDirectory($directory)) {
                $this->error("Directory not found: {$directory}");
                return [];
            }
            return File::glob($directory . '/*.json');
        }

        return [];
    }

    /**
     * Import a single page from JSON file
     */
    protected function importPage(string $file): string
    {
        try {
            $json = File::get($file);
            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("✗ Invalid JSON in file: " . basename($file));
                return 'error';
            }

            // Validate data
            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255',
                'status' => 'required|in:draft,published',
                'order' => 'required|integer',
                'content' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                $this->error("✗ Validation failed for: " . basename($file));
                foreach ($validator->errors()->all() as $error) {
                    $this->error("  - {$error}");
                }
                return 'error';
            }

            // Check if page already exists
            $existingPage = Page::where('slug', $data['slug'])->first();

            if ($existingPage) {
                if ($this->option('update')) {
                    return $this->updatePage($existingPage, $data, $file);
                } else {
                    $this->comment("⊗ Skipped (already exists): {$data['title']} ({$data['slug']})");
                    return 'skipped';
                }
            }

            // Create new page
            DB::beginTransaction();
            
            try {
                Page::create([
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'status' => $data['status'],
                    'order' => $data['order'],
                    'content' => $data['content'],
                ]);

                DB::commit();
                $this->info("✓ Imported: {$data['title']} ({$data['slug']})");
                return 'imported';
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✗ Failed to import: {$data['title']} - {$e->getMessage()}");
                return 'error';
            }

        } catch (\Exception $e) {
            $this->error("✗ Error processing file: " . basename($file) . " - {$e->getMessage()}");
            return 'error';
        }
    }

    /**
     * Update an existing page
     */
    protected function updatePage(Page $page, array $data, string $file): string
    {
        DB::beginTransaction();
        
        try {
            $page->update([
                'title' => $data['title'],
                'status' => $data['status'],
                'order' => $data['order'],
                'content' => $data['content'],
            ]);

            DB::commit();
            $this->info("✓ Updated: {$data['title']} ({$data['slug']})");
            return 'updated';
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("✗ Failed to update: {$data['title']} - {$e->getMessage()}");
            return 'error';
        }
    }
}

