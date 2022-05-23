<?php

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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('web')
    ->prefix(config('formbuilder.url_path', '/form-builder'))
->namespace('jazmy\FormBuilder\Controllers')
->name('formbuilder::')
->group(function () {
    Route::redirect('/', url(config('formbuilder.url_path', '/form-builder').'/forms'));

    /**
     * Public form url
     */
    Route::get('/form/{identifier}', 'RenderFormController@render')->name('form.render');
    Route::post('/form/{identifier}', 'RenderFormController@submit')->name('form.submit');
    Route::get('/form/{identifier}/feedback', 'RenderFormController@feedback')->name('form.feedback');

    /**
     * My submission routes
     */
    Route::resource('/my-submissions', 'MySubmissionController');
    
    /**
     * Form submission management routes
     */
    Route::name('forms.')
        ->prefix('/forms/{fid}')
        ->group(function () {
            Route::resource('/submissions', 'SubmissionController');
        });

    /**
     * Form management routes
     */
    Route::resource('/forms', 'FormController');
});
