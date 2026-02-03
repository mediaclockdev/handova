<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\ForgotPasswordController as SuperAdminForgotPasswordController;
use App\Http\Controllers\SuperAdmin\ResetPasswordController as SuperAdminResetPasswordController;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\Admin\PropertiesController;
use App\Http\Controllers\Admin\HouseOwnerController;
use App\Http\Controllers\Admin\IssueReportController;
use App\Http\Controllers\Admin\ServiceProviderController;
use App\Http\Controllers\Admin\HousePlansController;
use \App\Http\Controllers\Admin\ReportAnalyticsController;
use \App\Http\Controllers\SuperAdmin\PlanController;
use \App\Http\Controllers\SuperAdmin\BuildersController;
use \App\Http\Controllers\SuperAdmin\PropertiesListController;
use \App\Http\Controllers\Admin\AppliancesController;
use \App\Http\Controllers\Admin\SubscriptionsPlanController;
use \App\Http\Controllers\Admin\ComplianceCertificatesController;
use \App\Http\Controllers\Admin\PageContentDetailsController;
use \App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\HelpContentsController;
use \App\Http\Controllers\SuperAdmin\OwnersController;
use \App\Http\Controllers\SuperAdmin\ServiceProvidersListController;
use \App\Http\Controllers\SuperAdmin\ServiceSpecializationController;



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

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('signup', [AuthController::class, 'showRegister'])->name('register');
Route::post('signup', [AuthController::class, 'register']);
Route::get('dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendVerificationCode'])->name('password.send-code');
Route::get('verify-code', [ForgotPasswordController::class, 'showVerifyCodeForm'])->name('password.verify-code-form');
Route::post('verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify-code');
Route::get('reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/admin/property/{id}/owners', [HouseOwnerController::class, 'getOwners']);
Route::get('/admin/property/{id}/issues', [IssueReportController::class, 'getIssuesByProperty']);
Route::get('/admin/property/{id}/houseplans', [HousePlansController::class, 'getHousePlansByProperty']);
Route::get('/admin/property/{id}/certificates', [ComplianceCertificatesController::class, 'getCertificatesByProperty']);
Route::get('/admin/filter/issues', [IssueReportController::class, 'filter']);


Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('profile', ProfileController::class);
    Route::resource('properties', PropertiesController::class);
    Route::resource('house_owners', HouseOwnerController::class);
    Route::resource('issue_report', IssueReportController::class);
    Route::resource('service_provider', ServiceProviderController::class);
    Route::resource('house_plans', HousePlansController::class);
    Route::resource('compliance_certificates', ComplianceCertificatesController::class);
    Route::resource('page_content', PageContentDetailsController::class);
    Route::resource('report_analytics', ReportAnalyticsController::class);
    Route::resource('appliances', AppliancesController::class);
    Route::resource('subscription-plan', SubscriptionsPlanController::class);
    Route::resource('help', HelpContentsController::class)->only(['index']);
});

Route::get('/admin/service-providers/by-property', [IssueReportController::class, 'getServiceProvidersByProperty'])->name('admin.service.providers.by.property');


Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

Route::post('superadmin/builders/bulk-action', [BuildersController::class, 'bulkAction'])->name('superadmin.builders.bulkAction');
Route::get('superadmin/builders/export', [BuildersController::class, 'export'])->name('superadmin.builders.export');
Route::patch('superadmin/builders/{id}/suspend', [BuildersController::class, 'suspend'])->name('superadmin.builders.suspend');
Route::post('superadmin/builders/send-mail', [BuildersController::class, 'sendMail'])->name('superadmin.builders.sendMail');

Route::get('/superadmin/properties/export', [\App\Http\Controllers\SuperAdmin\PropertiesListController::class, 'export'])->name('superadmin.properties.export');
Route::patch('superadmin/properties/{id}/suspend', [\App\Http\Controllers\SuperAdmin\PropertiesListController::class, 'suspend'])->name('superadmin.properties.suspend');
Route::post('superadmin/properties/bulk-action', [\App\Http\Controllers\SuperAdmin\PropertiesListController::class, 'bulkAction'])->name('superadmin.properties.bulkAction');


Route::patch('superadmin/owners/{id}/suspend', [OwnersController::class, 'suspend'])->name('superadmin.owners.suspend');
Route::get('superadmin/owners/export', [OwnersController::class, 'export'])->name('superadmin.owners.export');


Route::get('/superadmin/login', [AuthController::class, 'showLoginForm'])->middleware('superadmin.guest')->name('superadmin.login');


Route::post('superadmin/providers/bulk-action', [ServiceProvidersListController::class, 'bulkAction'])->name('superadmin.providers.bulkAction');
Route::get('superadmin/providers/export', [ServiceProvidersListController::class, 'export'])->name('superadmin.providers.export');
Route::patch('superadmin/providers/{id}/suspend', [ServiceProvidersListController::class, 'suspend'])->name('superadmin.providers.suspend');
Route::post('superadmin/providers/send-mail', [ServiceProvidersListController::class, 'sendMail'])->name('superadmin.providers.sendMail');


Route::patch('superadmin/specialization/{id}/suspend', [ServiceSpecializationController::class, 'suspend'])->name('superadmin.specialization.suspend');
Route::post('superadmin/specialization/bulk-action', [ServiceSpecializationController::class, 'bulkAction'])->name('superadmin.specialization.bulkAction');
Route::get('superadmin/specialization/export', [ServiceSpecializationController::class, 'export'])->name('superadmin.specialization.export');


Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('superadmin.login');
    });

    Route::get('login', [SuperAdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [SuperAdminAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
    Route::get('forgot-password', [SuperAdminForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [SuperAdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [SuperAdminResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [SuperAdminResetPasswordController::class, 'reset'])->name('password.update');
    Route::middleware(['auth', 'superadmin'])->group(function () {
        Route::get('dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('plans', PlanController::class);
        Route::resource('builders', BuildersController::class);
        Route::resource('properties', PropertiesListController::class);
        Route::resource('owners', OwnersController::class);
        Route::resource('providers', ServiceProvidersListController::class);
        Route::resource('specialization', ServiceSpecializationController::class);
    });
});
