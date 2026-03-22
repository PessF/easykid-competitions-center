<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\Admin\GameTypeController;
use App\Http\Controllers\Admin\RobotModelController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\CategorySettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminController;
// 🟢 1. ดึง CompetitionClassController เข้ามาใช้งาน
use App\Http\Controllers\Admin\CompetitionClassController; 
use Illuminate\Support\Facades\Route;

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

// --- กลุ่มที่ 1: สำหรับ USER ทั่วไปที่กรอกโปรไฟล์ "เสร็จแล้ว" ---
Route::middleware(['auth', 'verified', 'user_only', 'check.profile', 'revalidate'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

// --- กลุ่มที่ 2: สำหรับ USER ที่ "ยังกรอกโปรไฟล์ไม่เสร็จ" (หน้า Setup) ---
Route::middleware(['auth', 'verified', 'user_only', 'revalidate'])->group(function () {
    Route::get('/setup-profile', [ProfileSetupController::class, 'index'])->name('profile.setup');
    Route::post('/setup-profile', [ProfileSetupController::class, 'store'])->name('profile.setup.store');
});

// --- กลุ่มที่ 3: สำหรับ ADMIN เท่านั้น ---
Route::middleware(['auth', 'admin', 'revalidate'])->prefix('admin')->name('admin.')->group(function () {

    
    // Dashboard & Users
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users.index');

    // Category Settings
    Route::get('/category-settings', [CategorySettingController::class, 'index'])->name('category-settings');
    Route::resource('game-types', GameTypeController::class)->except(['index', 'show']);
    Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    
    // Robot Models
    Route::get('/robot-models/{id}/image', [RobotModelController::class, 'showImage'])->name('robot-models.image');
    Route::resource('robot-models', RobotModelController::class)->except(['show']);

    // Competitions (งานแข่งขันหลัก)
    Route::get('/competitions/{id}/banner', [CompetitionController::class, 'showBanner'])->name('competitions.banner');
    Route::resource('competitions', CompetitionController::class);

    // Competition Classes (รายการย่อย)
    // Proxy สำหรับดึงรูปภาพและ PDF (ต้องวางก่อน resource)
    Route::get('/competitions/{competition}/classes/{class}/picture', [CompetitionClassController::class, 'showPicture'])->name('competitions.classes.picture');
    Route::get('/competitions/{competition}/classes/{class}/rule', [CompetitionClassController::class, 'showRule'])->name('competitions.classes.rule');
    
    //ของรายการย่อย
    Route::resource('competitions.classes', CompetitionClassController::class)->except(['show']);

    // 5. Teams & Matches
    Route::get('/teams', fn() => view('admin.teams.index'))->name('teams.index');
    Route::get('/matches', fn() => view('admin.matches.index'))->name('matches.index');
});

require __DIR__.'/auth.php';