<?php

use App\Http\Controllers\AfterloodsenController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LoodsenController;
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
Auth::routes(['register' => false, 'password.request' => false,]);

//Dashboard
Route::get('/', [HomeController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::get('/changelog', [HomeController::class, 'changelog'])->name('changelog');
Route::get('/credits', [HomeController::class, 'credits'])->name('credits');

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

    Route::get('/administratie/account-beheer/wachtwoord/{id}', [AdminController::class, 'editAccountPassword'])->name('admin.account-management.password');
    Route::post('/administratie/account-beheer/wachtwoord/{id}', [AdminController::class, 'editAccountPasswordStore'])->name('admin.account-management.password.store');

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
    Route::post('/dolfijnen', [DolfijnenController::class, 'postMessage'])->name('dolfijnen.message-post');

    Route::get('/dolfijnen/post/{id}', [DolfijnenController::class, 'viewPost'])->name('dolfijnen.post');
    Route::post('/dolfijnen/post/{id}', [DolfijnenController::class, 'postComment'])->name('dolfijnen.comment-post');

    Route::get('/dolfijnen/post/edit/{id}', [DolfijnenController::class, 'editPost'])->name('dolfijnen.post.edit');
    Route::post('/dolfijnen/post/edit/{id}', [DolfijnenController::class, 'storePost'])->name('dolfijnen.post.store');

    Route::get('/dolfijnen/post/delete/{id}', [DolfijnenController::class, 'deletePost'])->name('dolfijnen.post.delete');
    Route::get('/dolfijnen/comment/delete/{id}/{postId}', [DolfijnenController::class, 'deleteComment'])->name('dolfijnen.comment.delete');

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

// Loodsen
Route::middleware(['checkRole:Administratie,Loods,Loodsen Stamoudste,Bestuur,Ouderraad'])->group(function () {
    Route::get('/loodsen', [LoodsenController::class, 'view'])->name('loodsen');
    Route::get('/loodsen/leiding', [LoodsenController::class, 'leiding'])->name('loodsen.leiding');

    Route::get('/loodsen/flunkyball', [LoodsenController::class, 'flunkyball'])->name('loodsen.flunkyball');
    Route::get('/loodsen/flunkyball/flunkydj', [LoodsenController::class, 'flunkydj'])->name('loodsen.flunkyball.flunkydj');
    Route::get('/loodsen/flunkyball/regels', [LoodsenController::class, 'rules'])->name('loodsen.flunkyball.rules');
});

Route::middleware(['checkRole:Administratie,Loodsen Stamoudste,Bestuur,Ouderraad'])->group(function () {
    Route::get('/loodsen/groep', [LoodsenController::class, 'group'])->name('loodsen.groep');
    Route::post('/loodsen/groep', [LoodsenController::class, 'groupSearch'])->name('loodsen.group.search');
});

Route::middleware(['checkRole:Administratie,Loodsen Stamoudste,Bestuur'])->group(function () {
    Route::get('/loodsen/groep/details/{id}', [LoodsenController::class, 'groupDetails'])->name('loodsen.groep.details');
});

// Flunkyball expansie
Route::middleware(['checkRole:Administratie,Loodsen Stamoudste'])->group(function () {
    Route::get('/loodsen/flunkyball/muziek', [LoodsenController::class, 'music'])->name('loodsen.flunkyball.music');

    Route::get('/loodsen/flunkyball/muziek/add', [LoodsenController::class, 'addMusic'])->name('loodsen.flunkyball.music.add');
    Route::post('/loodsen/flunkyball/muziek/add', [LoodsenController::class, 'storeMusic'])->name('loodsen.flunkyball.music.store');

    Route::get('/loodsen/flunkyball/muziek/bewerk/{id}', [LoodsenController::class, 'editMusic'])->name('loodsen.flunkyball.music.edit');
    Route::post('/loodsen/flunkyball/muziek/bewerk/{id}', [LoodsenController::class, 'saveMusic'])->name('loodsen.flunkyball.music.save');
    Route::get('/loodsen/flunkyball/muziek/verwijder/{id}', [LoodsenController::class, 'deleteMusic'])->name('loodsen.flunkyball.music.delete');

});

// After loodsen
Route::middleware(['checkRole:Administratie,Afterloods,Afterloodsen Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/afterloodsen', [AfterloodsenController::class, 'view'])->name('afterloodsen');
    Route::get('/afterloodsen/organisatie', [AfterloodsenController::class, 'leiding'])->name('afterloodsen.leiding');
});

Route::middleware(['checkRole:Administratie,Afterloodsen Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/afterloodsen/leden', [AfterloodsenController::class, 'group'])->name('afterloodsen.groep');
    Route::post('/afterloodsen/leden', [AfterloodsenController::class, 'groupSearch'])->name('afterloodsen.group.search');
});

Route::middleware(['checkRole:Administratie,Afterloodsen Leiding,Bestuur'])->group(function () {
    Route::get('/afterloodsen/leden/details/{id}', [AfterloodsenController::class, 'groupDetails'])->name('afterloodsen.groep.details');
});

// Forum
Route::post('/upload-image', [ForumController::class, 'upload'])->name('forum.image');
Route::post('/posts/{postId}/toggle-like', [ForumController::class, 'toggleLike'])->name('forum.toggle-like');
Route::post('/comments/{id}', [ForumController::class, 'updateComment'])->name('forum.comments.update');




//Insignes
//Route::get('/insignes', [InsigneController::class, 'myInsignes'])->name('insignes');
