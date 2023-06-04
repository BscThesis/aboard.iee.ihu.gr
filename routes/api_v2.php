<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API V2 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v2')->group(function () {

    /**
     * Announcements routes
     * Every route that requires privileges will be under the according middleware in order to generalize the auth functionality
     */
    Route::prefix('announcements')->group(function () {
        Route::middleware(['auth.author'])->group(function () {
            Route::get('/my_announcements', 'Announcement\AnnouncementController@user_announcements');
            Route::get('/edit_view/{id}', 'Announcement\AnnouncementController@showForEdit');
            Route::post('/', 'Announcement\AnnouncementController@store');
            // Route::post('/', function (Request $request) {
            //     // dd(\Input::all());  // returns an empty array
            //     die($request->input('test')); // returns a null
            // });
            Route::put('/{id}', 'Announcement\AnnouncementController@update');
            Route::delete('/{id}', 'Announcement\AnnouncementController@destroy');
        });
        Route::get('/', 'Announcement\AnnouncementController@index');
        // auth.can_show_announcement
        Route::middleware(['auth.can_show_announcement'])->group(function () {
            Route::get('/{id}', 'Announcement\AnnouncementController@show');
        });
        
        
        /**
         * Attachments Routes
         */
        Route::get('/{announcement_id}/attachments/{attachment_id}', 'Attachment\AttachmentController@show');
    });

    /**
     * Tags Routes
     * Every route that requires privileges will be under the according middleware in order to generalize the auth functionality
     */
    Route::prefix('tags')->group(function () {
        Route::middleware(['auth.admin'])->group(function () {
            Route::post('/', 'Tag\TagController@store');
            Route::put('/{id}', 'Tag\TagController@update');
            Route::delete('/{id}', 'Tag\TagController@destroy');
        });
        Route::get('/', 'Tag\TagController@index');
        Route::get('/{id}', 'Tag\TagController@show');
        Route::get('/{id}/users', 'Tag\TagController@returnUsers');
    });

    /**
     * Tags Routes
     * TODO::Rename routes in order to comply with the rest of the router naming logic
     */
    Route::get('/filtertags', 'Tag\TagController@indexForFiltering');
    Route::get('/subscribetags', 'Tag\TagController@basicIndexing');
    Route::get('/all_tags', 'Tag\TagController@indexForAnnouncementCreation');
    Route::get('/most_used_tags', 'Tag\TagController@indexMostUsed');

    /**
     * Tags Routes
     * TODO::Rename routes in order to comply with the rest of the router naming logic
     */
    Route::get('/authors', 'Authors\AuthorsController@index');
    Route::get('/all_authors', 'Authors\AuthorsController@fetch_all');

    /**
     * Auth Routes
     */
    
    Route::get('/authenticate', 'Auth\AuthJWTController@redirect');
    Route::prefix('auth')->group(function () {
        Route::get('/login_web', 'Auth\AuthJWTController@signInWeb');
        Route::post('/token', 'Auth\AuthJWTController@generateToken');
        Route::get('/login', 'Auth\AuthJWTController@signIn');
        Route::get('/logout', 'Auth\AuthJWTController@logout');
        Route::get('/user', 'Auth\AuthJWTController@user');
        Route::get('/whoami', 'Auth\AuthJWTController@me');
        Route::get('/user/notifications', 'Auth\AuthJWTController@notifications');
        Route::get('/user/notifications/read', 'Auth\AuthJWTController@readNotifications');
        Route::post('/subscribe', 'Auth\AuthJWTController@subscribe');
        Route::get('/authors', 'Auth\AuthJWTController@authors');
        Route::get('/subscriptions', 'Auth\AuthJWTController@getSubscriptions');
    });

    /**
     * Isuues Routes
     * Here we implemented a way to utilise middleware and prefix at the same time to avoid repetitive uri's
     */
    Route::middleware(['auth.admin'])->prefix('issues')->group(function () {
        Route::post('/', 'Issue\IssueController@store');
        Route::get('/', 'Issue\IssueController@index');
        Route::delete('/{id}', 'Issue\IssueController@destroy');
    });
});

//----------------------------------------
//          Fallback route
//----------------------------------------

// IMPLEMENT THIS
Route::options('*', function () {
    return response()->json([], 200);
});
Route::fallback(function () {
    return response()->json(['message' => 'Not found'], 404);
});
