<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\User\CompetitionUserController;
use App\Http\Controllers\Admin\GameTypeController;
use App\Http\Controllers\Admin\RobotModelController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminTeamController;
use App\Http\Controllers\Admin\CategorySettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CompetitionClassController;
use App\Http\Controllers\User\TeamController; 
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Http\Controllers\TicketVerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. หน้าแรกสุดให้ไปที่ Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Google Login (Public)
Route::controller(SocialiteController::class)->group(function () {
    Route::get('/auth/google', 'googleLogin')->name('auth.google');
    Route::get('/auth/google-callback', 'googleAuth')->name('auth.google-callback');
});

Route::get('/verify/ticket/{reg_no}', [TicketVerificationController::class, 'verify'])
    ->name('verify.ticket')
    ->middleware('signed');

Route::post('/verify/ticket/{reg_no}/check-in', [TicketVerificationController::class, 'checkIn'])
    ->name('verify.ticket.checkin')
    ->middleware('signed');

// --- เส้นทางที่ใช้ร่วมกันได้ทั้ง Admin และ User ---
Route::middleware(['auth', 'revalidate'])->group(function () {
    // โหลดรูปโปรไฟล์ (Avatar) จาก Google Drive แบบ Stream
    Route::get('/avatar/{id}', [ProfileController::class, 'showAvatar'])->name('avatar.show');
    
    // โหลดไฟล์กติกา (Rule PDF) จาก Google Drive เพื่อให้ User เข้ามาโหลดอ่านได้
    Route::get('/competitions/{competition}/classes/{class}/rule', [CompetitionClassController::class, 'showRule'])->name('competitions.classes.rule');
});

// --- กลุ่มที่ 1: สำหรับ USER ทั่วไปที่กรอกโปรไฟล์ "เสร็จแล้ว" ---
Route::middleware(['auth', 'verified', 'user_only', 'check.profile', 'revalidate'])->group(function () {
    
    // Dashboard & Show
    Route::get('/dashboard', [CompetitionUserController::class, 'index'])->name('user.dashboard');
    Route::get('/competitions/{id}', [CompetitionUserController::class, 'show'])->name('user.competitions.show');

    // Profile Settings
    Route::get('/profile', function() {
        return redirect()->route('user.dashboard');
    })->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Teams Management (จบในหน้าเดียวตามที่เราคุยกัน)
    Route::resource('/teams', TeamController::class)->except(['create', 'show'])->names('user.teams');

    // Policies & Terms
    Route::get('/privacy-policy', function () {
        return view('user.privacy');
    })->name('privacy.policy');

    Route::get('/terms-of-service', function () {
        return view('user.terms');
    })->name('terms.service');

    // ระบบรับสมัครแข่งขัน
    Route::post('/competitions/{competition}/classes/{class}/register', [CompetitionUserController::class, 'register'])
             ->name('competitions.classes.register');

    Route::get('/registrations', [CompetitionUserController::class, 'myRegistrations'])->name('user.registrations');
    
    // เปลี่ยนเป็น Route ของระบบ Group Payment (Cart System)
    Route::post('/my-registrations/group-payment', [CompetitionUserController::class, 'submitGroupPayment'])->name('user.registrations.payment');

    Route::delete('/my-registrations/{id}/delete', [CompetitionUserController::class, 'destroy'])->name('user.registrations.destroy');
    
    // สำหรับเปิดหน้าบัตร E-Ticket ของ User
    Route::get('/my-registrations/{id}/e-ticket', [\App\Http\Controllers\User\CompetitionUserController::class, 'eTicket'])->name('user.registrations.e-ticket');
});


// --- กลุ่มที่ 2: สำหรับ USER ที่ "ยังกรอกโปรไฟล์ไม่เสร็จ" (หน้า Setup) ---
Route::middleware(['auth', 'verified', 'user_only', 'revalidate'])->group(function () {
    Route::get('/setup-profile', [ProfileSetupController::class, 'index'])->name('profile.setup');
    Route::post('/setup-profile', [ProfileSetupController::class, 'store'])->name('profile.setup.store');
});


// --- กลุ่มที่ 3: สำหรับระบบหลังบ้าน (Admin และ Staff) ---
// ด่านที่ 1: เปลี่ยนจาก admin เป็น admin_or_staff เพื่อให้ทีมงานเข้ามาในโซนนี้ได้
Route::middleware(['auth', 'admin_or_staff', 'revalidate'])->prefix('admin')->name('admin.')->group(function () {

    // โซนส่วนรวม: เข้าได้ทั้ง Admin และ Staff
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    
    // Dashboard (ดูสถิติรวม)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Teams (ดูรายชื่อทีมผู้สมัครทั้งหมดได้ แต่จะไม่มีปุ่มลบ)
    Route::get('/teams', [AdminTeamController::class, 'index'])->name('teams.index');

    // โซนหวงห้าม: ด่านที่ 2 เข้าได้เฉพาะ "Admin ตัวจริง" เท่านั้น!
    Route::middleware(['admin'])->group(function () {
        
        // ระบบจัดการผู้ใช้งาน (User Management)
        Route::get('/users', [AdminController::class, 'manageUsers'])->name('users.index');
        Route::patch('/users/{id}/role', [AdminController::class, 'updateRole'])->name('users.updateRole'); // <-- เพิ่มตัวเปลี่ยนสิทธิ์
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        
        // ลบทีม (Staff ดูได้ แต่กดลบผ่าน URL ไม่ได้ โดนดักตรงนี้)
        Route::delete('/teams/{id}', [AdminTeamController::class, 'destroy'])->name('teams.destroy');

        // Category Settings & Game Rules
        Route::get('/category-settings', [CategorySettingController::class, 'index'])->name('category-settings');
        Route::resource('game-types', GameTypeController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);

        // Robot Models
        Route::get('/robot-models/{id}/image', [RobotModelController::class, 'showImage'])->name('robot-models.image');
        Route::resource('robot-models', RobotModelController::class)->except(['show']);

        // Competitions (งานแข่งขันหลักและรายการย่อย)
        Route::resource('competitions', CompetitionController::class);

        Route::patch('/competitions/{competition}/classes/{class}/toggle-status', [CompetitionClassController::class, 'toggleStatus'])
            ->name('competitions.classes.toggle-status');

        Route::resource('competitions.classes', CompetitionClassController::class)->except(['show']);

        // Payments (ตรวจบิลการเงิน Admin อนุมัติเท่านั้น)
        Route::get('/payments/{id}/slip', [AdminPaymentController::class, 'slip'])->name('payments.slip');
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'update']);
    });
});

require __DIR__.'/auth.php';