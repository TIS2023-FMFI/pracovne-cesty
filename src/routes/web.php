<?php

use App\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;
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

Route::get('/send-test-mail', function () {
    Mail::to('test@example.com')->send(new SimpleMail('Testovacia správa', 'test@example.com'));
    return 'E-mail bol odoslaný.';
});

Route::get('/', function () {
    return view('welcome');
});
