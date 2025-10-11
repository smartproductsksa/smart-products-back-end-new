<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Get all news with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        
        $news = News::orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }

    /**
     * Get single news by slug
     */
    public function show(string $slug): JsonResponse
    {
        $news = News::where('slug', $slug)->first();

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }
}
