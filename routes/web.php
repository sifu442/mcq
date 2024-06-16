<?php

use App\Livewire\ExamPage;
use App\Livewire\ExamResults;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
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
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard/{course}/exams', [DashboardController::class, 'exams'])->name('dashboard.exams');


Route::middleware(['auth:sanctum', 'verified'])->get('/exam/{examId}', ExamPage::class)->name('exam.page');
Route::middleware(['auth:sanctum', 'verified'])->get('/exam-results/{examId}', ExamResults::class)->name('exam-results');

Route::middleware(['auth:sanctum', 'verified'])->get('/course/{course}/buy', [CourseController::class, 'buy'])->name('course.buy');
Route::post('/course/{course}/purchase', [CourseController::class, 'purchase'])->name('course.purchase');


