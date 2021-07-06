<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//----------------------------------------
//          Announcements Routes
//----------------------------------------

Route::post('/announcements', 'AnnouncementController@store');

Route::put('/announcements/{id}', 'AnnouncementController@update');

Route::delete('/announcements/{id}', 'AnnouncementController@destroy');

Route::get('/announcements', 'AnnouncementController@index');

Route::get('/announcements/{id}', 'AnnouncementController@show');

Route::get('/events', 'AnnouncementController@events');

Route::get('/pinned', 'AnnouncementController@pinned');

Route::get('/search/tag/{id}', 'AnnouncementController@searchByTag');

Route::get('/search/author/{id}', 'AnnouncementController@searchByAuthor');

Route::get('/search', 'AnnouncementController@customSearch');

//----------------------------------------
//          Attachments Routes
//----------------------------------------

Route::get('/announcements/{an_id}/attachments/{at_id}', 'AttachmentController@show');

//----------------------------------------
//          Tags Routes
//----------------------------------------

Route::post('/tags', 'TagController@store');

Route::put('/tags/{id}', 'TagController@update');

Route::delete('/tags/{id}', 'TagController@destroy');

Route::get('/tags', 'TagController@index');

Route::get('/tags/{id}', 'TagController@show');

//----------------------------------------
//          Auth Routes
//----------------------------------------


Route::prefix('auth')->group(function () {

    Route::post('/login', 'AuthController@login');

    Route::get('/logout', 'AuthController@logout');

    Route::get('/user', 'AuthController@user');

    Route::get('/user/notifications', 'AuthController@notifications');

    Route::get('/user/notifications/read', 'AuthController@readNotifications');

    Route::post('/subscribe', 'AuthController@subscribe');

    Route::get('/authors', 'AuthController@authors');
});

//----------------------------------------
//          Issues Routes
//----------------------------------------

Route::post('/issues', 'IssueController@store');

Route::get('/issues', 'IssueController@index');

//----------------------------------------
//          Fallback route
//----------------------------------------

// IMPLEMENT THIS

Route::fallback(function () {
    return response()->json(['message' => 'Not found'], 404);
});
