<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class ApiDocumentation extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';

    protected string $view = 'filament.pages.api-documentation';
    
    protected static ?string $navigationLabel = 'API Documentation';
    
    protected static ?string $title = 'API Documentation';
    
    protected static ?int $navigationSort = 99;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Dev';
    }

    public function getApiRoutes(): array
    {
        return [
            [
                'group' => 'Pages',
                'routes' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/pages',
                        'description' => 'Get all pages',
                        'parameters' => [],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                [
                                    'id' => 1,
                                    'title' => 'About Us',
                                    'slug' => 'about-us',
                                    'status' => 'published',
                                    'order' => 1,
                                    'content' => [],
                                ]
                            ]
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/pages/{slug}',
                        'description' => 'Get single page by slug',
                        'parameters' => ['slug' => 'about-us'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                'id' => 1,
                                'title' => 'About Us',
                                'slug' => 'about-us',
                                'status' => 'published',
                                'content' => [],
                            ]
                        ]
                    ],
                ]
            ],
            [
                'group' => 'Articles',
                'routes' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/articles',
                        'description' => 'Get all articles (paginated)',
                        'parameters' => ['per_page' => '15 (optional)'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                [
                                    'id' => 1,
                                    'title' => 'Sample Article',
                                    'slug' => 'sample-article',
                                    'content' => 'Article content...',
                                    'image' => '/storage/articles/image.jpg',
                                    'tags' => ['laravel', 'php'],
                                    'category' => [
                                        'id' => 1,
                                        'name' => 'Technology',
                                        'slug' => 'technology'
                                    ]
                                ]
                            ],
                            'links' => [],
                            'meta' => []
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/articles/{slug}',
                        'description' => 'Get single article by slug',
                        'parameters' => ['slug' => 'sample-article'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                'id' => 1,
                                'title' => 'Sample Article',
                                'slug' => 'sample-article',
                                'content' => 'Full article content...',
                                'category' => ['id' => 1, 'name' => 'Technology']
                            ]
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/articles/tag/{tag}',
                        'description' => 'Get articles by tag',
                        'parameters' => ['tag' => 'laravel'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [],
                            'tag' => 'laravel'
                        ]
                    ],
                ]
            ],
            [
                'group' => 'Categories',
                'routes' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/categories',
                        'description' => 'Get all categories',
                        'parameters' => [],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                [
                                    'id' => 1,
                                    'name' => 'Technology',
                                    'slug' => 'technology',
                                    'description' => 'Tech articles',
                                    'articles_count' => 5
                                ]
                            ]
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/categories/{slug}',
                        'description' => 'Get single category',
                        'parameters' => ['slug' => 'technology'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                'id' => 1,
                                'name' => 'Technology',
                                'slug' => 'technology',
                                'articles_count' => 5
                            ]
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/categories/{slug}/articles',
                        'description' => 'Get articles by category',
                        'parameters' => ['slug' => 'technology', 'per_page' => '15 (optional)'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [],
                            'category' => [
                                'id' => 1,
                                'name' => 'Technology',
                                'slug' => 'technology'
                            ]
                        ]
                    ],
                ]
            ],
            [
                'group' => 'News',
                'routes' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/news',
                        'description' => 'Get all news (paginated)',
                        'parameters' => ['per_page' => '15 (optional)'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                [
                                    'id' => 1,
                                    'title' => 'Breaking News',
                                    'slug' => 'breaking-news',
                                    'content' => 'News content...',
                                    'image' => '/storage/news/image.jpg',
                                    'tags' => ['breaking', 'trending']
                                ]
                            ]
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/news/{slug}',
                        'description' => 'Get single news by slug',
                        'parameters' => ['slug' => 'breaking-news'],
                        'sample_response' => [
                            'success' => true,
                            'data' => [
                                'id' => 1,
                                'title' => 'Breaking News',
                                'slug' => 'breaking-news',
                                'content' => 'Full news content...'
                            ]
                        ]
                    ],
                ]
            ],
            [
                'group' => 'Contact',
                'routes' => [
                    [
                        'method' => 'POST',
                        'path' => '/api/v1/contact',
                        'description' => 'Submit contact form',
                        'parameters' => [
                            'name' => 'John Doe (required)',
                            'phone' => '+1234567890 (required)',
                            'email' => 'john@example.com (required)',
                            'message' => 'Your message here (required, max 5000 chars)'
                        ],
                        'sample_response' => [
                            'success' => true,
                            'message' => 'Thank you for contacting us. We will get back to you soon.',
                            'data' => [
                                'id' => 1,
                                'created_at' => '2025-10-11T15:47:21.000000Z'
                            ]
                        ]
                    ],
                ]
            ],
            [
                'group' => 'Mailing List',
                'routes' => [
                    [
                        'method' => 'POST',
                        'path' => '/api/v1/subscribe',
                        'description' => 'Subscribe to mailing list',
                        'parameters' => [
                            'email' => 'subscriber@example.com (required, unique)'
                        ],
                        'sample_response' => [
                            'success' => true,
                            'message' => 'Successfully subscribed to our mailing list!',
                            'data' => [
                                'id' => 1,
                                'email' => 'subscriber@example.com',
                                'created_at' => '2025-10-11T15:59:55.000000Z'
                            ]
                        ]
                    ],
                ]
            ],
        ];
    }
}
