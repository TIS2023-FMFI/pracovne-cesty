<?php

use App\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessTripController;
use App\Enums\DocumentType;
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
    $a = new BusinessTripController();
    return $a->exportPdf(10, DocumentType::COMPENSATION_AGREEMENT);
});


Route::get('/', function () {
    return view('welcome');
});
