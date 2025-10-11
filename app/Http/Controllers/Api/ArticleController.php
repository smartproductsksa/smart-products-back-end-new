<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Get all articles with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        
        $articles = Article::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    /**
     * Get articles by tag
     */
    public function byTag(string $tag): JsonResponse
    {
        $articles = Article::whereJsonContains('tags', $tag)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $articles,
            'tag' => $tag,
        ]);
    }

    /**
     * Get all unique tags from articles
     */
    public function tags(): JsonResponse
    {
        $articles = Article::whereNotNull('tags')->get(['tags']);
        
        $tags = $articles->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $tags,
        ]);
    }

    /**
     * Get single article by slug
     */
    public function show(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)
            ->with('category')
            ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }
}
