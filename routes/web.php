<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExperienceCenterController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SiteVisitController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BuilderController;
use App\Http\Controllers\ArchitectController;

/*
|--------------------------------------------------------------------------
| Web Routes - Y5Home CRM
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Leads
    Route::resource('leads', LeadController::class);
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('/leads/{lead}/assign', [LeadController::class, 'assign'])->name('leads.assign');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Experience Centers
    Route::resource('experience-centers', ExperienceCenterController::class);
    Route::patch('/experience-centers/{center}/status', [ExperienceCenterController::class, 'updateStatus'])->name('experience-centers.status');

    // Opportunities
    Route::resource('opportunities', OpportunityController::class);
    Route::patch('/opportunities/{opportunity}/stage', [OpportunityController::class, 'updateStage'])->name('opportunities.stage');

    // Quotations
    Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
    Route::delete('/quotations/{quotation}/documents/{document}', [QuotationController::class, 'destroyDocument'])->name('quotations.documents.destroy');
    Route::resource('quotations', QuotationController::class);
    Route::patch('/quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.status');

    // Site Visits
    Route::resource('site-visits', SiteVisitController::class);

    // Documents
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/lead-source', [ReportController::class, 'leadSource'])->name('reports.lead-source');
    Route::get('/reports/experience-center', [ReportController::class, 'experienceCenter'])->name('reports.experience-center');
    Route::get('/reports/sales-pipeline', [ReportController::class, 'salesPipeline'])->name('reports.sales-pipeline');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    // Users (Super Admin only)
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/users/stop-impersonate', [UserController::class, 'stopImpersonate'])->name('users.stop-impersonate');

    // Experience Center Staff Management
    Route::post('/experience-centers/{experience_center}/add-staff', [ExperienceCenterController::class, 'addStaff'])->name('experience-centers.add-staff');
    Route::delete('/experience-centers/{experience_center}/remove-staff/{user}', [ExperienceCenterController::class, 'removeStaff'])->name('experience-centers.remove-staff');

    // Builders & Architects
    Route::resource('builders', BuilderController::class);
    Route::resource('architects', ArchitectController::class);

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});
