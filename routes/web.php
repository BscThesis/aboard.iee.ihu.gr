<?php

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

// Auth::routes();

Route::get('/', function () {
    return redirect('/announcements');
});

Route::get('/announcements/create', function () {
    return view('pages.create-announcement');
})->middleware('auth', 'is.author');

Route::get('/announcements', function () {
    return view('pages.announcements');
})->name('announcements');

Route::get('/announcements/{id}', function ($id) {
    return view('pages.announcement')->with('id', $id);
})->middleware('web.id.check', 'web.announcement.check')->name('announcement');

Route::get('/announcements/{id}/edit', function ($id) {
    return view('pages.edit-announcement')->with('id', $id);
})->middleware('auth', 'web.id.check', 'is.the.author', 'web.announcement.check')->name('announcement.edit');

Route::get('/announcements/{an_id}/attachments/{at_id}', 'Attachment\AttachmentController@show')
    ->middleware('android.app', 'announcement.attachment.check');

Route::get('/documentation', function () {
    return view('pages.docs');
})->middleware('auth');

// Route::get('/login', function () {
//     return view('user.login');
// })->name('login');

Route::get('/sign-in', 'Auth\AuthController@signIn')->name('sign-in');
Route::get('/sign-in/redirect', 'Auth\AuthController@redirect');

Route::get('/user/preferences', function () {
    return view('user.preferences');
})->middleware('auth')->name('preferences');

Route::prefix('errors')->group(function () {
    Route::get('400', function () {
        abort(400);
    });
    Route::get('401', function () {
        abort(401);
    });
    Route::get('403', function () {
        abort(403);
    });
    Route::get('404', function () {
        abort(404);
    });
    Route::get('419', function () {
        abort(419);
    });
    Route::get('429', function () {
        abort(429);
    });
    Route::get('500', function () {
        abort(500);
    });
    Route::get('503', function () {
        abort(503);
    });
    Route::any('{any}', function () {
        abort(400);
    });
});

Route::get('/announcements/{announcement}', 'Announcement\AnnouncementController@show')->name('announcements.rss');
Route::feeds();
