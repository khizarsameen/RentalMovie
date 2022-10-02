<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['is.loggedin'])->group(function() {
    Route::get('login', [LoginController::class, 'login']);
    Route::post('login', [LoginController::class, 'store'])->name('login');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('register', [RegisterController::class, 'register']);
    Route::post('register', [RegisterController::class, 'store'])->name('register');
});
Route::middleware(['auth.check'])->group(function() {
    Route::get('home', [HomeController::class, 'home'])->name('home');
    Route::resource('movies', MovieController::class);
    Route::get('list/movies', [MovieController::class, 'listMovies'])->name('list.movies');
    Route::get('view/moviedetail/{id}', [MovieController::class, 'movieDetail'])->name('view.moviedetail');
    Route::post('rent/movies', [MovieController::class, 'rentMovie'])->name('rent.movies');
    Route::resource('admin-users', AdminUserController::class);
    Route::get('guest-profile', [AdminUserController::class, 'guestProfile'])->name('guest-profile');
    Route::get('get/usermovies', [AdminUserController::class, 'userMovies'])->name('get.usermovies');
    Route::post('update/guestprofile', [AdminUserController::class, 'updateGuestProfile'])->name('update.guestprofile');




});