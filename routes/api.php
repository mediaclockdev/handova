<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\HouseOwnerApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/request-a-service', [HouseOwnerApiController::class, 'requestApplianceService']);
    Route::post('/house-owner', [HouseOwnerApiController::class, 'getOwnerWithProperties']);
    Route::post('/all-appliance', [HouseOwnerApiController::class, 'getAppliancesByProperty']);
    Route::post('/appliance', [HouseOwnerApiController::class, 'getApplianceById']);
    Route::post('/house-plans', [HouseOwnerApiController::class, 'getHousePlanByProperty']);
    Route::post('/appliance-feedback', [HouseOwnerApiController::class, 'applianceFeedbackForm']);
    Route::post('/profile-update', [HouseOwnerApiController::class, 'profileUpdate']);
    Route::post('/service-history', [HouseOwnerApiController::class, 'appliancesServiceHistory']);
    Route::post('/id-service-history', [HouseOwnerApiController::class, 'getAppliancesServiceHistoryId']);
    Route::post('/house-plan-amenities', [HouseOwnerApiController::class, 'housePlanAmenities']);
    Route::post('/page-content', [HouseOwnerApiController::class, 'getPageContent']);
    Route::post('/notification-listing', [HouseOwnerApiController::class, 'notificationListing']);
    Route::post('/mark-notifications-read', [HouseOwnerApiController::class, 'markNotificationsAsRead']);
    Route::post('/search-appliance', [HouseOwnerApiController::class, 'searchAppliance']);
    Route::post('/email-us', [HouseOwnerApiController::class, 'emailUs']);
    Route::post('/compliance-certificate', [HouseOwnerApiController::class, 'getComplianceCertificatesByProperty']);
    Route::get('/dashboard', [AuthApiController::class, 'dashboard']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/profile', [AuthApiController::class, 'getUserProfile']);
    Route::post('/issue-reports/by-user', [HouseOwnerApiController::class, 'getIssuesByUser']);
    Route::post('/issue-reports/accepted-list', [HouseOwnerApiController::class, 'getIssuesAcceptedList']);
    Route::post('/update-issue-reports-by-serviceprovider', [HouseOwnerApiController::class, 'updateIssueReportByServiceProvider']);
    Route::post('/service-provider/availability', [HouseOwnerApiController::class, 'updateServiceProviderAvailabilityPreferences']);
    Route::post('/service-provider/coverage', [HouseOwnerApiController::class, 'updateServiceProvidersCoverage']);
    Route::get('/issue-reports/service-history', [HouseOwnerApiController::class, 'getServiceHistoryByUser']);

    Route::post('/issue-report/otp-verify', [HouseOwnerApiController::class, 'requestIssueCompletion']);
    Route::post('/issue-reports/update-issue-status', [HouseOwnerApiController::class, 'completeIssueReportByServiceProvider']);
});
Route::get('/service-specializations', [HouseOwnerApiController::class, 'fetchServiceSpecialization']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthApiController::class, 'register']);
Route::get('/verify-email/{token}', [AuthApiController::class, 'verifyEmail']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
Route::post('/verify-code', [AuthApiController::class, 'verifyCode']);
Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);
