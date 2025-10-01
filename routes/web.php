<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\BadWordController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [QuestionController::class, 'index'])->name('home');
Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');

// Profile public view
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profile.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Questions
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    // Answers
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])->name('answers.store');
    Route::get('/answers/{answer}/edit', [AnswerController::class, 'edit'])->name('answers.edit');
    Route::put('/answers/{answer}', [AnswerController::class, 'update'])->name('answers.update');
    Route::delete('/answers/{answer}', [AnswerController::class, 'destroy'])->name('answers.destroy');
    Route::post('/answers/{answer}/best', [AnswerController::class, 'markAsBest'])->name('answers.markAsBest');

    // Votes
    Route::post('/questions/{question}/vote', [VoteController::class, 'voteQuestion'])->name('questions.vote');
    Route::post('/answers/{answer}/vote', [VoteController::class, 'voteAnswer'])->name('answers.vote');

    // Comments
    Route::post('/answers/{answer}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Reports
    Route::post('/questions/{question}/report', [ReportController::class, 'reportQuestion'])->name('questions.report');
    Route::post('/answers/{answer}/report', [ReportController::class, 'reportAnswer'])->name('answers.report');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/title', [ProfileController::class, 'updateTitle'])->name('profile.updateTitle');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Reports Management
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');
    Route::delete('/reports/{report}/content', [AdminReportController::class, 'deleteReportable'])->name('reports.deleteContent');
    
    // Bad Words Management
    Route::get('/badwords', [BadWordController::class, 'index'])->name('badwords.index');
    Route::post('/badwords', [BadWordController::class, 'store'])->name('badwords.store');
    Route::post('/badwords/bulk', [BadWordController::class, 'bulkStore'])->name('badwords.bulkStore');
    Route::delete('/badwords/{badWord}', [BadWordController::class, 'destroy'])->name('badwords.destroy');
});

require __DIR__.'/auth.php';