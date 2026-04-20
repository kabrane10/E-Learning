<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumCategoryController;
use App\Http\Controllers\ForumTopicController;
use App\Http\Controllers\ForumPostController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\SocialAuthController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Http\Controllers\Admin\ForumController as AdminForumController;
use App\Http\Controllers\Admin\GamificationController as AdminGamificationController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\ChapterController;
use App\Http\Controllers\Instructor\LessonController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\LearningController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\BookmarkController;
use App\Http\Controllers\ReviewController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $featuredCourses = App\Models\Course::where('is_published', true)
        ->with(['instructor'])
        ->withCount(['lessons', 'reviews'])
        ->latest()
        ->take(6)
        ->get();
    
    $coursesCount = App\Models\Course::where('is_published', true)->count();
    $categories = App\Models\Course::where('is_published', true)
        ->distinct()
        ->pluck('category')
        ->take(8);
    
    return view('welcome', compact('featuredCourses', 'coursesCount', 'categories'));
})->name('welcome');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/courses/{course}/review', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/courses/{course}/review', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Routes d'Authentification
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Routes Dashboard Principal
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('instructor')) {
        return redirect()->route('instructor.courses.index');
    } else {
        return redirect()->route('student.my-courses');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Routes Étudiant
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:student,admin'])->prefix('student')->name('student.')->group(function () {
    Route::get('/my-courses', [DashboardController::class, 'myCourses'])->name('my-courses');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])->name('enroll');
    Route::delete('/unenroll/{course}', [EnrollmentController::class, 'destroy'])->name('unenroll');
    
    Route::get('/learn/{course}', [LearningController::class, 'index'])->name('learn');
    Route::get('/learn/{course}/{lesson}', [LearningController::class, 'show'])->name('learn.lesson');
    Route::post('/learn/{course}/{lesson}/note', [LearningController::class, 'saveNote'])->name('learn.note');
    
    Route::post('/progress/{course}/{lesson}/complete', [ProgressController::class, 'markComplete'])->name('progress.complete');
    Route::post('/progress/{course}/update', [ProgressController::class, 'updateProgress'])->name('progress.update');
    Route::post('/progress/{course}/{lesson}/duration', [ProgressController::class, 'saveDuration'])->name('progress.duration');
    
    Route::get('quiz/{quiz}/take/{enrollment}', fn() => view('student.quiz-coming-soon'))->name('quiz.take');
    Route::get('/quiz/{quiz}/results/{attempt}', [ProgressController::class, 'quizResults'])->name('quiz.results');
    Route::get('/quiz/history', [ProgressController::class, 'quizHistory'])->name('quiz.history');
    
    Route::get('/certificate/{course}', [CertificateController::class, 'show'])->name('certificate');
    Route::get('/certificate/{course}/download', [CertificateController::class, 'download'])->name('certificate.download');
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates');
    
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks');
    Route::post('/bookmark/{course}', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
    Route::delete('/bookmarks/clear', [BookmarkController::class, 'clear'])->name('bookmarks.clear');
});

/*
|--------------------------------------------------------------------------
| Routes Formateur
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:instructor,admin'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorCourseController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [InstructorCourseController::class, 'analytics'])->name('analytics');
    Route::get('/earnings', [InstructorCourseController::class, 'earnings'])->name('earnings');
    
    Route::resource('courses', InstructorCourseController::class);
    Route::post('courses/{course}/toggle-publish', [InstructorCourseController::class, 'togglePublish'])->name('courses.toggle-publish');
    Route::post('courses/{course}/duplicate', [InstructorCourseController::class, 'duplicate'])->name('courses.duplicate');
    Route::get('courses/{course}/analytics', [InstructorCourseController::class, 'courseAnalytics'])->name('courses.analytics');
    Route::get('courses/{course}/students', [InstructorCourseController::class, 'students'])->name('courses.students');
    
    Route::post('courses/{course}/chapters', [ChapterController::class, 'store'])->name('chapters.store');
    Route::put('courses/{course}/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('courses/{course}/chapters/{chapter}', [ChapterController::class, '41destroy'])->name('chapters.destroy');
    Route::post('courses/{course}/chapters/reorder', [ChapterController::class, 'reorder'])->name('chapters.reorder');
    
    Route::post('courses/{course}/lessons/{chapter?}', [LessonController::class, 'store'])->name('lessons.store');
    Route::put('courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::post('courses/{course}/lessons/reorder', [LessonController::class, 'reorder'])->name('lessons.reorder');
    Route::post('lessons/{lesson}/preview', [LessonController::class, 'preview'])->name('lessons.preview');
    
    Route::get('lessons/{lesson}/quiz/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('lessons/{lesson}/quiz', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::post('quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
    Route::put('questions/{question}', [QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
    Route::delete('questions/{question}', [QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');
    Route::post('quizzes/{quiz}/questions/reorder', [QuizController::class, 'reorderQuestions'])->name('quizzes.questions.reorder');
    Route::get('quizzes/{quiz}/attempts', [QuizController::class, 'attempts'])->name('quizzes.attempts');
    Route::get('quizzes/{quiz}/statistics', [QuizController::class, 'statistics'])->name('quizzes.statistics');
    
    Route::get('courses/{course}/reviews', [InstructorCourseController::class, 'reviews'])->name('courses.reviews');
    Route::post('courses/{course}/reviews/{review}/reply', [InstructorCourseController::class, 'replyReview'])->name('courses.reviews.reply');
});

/*
|--------------------------------------------------------------------------
| Routes Administrateur
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [AdminDashboardController::class, 'search'])->name('search');
    Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('notifications');
    
    // Utilisateurs
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/change-role', [AdminUserController::class, 'changeRole'])->name('users.change-role');
    Route::post('users/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('users.impersonate');
    Route::post('users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('users/export/{format?}', [AdminUserController::class, 'export'])->name('users.export');
    
    // Cours
    Route::resource('courses', AdminCourseController::class);
    Route::post('courses/{course}/toggle-publish', [AdminCourseController::class, 'togglePublish'])->name('courses.toggle-publish');
    Route::post('courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
    Route::post('courses/{course}/feature', [AdminCourseController::class, 'feature'])->name('courses.feature');
    Route::post('courses/bulk-action', [AdminCourseController::class, 'bulkAction'])->name('courses.bulk-action');
    Route::get('courses/export/{format?}', [AdminCourseController::class, 'export'])->name('courses.export');
    
    // Catégories
    Route::resource('categories', AdminCategoryController::class);
    Route::post('categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('categories/reorder', [AdminCategoryController::class, 'reorder'])->name('categories.reorder');
    Route::post('categories/bulk-action', [AdminCategoryController::class, 'bulkAction'])->name('categories.bulk-action');
    
    // Quiz
    Route::resource('quizzes', AdminQuizController::class);
    Route::get('quizzes/{quiz}/statistics', [AdminQuizController::class, 'statistics'])->name('quizzes.statistics');
    Route::get('quizzes/{quiz}/attempts', [AdminQuizController::class, 'attempts'])->name('quizzes.attempts');
    Route::post('quizzes/{quiz}/toggle-status', [AdminQuizController::class, 'toggleStatus'])->name('quizzes.toggle-status');
    Route::post('quizzes/bulk-action', [AdminQuizController::class, 'bulkAction'])->name('quizzes.bulk-action');
    
    // Rapports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/enrollments', [AdminReportController::class, 'enrollments'])->name('reports.enrollments');
    Route::get('/reports/completions', [AdminReportController::class, 'completions'])->name('reports.completions');
    Route::get('/reports/revenue', [AdminReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/export/{type}/{format}', [AdminReportController::class, 'export'])->name('reports.export');
    
    // Analytique
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/retention', [AdminAnalyticsController::class, 'retention'])->name('analytics.retention');
    Route::get('/analytics/engagement', [AdminAnalyticsController::class, 'engagement'])->name('analytics.engagement');
    Route::get('/analytics/geography', [AdminAnalyticsController::class, 'geography'])->name('analytics.geography');
    Route::get('/analytics/devices', [AdminAnalyticsController::class, 'devices'])->name('analytics.devices');
    
    // Paramètres
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
    Route::post('/settings/general', [AdminSettingController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/email', [AdminSettingController::class, 'updateEmail'])->name('settings.email');
    Route::post('/settings/payment', [AdminSettingController::class, 'updatePayment'])->name('settings.payment');
    Route::post('/settings/security', [AdminSettingController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/social', [AdminSettingController::class, 'updateSocial'])->name('settings.social');
    Route::post('/settings/seo', [AdminSettingController::class, 'updateSeo'])->name('settings.seo');
    Route::post('/settings/email/test', [AdminSettingController::class, 'testEmail'])->name('settings.email.test');
    Route::post('/settings/cache/clear', [AdminSettingController::class, 'clearCache'])->name('settings.cache.clear');
    
    // Logs
    Route::get('/logs', [AdminLogController::class, 'index'])->name('logs');
    Route::get('/logs/activity', [AdminLogController::class, 'activity'])->name('logs.activity');
    Route::get('/logs/search', [AdminLogController::class, 'search'])->name('logs.search');
    Route::get('/logs/export', [AdminLogController::class, 'export'])->name('logs.export');
    Route::get('/logs/system', [AdminLogController::class, 'system'])->name('logs.system');
    Route::get('/logs/{date}/{filename}', [AdminLogController::class, 'show'])->name('logs.show');
    Route::get('/logs/download', [AdminLogController::class, 'download'])->name('logs.download');
    Route::post('/logs/clear', [AdminLogController::class, 'clear'])->name('logs.clear');
    Route::delete('/logs/{filename}', [AdminLogController::class, 'destroy'])->name('logs.destroy');

    // Admin Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [AdminChatController::class, 'index'])->name('index');
        Route::get('/conversations', [AdminChatController::class, 'conversations'])->name('conversations');
        Route::get('/conversations/{conversation}', [AdminChatController::class, 'showConversation'])->name('conversations.show');
        Route::delete('/conversations/{conversation}', [AdminChatController::class, 'destroyConversation'])->name('conversations.destroy');
        Route::get('/messages', [AdminChatController::class, 'messages'])->name('messages');
        Route::delete('/messages/{message}', [AdminChatController::class, 'destroyMessage'])->name('messages.destroy');
        Route::get('/settings', [AdminChatController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminChatController::class, 'updateSettings'])->name('settings.update');
        Route::get('/export', [AdminChatController::class, 'export'])->name('export');
    });

    // Admin Forum
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/categories', [AdminForumController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminForumController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [AdminForumController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminForumController::class, 'destroyCategory'])->name('categories.destroy');
        
        Route::get('/topics', [AdminForumController::class, 'topics'])->name('topics.index');
        Route::delete('/topics/{topic}', [AdminForumController::class, 'destroyTopic'])->name('topics.destroy');
        Route::post('/topics/{topic}/pin', [AdminForumController::class, 'togglePin'])->name('topics.pin');
        Route::post('/topics/{topic}/close', [AdminForumController::class, 'toggleClose'])->name('topics.close');
        Route::post('/topics/bulk-action', [AdminForumController::class, 'bulkActionTopics'])->name('topics.bulk-action');
        
        Route::get('/posts', [AdminForumController::class, 'posts'])->name('posts.index');
        Route::delete('/posts/{post}', [AdminForumController::class, 'destroyPost'])->name('posts.destroy');
        Route::post('/posts/{post}/solution', [AdminForumController::class, 'markAsSolution'])->name('posts.solution');
        Route::post('/posts/bulk-action', [AdminForumController::class, 'bulkActionPosts'])->name('posts.bulk-action');
        
        Route::get('/statistics', [AdminForumController::class, 'statistics'])->name('statistics');
    });

    // Admin Gamification
    Route::prefix('gamification')->name('gamification.')->group(function () {
        Route::get('/', [AdminGamificationController::class, 'index'])->name('index');
        
        // Badges
        Route::get('/badges', [AdminGamificationController::class, 'badges'])->name('badges');
        Route::post('/badges', [AdminGamificationController::class, 'storeBadge'])->name('badges.store');
        Route::get('/badges/{badge}', [AdminGamificationController::class, 'showBadge'])->name('badges.show');
        Route::put('/badges/{badge}', [AdminGamificationController::class, 'updateBadge'])->name('badges.update');
        Route::delete('/badges/{badge}', [AdminGamificationController::class, 'destroyBadge'])->name('badges.destroy');
        Route::post('/badges/{badge}/toggle', [AdminGamificationController::class, 'toggleBadge'])->name('badges.toggle');
        
        // Achievements
        Route::get('/achievements', [AdminGamificationController::class, 'achievements'])->name('achievements');
        Route::post('/achievements', [AdminGamificationController::class, 'storeAchievement'])->name('achievements.store');
        Route::get('/achievements/{achievement}', [AdminGamificationController::class, 'showAchievement'])->name('achievements.show');
        Route::put('/achievements/{achievement}', [AdminGamificationController::class, 'updateAchievement'])->name('achievements.update');
        Route::delete('/achievements/{achievement}', [AdminGamificationController::class, 'destroyAchievement'])->name('achievements.destroy');
        Route::post('/achievements/{achievement}/toggle', [AdminGamificationController::class, 'toggleAchievement'])->name('achievements.toggle');
        
        // Levels
        Route::get('/levels', [AdminGamificationController::class, 'levels'])->name('levels');
        Route::post('/levels', [AdminGamificationController::class, 'storeLevel'])->name('levels.store');
        Route::get('/levels/{level}', [AdminGamificationController::class, 'showLevel'])->name('levels.show');
        Route::put('/levels/{level}', [AdminGamificationController::class, 'updateLevel'])->name('levels.update');
        Route::delete('/levels/{level}', [AdminGamificationController::class, 'destroyLevel'])->name('levels.destroy');
    });
});

// Arrêter l'impersonation
Route::post('/admin/stop-impersonating', [AdminUserController::class, 'stopImpersonating'])
    ->middleware(['auth'])->name('admin.stop-impersonating');

/*
|--------------------------------------------------------------------------
| Routes Notifications
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');
    
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    })->name('notifications.mark-all-read');
    
    Route::post('/notifications/{id}/mark-read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    })->name('notifications.mark-read');
    
    Route::delete('/notifications/{id}', function ($id) {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back()->with('success', 'Notification supprimée.');
    })->name('notifications.destroy');
    
    Route::post('/notifications/subscribe', function () {
        return response()->json(['success' => true]);
    })->name('notifications.subscribe');
});

/*
|--------------------------------------------------------------------------
| Routes de Profil
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/delete', [ProfileController::class, 'confirmDelete'])->name('profile.confirm-delete');
});

/*
|--------------------------------------------------------------------------
| Routes d'Authentification Sociale
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

Route::middleware(['auth'])->prefix('auth')->name('social.')->group(function () {
    Route::get('/{provider}/link', [SocialAuthController::class, 'link'])->name('link');
    Route::get('/{provider}/link/callback', [SocialAuthController::class, 'linkCallback'])->name('callback.link');
    Route::post('/{provider}/unlink', [SocialAuthController::class, 'unlink'])->name('unlink');
});

/*
|--------------------------------------------------------------------------
| Pages Légales
|--------------------------------------------------------------------------
*/

Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/cookies', 'legal.cookies')->name('cookies');
Route::view('/about', 'legal.about')->name('about');
Route::view('/contact', 'legal.contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Routes du Chat
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [ChatController::class, 'index'])->name('chat.index');
    
    Route::resource('conversations', ConversationController::class)->except(['edit']);
    Route::post('conversations/{conversation}/participants', [ConversationController::class, 'addParticipants'])->name('conversations.participants.add');
    Route::delete('conversations/{conversation}/participants/{user}', [ConversationController::class, 'removeParticipant'])->name('conversations.participants.remove');
    Route::post('conversations/{conversation}/leave', [ConversationController::class, 'leave'])->name('conversations.leave');
    Route::post('conversations/{conversation}/mute', [ConversationController::class, 'toggleMute'])->name('conversations.mute');
    Route::post('conversations/{conversation}/pin', [ConversationController::class, 'togglePin'])->name('conversations.pin');
    
    Route::get('chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('chat/{conversation}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::post('chat/{conversation}/typing', [ChatController::class, 'typing'])->name('chat.typing');
    Route::post('chat/{conversation}/upload', [ChatController::class, 'uploadFile'])->name('chat.upload');
});

/*
|--------------------------------------------------------------------------
| Routes Gamification
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('gamification')->name('gamification.')->group(function () {
    Route::get('/', [GamificationController::class, 'index'])->name('index');
    Route::get('/leaderboard', [GamificationController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/badges', [GamificationController::class, 'badges'])->name('badges');
    Route::post('/claim/{achievement}', [GamificationController::class, 'claim'])->name('claim');
});

/*
|--------------------------------------------------------------------------
| Routes Forum
|--------------------------------------------------------------------------
*/

Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('/search', [ForumController::class, 'search'])->name('search');
    
    Route::get('/categories', [ForumCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [ForumCategoryController::class, '50create'])->name('categories.create');
    Route::post('/categories', [ForumCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [ForumCategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [ForumCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [ForumCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [ForumCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/subscribe', [ForumCategoryController::class, 'subscribe'])->name('categories.subscribe');
    
    Route::get('/topics', [ForumTopicController::class, 'index'])->name('topics.index');
    Route::get('/topics/create', [ForumTopicController::class, 'create'])->name('topics.create');
    Route::post('/topics', [ForumTopicController::class, 'store'])->name('topics.store');
    Route::get('/topics/{topic}', [ForumTopicController::class, 'show'])->name('topics.show');
    Route::get('/topics/{topic}/edit', [ForumTopicController::class, 'edit'])->name('topics.edit');
    Route::put('/topics/{topic}', [ForumTopicController::class, 'update'])->name('topics.update');
    Route::delete('/topics/{topic}', [ForumTopicController::class, 'destroy'])->name('topics.destroy');
    Route::post('/topics/{topic}/pin', [ForumTopicController::class, 'togglePin'])->name('topics.pin');
    Route::post('/topics/{topic}/close', [ForumTopicController::class, 'toggleClose'])->name('topics.close');
    Route::post('/topics/{topic}/subscribe', [ForumTopicController::class, 'subscribe'])->name('topics.subscribe');
    
    Route::post('/topics/{topic}/posts', [ForumPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [ForumPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [ForumPostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [ForumPostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [ForumPostController::class, 'like'])->name('posts.like');
    Route::post('/topics/{topic}/posts/{post}/solution', [ForumPostController::class, 'markAsSolution'])->name('posts.solution');
});

/*
|--------------------------------------------------------------------------
| Route de Fallback
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});