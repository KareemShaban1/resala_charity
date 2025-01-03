<?php

use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DonationCategoryController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MonthlyDonationCancellationController;
use App\Http\Controllers\MonthlyDonationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

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
            'auth:web',
            'localeCookieRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ],
    function () {

        Route::get('/', function () {
            return view('backend.dashboard');
        });

        // Departments Routes
        Route::get('/departments/data', [DepartmentController::class, 'data'])->name('departments.data');
        Route::resource('departments', DepartmentController::class);

        // Employees Routes
        Route::get('/employees/data', [EmployeeController::class, 'data'])->name('employees.data');
        Route::resource('employees', EmployeeController::class);


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
        Route::get('/donors/search', [DonorController::class, 'search'])->name('donors.search'); // Define search first
        Route::get('/donors/data', [DonorController::class, 'data'])->name('donors.data');
        Route::post('/donors/import', [DonorController::class, 'importDonors'])->name('donors.import');
        Route::resource('donors', DonorController::class); // Resource route last
        Route::post('/donors-assign', [DonorController::class, 'assignDonors'])->name('donors.assign');
        Route::post('/donors-children', [DonorController::class, 'donorChildren'])->name('donors.children');
        Route::post('/donors-not-assigned', [DonorController::class, 'notAssignedDonors'])->name('donors.not-assigned');

        // Donation Categories Routes
        Route::get('/donation-categories/data', [DonationCategoryController::class, 'data'])->name('donation-categories.data');
        Route::resource('donation-categories', DonationCategoryController::class);

        Route::get('/donations/data', [DonationController::class, 'data'])->name('donations.data');
        Route::resource('donations', DonationController::class);
        Route::delete('/donations/delete-donatation-item/{id}', [DonationController::class, 'deleteDonatationItem'])
            ->name('donations.delete-donatation-item');
        Route::get(
            '/donations/{id}/details',
            [DonationController::class, 'getDonationDetails']
        )->name('donations.details');



        // Donation Requests Routes
        Route::get('/monthly-donations/data', [MonthlyDonationController::class, 'data'])->name('monthly-donations.data');
        Route::resource('monthly-donations', MonthlyDonationController::class);
        Route::get('/monthly-donations-cancelled', [MonthlyDonationController::class, 'cancelledMonthlyDonations'])->name('monthly-donations.cancelled');
        Route::delete('/monthly-donations/delete-donate/{id}', [MonthlyDonationController::class, 'deleteDonate'])->name('monthly-donations.delete-donate');

        Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
        Route::resource('users', UserController::class);

        Route::get('/roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::resource('roles', RoleController::class);

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/data', [ActivityLogController::class, 'data'])->name('activity-logs.data');
    }
);
