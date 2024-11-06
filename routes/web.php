<?php

use App\Http\Controllers\AfterloodsenController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LeidingController;
use App\Http\Controllers\LoodsenController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NonLoggedInController;
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


//Non logged in
Route::get('/contact', [NonLoggedInController::class, 'contact'])->name('contact');
Route::post('/contact', [NonLoggedInController::class, 'contactSubmit'])->name('contact.submit');

Route::get('/inschrijven', [NonLoggedInController::class, 'inschrijven'])->name('inschrijven');
Route::post('/inschrijven', [NonLoggedInController::class, 'inschrijvenSubmit'])->name('inschrijven.submit');

Route::get('/nieuws/item/{id}', [NewsController::class, 'viewNewsItem'])->name('news.item');
Route::get('/nieuws/overzicht', [NewsController::class, 'viewNewsPage'])->name('news.list');

Route::get('/agenda/public/maand', [AgendaController::class, 'agendaMonthPublic'])->name('agenda.public.month');
Route::get('/agenda/public/overzicht', [AgendaController::class, 'agendaSchedulePublic'])->name('agenda.public.schedule');
Route::get('/agenda/public/activiteit/{id}', [AgendaController::class, 'agendaActivityPublic'])->name('agenda.public.activity');


Route::post('/agenda/public/activiteit/{id}', [NonLoggedInController::class, 'handleActivityForm'])->name('agenda.activity.submit');


Auth::routes(['register' => false, 'password.request' => false,]);

//Dashboard
Route::get('/', [HomeController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::get('/changelog', [HomeController::class, 'changelog'])->name('changelog');
Route::get('/credits', [HomeController::class, 'credits'])->name('credits');

//Notificaties
Route::get('/notificaties', [HomeController::class, 'notifications'])->name('notifications');

//Instellingen (account veranderen etc.)
Route::get('/instellingen', [SettingsController::class, 'account'])->name('settings');

Route::get('/instellingen/account/bewerk', [SettingsController::class, 'editAccount'])->name('settings.account.edit');
Route::post('/instellingen/account/bewerk', [SettingsController::class, 'editAccountSave'])->name('settings.account.store');

Route::get('/instellingen/verander-wachtwoord', [SettingsController::class, 'changePassword'])->name('settings.change-password');
Route::post('/instellingen/verander-wachtwoord', [SettingsController::class, 'updatePassword'])->name('settings.change-password.store');

Route::get('/instellingen/notificaties', [SettingsController::class, 'notifications'])->name('settings.edit-notifications');
Route::post('/instellingen/notificaties', [SettingsController::class, 'notificationsSave'])->name('settings.edit-notifications.store');

Route::middleware(['checkAccepted'])->group(function () {
    Route::get('/instellingen/ouder-account', [SettingsController::class, 'parent'])->name('settings.parent');

    Route::get('/instellingen/ouder-account/link', [SettingsController::class, 'linkParent'])->name('settings.link-parent');
    Route::post('/instellingen/ouder-account/link', [SettingsController::class, 'linkParentStore'])->name('settings.link-parent.store');
    Route::get('/instellingen/ouder-account/link/{id}', [SettingsController::class, 'confirmParent'])->name('settings.link-parent.confirm');

    Route::get('/instellingen/ouder-account/maak-account', [SettingsController::class, 'createAccount'])->name('settings.link-new-parent.create');
    Route::post('/instellingen/ouder-account/maak-account', [SettingsController::class, 'createAccountStore'])->name('settings.link-new-parent.store');
});

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

// News
Route::middleware(['auth'])->group(function () {
    Route::get('/nieuws', [NewsController::class, 'home'])->name('news');

    Route::get('/nieuws/nieuw-nieuwtje', [NewsController::class, 'news'])->name('news.new');
    Route::post('/nieuws/nieuw-nieuwtje', [NewsController::class, 'newsCreate'])->name('news.new.create');

    Route::get('/nieuws/mijn-nieuws', [NewsController::class, 'userNews'])->name('news.user');

    Route::get('/nieuws/mijn-nieuws/bewerk/{id}', [NewsController::class, 'editNews'])->name('news.user.edit');
    Route::post('/nieuws/mijn-nieuws/bewerk/{id}', [NewsController::class, 'saveEditNews'])->name('news.user.edit.save');

    Route::get('/nieuws/mijn-nieuws/verwijder/{id}', [NewsController::class, 'deleteNews'])->name('news.user.edit.delete');
});

//Agenda
Route::middleware(['auth'])->group(function () {
    Route::get('/agenda/maand', [AgendaController::class, 'agendaMonth'])->name('agenda.month');
    Route::get('/agenda/overzicht', [AgendaController::class, 'agendaSchedule'])->name('agenda.schedule');

    Route::get('/agenda/activiteit/{id}', [AgendaController::class, 'agendaActivity'])->name('agenda.activity');

    Route::get('/agenda/activiteit/aanwezig/{id}/{user}', [AgendaController::class, 'agendaPresent'])->name('agenda.activity.present');
    Route::get('/agenda/activiteit/niet-aanwezig/{id}/{user}', [AgendaController::class, 'agendaAbsent'])->name('agenda.activity.absent');
});

Route::middleware(['checkRole:Dolfijnen Leiding,Zeeverkenners Leiding,Loodsen Stamoudste,Loods,Afterloodsen Organisator,Administratie,Bestuur,Praktijkbegeleider,Loodsen Mentor,Ouderraad'])->group(function () {
    Route::get('/agenda/nieuw', [AgendaController::class, 'createAgenda'])->name('agenda.new');
    Route::post('/agenda/nieuw', [AgendaController::class, 'createAgendaSave'])->name('agenda.new.create');

    Route::get('/agenda', [AgendaController::class, 'home'])->name('agenda');

    Route::get('/agenda/bewerken', [AgendaController::class, 'edit'])->name('agenda.edit');
    Route::get('/agenda/bewerken/{id}', [AgendaController::class, 'editActivity'])->name('agenda.edit.activity');
    Route::post('/agenda/bewerken/{id}', [AgendaController::class, 'editActivitySave'])->name('agenda.edit.activity.save');

    Route::get('/agenda/delete/{id}', [AgendaController::class, 'deleteActivity'])->name('agenda.delete');

    Route::get('/agenda/aanwezigheid', [AgendaController::class, 'agendaPresence'])->name('agenda.presence');
    Route::get('/agenda/aanwezigheid/{id}', [AgendaController::class, 'agendaPresenceActivity'])->name('agenda.presence.activity');
    Route::post('/agenda/aanwezigheid/export', [AgendaController::class, 'exportPresenceData'])->name('agenda.presence.export');

    Route::get('/agenda/inschrijvingen', [AgendaController::class, 'agendaSubmissions'])->name('agenda.submissions');
    Route::get('/agenda/inschrijvingen/{id}', [AgendaController::class, 'agendaSubmissionsActivity'])->name('agenda.submissions.activity');
});


//Admin
Route::middleware(['checkRole:Administratie,Secretaris'])->group(function () {
    Route::get('/administratie', [AdminController::class, 'admin'])->name('admin');


    Route::get('/administratie/debug/mail', [AdminController::class, 'debugMail'])->name('admin.debug.mail');
    Route::get('/administratie/debug/mail/{id}', [AdminController::class, 'mail'])->name('admin.debug.mail.view');

    // Content
    Route::get('/administratie/contact', [AdminController::class, 'contact'])->name('admin.contact');
    Route::get('/administratie/contact/details/{id}', [AdminController::class, 'contactDetails'])->name('admin.contact.details');
    Route::get('/administratie/contact/verwijder/{id}', [AdminController::class, 'contactDelete'])->name('admin.contact.delete');
    Route::get('/administratie/contact/afgehandeld/{id}', [AdminController::class, 'contactSeen'])->name('admin.contact.seen');

    Route::get('/administratie/inschrijvingen', [AdminController::class, 'signup'])->name('admin.signup');
    Route::get('/administratie/inschrijvingen/details/{id}', [AdminController::class, 'signupAccountDetails'])->name('admin.signup.details');

    Route::get('/administratie/inschrijvingen/accepteer/{id}', [AdminController::class, 'signupAccept'])->name('admin.signup.accept');
    Route::get('/administratie/inschrijvingen/verwijder/{id}', [AdminController::class, 'signupDelete'])->name('admin.signup.delete');

    Route::get('/administratie/nieuws', [AdminController::class, 'news'])->name('admin.news');
    Route::get('/administratie/nieuws/details/{id}', [AdminController::class, 'newsDetails'])->name('admin.news.details');

    Route::get('/administratie/nieuws/bewerk/{id}', [AdminController::class, 'newsEdit'])->name('admin.news.edit');
    Route::post('/administratie/nieuws/bewerk/{id}', [AdminController::class, 'newsEditSave'])->name('admin.news.edit.save');

    Route::get('/administratie/nieuws/publiceer/{id}', [AdminController::class, 'newsPublish'])->name('admin.news.publish');
    Route::get('/administratie/nieuws/verwijder/{id}', [AdminController::class, 'newsDelete'])->name('admin.news.delete');

    // Account management
    Route::get('/administratie/account-beheer', [AdminController::class, 'accountManagement'])->name('admin.account-management');

    Route::post('/administratie/account-beheer/export', [AdminController::class, 'exportData'])->name('admin.account-management.export');

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

    Route::get('/administratie/rol-beheer/bewerk/{id}', [AdminController::class, 'editRole'])->name('admin.role-management.edit');
    Route::post('/administratie/rol-beheer/bewerk/{id}', [AdminController::class, 'storeRole'])->name('admin.role-management.store');

    Route::get('/administratie/rol-beheer/verwijder/{id}', [AdminController::class, 'deleteRole'])->name('admin.role-management.delete');

    Route::get('/administratie/rol-beheer/nieuw', [AdminController::class, 'createRole'])->name('admin.role-management.create');
    Route::post('/administratie/rol-beheer/nieuw', [AdminController::class, 'createRoleStore'])->name('admin.role-management.create.store');

    Route::get('/administratie/prikbord-beheer/posts', [AdminController::class, 'postManagement'])->name('admin.forum-management.posts');
    Route::get('/administratie/prikbord-beheer/comments', [AdminController::class, 'commentManagement'])->name('admin.forum-management.comments');

    Route::get('/administratie/prikbord-beheer/post/{id}', [AdminController::class, 'viewPost'])->name('admin.forum-management.post');

    Route::get('/administratie/prikbord-beheer/post/delete/{id}', [AdminController::class, 'deletePost'])->name('admin.forum-management.post.delete');
    Route::get('/administratie/prikbord-beheer/comment/delete/{id}/{postId}', [AdminController::class, 'deleteComment'])->name('admin.forum-management.comment.delete');

    Route::get('/administratie/logs', [AdminController::class, 'logs'])->name('admin.logs');
});

// Leiding
Route::middleware(['checkRole:Dolfijnen Leiding,Zeeverkenners Leiding,Loodsen Stamoudste,Afterloodsen Organisator,Vrijwilliger,Administratie,Bestuur,Ouderraad,Praktijkbegeleider,Loodsen Mentor'])->group(function () {
    Route::get('/leiding', [LeidingController::class, 'view'])->name('leiding');


    Route::post('/leiding', [LeidingController::class, 'postMessage'])->name('leiding.message-post');

    Route::get('/leiding/post/{id}', [LeidingController::class, 'viewPost'])->name('leiding.post');
    Route::post('/leiding/post/{id}', [LeidingController::class, 'postComment'])->name('leiding.comment-post');
    Route::post('/leiding/post/reaction/{id}/{commentId}', [LeidingController::class, 'postReaction'])->name('leiding.reaction-post');

    Route::get('/leiding/post/edit/{id}', [LeidingController::class, 'editPost'])->name('leiding.post.edit');
    Route::post('/leiding/post/edit/{id}', [LeidingController::class, 'storePost'])->name('leiding.post.store');

    Route::get('/leiding/post/delete/{id}', [LeidingController::class, 'deletePost'])->name('leiding.post.delete');
    Route::get('/leiding/comment/delete/{id}/{postId}', [LeidingController::class, 'deleteComment'])->name('leiding.comment.delete');

});
Route::get('/leiding/leiding-en-organisatie', [LeidingController::class, 'leiding'])->name('leiding.leiding');


// Dolfijnen
Route::middleware(['checkRole:Administratie,Dolfijn,Dolfijnen Leiding,Bestuur,Ouderraad', 'checkAccepted'])->group(function () {
    Route::get('/dolfijnen', [DolfijnenController::class, 'view'])->name('dolfijnen');
    Route::post('/dolfijnen', [DolfijnenController::class, 'postMessage'])->name('dolfijnen.message-post');

    Route::get('/dolfijnen/post/{id}', [DolfijnenController::class, 'viewPost'])->name('dolfijnen.post');
    Route::post('/dolfijnen/post/{id}', [DolfijnenController::class, 'postComment'])->name('dolfijnen.comment-post');
    Route::post('/dolfijnen/post/reaction/{id}/{commentId}', [DolfijnenController::class, 'postReaction'])->name('dolfijnen.reaction-post');

    Route::get('/dolfijnen/post/edit/{id}', [DolfijnenController::class, 'editPost'])->name('dolfijnen.post.edit');
    Route::post('/dolfijnen/post/edit/{id}', [DolfijnenController::class, 'storePost'])->name('dolfijnen.post.store');

    Route::get('/dolfijnen/post/delete/{id}', [DolfijnenController::class, 'deletePost'])->name('dolfijnen.post.delete');
    Route::get('/dolfijnen/comment/delete/{id}/{postId}', [DolfijnenController::class, 'deleteComment'])->name('dolfijnen.comment.delete');

    Route::get('/dolfijnen/leiding', [DolfijnenController::class, 'leiding'])->name('dolfijnen.leiding');

});

Route::middleware(['checkRole:Administratie,Dolfijnen Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/dolfijnen/groep', [DolfijnenController::class, 'group'])->name('dolfijnen.groep');
    Route::post('/dolfijnen/groep/export', [DolfijnenController::class, 'exportData'])->name('dolfijnen.groep.export');
});

Route::middleware(['checkRole:Administratie,Dolfijnen Leiding,Bestuur'])->group(function () {
    Route::get('/dolfijnen/groep/details/{id}', [DolfijnenController::class, 'groupDetails'])->name('dolfijnen.groep.details');
});

// Zeeverkenners
Route::middleware(['checkRole:Administratie,Zeeverkenner,Zeeverkenners Leiding,Bestuur,Ouderraad', 'checkAccepted'])->group(function () {
    Route::get('/zeeverkenners', [ZeeverkennerController::class, 'view'])->name('zeeverkenners');

    Route::post('/zeeverkenners', [ZeeverkennerController::class, 'postMessage'])->name('zeeverkenners.message-post');

    Route::get('/zeeverkenners/post/{id}', [ZeeverkennerController::class, 'viewPost'])->name('zeeverkenners.post');
    Route::post('/zeeverkenners/post/{id}', [ZeeverkennerController::class, 'postComment'])->name('zeeverkenners.comment-post');
    Route::post('/zeeverkenners/post/reaction/{id}/{commentId}', [ZeeverkennerController::class, 'postReaction'])->name('zeeverkenners.reaction-post');

    Route::get('/zeeverkenners/post/edit/{id}', [ZeeverkennerController::class, 'editPost'])->name('zeeverkenners.post.edit');
    Route::post('/zeeverkenners/post/edit/{id}', [ZeeverkennerController::class, 'storePost'])->name('zeeverkenners.post.store');

    Route::get('/zeeverkenners/post/delete/{id}', [ZeeverkennerController::class, 'deletePost'])->name('zeeverkenners.post.delete');
    Route::get('/zeeverkenners/comment/delete/{id}/{postId}', [ZeeverkennerController::class, 'deleteComment'])->name('zeeverkenners.comment.delete');

    Route::get('/zeeverkenners/leiding', [ZeeverkennerController::class, 'leiding'])->name('zeeverkenners.leiding');
});

Route::middleware(['checkRole:Administratie,Zeeverkenners Leiding,Bestuur,Ouderraad'])->group(function () {
    Route::get('/zeeverkenners/groep', [ZeeverkennerController::class, 'group'])->name('zeeverkenners.groep');
    Route::post('/zeeverkenners/groep', [ZeeverkennerController::class, 'groupSearch'])->name('zeeverkenners.group.search');
    Route::post('/zeeverkenners/groep/export', [ZeeverkennerController::class, 'exportData'])->name('zeeverkenners.groep.export');
});

Route::middleware(['checkRole:Administratie,Zeeverkenners Leiding,Bestuur'])->group(function () {
    Route::get('/zeeverkenners/groep/details/{id}', [ZeeverkennerController::class, 'groupDetails'])->name('zeeverkenners.groep.details');
});

// Loodsen
Route::middleware(['checkRole:Administratie,Loods,Loodsen Stamoudste,Bestuur,Ouderraad,Loodsen Mentor', 'checkAccepted'])->group(function () {
    Route::get('/loodsen', [LoodsenController::class, 'view'])->name('loodsen');

    Route::post('/loodsen', [LoodsenController::class, 'postMessage'])->name('loodsen.message-post');

    Route::get('/loodsen/post/{id}', [LoodsenController::class, 'viewPost'])->name('loodsen.post');
    Route::post('/loodsen/post/{id}', [LoodsenController::class, 'postComment'])->name('loodsen.comment-post');
    Route::post('/loodsen/post/reaction/{id}/{commentId}', [LoodsenController::class, 'postReaction'])->name('loodsen.reaction-post');

    Route::get('/loodsen/post/edit/{id}', [LoodsenController::class, 'editPost'])->name('loodsen.post.edit');
    Route::post('/loodsen/post/edit/{id}', [LoodsenController::class, 'storePost'])->name('loodsen.post.store');

    Route::get('/loodsen/post/delete/{id}', [LoodsenController::class, 'deletePost'])->name('loodsen.post.delete');
    Route::get('/loodsen/comment/delete/{id}/{postId}', [LoodsenController::class, 'deleteComment'])->name('loodsen.comment.delete');


    Route::get('/loodsen/leiding', [LoodsenController::class, 'leiding'])->name('loodsen.leiding');

    Route::get('/loodsen/flunkyball', [LoodsenController::class, 'flunkyball'])->name('loodsen.flunkyball');
    Route::get('/loodsen/flunkyball/flunkydj', [LoodsenController::class, 'flunkydj'])->name('loodsen.flunkyball.flunkydj');
    Route::get('/loodsen/flunkyball/regels', [LoodsenController::class, 'rules'])->name('loodsen.flunkyball.rules');
});

Route::middleware(['checkRole:Administratie,Loodsen Stamoudste,Bestuur,Ouderraad,Loodsen Mentor'])->group(function () {
    Route::get('/loodsen/groep', [LoodsenController::class, 'group'])->name('loodsen.groep');
    Route::post('/loodsen/groep', [LoodsenController::class, 'groupSearch'])->name('loodsen.group.search');
    Route::post('/loodsen/groep/export', [LoodsenController::class, 'exportData'])->name('loodsen.group.export');
});

Route::middleware(['checkRole:Administratie,Loodsen Stamoudste,Bestuur,Loodsen Mentor'])->group(function () {
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
Route::middleware(['checkRole:Administratie,Afterloods,Afterloodsen Organisator,Bestuur,Ouderraad', 'checkAccepted'])->group(function () {
    Route::get('/afterloodsen', [AfterloodsenController::class, 'view'])->name('afterloodsen');


    Route::post('/afterloodsen', [AfterloodsenController::class, 'postMessage'])->name('afterloodsen.message-post');

    Route::get('/afterloodsen/post/{id}', [AfterloodsenController::class, 'viewPost'])->name('afterloodsen.post');
    Route::post('/afterloodsen/post/{id}', [AfterloodsenController::class, 'postComment'])->name('afterloodsen.comment-post');
    Route::post('/afterloodsen/post/reaction/{id}/{commentId}', [AfterloodsenController::class, 'postReaction'])->name('afterloodsen.reaction-post');

    Route::get('/afterloodsen/post/edit/{id}', [AfterloodsenController::class, 'editPost'])->name('afterloodsen.post.edit');
    Route::post('/afterloodsen/post/edit/{id}', [AfterloodsenController::class, 'storePost'])->name('afterloodsen.post.store');

    Route::get('/afterloodsen/post/delete/{id}', [AfterloodsenController::class, 'deletePost'])->name('afterloodsen.post.delete');
    Route::get('/afterloodsen/comment/delete/{id}/{postId}', [AfterloodsenController::class, 'deleteComment'])->name('afterloodsen.comment.delete');


    Route::get('/afterloodsen/organisatie', [AfterloodsenController::class, 'leiding'])->name('afterloodsen.leiding');
});

Route::middleware(['checkRole:Administratie,Afterloodsen Organisator,Bestuur,Ouderraad'])->group(function () {
    Route::get('/afterloodsen/leden', [AfterloodsenController::class, 'group'])->name('afterloodsen.groep');
    Route::post('/afterloodsen/leden', [AfterloodsenController::class, 'groupSearch'])->name('afterloodsen.group.search');
    Route::post('/afterloodsen/leden/export', [AfterloodsenController::class, 'exportData'])->name('afterloodsen.group.export');
});

Route::middleware(['checkRole:Administratie,Afterloodsen Organisator,Bestuur'])->group(function () {
    Route::get('/afterloodsen/leden/details/{id}', [AfterloodsenController::class, 'groupDetails'])->name('afterloodsen.groep.details');
});

// Forum
Route::post('/upload-image', [ForumController::class, 'uploadImage'])->name('forum.image');
Route::post('/upload-pdf', [ForumController::class, 'uploadPdf'])->name('forum.pdf');
Route::post('/posts/{postId}/{likeType}/toggle-like', [ForumController::class, 'toggleLike'])->name('forum.toggle-like');
Route::post('/comments/{id}', [ForumController::class, 'updateComment'])->name('forum.comments.update');

Route::post('/user-search', [ForumController::class, 'searchUser'])->name('search-user');


//Insignes
//Route::get('/insignes', [InsigneController::class, 'myInsignes'])->name('insignes');
