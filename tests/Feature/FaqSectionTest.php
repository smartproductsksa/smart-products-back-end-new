<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FaqSectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role and user
        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        
        $this->admin->assignRole($adminRole);
    }

    /** @test */
    public function it_can_create_page_with_faq_section()
    {
        $this->actingAs($this->admin);

        $page = Page::create([
            'title' => 'FAQ Page',
            'slug' => 'faq',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'Frequently Asked Questions',
                        'section_description' => 'Find answers to common questions',
                        'items' => [
                            [
                                'question' => 'What are your business hours?',
                                'answer' => '<p>We are open Monday to Friday, 9 AM to 5 PM.</p>',
                            ],
                            [
                                'question' => 'How can I contact support?',
                                'answer' => '<p>You can reach us via email at support@example.com or call us at 123-456-7890.</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('pages', [
            'title' => 'FAQ Page',
            'slug' => 'faq',
            'status' => 'published',
        ]);

        $content = $page->fresh()->content;
        $this->assertIsArray($content);
        $this->assertEquals('faq', $content[0]['type']);
        $this->assertEquals('Frequently Asked Questions', $content[0]['data']['section_title']);
        $this->assertCount(2, $content[0]['data']['items']);
    }

    /** @test */
    public function it_returns_faq_section_via_api()
    {
        Page::create([
            'title' => 'Help Page',
            'slug' => 'help',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'How Can We Help?',
                        'section_description' => 'Browse our most frequently asked questions',
                        'items' => [
                            [
                                'question' => 'How do I reset my password?',
                                'answer' => '<p>Click on "Forgot Password" on the login page.</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->getJson('/api/v1/pages/help');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.content.0.type', 'faq')
            ->assertJsonPath('data.content.0.data.section_title', 'How Can We Help?')
            ->assertJsonCount(1, 'data.content.0.data.items');
    }

    /** @test */
    public function faq_section_can_have_optional_description()
    {
        $page = Page::create([
            'title' => 'FAQ without Description',
            'slug' => 'faq-no-desc',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'FAQ',
                        'items' => [
                            [
                                'question' => 'Question?',
                                'answer' => '<p>Answer</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $content = $page->fresh()->content;
        $this->assertEquals('FAQ', $content[0]['data']['section_title']);
        $this->assertArrayNotHasKey('section_description', $content[0]['data']);
    }

    /** @test */
    public function faq_items_support_rich_text_answers()
    {
        $page = Page::create([
            'title' => 'FAQ with Rich Text',
            'slug' => 'faq-rich',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'FAQ',
                        'items' => [
                            [
                                'question' => 'What features do you offer?',
                                'answer' => '<p>We offer:</p><ul><li>Feature 1</li><li>Feature 2</li><li>Feature 3</li></ul><p>For more info, <a href="/contact">contact us</a>.</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $content = $page->fresh()->content;
        $answer = $content[0]['data']['items'][0]['answer'];
        
        $this->assertStringContainsString('<ul>', $answer);
        $this->assertStringContainsString('<li>Feature 1</li>', $answer);
        $this->assertStringContainsString('<a href="/contact">contact us</a>', $answer);
    }

    /** @test */
    public function faq_items_are_reorderable()
    {
        $page = Page::create([
            'title' => 'FAQ Reorder Test',
            'slug' => 'faq-reorder',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'FAQ',
                        'items' => [
                            [
                                'question' => 'First Question?',
                                'answer' => '<p>First Answer</p>',
                            ],
                            [
                                'question' => 'Second Question?',
                                'answer' => '<p>Second Answer</p>',
                            ],
                            [
                                'question' => 'Third Question?',
                                'answer' => '<p>Third Answer</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // Reorder items
        $content = $page->content;
        $items = $content[0]['data']['items'];
        $content[0]['data']['items'] = [$items[2], $items[0], $items[1]]; // Swap order
        
        $page->update(['content' => $content]);
        
        $updatedContent = $page->fresh()->content;
        $this->assertEquals('Third Question?', $updatedContent[0]['data']['items'][0]['question']);
        $this->assertEquals('First Question?', $updatedContent[0]['data']['items'][1]['question']);
        $this->assertEquals('Second Question?', $updatedContent[0]['data']['items'][2]['question']);
    }

    /** @test */
    public function page_can_have_multiple_faq_sections()
    {
        $page = Page::create([
            'title' => 'Multiple FAQ Page',
            'slug' => 'multiple-faq',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'General Questions',
                        'items' => [
                            [
                                'question' => 'General Question?',
                                'answer' => '<p>General Answer</p>',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'Technical Questions',
                        'items' => [
                            [
                                'question' => 'Technical Question?',
                                'answer' => '<p>Technical Answer</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $content = $page->fresh()->content;
        $this->assertCount(2, $content);
        $this->assertEquals('faq', $content[0]['type']);
        $this->assertEquals('faq', $content[1]['type']);
        $this->assertEquals('General Questions', $content[0]['data']['section_title']);
        $this->assertEquals('Technical Questions', $content[1]['data']['section_title']);
    }

    /** @test */
    public function faq_can_be_combined_with_other_content_blocks()
    {
        $page = Page::create([
            'title' => 'Mixed Content Page',
            'slug' => 'mixed-faq',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'title' => 'Welcome',
                        'text' => '<p>Welcome text</p>',
                    ],
                ],
                [
                    'type' => 'text_section',
                    'data' => [
                        'title' => 'About',
                        'text' => '<p>About text</p>',
                    ],
                ],
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'FAQ',
                        'items' => [
                            [
                                'question' => 'Question?',
                                'answer' => '<p>Answer</p>',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $content = $page->fresh()->content;
        $this->assertCount(3, $content);
        $this->assertEquals('hero', $content[0]['type']);
        $this->assertEquals('text_section', $content[1]['type']);
        $this->assertEquals('faq', $content[2]['type']);
    }

    /** @test */
    public function faq_section_supports_many_items()
    {
        $items = [];
        for ($i = 1; $i <= 20; $i++) {
            $items[] = [
                'question' => "Question number {$i}?",
                'answer' => "<p>Answer number {$i}</p>",
            ];
        }

        $page = Page::create([
            'title' => 'Large FAQ',
            'slug' => 'large-faq',
            'status' => 'published',
            'order' => 1,
            'content' => [
                [
                    'type' => 'faq',
                    'data' => [
                        'section_title' => 'Comprehensive FAQ',
                        'section_description' => 'All your questions answered',
                        'items' => $items,
                    ],
                ],
            ],
        ]);

        $content = $page->fresh()->content;
        $this->assertCount(20, $content[0]['data']['items']);
        $this->assertEquals('Question number 1?', $content[0]['data']['items'][0]['question']);
        $this->assertEquals('Question number 20?', $content[0]['data']['items'][19]['question']);
    }
}

