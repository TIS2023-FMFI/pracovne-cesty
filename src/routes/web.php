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
    //Mail::to('externist@example.com')->send(new SimpleMail('Vitajte v našom systéme', 'externist@example.com', 'emails.registration_externist'));
    Mail::to('slavik45@uniba.sk')->send(new SimpleMail('Vitajte v našom systéme', 'slavik45@uniba.sk', 'emails.registration_externist'));
    return 'E-mail bol odoslaný.';
});

Route::get('/', function () {
    return view('welcome');
});
