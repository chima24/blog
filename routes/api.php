<?php

use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CommentLikesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\Profile\ChangeUserNameController;
use App\Http\Controllers\Profile\ChangeUserPasswordController;
use App\Http\Controllers\Profile\ChangeUserSurnameController;
use App\Http\Controllers\PostLikesController;
use App\Http\Controllers\PostsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('posts', PostsController::class)->only(['index', 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::patch('/profile/change-password', ChangeUserPasswordController::class);
    Route::patch('/profile/change-name', ChangeUserNameController::class);
    Route::patch('/profile/change-surname', ChangeUserSurnameController::class);

    Route::get('/activities', ActivitiesController::class);

    Route::post('/categories', [CategoriesController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentsController::class, 'destroy']);

    Route::apiResource('posts', PostsController::class)->only(['store', 'destroy', 'update']);

    Route::post('/posts/{post}/comments', [CommentsController::class, 'store']);

    Route::post('/posts/{post}/like', [PostLikesController::class, 'store']);
    Route::delete('/posts/{post}/like', [PostLikesController::class, 'destroy']);

    Route::post('/comments/{comment}/like', [CommentLikesController::class, 'store']);
    Route::delete('/comments/{comment}/like', [CommentLikesController::class, 'destroy']);
});
