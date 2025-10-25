<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DetailedGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Create admin role and user
        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        
        $this->admin->assignRole($adminRole);
    }

    /** @test */
    public function it_can_create_page_with_detailed_gallery()
    {
        $this->actingAs($this->admin);

        $page = Page::create([
            'title' => 'Our Clients',
            'slug' => 'our-clients',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'detailed_gallery',
                    'data' => [
                        'section_title' => 'Our Valued Clients',
                        'items' => [
                            [
                                'title' => 'Client A',
                                'image' => 'pages/detailed-gallery/client-a.jpg',
                                'description' => 'A leading technology company',
                            ],
                            [
                                'title' => 'Client B',
                                'image' => 'pages/detailed-gallery/client-b.jpg',
                                'description' => 'Global retail brand',
                            ],
                            [
                                'title' => 'Client C',
                                'image' => 'pages/detailed-gallery/client-c.jpg',
                                'description' => null, // Optional description
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('pages', [
            'title' => 'Our Clients',
            'slug' => 'our-clients',
            'status' => 'published',
        ]);

        $content = $page->fresh()->content;
        $this->assertIsArray($content);
        $this->assertEquals('detailed_gallery', $content[0]['type']);
        $this->assertEquals('Our Valued Clients', $content[0]['data']['section_title']);
        $this->assertCount(3, $content[0]['data']['items']);
        $this->assertEquals('Client A', $content[0]['data']['items'][0]['title']);
    }

    /** @test */
    public function it_returns_detailed_gallery_via_api()
    {
        Page::create([
            'title' => 'Team Page',
            'slug' => 'team',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'detailed_gallery',
                    'data' => [
                        'section_title' => 'Meet Our Team',
                        'items' => [
                            [
                                'title' => 'John Doe',
                                'image' => 'pages/detailed-gallery/john.jpg',
                                'description' => 'CEO & Founder',
                            ],
                            [
                                'title' => 'Jane Smith',
                                'image' => 'pages/detailed-gallery/jane.jpg',
                                'description' => 'CTO',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->getJson('/api/v1/pages/team');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.content.0.type', 'detailed_gallery')
            ->assertJsonPath('data.content.0.data.section_title', 'Meet Our Team')
            ->assertJsonCount(2, 'data.content.0.data.items');
    }

    /** @test */
    public function it_can_have_items_without_description()
    {
        $page = Page::create([
            'title' => 'Partners',
            'slug' => 'partners',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'detailed_gallery',
                    'data' => [
                        'section_title' => 'Our Partners',
                        'items' => [
                            [
                                'title' => 'Partner A',
                                'image' => 'pages/detailed-gallery/partner-a.jpg',
                                // No description - it's optional
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $content = $page->fresh()->content;
        $this->assertEquals('Partner A', $content[0]['data']['items'][0]['title']);
        $this->assertArrayNotHasKey('description', $content[0]['data']['items'][0]);
    }

    /** @test */
    public function it_can_have_multiple_gallery_types_on_same_page()
    {
        $page = Page::create([
            'title' => 'Mixed Gallery Page',
            'slug' => 'mixed-gallery',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'image_gallery',
                    'data' => [
                        'title' => 'Simple Gallery',
                        'images' => ['image1.jpg', 'image2.jpg'],
                    ],
                ],
                [
                    'type' => 'detailed_gallery',
                    'data' => [
                        'section_title' => 'Detailed Gallery',
                        'items' => [
                            [
                                'title' => 'Item 1',
                                'image' => 'pages/detailed-gallery/item1.jpg',
                                'description' => 'Description 1',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $content = $page->fresh()->content;
        $this->assertCount(2, $content);
        $this->assertEquals('image_gallery', $content[0]['type']);
        $this->assertEquals('detailed_gallery', $content[1]['type']);
    }

    /** @test */
    public function detailed_gallery_items_are_reorderable()
    {
        $page = Page::create([
            'title' => 'Reorderable Test',
            'slug' => 'reorderable-test',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'detailed_gallery',
                    'data' => [
                        'section_title' => 'Test Gallery',
                        'items' => [
                            [
                                'title' => 'First Item',
                                'image' => 'pages/detailed-gallery/first.jpg',
                                'description' => 'This is first',
                            ],
                            [
                                'title' => 'Second Item',
                                'image' => 'pages/detailed-gallery/second.jpg',
                                'description' => 'This is second',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // Reorder items
        $content = $page->content;
        $items = $content[0]['data']['items'];
        $content[0]['data']['items'] = [$items[1], $items[0]]; // Swap order
        
        $page->update(['content' => $content]);
        
        $updatedContent = $page->fresh()->content;
        $this->assertEquals('Second Item', $updatedContent[0]['data']['items'][0]['title']);
        $this->assertEquals('First Item', $updatedContent[0]['data']['items'][1]['title']);
    }
}

