<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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
//User Related routes:
Route::get('/', [UserController::class, "homefeed"])->name('login');
Route::post('/register', [UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('auth');

// Blog post related routes:
Route::get('/create-post', [PostController::class, 'showPostForm'])->middleware('shouldBeloggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('shouldBeloggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::get('/profile/{user:username}', [UserController::class, 'profile']);
Route::delete('/post/{post}', [UserController::class, 'delete']);
