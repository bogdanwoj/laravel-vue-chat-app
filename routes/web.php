<?php

    use App\Http\Controllers\HomeController;
    use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('chat', [HomeController::class, 'chat']);
Route::get('messages', [HomeController::class, 'messages'])->name('messages');
Route::post('messages', [HomeController::class, 'messageStore'])->name('messageStore');
Route::post('privateMessages', [HomeController::class, 'privateMessageStore'])->name('privateMessageStore');
Route::get('private', [HomeController::class, 'private'])->name('private');
Route::get('users', [HomeController::class, 'users'])->name('users');
Route::get('private-messages/{user}', [HomeController::class, 'privateMessages'])->name('privateMessages');
Route::post('private-message/{user}', [HomeController::class, 'sendPrivateMessage'])->name('sendPrivateMessage');


