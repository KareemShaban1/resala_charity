<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ActivityStatusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AreaGroupController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CallTypeController;
use App\Http\Controllers\CollectingLineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DonationCategoryController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationReportController;
use App\Http\Controllers\DonorActivityController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\DonorHistoryController;
use App\Http\Controllers\DonorReportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MonthlyFormCancellationController;
use App\Http\Controllers\MonthlyFormController;
use App\Http\Controllers\MonthlyFormDonationController;
use App\Http\Controllers\MonthlyFormReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');

        Route::get('/monthly-forms-report', [MonthlyFormReportController::class, 'index'])->name('monthly-forms-report.index');
        Route::get('/monthly-forms-report/filter', [MonthlyFormReportController::class, 'filter'])->name('monthly-forms-report.filter');

        Route::get('/donor-activities-report', [DonorReportController::class, 'donorActivities'])->name('donor-report.donorActivities');
        Route::get('/donor-activities/statistics', [DonorReportController::class, 'donorStatistics'])->name('donor-report.statistics');

        Route::get('/donor-calls-report', [DonorReportController::class, 'donorCalls'])->name('donor-report.donor-calls');
        Route::get('/donor-calls/statistics', [DonorReportController::class, 'donorCallsStatistics'])->name('donor-report.calls-statistics');

        // Route::get('/donor-activities-report/filter', [DonorReportController::class, 'filter'])->name('donor-report.filter');

        // Departments Routes
        Route::get('/departments/data', [DepartmentController::class, 'data'])->name('departments.data');
        Route::resource('departments', DepartmentController::class);

        // Employees Routes
        Route::get('/employees/data', [EmployeeController::class, 'data'])->name('employees.data');
        Route::resource('employees', EmployeeController::class);
        Route::get('/get-employee-by-department', [EmployeeController::class, 'getEmployeesByDepartment'])
            ->name('employee.getEmployeesByDepartment');


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
        Route::post('/areas/import', [AreaController::class, 'importAreas'])
            ->name('areas.import');


        Route::get('/areas-groups/data', [AreaGroupController::class, 'data'])->name('areas-groups.data');
        Route::resource('areas-groups', AreaGroupController::class);
        Route::put('/area-groups/{id}', [AreaGroupController::class, 'update'])->name('area-groups.update');
        // Donors Routes
        Route::get('/donors/search', [DonorController::class, 'search'])->name('donors.search'); // Define search first
        Route::get('/donors/data', [DonorController::class, 'data'])->name('donors.data');
        Route::post('/donors/import', [DonorController::class, 'importDonors'])->name('donors.import');
        Route::resource('donors', DonorController::class); // Resource route last
        Route::post('/donors-assign', [DonorController::class, 'assignDonors'])->name('donors.assign');
        Route::post('/donors-re-assign', [DonorController::class, 'reAssignDonors'])->name('donors.reAassign');
        Route::post('/donors-children', [DonorController::class, 'donorChildren'])->name('donors.children');
        Route::post('/donors-not-assigned', [DonorController::class, 'notAssignedDonors'])->name('donors.not-assigned');
        Route::get('/donors-random', [DonorController::class, 'randomDonors'])->name('donors.random');

        // Route::post('/activity', [DonorController::class, 'addActivity'])->name('donors.add-activity');
        // Route::post('/update-activity/{id}', [DonorController::class, 'addActivity'])->name('donors.add-activity');

        Route::resource('activities', DonorActivityController::class);

        Route::post('/donors/upload-phone-numbers', [DonorController::class, 'uploadPhoneNumbers'])->name('donors.uploadPhoneNumbers');
        Route::delete('/donors/delete-donor-phone/{id}', [DonorController::class, 'deleteDonorPhone'])
        ->name('donors.delete-donor-phone');
        
        
        // Donation Categories Routes
        Route::get('/donation-categories/data', [DonationCategoryController::class, 'data'])->name('donation-categories.data');
        Route::resource('donation-categories', DonationCategoryController::class);

        Route::get('/donations/data', [DonationController::class, 'data'])->name('donations.data');
        Route::get('/monthly-donations', [DonationController::class, 'monthlyDonations'])->name('donations.monthly-donations');
        Route::get('/gathered-donations', [DonationController::class, 'gatheredDonations'])->name('donations.gathered-donations');
        Route::resource('donations', DonationController::class);
        Route::post('/donations/store-gathered-donation', [DonationController::class, 'storeGatheredDonation'])
            ->name('donations.store-gathered-donation');
        Route::delete('/donations/delete-donatation-item/{id}', [DonationController::class, 'deleteDonatationItem'])
            ->name('donations.delete-donatation-item');
        Route::get(
            '/donations/{id}/details',
            [DonationController::class, 'getDonationDetails']
        )->name('donations.details');

        Route::get('/collected-donations-reports', [DonationReportController::class, 'collectedDonations'])
            ->name('donations-report.collected');
            Route::get('/not-collected-donations-reports', [DonationReportController::class, 'notCollectedDonations'])
            ->name('donations-report.not-collected');


        // Donation Requests Routes
        Route::get('/monthly-forms/data', [MonthlyFormController::class, 'data'])->name('monthly-forms.data');
        Route::resource('monthly-forms', MonthlyFormController::class);
        Route::get('/monthly-forms-cancelled', [MonthlyFormController::class, 'cancelledMonthlyForms'])->name('monthly-forms.cancelled');
        Route::delete('/monthly-forms/delete-item/{id}', [MonthlyFormController::class, 'deleteItem'])->name('monthly-forms.delete-item');
        Route::get(
            '/monthly-forms/{id}/details',
            [MonthlyFormController::class, 'getMonthlyFormDetails']
        )->name('monthly-forms.details');
        Route::post('/monthly-forms/import-forms', [MonthlyFormController::class, 'importMonthlyForms'])->name('monthly-forms.import-forms');
        Route::post('/monthly-forms/import-items', [MonthlyFormController::class, 'importMonthlyFormItems'])->name('monthly-forms.import-items');


        Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
        Route::resource('users', UserController::class);
        Route::get('/users/{id}/details', [UserController::class, 'userDetails'])
            ->name('users.details');
        Route::post(
            '/users/change-password',
            [UserController::class, 'changePassword']
        )->name('users.change-password');
        Route::get(
            '/users/change-password/view',
            function () {
                return view('backend.pages.settings.account');
            }
        )->name('users.change-password.view');


        Route::get('/roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::resource('roles', RoleController::class);

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/data', [ActivityLogController::class, 'data'])->name('activity-logs.data');

        Route::get('/donor-history/{id}', [DonorHistoryController::class, 'show'])->name('donor-history.show');
        Route::get('/donor-history/{id}/donations', [DonorHistoryController::class, 'getDonations'])->name('donor-history.getDonations');
        Route::get('/donor-history/{id}/monthly-forms', [DonorHistoryController::class, 'getMonthlyForms'])->name('donor-history.getMonthlyForms');
        Route::get('/donor-history/{id}/activities', [DonorHistoryController::class, 'getActivities'])->name('donor-history.getActivities');

        Route::get('donor-history/activity/{id}', [DonorHistoryController::class, 'showActivity'])->name('donor-history.showActivity');

        Route::get('/call-types/data', [CallTypeController::class, 'data'])->name('call-types.data');
        Route::resource('call-types', CallTypeController::class);

        Route::get('/activity-statuses/data', [ActivityStatusController::class, 'data'])->name('activity-statuses.data');
        Route::resource('activity-statuses', ActivityStatusController::class);

        // Collecting Lines Routes
        // Route::resource('collecting-lines', CollectingLineController::class);
        Route::get('/collecting-lines', [CollectingLineController::class, 'index'])
            ->name('collecting-lines.index');
        Route::get('/add-collecting-lines', [CollectingLineController::class, 'addCollectingLines'])
            ->name('collecting-lines.addCollectingLines');
        Route::get('/collecting-lines/{id}/show', [CollectingLineController::class, 'showCollectingLine'])
            ->name('collecting-lines.showCollectingLine');
        Route::post('/collecting-lines', [CollectingLineController::class, 'store'])->name('collecting-lines.store');
        Route::put('/collecting-lines/{collectingLine}', [CollectingLineController::class, 'update'])->name('collecting-lines.update');
        Route::delete('/collecting-lines/{collectingLine}', [CollectingLineController::class, 'destroy'])->name('collecting-lines.destroy');
        // Route::post('/donations/{donation}/assign', [CollectingLineController::class, 'assignDonation'])->name('donations.assign');

        Route::get('/collecting-lines/data', [CollectingLineController::class, 'getCollectingLinesData'])
            ->name('collecting-lines.data');

        Route::get('/collecting-lines/get-data-by-date', [CollectingLineController::class, 'getCollectingLinesByDate'])
            ->name('collecting-lines.get-data-by-date');
        Route::get('/collecting-lines/donations', [CollectingLineController::class, 'getDonationsData'])
            ->name('collecting-lines.donations');
        Route::get('/collecting-lines/monthly-forms', [CollectingLineController::class, 'getMonthlyFormsData'])
            ->name('collecting-lines.monthly-forms');


        Route::post('/collecting-lines/assign-donation', [CollectingLineController::class, 'assignDonation'])
            ->name('collecting-lines.assign-donation');

        Route::get('/collecting-lines/donations/data', [CollectingLineController::class, 'getDonationsByCollectingLine'])
            ->name('collecting-lines.donations.data');

        Route::get('/collecting-lines/export-pdf', [CollectingLineController::class, 'exportCollectingLineToPdf'])
            ->name('collecting-lines.export-pdf');

        Route::post('/monthly-forms/{monthlyFormId}/donations', [MonthlyFormDonationController::class, 'storeMonthlyFormDonation'])
            ->name('monthly-forms-donations.store');


        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');

        Route::get('/backups/data', [BackupController::class, 'data'])->name('backups.data');

        Route::resource('events', EventController::class);
        Route::post('/events/{id}', [EventController::class, 'update'])->name('events.update');

        Route::get('/events-data', [EventController::class, 'data'])->name('events.data');
        Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar');
        Route::post('/calendar', [EventController::class, 'storeCalendarEvent'])->name('calendar.store');
        Route::get('/calendar-events', [EventController::class, 'calendarEvents'])->name('calendar-events');
        Route::delete('/calendar-events/{id}', [EventController::class, 'destroy'])->name('calendar-events.destroy');
    }

);

Route::get('/backups/create', [BackupController::class, 'create'])->name('backups.create');

// Ensure this route is defined
Route::get('/backups/download/{filename}', [BackupController::class, 'download'])
    ->where('filename', '.*') // Allow slashes in the filename
    ->name('backup.download');
Route::get('/export-reports', [DashboardController::class, 'exportMonthlyForms'])->name('dashboard.export_reports');

Route::get('/notifications', [NotificationController::class, 'getNotificationsByDate']);

Route::post('/change_donors_category', [TestController::class, 'changeDonorsData'])
    ->name('changeDonorsData');
