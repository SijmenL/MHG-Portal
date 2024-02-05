<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DolfijnenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsigneController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ZeeverkennerController;
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

//Instelling (account veranderen etc.)
Route::get('/instellingen', [SettingsController::class, 'account'])->name('settings');

Route::get('/instellingen/account/bewerk', [SettingsController::class, 'editAccount'])->name('settings.account.edit');
Route::post('/instellingen/account/bewerk', [SettingsController::class, 'editAccountSave'])->name('settings.account.store');

Route::get('/instellingen/verander-wachtwoord', [SettingsController::class, 'changePassword'])->name('settings.change-password');
Route::post('/instellingen/verander-wachtwoord', [SettingsController::class, 'updatePassword'])->name('settings.change-password.store');

Route::get('/instellingen/ouder-account', [SettingsController::class, 'parent'])->name('settings.parent');

Route::get('/instellingen/ouder-account/link', [SettingsController::class, 'linkParent'])->name('settings.link-parent');
Route::post('/instellingen/ouder-account/link', [SettingsController::class, 'linkParentStore'])->name('settings.link-parent.store');
Route::get('/instellingen/ouder-account/link/{id}', [SettingsController::class, 'confirmParent'])->name('settings.link-parent.confirm');

Route::get('/instellingen/ouder-account/maak-account', [SettingsController::class, 'createAccount'])->name('settings.link-new-parent.create');
Route::post('/instellingen/ouder-account/maak-account', [SettingsController::class, 'createAccountStore'])->name('settings.link-new-parent.store');

Route::middleware(['isAllowedToRemoveParents'])->group(function () {
    Route::get('/instellingen/ouder-account/verwijder', [SettingsController::class, 'removeParentLink'])->name('settings.remove-parent-link');
    Route::get('/instellingen/ouder-account/verwijder/{id}', [SettingsController::class, 'removeParentLinkId'])->name('settings.remove-parent-link.id');
    Route::get('/instellingen/ouder-account/bevestig/{id}', [SettingsController::class, 'removeParentLinkConfirm'])->name('settings.remove-parent-link.confirm');
});


Route::middleware(['hasChildren'])->group(function () {
    //Instellingen
    Route::get('/instellingen/kind-account/verwijder', [SettingsController::class, 'removeChildLink'])->name('settings.remove-child-link');
    Route::get('/instellingen/kind-account/verwijder/{id}', [SettingsController::class, 'removeChildLinkId'])->name('settings.remove-child-link.id');
    Route::get('/instellingen/kind-account/bevestig/{id}', [SettingsController::class, 'removeChildLinkConfirm'])->name('settings.remove-child-link.confirm');

    //Ouders
    Route::get('/kinderen', [ParentController::class, 'myChildren'])->name('children');

    Route::get('/kinderen/edit/{id}', [ParentController::class, 'editChild'])->name('children.edit');
    Route::post('/kinderen/edit/{id}', [ParentController::class, 'editChildSave'])->name('children.store');
});

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

Route::middleware(['checkRole:Administratie,Dolfijnen Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/dolfijnen/groep', [DolfijnenController::class, 'group'])->name('dolfijnen.groep');
    Route::post('/dolfijnen/groep', [DolfijnenController::class, 'groupSearch'])->name('dolfijnen.group.search');
});

Route::middleware(['checkRole:Administratie,Dolfijnen Leiding,Bestuur'])->group(function () {
    Route::get('/dolfijnen/groep/details/{id}', [DolfijnenController::class, 'groupDetails'])->name('dolfijnen.groep.details');
});

// Zeeverkenners
Route::middleware(['checkRole:Administratie,Zeeverkenner,Zeeverkenner Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/zeeverkenners', [ZeeverkennerController::class, 'view'])->name('zeeverkenners');
    Route::get('/zeeverkenners/leiding', [ZeeverkennerController::class, 'leiding'])->name('zeeverkenners.leiding');
});

Route::middleware(['checkRole:Administratie,Zeeverkenner Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/zeeverkenners/groep', [ZeeverkennerController::class, 'group'])->name('zeeverkenners.groep');
    Route::post('/zeeverkenners/groep', [ZeeverkennerController::class, 'groupSearch'])->name('zeeverkenners.group.search');
});

Route::middleware(['checkRole:Administratie,Zeeverkenner Leiding,Bestuur'])->group(function () {
    Route::get('/zeeverkenners/groep/details/{id}', [ZeeverkennerController::class, 'groupDetails'])->name('zeeverkenners.groep.details');
});

//Insignes
//Route::get('/insignes', [InsigneController::class, 'myInsignes'])->name('insignes');
