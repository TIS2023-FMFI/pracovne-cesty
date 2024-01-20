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
    $controller = new BusinessTripController();
    return $controller->exportPdf(0, DocumentType::FOREIGN_TRIP_AFFIDAVIT);
});


Route::get('/', function () {
    return view('welcome');
});
