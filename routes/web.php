<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IndexController;
use App\Models\Portfolio;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Admin Portfolios All Routes

Route::prefix('admin')->group(function(){


    Route::get('/delete/{id}', [AdminController::class, 'PortfolioDelete'])->name('Portfolio.delete');
    Route::get('/all/portfolio', [AdminController::class,'AllPortfolio'])->name('all.portfolio');
    Route::get('/add/portfolio', [AdminController::class,'AddPortfolio'])->name('add.portfolio');
    Route::post('/store/portfolio', [AdminController::class,'StorePortfolio'])->name('store.portfolio');
    Route::get('/edit/portfolio/{id}',[AdminController::class, 'EditPortfolio'])->name('edit.portfolio');
    Route::post('/update/portfolio', [AdminController::class,'UpdatePortfolio'])->name('update.portfolio');
    Route::get('/delete/portfolio/{id}', [AdminController::class,'DeletePortfolio'])->name('delete.portfolio');
    Route::post('/thambnail/update', [AdminController::class, 'ImageUpdate'])->name('update-image');

    Route::get('/portfolio/details/{id}', [AdminController::class,'PortfolioDetails'])->name('portfolio.details');

    Route::get('/portfolio', [AdminController::class,'HomePortfolio'])->name('home.portfolio');

    Route::post('/image/update', [AdminController::class, 'MultiImageUpdate'])->name('update-product-image');

    Route::get('/multiimg/delete/{id}', [AdminController::class, 'MultiImageDelete'])->name('product.multiimg.delete');


});
route::controller(AdminController::class)->group(function(){
    Route::get('/admin/logout', 'destroy')->name('admin.logout');
});

Route::get('/', function () {
    $projects = Portfolio::all();

    return view('frontend.main_master',compact('projects'));
});

Route::get('/portfolio', [AdminController::class, 'HomePortfolio'] )->name('portfolio');

Route::get('/project/details/{id}', [IndexController::class, 'projectDetails'] )->name('project.details');


Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
