<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BadWordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserModerationController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MarkdownPreviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersonalDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [QuestionController::class, 'index'])->name('home');
Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/questions/suggestions', [QuestionController::class, 'suggestions'])
    ->middleware('throttle:30,1')->name('questions.suggestions');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Profile public view
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profile.show');

// Authenticated routes (create must register before GET /questions/{question} or "create" is treated as an id)
Route::middleware('auth')->group(function () {
    Route::middleware('not_suspended')->group(function () {
        // Questions
        Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('/questions', [QuestionController::class, 'store'])->middleware('throttle:10,1')->name('questions.store');
        Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::patch('/questions/{question}/status', [QuestionController::class, 'updateStatus'])->name('questions.status');
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

        // Answers
        Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])->middleware('throttle:20,1')->name('answers.store');
        Route::get('/answers/{answer}/edit', [AnswerController::class, 'edit'])->name('answers.edit');
        Route::put('/answers/{answer}', [AnswerController::class, 'update'])->name('answers.update');
        Route::delete('/answers/{answer}', [AnswerController::class, 'destroy'])->name('answers.destroy');
        Route::post('/answers/{answer}/best', [AnswerController::class, 'markAsBest'])->name('answers.markAsBest');

        // Votes and comments
        Route::post('/questions/{question}/vote', [VoteController::class, 'voteQuestion'])->middleware('throttle:60,1')->name('questions.vote');
        Route::post('/answers/{answer}/vote', [VoteController::class, 'voteAnswer'])->middleware('throttle:60,1')->name('answers.vote');
        Route::post('/answers/{answer}/comments', [CommentController::class, 'store'])->middleware('throttle:30,1')->name('comments.store');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // Reports
        Route::post('/questions/{question}/report', [ReportController::class, 'reportQuestion'])->middleware('throttle:10,1')->name('questions.report');
        Route::post('/answers/{answer}/report', [ReportController::class, 'reportAnswer'])->middleware('throttle:10,1')->name('answers.report');
    });

    Route::post('/questions/{question}/bookmark', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/questions/{question}/bookmark', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    Route::post('/questions/{question}/follow', [FollowController::class, 'storeQuestion'])->name('questions.follow');
    Route::delete('/questions/{question}/follow', [FollowController::class, 'destroyQuestion'])->name('questions.unfollow');
    Route::post('/tags/{tag}/follow', [FollowController::class, 'storeTag'])->name('tags.follow');
    Route::delete('/tags/{tag}/follow', [FollowController::class, 'destroyTag'])->name('tags.unfollow');
    Route::post('/markdown/preview', MarkdownPreviewController::class)->middleware('throttle:60,1')->name('markdown.preview');
    Route::get('/dashboard', [PersonalDashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/title', [ProfileController::class, 'updateTitle'])->name('profile.updateTitle');
});

Route::get('/questions/{question}/{slug?}', [QuestionController::class, 'show'])->name('questions.show');

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

    Route::post('/users/{user}/suspend', [UserModerationController::class, 'suspend'])->name('users.suspend');
    Route::delete('/users/{user}/suspend', [UserModerationController::class, 'restore'])->name('users.restore');
    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
});

require __DIR__.'/auth.php';
