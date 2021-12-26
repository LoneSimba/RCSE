<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthController;

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
//    return view('welcome');
    return view('index');
});


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/login/{provider}', [OAuthController::class, 'redirectToProvider'])
     ->name('social.login');
Route::get('/login/{provider}/callback', [OAuthController::class, 'handleProviderCallback'])
     ->name('social.callback');
