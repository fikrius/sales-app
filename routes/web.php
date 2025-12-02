<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

Route::get('/', function(){ return redirect()->route('dashboard'); });
Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard')->middleware('auth');

Route::middleware(['auth'])->group(function(){
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Sales routes
    Route::middleware(['permission:sale-read'])->group(function(){
        Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    });
    Route::middleware(['permission:sale-create'])->group(function(){
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
    });
    Route::middleware(['permission:sale-update'])->group(function(){
        Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
    });
    Route::middleware(['permission:sale-delete'])->group(function(){
        Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    });
    Route::middleware(['permission:sale-read'])->group(function(){
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });
    
    // Payment routes
    Route::middleware(['permission:payment-read'])->group(function(){
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    });
    Route::middleware(['permission:payment-create'])->group(function(){
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    });
    Route::middleware(['permission:payment-update'])->group(function(){
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    });
    Route::middleware(['permission:payment-delete'])->group(function(){
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });
    Route::middleware(['permission:payment-read'])->group(function(){
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    });
    
    // Item routes
    Route::middleware(['permission:item-read'])->group(function(){
        Route::get('items', [ItemController::class, 'index'])->name('items.index');
    });
    Route::middleware(['permission:item-create'])->group(function(){
        Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('items', [ItemController::class, 'store'])->name('items.store');
    });
    Route::middleware(['permission:item-read'])->group(function(){
        Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');
    });
    Route::middleware(['permission:item-update'])->group(function(){
        Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('items/{item}', [ItemController::class, 'update'])->name('items.update');
    });
    Route::middleware(['permission:item-delete'])->group(function(){
        Route::delete('items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });
    
    // User routes - admin and superadmin
    Route::middleware(['role:admin|superadmin'])->group(function(){
        Route::middleware(['permission:user-read'])->group(function(){
            Route::get('users', [UserController::class, 'index'])->name('users.index');
        });
        Route::middleware(['permission:user-create'])->group(function(){
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
        });
        Route::middleware(['permission:user-update'])->group(function(){
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        });
        Route::middleware(['permission:user-delete'])->group(function(){
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });
});

Route::prefix('api')->middleware('auth')->group(function(){
    Route::post('datatables/sales', [App\Http\Controllers\Api\DataTableController::class,'sales']);
    Route::post('datatables/items', [App\Http\Controllers\Api\DataTableController::class,'items']);
    Route::post('datatables/payments', [App\Http\Controllers\Api\DataTableController::class,'payments']);
    Route::get('search/items', [App\Http\Controllers\Api\DataTableController::class,'searchItems']);
    Route::get('charts/sales-monthly', [App\Http\Controllers\Api\ChartController::class,'salesMonthly']);
    Route::get('charts/items-pie', [App\Http\Controllers\Api\ChartController::class,'itemsPie']);
});

require __DIR__.'/auth.php';
