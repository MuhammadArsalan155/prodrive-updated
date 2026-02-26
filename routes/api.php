<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\RegisterStudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// routes/api.php
Route::get('/courses', [RegisterStudentController::class, 'getCourses']);
Route::get('/courses/{courseId}/instructors', [RegisterStudentController::class, 'getCourseInstructors']);
Route::get('/courses/{courseId}/instructors/{instructorId}/schedules', [RegisterStudentController::class, 'getAvailableSchedules']);
Route::get('/payment-methods', [RegisterStudentController::class, 'getPaymentMethods']);
//Route::post('/register', [RegisterStudentController::class, 'register']);

Route::get('/course-payment-details/{courseId}', 
    [PaymentController::class, 'getCoursePaymentDetails']);
Route::post('/process-payment', 
    [PaymentController::class, 'processPayment']);

    Route::post('/submit-student', [RegisterStudentController::class, 'register']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
