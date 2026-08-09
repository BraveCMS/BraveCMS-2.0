<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\PageNotFoundController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\CkEditorController;
use App\Http\Controllers\Dashboard\UserRightsController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\ArticleCategoryController;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\Dashboard\ArticleAIController;
use App\Http\Controllers\Dashboard\ArticleController;
use App\Http\Controllers\Dashboard\CommentController;

// Install 
Route::get('/install', [InstallController::class, 'index']);

// 404 custom page 
Route::any('/404', [PageNotFoundController::class, 'notfound'])->name('404');

// Article routes
Route::get('/', [ArticlesController::class, 'index'])->name('homepage');
Route::get('/category/{category_id}', [ArticlesController::class, 'category'])->name('category');
Route::get('/tag/{tag_id}', [ArticlesController::class, 'tag'])->name('tag');
Route::get('/author/{user_id}', [ArticlesController::class, 'author'])->name('author');
Route::get('/show/{slug}', [ArticlesController::class, 'show'])->name('show');
Route::post('/load_comments', [ArticlesController::class, 'get_comments_ajax'])->name('load_comments');

// Comments
Route::post('/comment/submit', [ArticlesController::class, 'add_comment'])->name('comment.submit');
Route::post('/comment/update/{id}', [ArticlesController::class, 'update_comment'])->name('comment.update');
Route::post('/comment/delete/{id}', [ArticlesController::class, 'delete_comment'])->name('comment.delete');

// Page routes
Route::get('/page/{id}', [PagesController::class, 'page'])->name('page');

// Contact page routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Newsletter subscription
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Auth::routes();
Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('check.email');

// Dashboard routes
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

  // Settings routes
  Route::group(['prefix' => 'settings', 'middleware' => ['checkUserPermissions:edit-settings']], function () {
    Route::get('/', [SettingsController::class, 'index'])->name('dashboard.settings');
    Route::post('/update', [SettingsController::class, 'update'])->name('dashboard.settings.update');
  });

  Route::post('/ckupload', [CkEditorController::class, 'ckupload'])->name('dashboard.ckupload');

  // Pages routes
  Route::group(['prefix' => 'pages'], function () {
    Route::get('/', [PageController::class, 'index'])->name('dashboard.pages')->middleware('checkUserPermissions:view-pages');
    Route::get('/new', [PageController::class, 'create'])->name('dashboard.pages.new')->middleware('checkUserPermissions:add-pages');
    Route::post('/add', [PageController::class, 'save'])->name('dashboard.pages.add')->middleware('checkUserPermissions:add-pages');
    Route::get('/edit/{id}', [PageController::class, 'edit'])->name('dashboard.pages.edit')->middleware('checkUserPermissions:edit-pages');
    Route::put('/update/{id}', [PageController::class, 'update'])->name('dashboard.pages.update')->middleware('checkUserPermissions:edit-pages');
    Route::get('/delete/{id}', [PageController::class, 'delete'])->name('dashboard.pages.delete')->middleware('checkUserPermissions:delete-pages');
  });

  // Category routes
  Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [ArticleCategoryController::class, 'index'])->name('dashboard.categories')->middleware('checkUserPermissions:view-categories');
    Route::get('/new', [ArticleCategoryController::class, 'create'])->name('dashboard.categories.new')->middleware('checkUserPermissions:add-categories');
    Route::post('/add', [ArticleCategoryController::class, 'save'])->name('dashboard.categories.add')->middleware('checkUserPermissions:add-categories');
    Route::get('/edit/{id}', [ArticleCategoryController::class, 'edit'])->name('dashboard.categories.edit')->middleware('checkUserPermissions:edit-categories');
    Route::put('/update/{id}', [ArticleCategoryController::class, 'update'])->name('dashboard.categories.update')->middleware('checkUserPermissions:edit-categories');
    Route::get('/delete/{id}', [ArticleCategoryController::class, 'delete'])->name('dashboard.categories.delete')->middleware('checkUserPermissions:delete-categories');
  });

  // Tag routes 
  Route::group(['prefix' => 'tags'], function () {
    Route::get('/', [TagController::class, 'index'])->name('dashboard.tags')->middleware('checkUserPermissions:view-tags');
    Route::get('/new', [TagController::class, 'create'])->name('dashboard.tags.new')->middleware('checkUserPermissions:add-tags');
    Route::post('/add', [TagController::class, 'save'])->name('dashboard.tags.add')->middleware('checkUserPermissions:add-tags');
    Route::get('/edit/{id}', [TagController::class, 'edit'])->name('dashboard.tags.edit')->middleware('checkUserPermissions:edit-tags');
    Route::put('/update/{id}', [TagController::class, 'update'])->name('dashboard.tags.update')->middleware('checkUserPermissions:edit-tags');
    Route::get('/delete/{id}', [TagController::class, 'delete'])->name('dashboard.tags.delete')->middleware('checkUserPermissions:delete-tags');
  });

  // Article routes
  Route::group(['prefix' => 'articles'], function () {
    Route::get('/', [ArticleController::class, 'index'])->name('dashboard.articles')->middleware('checkUserPermissions:view-articles');
    Route::get('/new', [ArticleController::class, 'create'])->name('dashboard.articles.new')->middleware('checkUserPermissions:add-articles');
    Route::post('/add', [ArticleController::class, 'save'])->name('dashboard.articles.add')->middleware('checkUserPermissions:add-articles');
    Route::post('/ai-generate', [ArticleAIController::class, 'generate'])->name('dashboard.articles.ai-generate');
    Route::get('/edit/{id}', [ArticleController::class, 'edit'])->name('dashboard.articles.edit')->middleware('checkUserPermissions:edit-articles');
    Route::post('/delete-image/{id}/{fileName}', [ArticleController::class, 'deleteImage'])->name('dashboard.articles.delete-image')->middleware('checkUserPermissions:edit-articles');
    Route::post('/delete-video/{id}/{fileName}', [ArticleController::class, 'deleteVideo'])->name('dashboard.articles.delete-video')->middleware('checkUserPermissions:edit-articles');
    Route::put('/update/{id}', [ArticleController::class, 'update'])->name('dashboard.articles.update')->middleware('checkUserPermissions:edit-articles');
    Route::get('/delete/{id}', [ArticleController::class, 'delete'])->name('dashboard.articles.delete')->middleware('checkUserPermissions:delete-articles');
  });

  // Comments routes
  Route::group(['prefix' => 'comments'], function () {
    Route::get('/', [CommentController::class, 'index'])->name('dashboard.comments')->middleware('checkUserPermissions:view-comments');
    Route::get('/delete/{id}', [CommentController::class, 'delete'])->name('dashboard.comments.delete')->middleware('checkUserPermissions:delete-comments');
    Route::get('/approve/{id}', [CommentController::class, 'approve'])->name('dashboard.comments.approve')->middleware('checkUserPermissions:approve-comments');
    Route::get('/unapprove/{id}', [CommentController::class, 'unapprove'])->name('dashboard.comments.unapprove')->middleware('checkUserPermissions:unapprove-comments');
  });

  // User management routes
  Route::group(['prefix' => 'users'], function () {
    Route::get('/rights', [UserRightsController::class, 'index'])->name('user-rights')->middleware('checkUserPermissions:manage-user-rights');
    Route::get('/available-permissions', [UserRightsController::class, 'permissions'])->name('available-permissions')->middleware('checkUserPermissions:manage-user-rights');
    Route::get('/rights/change-role/{id}', [UserRightsController::class, 'change_role'])->name('change-role')->middleware('checkUserPermissions:assign-user-roles');
    Route::post('/rights/update-role/{id}', [UserRightsController::class, 'update_role'])->name('update-role')->middleware('checkUserPermissions:assign-user-roles');
    Route::get('/rights/ban/{id}', [UserRightsController::class, 'ban_user'])->name('ban-user')->middleware('checkUserPermissions:ban-users');
    Route::get('/rights/activate/{id}', [UserRightsController::class, 'activate_user'])->name('activate-user')->middleware('checkUserPermissions:activate-users');
  });

  // Profile routes
  Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index'])->name('user');
    Route::match(['get', 'post'], '/update', [UserController::class, 'update'])->name('user.update');
    Route::post('/deleteavatar/{id}/{fileName}', [UserController::class, 'deleteavatar'])->name('user.deleteavatar');
  });
});
