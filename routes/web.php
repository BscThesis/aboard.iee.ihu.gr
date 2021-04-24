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
});

Route::get('/announcements/{id}', function ($id) {
    return view('pages.announcement')->with('id', $id);
})->middleware('web.id.check', 'web.announcement.check')->name('announcement');

Route::get('/announcements/{id}/edit', function ($id) {
    return view('pages.edit-announcement')->with('id', $id);
})->middleware('auth', 'web.id.check', 'is.the.author', 'web.announcement.check')->name('announcement.edit');

Route::get('/events', function () {
    return view('pages.events');
});

Route::get('/authors', function () {
    return view('pages.authors');
})->middleware('auth');

Route::get('/search/tag/{id}', function ($id) {
    return view('search.bytag')->with('id', $id);
})->middleware('web.id.check', 'web.tag.check');

Route::get('/search/author/{id}', function ($id) {
    return view('search.byauthor')->with('id', $id);
})->middleware('web.id.check');

Route::get('/search/q={params}', function ($params) {
    return view('search.custom')->with('params', $params);
});

Route::get('/documentation', function () {
    return view('pages.docs');
})->middleware('auth');

Route::get('/login', function () {
    return view('user.login');
})->name('login');

Route::get('/user/preferences', function () {
    return view('user.preferences');
})->middleware('auth')->name('preferences');

Route::get('/announcements/{an_id}/attachments/{at_id}', 'AttachmentController@show');

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

Route::get('/announcements/{announcement}', 'AnnouncementController@show')->name('announcements.rss');
Route::feeds();
