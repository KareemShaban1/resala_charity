<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DonorController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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

Route::group(
    [
          'prefix' => LaravelLocalization::setLocale(),
          'middleware' => [
            // 'auth:web',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath']
    ],
    function () {

        Route::get('/', function () {
            return view('backend.dashboard');
        });

        // Governorates Routes
        Route::get('/governorates/data', [GovernorateController::class, 'data'])->name('governorates.data');
        Route::resource('governorates', GovernorateController::class);
        
        // Cities Routes
        Route::get('/cities/data', [CityController::class, 'data'])->name('cities.data');
        Route::get('/cities/by-governorate', [CityController::class, 'getCitiesByGovernorate'])->name('cities.by-governorate');
        Route::resource('cities', CityController::class);
        
        // Areas Routes
        Route::get('/areas/data', [AreaController::class, 'data'])->name('areas.data');
        Route::get('/areas/by-city', [AreaController::class, 'getAreasByCity'])->name('areas.by-city');
        Route::resource('areas', AreaController::class);
        
        // Donors Routes
        Route::get('/donors/data', [DonorController::class, 'data'])->name('donors.data');
        Route::resource('donors', DonorController::class);
        Route::post('/donors/import', [DonorController::class, 'importDonors'])->name('donors.import');

        
    });

Route::group(
    [
          'prefix' => LaravelLocalization::setLocale() . '/backend',
          'as' => 'backend.',
          'namespace' => 'App\Http\Controllers\Backend',
          'middleware' => [
            'auth:web',
            'verified',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath']
    ],
    function () {
        
    });
