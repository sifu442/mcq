<?php

use App\Livewire\ExamPage;
use App\Livewire\ExamResults;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;

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

Route::get('/exam/{examId}', ExamPage::class)->name('exam.page');

Route::get('/', HomeController::class)->name('home');

Route::get('/course/{course:slug}', [CourseController::class, 'show'])->name('course.show');

// Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
//          Route::get('/dashboard', function () {
//              return view('dashboard');
//          })->name('dashboard');
//      });

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/user/profile', function () {
        return view('profile.show');
    })->name('profile.show');


// web.php
Route::middleware(['auth:sanctum', 'verified'])->get('/courses/{course}/exams', [ExamController::class, 'exams'])->name('courses.exams');


Route::middleware(['auth:sanctum', 'verified'])->get('/exam/{examId}', ExamPage::class)->name('exam.page');
Route::middleware(['auth:sanctum', 'verified'])->get('/exam-results/{examId}', ExamResults::class)->name('exam-results');

Route::get('/course/{course}/purchase', [PurchaseController::class, 'show'])->name('course.purchase')->middleware('auth');
Route::post('/course/{course}/purchase', [PurchaseController::class, 'purchase'])->name('course.purchase.submit')->middleware('auth');
