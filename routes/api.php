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

Route::post('/announcements', 'Announcement\AnnouncementController@store');

Route::put('/announcements/{id}', 'Announcement\AnnouncementController@update');

Route::delete('/announcements/{id}', 'Announcement\AnnouncementController@destroy');

Route::get('/announcements', 'Announcement\AnnouncementController@index');

Route::get('/announcements/{id}', 'Announcement\AnnouncementController@show');

Route::get('/events', 'Announcement\AnnouncementController@events');

Route::get('/pinned', 'Announcement\AnnouncementController@pinned');

Route::get('/search/tag/{id}', 'Announcement\AnnouncementController@searchByTag');

Route::get('/search/author/{id}', 'Announcement\AnnouncementController@searchByAuthor');

Route::get('/search', 'Announcement\AnnouncementController@customSearch');

//----------------------------------------
//          Attachments Routes
//----------------------------------------

Route::get('/announcements/{an_id}/attachments/{at_id}', 'Attachment\AttachmentController@show');

//----------------------------------------
//          Tags Routes
//----------------------------------------

Route::post('/tags', 'Tag\TagController@store');

Route::put('/tags/{id}', 'Tag\TagController@update');

Route::delete('/tags/{id}', 'Tag\TagController@destroy');

Route::get('/tags', 'Tag\TagController@index');

Route::get('/tags/{id}', 'Tag\TagController@show');

//----------------------------------------
//          Auth Routes
//----------------------------------------


Route::prefix('auth')->group(function () {

    Route::post('/login', 'Auth\AuthController@login');

    Route::post('/refresh', 'Auth\AuthController@refresh');

    Route::get('/logout', 'Auth\AuthController@logout');

    Route::get('/user', 'Auth\AuthController@user');

    Route::get('/user/notifications', 'Auth\AuthController@notifications');

    Route::get('/user/notifications/read', 'Auth\AuthController@readNotifications');

    Route::post('/subscribe', 'Auth\AuthController@subscribe');

    Route::get('/authors', 'Auth\AuthController@authors');
});

//----------------------------------------
//          Issues Routes
//----------------------------------------

Route::post('/issues', 'Issue\IssueController@store');

Route::get('/issues', 'Issue\IssueController@index');

Route::delete('/issues/{id}', 'Issue\IssueController@destroy');

//----------------------------------------
//          Fallback route
//----------------------------------------

// IMPLEMENT THIS

Route::fallback(function () {
    return response()->json(['message' => 'Not found'], 404);
});
