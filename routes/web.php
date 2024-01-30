<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DolfijnenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsigneController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Register should be disabled in the future, only admin accounts can create accounts.
Auth::routes(['register' => false]);

//Dashboard
Route::get('/', [HomeController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

//Account
Route::get('/account', [AccountController::class, 'myAccount'])->name('account');
Route::post('/account', [AccountController::class, 'updateAccount'])->name('account.update');


//Admin
Route::middleware(['checkRole:Administratie'])->group(function () {
    Route::get('/admin', [AdminController::class, 'admin'])->name('admin');

    // Account management
    Route::get('/admin/account-management', [AdminController::class, 'accountManagement'])->name('admin.account-management');
    Route::post('/admin/account-management', [AdminController::class, 'accountManagementSearch'])->name('admin.account-management.search');

    Route::get('/admin/account-management/details/{id}', [AdminController::class, 'accountDetails'])->name('admin.account-management.details');

    Route::get('/admin/account-management/edit/{id}', [AdminController::class, 'editAccount'])->name('admin.account-management.edit');
    Route::post('/admin/account-management/edit/{id}', [AdminController::class, 'storeAccount'])->name('admin.account-management.store');

    Route::get('/admin/account-management/delete/{id}', [AdminController::class, 'deleteAccount'])->name('admin.account-management.delete');


    // Create account
    Route::get('/admin/create-account', [AdminController::class, 'createAccount'])->name('admin.create-account');
    Route::post('/admin/create-account', [AdminController::class, 'createAccountStore'])->name('admin.create-account-store');


    // Role management
    Route::get('/admin/role-management', [AdminController::class, 'roleManagement'])->name('admin.role-management');
    Route::post('/admin/role-management', [AdminController::class, 'roleManagementSearch'])->name('admin.role-management.search');


    Route::get('/admin/role-management/edit/{id}', [AdminController::class, 'editRole'])->name('admin.role-management.edit');
    Route::post('/admin/role-management/edit/{id}', [AdminController::class, 'storeRole'])->name('admin.role-management.store');

    Route::get('/admin/role-management/delete/{id}', [AdminController::class, 'deleteRole'])->name('admin.role-management.delete');

    Route::get('/admin/role-management/create', [AdminController::class, 'createRole'])->name('admin.role-management.create');
    Route::post('/admin/role-management/create', [AdminController::class, 'createRoleStore'])->name('admin.role-management.create.store');
});


// Dolfijnen
Route::middleware(['checkRole:Administratie,Dolfijn,Dolfijnen Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/dolfijnen', [DolfijnenController::class, 'view'])->name('dolfijnen');
    Route::get('/dolfijnen/leiding', [DolfijnenController::class, 'leiding'])->name('dolfijnen.leiding');
});

//Insignes
//Route::get('/insignes', [InsigneController::class, 'myInsignes'])->name('insignes');
