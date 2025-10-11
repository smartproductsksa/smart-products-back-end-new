<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * Get all published pages
     */
    public function index(): JsonResponse
    {
        $pages = Page::where('status', 'published')
            ->orderBy('order')
            ->get(['id', 'title', 'slug', 'order', 'created_at', 'updated_at']);

        return response()->json([
            'success' => true,
            'data' => $pages,
        ]);
    }

    /**
     * Get a single page by slug with full content
     */
    public function show(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        // Process content sections to resolve model lists
        $content = $this->processContent($page->content);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $content,
                'created_at' => $page->created_at,
                'updated_at' => $page->updated_at,
            ],
        ]);
    }

    /**
     * Process content sections and resolve model lists
     */
    private function processContent(?array $content): array
    {
        if (!$content) {
            return [];
        }

        return collect($content)->map(function ($section) {
            // If this is a model_list section, fetch the actual data
            if ($section['type'] === 'model_list' && isset($section['data'])) {
                $data = $section['data'];
                $section['data']['items'] = $this->fetchModelData(
                    $data['model'] ?? null,
                    $data['limit'] ?? 4,
                    $data['order_by'] ?? 'created_at_desc'
                );
            }

            return $section;
        })->toArray();
    }

    /**
     * Fetch data from specified model
     */
    private function fetchModelData(?string $model, int $limit, string $orderBy): array
    {
        if (!$model) {
            return [];
        }

        // Map model names to actual models
        $modelClass = match ($model) {
            'articles' => Article::class,
            'news' => News::class,
            'categories' => Category::class,
            default => null,
        };

        if (!$modelClass) {
            return [];
        }

        // Parse order by
        [$field, $direction] = match ($orderBy) {
            'created_at_desc' => ['created_at', 'desc'],
            'created_at_asc' => ['created_at', 'asc'],
            'title_asc' => ['title', 'asc'],
            'title_desc' => ['title', 'desc'],
            default => ['created_at', 'desc'],
        };

        // Fetch the data
        return $modelClass::orderBy($field, $direction)
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
