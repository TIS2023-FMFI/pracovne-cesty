<?php

use App\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessTripController;
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
Route::get('/test-export-pdf', function () {
    $controller = new BusinessTripController();
    return $controller->exportPdf(request(), 0, 'cestne_vyhlasenie_k_zahranicnej_pc.pdf'); // Nahraďte 1 skutočným ID pracovnej cesty
});


Route::get('/', function () {
    return view('welcome');
});
