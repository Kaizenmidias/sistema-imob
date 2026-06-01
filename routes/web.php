<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/imoveis', [HomeController::class, 'properties'])->name('properties');
Route::get('/imoveis/{slug}', [HomeController::class, 'showProperty'])->name('property.show');
Route::get('/venda-seu-imovel', [HomeController::class, 'sell'])->name('sell');
Route::get('/quem-somos', [PageController::class, 'about'])->name('about');
Route::get('/off-market', [PageController::class, 'offMarket'])->name('off-market');
Route::get('/gestao-exclusiva', [PageController::class, 'exclusiveManagement'])->name('exclusive-management');
Route::get('/calculadora', [CalculatorController::class, 'index'])->name('calculator');
Route::get('/avalie-seu-imovel', [PageController::class, 'evaluate'])->name('evaluate');
Route::get('/corretor-parceiro', [PageController::class, 'partnerAgent'])->name('partner-agent');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contato', [ContactController::class, 'index'])->name('contact');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/properties', [AdminController::class, 'properties'])->name('properties');
    Route::get('/properties/create', [AdminController::class, 'createProperty'])->name('properties.create');
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
    Route::get('/appearance', [AdminController::class, 'appearance'])->name('appearance');
    Route::get('/layout', [AdminController::class, 'layout'])->name('layout');
    Route::get('/pages', [AdminController::class, 'pages'])->name('pages');
    Route::get('/pages/{page}', [AdminController::class, 'editPage'])->name('pages.edit');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});

// Form submissions
Route::post('/contato/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/avalie-seu-imovel/send', [PageController::class, 'sendEvaluate'])->name('evaluate.send');
Route::post('/corretor-parceiro/send', [PageController::class, 'sendPartnerAgent'])->name('partner-agent.send');
Route::post('/off-market/send', [PageController::class, 'sendOffMarket'])->name('off-market.send');
