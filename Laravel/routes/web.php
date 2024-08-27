<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\FormSubmissionController;
use App\Models\Country;

Route::get('/', function () {
    // return Country::get();
    return view('vue-repeater');
});


Route::resource('forms', FormController::class);
Route::resource('forms.fields', FormFieldController::class);
Route::resource('forms.submissions', FormSubmissionController::class);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/admin/dashboard', [DashboardController::class, 'index']);
