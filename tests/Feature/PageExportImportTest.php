<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PageExportImportTest extends TestCase
{
    use RefreshDatabase;

    protected string $exportDir;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->exportDir = storage_path('exports/pages-test');
        
        // Clean up test directory
        if (File::exists($this->exportDir)) {
            File::deleteDirectory($this->exportDir);
        }
    }

    protected function tearDown(): void
    {
        // Clean up after tests
        if (File::exists($this->exportDir)) {
            File::deleteDirectory($this->exportDir);
        }
        
        parent::tearDown();
    }

    /** @test */
    public function it_can_export_a_single_page()
    {
        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'hero',
                    'data' => ['title' => 'Welcome'],
                ],
            ],
        ]);

        $this->artisan('pages:export', [
            '--slug' => 'test-page',
            '--output' => $this->exportDir,
        ])->assertSuccessful();

        $exportFile = $this->exportDir . '/test-page.json';
        
        $this->assertFileExists($exportFile);
        
        $data = json_decode(File::get($exportFile), true);
        
        $this->assertEquals('Test Page', $data['title']);
        $this->assertEquals('test-page', $data['slug']);
        $this->assertEquals('published', $data['status']);
        $this->assertEquals(1, $data['order']);
        $this->assertIsArray($data['content']);
        $this->assertArrayHasKey('exported_at', $data);
        $this->assertArrayHasKey('export_version', $data);
    }

    /** @test */
    public function it_can_export_all_pages()
    {
        Page::create(['title' => 'Page 1', 'slug' => 'page-1', 'status' => 'published', 'order' => 1]);
        Page::create(['title' => 'Page 2', 'slug' => 'page-2', 'status' => 'draft', 'order' => 2]);
        Page::create(['title' => 'Page 3', 'slug' => 'page-3', 'status' => 'published', 'order' => 3]);

        $this->artisan('pages:export', [
            '--all' => true,
            '--output' => $this->exportDir,
        ])->assertSuccessful();

        $this->assertFileExists($this->exportDir . '/page-1.json');
        $this->assertFileExists($this->exportDir . '/page-2.json');
        $this->assertFileExists($this->exportDir . '/page-3.json');
    }

    /** @test */
    public function it_can_import_a_page()
    {
        $exportFile = $this->exportDir . '/import-test.json';
        
        File::makeDirectory($this->exportDir, 0755, true);
        
        $pageData = [
            'title' => 'Imported Page',
            'slug' => 'imported-page',
            'status' => 'published',
            'order' => 5,
            'content' => [
                [
                    'type' => 'text_section',
                    'data' => ['title' => 'Section 1', 'text' => '<p>Content</p>'],
                ],
            ],
            'exported_at' => now()->toISOString(),
            'export_version' => '1.0',
        ];
        
        File::put($exportFile, json_encode($pageData, JSON_PRETTY_PRINT));

        $this->artisan('pages:import', [
            '--file' => $exportFile,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('pages', [
            'title' => 'Imported Page',
            'slug' => 'imported-page',
            'status' => 'published',
            'order' => 5,
        ]);

        $page = Page::where('slug', 'imported-page')->first();
        $this->assertIsArray($page->content);
        $this->assertEquals('text_section', $page->content[0]['type']);
    }

    /** @test */
    public function it_skips_existing_pages_by_default()
    {
        Page::create([
            'title' => 'Existing Page',
            'slug' => 'existing',
            'status' => 'published',
            'order' => 1,
        ]);

        $exportFile = $this->exportDir . '/existing.json';
        File::makeDirectory($this->exportDir, 0755, true);
        
        $pageData = [
            'title' => 'Updated Title',
            'slug' => 'existing',
            'status' => 'draft',
            'order' => 10,
            'content' => [],
            'exported_at' => now()->toISOString(),
            'export_version' => '1.0',
        ];
        
        File::put($exportFile, json_encode($pageData));

        $this->artisan('pages:import', [
            '--file' => $exportFile,
            '--force' => true,
        ])->assertSuccessful();

        // Title should not change (page was skipped)
        $this->assertDatabaseHas('pages', [
            'title' => 'Existing Page',
            'slug' => 'existing',
        ]);
    }

    /** @test */
    public function it_updates_existing_pages_when_update_flag_is_used()
    {
        Page::create([
            'title' => 'Old Title',
            'slug' => 'update-test',
            'status' => 'published',
            'order' => 1,
            'content' => [],
        ]);

        $exportFile = $this->exportDir . '/update-test.json';
        File::makeDirectory($this->exportDir, 0755, true);
        
        $pageData = [
            'title' => 'New Title',
            'slug' => 'update-test',
            'status' => 'draft',
            'order' => 5,
            'content' => [
                ['type' => 'hero', 'data' => ['title' => 'Updated']],
            ],
            'exported_at' => now()->toISOString(),
            'export_version' => '1.0',
        ];
        
        File::put($exportFile, json_encode($pageData));

        $this->artisan('pages:import', [
            '--file' => $exportFile,
            '--update' => true,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('pages', [
            'title' => 'New Title',
            'slug' => 'update-test',
            'status' => 'draft',
            'order' => 5,
        ]);

        $page = Page::where('slug', 'update-test')->first();
        $this->assertCount(1, $page->content);
    }

    /** @test */
    public function it_can_import_multiple_pages_from_directory()
    {
        File::makeDirectory($this->exportDir, 0755, true);

        $pages = [
            ['title' => 'Page A', 'slug' => 'page-a', 'status' => 'published', 'order' => 1, 'content' => []],
            ['title' => 'Page B', 'slug' => 'page-b', 'status' => 'published', 'order' => 2, 'content' => []],
            ['title' => 'Page C', 'slug' => 'page-c', 'status' => 'draft', 'order' => 3, 'content' => []],
        ];

        foreach ($pages as $pageData) {
            $pageData['exported_at'] = now()->toISOString();
            $pageData['export_version'] = '1.0';
            
            File::put(
                $this->exportDir . '/' . $pageData['slug'] . '.json',
                json_encode($pageData)
            );
        }

        $this->artisan('pages:import', [
            '--directory' => $this->exportDir,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('pages', ['slug' => 'page-a']);
        $this->assertDatabaseHas('pages', ['slug' => 'page-b']);
        $this->assertDatabaseHas('pages', ['slug' => 'page-c']);
    }

    /** @test */
    public function it_validates_imported_data()
    {
        $exportFile = $this->exportDir . '/invalid.json';
        File::makeDirectory($this->exportDir, 0755, true);
        
        // Missing required 'title' field
        $invalidData = [
            'slug' => 'invalid',
            'status' => 'published',
            'order' => 1,
        ];
        
        File::put($exportFile, json_encode($invalidData));

        $this->artisan('pages:import', [
            '--file' => $exportFile,
            '--force' => true,
        ])->assertFailed();

        $this->assertDatabaseMissing('pages', ['slug' => 'invalid']);
    }

    /** @test */
    public function it_exports_pages_with_complex_content()
    {
        $page = Page::create([
            'title' => 'Complex Page',
            'slug' => 'complex',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'FAQ',
                        'items' => [
                            ['question' => 'Q1?', 'answer' => '<p>A1</p>'],
                            ['question' => 'Q2?', 'answer' => '<p>A2</p>'],
                        ],
                    ],
                ],
                [
                    'type' => 'detailed_gallery',
                    'data' => [
                        'section_title' => 'Gallery',
                        'items' => [
                            ['title' => 'Item 1', 'image' => 'path.jpg', 'description' => 'Desc'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->artisan('pages:export', [
            '--slug' => 'complex',
            '--output' => $this->exportDir,
        ])->assertSuccessful();

        $data = json_decode(File::get($this->exportDir . '/complex.json'), true);
        
        $this->assertCount(2, $data['content']);
        $this->assertEquals('faq', $data['content'][0]['type']);
        $this->assertEquals('detailed_gallery', $data['content'][1]['type']);
        $this->assertCount(2, $data['content'][0]['data']['items']);
    }

    /** @test */
    public function export_and_import_roundtrip_preserves_data()
    {
        $originalPage = Page::create([
            'title' => 'Roundtrip Test',
            'slug' => 'roundtrip',
            'status' => 'published',
            'order' => 7,
            'content' => [
                [
                    'type' => 'hero',
                    'data' => ['title' => 'Hero Title', 'text' => '<p>Text</p>'],
                ],
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'Questions',
                        'items' => [['question' => 'Q?', 'answer' => '<p>A</p>']],
                    ],
                ],
            ],
        ]);

        // Export
        $this->artisan('pages:export', [
            '--slug' => 'roundtrip',
            '--output' => $this->exportDir,
        ])->assertSuccessful();

        // Delete original
        $originalPage->delete();

        // Import
        $this->artisan('pages:import', [
            '--file' => $this->exportDir . '/roundtrip.json',
            '--force' => true,
        ])->assertSuccessful();

        // Verify
        $importedPage = Page::where('slug', 'roundtrip')->first();
        
        $this->assertNotNull($importedPage);
        $this->assertEquals('Roundtrip Test', $importedPage->title);
        $this->assertEquals('published', $importedPage->status);
        $this->assertEquals(7, $importedPage->order);
        $this->assertCount(2, $importedPage->content);
        $this->assertEquals('hero', $importedPage->content[0]['type']);
        $this->assertEquals('faq', $importedPage->content[1]['type']);
    }
}

