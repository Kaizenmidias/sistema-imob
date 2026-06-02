<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/feed/imoveis.xml', [HomeController::class, 'feedImoveisXml'])->name('feed.imoveis');
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
Route::get('/instagram/media/{mediaId}', [HomeController::class, 'instagramMedia'])->name('instagram.media');
Route::get('/storage/{path}', [HomeController::class, 'storageMedia'])->where('path', '.*')->name('storage.media');

Route::middleware('guest')->get('/login', [AdminController::class, 'showLogin'])->name('login');
Route::middleware('guest')->post('/login', [AdminController::class, 'login'])->name('login.store');
Route::post('/logout', [AdminController::class, 'logout'])->middleware('auth')->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/properties', [AdminController::class, 'properties'])->name('properties');
    Route::get('/properties/create', [AdminController::class, 'createProperty'])->name('properties.create');
    Route::post('/properties', [AdminController::class, 'storeProperty'])->name('properties.store');
    Route::get('/properties/{property}/edit', [AdminController::class, 'editProperty'])->name('properties.edit');
    Route::put('/properties/{property}', [AdminController::class, 'updateProperty'])->name('properties.update');
    Route::delete('/properties/{property}', [AdminController::class, 'destroyProperty'])->name('properties.destroy');
    Route::get('/business-types', [AdminController::class, 'businessTypes'])->name('business-types');
    Route::post('/business-types', [AdminController::class, 'storeBusinessType'])->name('business-types.store');
    Route::put('/business-types/{businessType}', [AdminController::class, 'updateBusinessType'])->name('business-types.update');
    Route::delete('/business-types/{businessType}', [AdminController::class, 'destroyBusinessType'])->name('business-types.destroy');
    Route::get('/categories/property-types', [AdminController::class, 'propertyTypes'])->name('property-types');
    Route::post('/categories/property-types', [AdminController::class, 'storePropertyType'])->name('property-types.store');
    Route::put('/categories/property-types/{propertyType}', [AdminController::class, 'updatePropertyType'])->name('property-types.update');
    Route::delete('/categories/property-types/{propertyType}', [AdminController::class, 'destroyPropertyType'])->name('property-types.destroy');
    Route::get('/categories/special', [AdminController::class, 'specialCategories'])->name('special-categories');
    Route::post('/categories/special', [AdminController::class, 'storeSpecialCategory'])->name('special-categories.store');
    Route::put('/categories/special/{specialCategory}', [AdminController::class, 'updateSpecialCategory'])->name('special-categories.update');
    Route::delete('/categories/special/{specialCategory}', [AdminController::class, 'destroySpecialCategory'])->name('special-categories.destroy');
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
    Route::get('/appearance', [AdminController::class, 'appearance'])->name('appearance');
    Route::post('/appearance', [AdminController::class, 'updateAppearance']);
    Route::put('/appearance', [AdminController::class, 'updateAppearance'])->name('appearance.update');
    Route::get('/pages', [AdminController::class, 'pages'])->name('pages');
    Route::get('/pages/create', [AdminController::class, 'createPage'])->name('pages.create');
    Route::post('/pages', [AdminController::class, 'storePage'])->name('pages.store');
    Route::get('/pages/{page}', [AdminController::class, 'editPage'])->name('pages.edit');
    Route::put('/pages/{page}', [AdminController::class, 'updatePage'])->name('pages.update');
    Route::delete('/pages/{page}', [AdminController::class, 'destroyPage'])->name('pages.destroy');
    Route::post('/pages/{page}/duplicate', [AdminController::class, 'duplicatePage'])->name('pages.duplicate');
    Route::post('/pages/{page}/media', [AdminController::class, 'uploadPageMedia'])->name('pages.media');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/instagram', [AdminController::class, 'instagram'])->name('instagram');
    Route::put('/instagram', [AdminController::class, 'updateInstagram'])->name('instagram.update');
    Route::post('/instagram/refresh', [AdminController::class, 'refreshInstagramFeed'])->name('instagram.refresh');

    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/posts', [AdminController::class, 'blogPosts'])->name('posts');
        Route::get('/posts/create', [AdminController::class, 'createBlogPost'])->name('posts.create');
        Route::post('/posts', [AdminController::class, 'storeBlogPost'])->name('posts.store');
        Route::get('/posts/{post}/edit', [AdminController::class, 'editBlogPost'])->name('posts.edit');
        Route::put('/posts/{post}', [AdminController::class, 'updateBlogPost'])->name('posts.update');
        Route::delete('/posts/{post}', [AdminController::class, 'destroyBlogPost'])->name('posts.destroy');
        Route::get('/categories', [AdminController::class, 'blogCategories'])->name('categories');
        Route::post('/categories', [AdminController::class, 'storeBlogCategory'])->name('categories.store');
        Route::put('/categories/{category}', [AdminController::class, 'updateBlogCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'destroyBlogCategory'])->name('categories.destroy');
    });
});

// Form submissions
Route::post('/contato/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/avalie-seu-imovel/send', [PageController::class, 'sendEvaluate'])->name('evaluate.send');
Route::post('/corretor-parceiro/send', [PageController::class, 'sendPartnerAgent'])->name('partner-agent.send');
Route::post('/off-market/send', [PageController::class, 'sendOffMarket'])->name('off-market.send');

Route::get('/{page:slug}', [PageController::class, 'show'])->name('page.show');
