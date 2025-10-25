<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

class ListPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pages:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all pages with their details';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pages = Page::orderBy('order')->get();

        if ($pages->isEmpty()) {
            $this->info('No pages found.');
            return self::SUCCESS;
        }

        $this->info("Found {$pages->count()} page(s):");
        $this->newLine();

        $tableData = [];
        
        foreach ($pages as $page) {
            $contentBlocks = $page->content ? count($page->content) : 0;
            $contentTypes = $page->content 
                ? collect($page->content)->pluck('type')->unique()->implode(', ')
                : 'none';

            $tableData[] = [
                $page->id,
                $page->title,
                $page->slug,
                $page->status,
                $page->order,
                $contentBlocks,
                $contentTypes,
                $page->updated_at->format('Y-m-d H:i'),
            ];
        }

        $this->table(
            ['ID', 'Title', 'Slug', 'Status', 'Order', 'Blocks', 'Block Types', 'Updated'],
            $tableData
        );

        $this->newLine();
        $this->comment('To export pages, run: php artisan pages:export --all');

        return self::SUCCESS;
    }
}

