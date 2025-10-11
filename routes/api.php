<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\V1\ContactSubmissionController;
use App\Http\Controllers\Api\V1\MailingListController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Pages API
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    
    // Articles API
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/tag/{tag}', [ArticleController::class, 'byTag']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);
    
    // Categories API
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/categories/{slug}/articles', [CategoryController::class, 'articles']);
    

    // News API
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{slug}', [NewsController::class, 'show']);
    
    // Contact Submissions API
    Route::post('/contact', [ContactSubmissionController::class, 'store']);
    
    // Mailing List API
    Route::post('/subscribe', [MailingListController::class, 'subscribe']);
});
