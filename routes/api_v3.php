<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V3 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API V3 routes for your application.
| These routes reflect the new group-based and role-based structure.
|
*/

Route::prefix('v3')->group(function () {

    /**
     * Announcements routes
     */
    Route::prefix('announcements')->group(function () {
        Route::middleware(['auth.v3.author'])->group(function () {
            Route::get('/my_announcements', 'Announcement\AnnouncementController@user_announcements');
            Route::get('/edit_view/{id}', 'Announcement\AnnouncementController@showForEdit');
            Route::post('/', 'Announcement\AnnouncementController@store');
            Route::put('/{id}', 'Announcement\AnnouncementController@update');
            Route::delete('/{id}', 'Announcement\AnnouncementController@destroy');
        });
        Route::get('/', 'Announcement\AnnouncementController@index');

        Route::middleware(['auth.v3.can_show_announcement'])->group(function () {
            Route::get('/{id}', 'Announcement\AnnouncementController@show');
        });

        Route::get('/{announcement_id}/attachments/{attachment_id}', 'Attachment\AttachmentController@show');
    });

    /**
     * Tags Routes
     */
    Route::prefix('tags')->group(function () {
        Route::middleware(['auth.v3.admin'])->group(function () {
            Route::post('/', 'Tag\TagController@store');
            Route::put('/{id}', 'Tag\TagController@update');
            Route::delete('/{id}', 'Tag\TagController@destroy');
        });
        Route::get('/', 'Tag\TagController@index');
        Route::get('/{id}', 'Tag\TagController@show');
        Route::get('/{id}/users', 'Tag\TagController@returnUsers');
    });

    Route::get('/filtertags', 'Tag\TagController@indexForFiltering');
    Route::get('/subscribetags', 'Tag\TagController@basicIndexing');
    Route::get('/all_tags', 'Tag\TagController@indexForAnnouncementCreation');
    Route::get('/most_used_tags', 'Tag\TagController@indexMostUsed');

    /**
     * Authors
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
     * Issues Routes
     */
    Route::middleware(['auth.v3.admin'])->prefix('issues')->group(function () {
        Route::post('/', 'Issue\IssueController@store');
        Route::get('/', 'Issue\IssueController@index');
        Route::delete('/{id}', 'Issue\IssueController@destroy');
    });

    /**
     * Group Routes
     */
    Route::prefix('groups')->middleware(['auth.v3.master'])->group(function () {
        Route::get('/', 'Group\GroupController@index');
        Route::get('/{id}', 'Group\GroupController@show');
        Route::post('/', 'Group\GroupController@store');
        Route::put('/{id}', 'Group\GroupController@update');
        Route::delete('/{id}', 'Group\GroupController@destroy');
    });
    // @TODO: Uncomment the above route group when the authentication middleware is ready.
    // Route::prefix('groups')->group(function () {
    //     Route::get('/', 'Group\GroupController@index');
    //     Route::get('/{id}', 'Group\GroupController@show');
    //     Route::post('/', 'Group\GroupController@store');
    //     Route::put('/{id}', 'Group\GroupController@update');
    //     Route::delete('/{id}', 'Group\GroupController@destroy');
    // });

    /**
     * UserHasGroup Routes
     */
    Route::prefix('user-group-roles')->middleware(['auth.v3.master'])->group(function () {
        Route::get('/', 'Group\UserHasGroupController@index');
        Route::get('/{user_id}/{group_id}', 'Group\UserHasGroupController@show');
        Route::post('/', 'Group\UserHasGroupController@store');
        Route::match(['put', 'patch'], '/{user_id}/{group_id}', 'Group\UserHasGroupController@update');
        Route::delete('/{user_id}/{group_id}', 'Group\UserHasGroupController@destroy');
    });

    Route::prefix('group-tags')->middleware(['auth.v3.admin'])->group(function () {
        Route::get('/', 'Group\GroupTagController@index');
        Route::get('/{id}', 'Group\GroupTagController@show');
        Route::post('/', 'Group\GroupTagController@store');
        Route::delete('/{group_id}/{tag_id}', 'Group\GroupTagController@destroy');
    });
});

/**
 * Global fallback & OPTIONS
 */
Route::options('*', function () {
    return response()->json([], 200);
});
Route::fallback(function () {
    return response()->json(['message' => 'Not found'], 404);
});
