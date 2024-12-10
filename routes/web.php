<?php

use App\Http\Controllers\PaymentCategoryController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/login');
});


Auth::routes();

Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);


Route::middleware(['auth'])->group(function () {
    //dashboard routes

    Route::get('/dashboard', function () {
        return view('home');
    });


    // payment_category
    Route::prefix('/payment_category')->group(function () {
        Route::get('/',[PaymentCategoryController::class,'index'])->name('payment_categories');
        Route::post('/deleteSelected',[PaymentCategoryController::class,'deleteSelected'])->name('payment_category.deleteSelected');
        Route::post('/',[PaymentCategoryController::class,'store'])->name('payment_category-save');
        Route::get('/delete/{id}',[PaymentCategoryController::class,'destroy'])->name('payment_category-delete');
        Route::get('/edit/{id?}', [PaymentCategoryController::class, 'show'])->name('payment_category-edit');
    });
    
});
