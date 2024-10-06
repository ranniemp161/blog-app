<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Admin page:
Route::get('/admin-page', function () {
    return 'this is the admin page';
})->middleware('can:adminPageOnly');

//User Related routes:
Route::get('/', [UserController::class, "homefeed"])->name('login');
Route::post('/register', [UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('auth');
Route::get('/manage-avatar', [UserController::class, "showAvatar"])->middleware('shouldBeloggedIn');
Route::post('/manage-avatar', [UserController::class, "storeAvatar"])->middleware('shouldBeloggedIn');

// Blog post related routes:
Route::get('/create-post', [PostController::class, 'showPostForm'])->middleware('shouldBeloggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('shouldBeloggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'updatePost'])->middleware('can:update,post');

//Profile related routes:
Route::get('/profile/{user:username}', [UserController::class, 'profile']);
