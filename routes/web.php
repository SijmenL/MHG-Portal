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
    Route::get('/administratie', [AdminController::class, 'admin'])->name('admin');

    // Account management
    Route::get('/administratie/account-beheer', [AdminController::class, 'accountManagement'])->name('admin.account-management');
    Route::post('/administratie/account-beheer', [AdminController::class, 'accountManagementSearch'])->name('admin.account-management.search');

    Route::get('/administratie/account-beheer/details/{id}', [AdminController::class, 'accountDetails'])->name('admin.account-management.details');

    Route::get('/administratie/account-beheer/bewerk/{id}', [AdminController::class, 'editAccount'])->name('admin.account-management.edit');
    Route::post('/administratie/account-beheer/bewerk/{id}', [AdminController::class, 'storeAccount'])->name('admin.account-management.store');

    Route::get('/administratie/account-beheer/verwijder/{id}', [AdminController::class, 'deleteAccount'])->name('admin.account-management.delete');


    // Create account
    Route::get('/administratie/maak-account', [AdminController::class, 'createAccount'])->name('admin.create-account');
    Route::post('/administratie/maak-account', [AdminController::class, 'createAccountStore'])->name('admin.create-account-store');


    // Role management
    Route::get('/administratie/rol-beheer', [AdminController::class, 'roleManagement'])->name('admin.role-management');
    Route::post('/administratie/rol-beheer', [AdminController::class, 'roleManagementSearch'])->name('admin.role-management.search');


    Route::get('/administratie/rol-beheer/bewerk/{id}', [AdminController::class, 'editRole'])->name('admin.role-management.edit');
    Route::post('/administratie/rol-beheer/bewerk/{id}', [AdminController::class, 'storeRole'])->name('admin.role-management.store');

    Route::get('/administratie/rol-beheer/verwijder/{id}', [AdminController::class, 'deleteRole'])->name('admin.role-management.delete');

    Route::get('/administratie/rol-beheer/nieuw', [AdminController::class, 'createRole'])->name('admin.role-management.create');
    Route::post('/administratie/rol-beheer/nieuw', [AdminController::class, 'createRoleStore'])->name('admin.role-management.create.store');
});


// Dolfijnen
Route::middleware(['checkRole:Administratie,Dolfijn,Dolfijnen Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/dolfijnen', [DolfijnenController::class, 'view'])->name('dolfijnen');
    Route::get('/dolfijnen/leiding', [DolfijnenController::class, 'leiding'])->name('dolfijnen.leiding');
});

Route::middleware(['checkRole:Administratie,Dolfijnen Leiding,Bestuur'])->group(function () {
    Route::get('/dolfijnen/groep', [DolfijnenController::class, 'group'])->name('dolfijnen.groep');
    Route::post('/dolfijnen/groep', [DolfijnenController::class, 'groupSearch'])->name('dolfijnen.group.search');
    Route::get('/dolfijnen/groep/details/{id}', [DolfijnenController::class, 'groupDetails'])->name('dolfijnen.groep.details');
});

//Insignes
//Route::get('/insignes', [InsigneController::class, 'myInsignes'])->name('insignes');
