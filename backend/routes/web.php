<?php

use App\Http\Controllers\RemoteDocumentsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/remote/documents.xml', RemoteDocumentsController::class);
