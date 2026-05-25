<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Admin\AssessmentBatchController;
use App\Http\Controllers\Web\Admin\AssessmentManagementController;
use App\Http\Controllers\Web\Admin\CandidateController;
use App\Http\Controllers\Web\Admin\CompanyController;
use App\Http\Controllers\Web\Admin\HrUserController;
use App\Http\Controllers\Web\Admin\OrganizationController;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\BatchGuestController;
use App\Http\Controllers\Web\GuestShareController;
use App\Http\Controllers\Web\ShareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::get('/assessment/guest', [BatchGuestController::class, 'showEntry'])->name('shared.batch.entry');
Route::post('/assessment/guest/verify', [BatchGuestController::class, 'verifyEntry'])
    ->middleware('throttle:5,15')
    ->name('shared.batch.entry.verify');

Route::prefix('share')->name('shared.')->group(function () {
    Route::prefix('batch')->name('batch.')->group(function () {
        Route::get('/{token}', [BatchGuestController::class, 'showForm'])->name('form');
        Route::post('/{token}/verify', [BatchGuestController::class, 'verify'])
            ->middleware('throttle:5,15')
            ->name('verify');
        Route::middleware('batch.guest')->group(function () {
            Route::get('/{token}/portal', [BatchGuestController::class, 'portal'])->name('portal');
            Route::get('/{token}/assessments/{slug}/take', [BatchGuestController::class, 'take'])->name('take');
            Route::post('/{token}/assessments/{slug}/submit', [BatchGuestController::class, 'submit'])->name('submit');
            Route::get('/{token}/assessments/{slug}/result/{id}', [BatchGuestController::class, 'result'])->name('result');
        });
    });

    Route::get('/{token}', [GuestShareController::class, 'showForm'])->name('result.form');
    Route::post('/{token}/verify', [GuestShareController::class, 'verify'])
        ->middleware('throttle:5,15')
        ->name('result.verify');
    Route::middleware('share.guest')->group(function () {
        Route::get('/{token}/result', [GuestShareController::class, 'result'])->name('result.view');
        Route::get('/{token}/download', [GuestShareController::class, 'download'])->name('result.download');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        abort_unless(auth()->user()?->can('admin.dashboard'), 403);

        return redirect()->route('admin.dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::middleware('permission:profile.view_own')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    });

    Route::middleware('permission:profile.update_own')->group(function () {
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.destroy');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

Route::middleware(['auth', 'permission:admin.dashboard', 'organization.scope'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:results.share_create')->group(function () {
        Route::post('/results/{result}/share', [ShareController::class, 'store'])->name('results.share');
    });

    Route::middleware('permission:results.share_revoke')->group(function () {
        Route::delete('/shares/{share}', [ShareController::class, 'revoke'])->name('shares.revoke');
        Route::post('/shares/{share}/regenerate', [ShareController::class, 'regenerate'])->name('shares.regenerate');
    });

    // HR/Admin: perusahaan sendiri
    Route::middleware('permission:organizations.view_own')->group(function () {
        Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
        Route::patch('/company', [CompanyController::class, 'update'])->name('company.update');
    });

    // HR/Admin: kandidat/karyawan
    Route::middleware('permission:candidates.view')->group(function () {
        Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
    });
    Route::middleware('permission:candidates.create')->group(function () {
        Route::get('/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
        Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
    });
    Route::middleware('permission:candidates.update')->group(function () {
        Route::get('/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
        Route::patch('/candidates/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
    });
    Route::middleware('permission:candidates.delete')->group(function () {
        Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');
    });

    // HR/Admin: batch assessment
    Route::middleware('permission:batches.view')->group(function () {
        Route::get('/batches', [AssessmentBatchController::class, 'index'])->name('batches.index');
    });
    Route::middleware('permission:batches.create')->group(function () {
        Route::get('/batches/create', [AssessmentBatchController::class, 'create'])->name('batches.create');
        Route::post('/batches', [AssessmentBatchController::class, 'store'])->name('batches.store');
    });
    Route::middleware('permission:batches.view')->group(function () {
        Route::get('/batches/{batch}', [AssessmentBatchController::class, 'show'])->name('batches.show');
        Route::get('/batches/{batch}/candidates/{candidate}', [AssessmentBatchController::class, 'showCandidate'])->name('batches.candidates.show');
    });
    Route::middleware('permission:results.view_org')->group(function () {
        Route::get('/batches/{batch}/candidates/{candidate}/results/{result}', [AssessmentBatchController::class, 'showCandidateResult'])
            ->name('batches.candidates.results.show');
    });
    Route::middleware('permission:batches.create')->group(function () {
        Route::post('/batches/{batch}/candidates/{candidate}/share', [AssessmentBatchController::class, 'shareCandidate'])->name('batches.candidates.share');
    });
    Route::middleware('permission:batches.update')->group(function () {
        Route::patch('/batches/{batch}/close', [AssessmentBatchController::class, 'close'])->name('batches.close');
    });

    // HR/Admin: konfigurasi assessment perusahaan
    Route::middleware('permission:assessments.manage_org')->group(function () {
        Route::get('/assessments', [AssessmentManagementController::class, 'indexOrg'])->name('assessments.org.index');
        Route::get('/assessments/{orgAssessment}/edit', [AssessmentManagementController::class, 'editOrg'])->name('assessments.org.edit');
        Route::patch('/assessments/{orgAssessment}', [AssessmentManagementController::class, 'updateOrg'])->name('assessments.org.update');
    });
});

// Superadmin: sistem & multi-tenant
Route::middleware(['auth', 'permission:system.config'])->prefix('admin/system')->name('admin.system.')->group(function () {
    Route::view('/config', 'admin.system-config')->name('config');

    Route::middleware('permission:assessments.manage_global')->group(function () {
        Route::get('/assessments', [AssessmentManagementController::class, 'indexGlobal'])->name('assessments.index');
        Route::get('/assessments/{assessment}/edit', [AssessmentManagementController::class, 'editGlobal'])->name('assessments.edit');
        Route::patch('/assessments/{assessment}', [AssessmentManagementController::class, 'updateGlobal'])->name('assessments.update');
    });
});

Route::middleware(['auth', 'permission:organizations.manage'])->prefix('admin/organizations')->name('admin.organizations.')->group(function () {
    Route::get('/', [OrganizationController::class, 'index'])->name('index');
    Route::get('/create', [OrganizationController::class, 'create'])->name('create');
    Route::post('/', [OrganizationController::class, 'store'])->name('store');
    Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('edit');
    Route::patch('/{organization}', [OrganizationController::class, 'update'])->name('update');
    Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'permission:users.create', 'permission:users.assign_role'])->prefix('admin/hr-users')->name('admin.hr-users.')->group(function () {
    Route::get('/', [HrUserController::class, 'index'])->name('index');
    Route::get('/create', [HrUserController::class, 'create'])->name('create');
    Route::post('/', [HrUserController::class, 'store'])->name('store');
    Route::get('/{hrUser}/edit', [HrUserController::class, 'edit'])->name('edit');
    Route::patch('/{hrUser}', [HrUserController::class, 'update'])->name('update');
});

require __DIR__.'/auth.php';
