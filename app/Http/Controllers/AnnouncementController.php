<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\StoreAnnouncement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Announcement;
use App\Attachment;
use App\Tag;
use App\User;
use App\Http\Resources\Announcement as AnnouncementResource;
use App\Events\NewAnnouncementWasCreatedEvent;

class AnnouncementController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('store', 'update', 'destroy');

        $this->middleware('api.id.check')->only('update', 'show', 'searchByTag', 'destroy');

        $this->middleware('api.tag.check')->only('searchByTag');

        $this->middleware('api.announcement.check')->only('show', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $local_ip = $request->session()->get('local_ip', 0);

        \Log::info(print $request->post());

        if ($local_ip == 1 or Auth::guard('api')->check()) {
            $announcements = Announcement::orderBy('updated_at', 'desc')->paginate(10);
        } elseif ($request->filled('access_token') && !Auth::guard('api')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        } else {
            $announcements = Announcement::whereHas('tags', function (Builder $query) {
                $query->where('is_public', '=', 1);
            })->orderBy('updated_at', 'desc')->paginate(10);
        }

        // Return announcements as a json
        return AnnouncementResource::collection($announcements);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\StoreAttachment  $request
     * @return \Illuminate\Http\Response[]
     */
    public function store(StoreAnnouncement $request)
    {
        if (Auth::guard('api')->check()) {
            $announcement = new Announcement;
            $announcement->title = $request->input('title');
            $announcement->eng_title = $request->input('eng_title');
            $announcement->body = $request->input('body');
            $announcement->eng_body = $request->input('eng_body');
            $announcement->has_eng = $request->input('has_eng');
            $announcement->is_pinned = $request->input('is_pinned');
            $announcement->pinned_until = $request->input('pinned_until');
            $announcement->is_event = $request->input('is_event');
            $announcement->event_location = $request->input('event_location');
            $announcement->event_start_time = $request->input('event_start_time');
            $announcement->event_end_time = $request->input('event_end_time');
            $announcement->gmaps = $request->input('gmaps');

            if (isset($request->user_id)) {
                if (auth('api')->user()->is_admin) {
                    $announcement->user_id = $request->user_id;
                } else {
                    return response()->json(['message' => 'Not permitted to upload as other person'], 401);
                }
            } else {
                $announcement->user_id = auth('api')->user()->id;
            }

            if ($announcement->save()) {
                // Array of tags
                $tags = array();
                foreach ($request->input('tags') as $id) {
                    $tag = Tag::findOrFail($id);
                    while (!is_null($tag->parent_id)) {
                        if (!is_null($tag->parent_id)) {
                            array_push($tags, $tag->id);
                        }
                        $tag = Tag::findOrFail($tag->parent_id);
                    }
                }
                $announcement->tags()->sync($tags);

                // Array of files
                if ($request->hasfile('attachments')) {
                    foreach ($request->file('attachments') as $order => $attachment) {
                        $attach = new Attachment;
                        $attach->announcement_id = $announcement->id;
                        $attach->filename = $attachment->getClientOriginalName();
                        $attach->content = file_get_contents($attachment);
                        $attach->filesize = $attachment->getSize();
                        $attach->mime_type = $attachment->getMimeType();
                        if (!$attach->save()) {
                            $announcement->delete();
                            return response()->json(['Message' => 'Error on insert'], 400);
                        }
                    }
                }
            }

            // Raise an event
            event(new NewAnnouncementWasCreatedEvent($announcement));

            // Return the attachment
            return new AnnouncementResource($announcement);
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get single announcement
        $announcement = Announcement::findOrFail($id);

        // Return announcement
        return new AnnouncementResource($announcement);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Requests\StoreAttachment  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAnnouncement $request, $id)
    {
        if (Auth::guard('api')->check()) {
            $announcement = Announcement::findOrFail($id);

            $announcement->title = $request->input('title');
            $announcement->eng_title = $request->input('eng_title');
            $announcement->body = $request->input('body');
            $announcement->eng_body = $request->input('eng_body');
            $announcement->has_eng = $request->input('has_eng');
            $announcement->is_pinned = $request->input('is_pinned');
            $announcement->pinned_until = $request->input('pinned_until');
            $announcement->is_event = $request->input('is_event');
            $announcement->event_location = $request->input('event_location');
            $announcement->event_start_time = $request->input('event_start_time');
            $announcement->event_end_time = $request->input('event_end_time');
            $announcement->gmaps = $request->input('gmaps');
            $announcement->touch();

            if ($announcement->update()) {
                // Array of tags []
                $tags = array();
                foreach ($request->input('tags') as $id) {
                    $tag = Tag::findOrFail($id);

                    while (!is_null($tag->parent_id)) {
                        if (!is_null($tag->parent_id)) {
                            array_push($tags, $tag->id);
                        }
                        $tag = Tag::findOrFail($tag->parent_id);
                    }
                }

                $announcement->tags()->sync($tags);

                // Turn all attachments sent into a collection
                $all_attachments = collect($request->input('attachments_old'));

                // Get the already uploaded attachments on the announcement, and grab the ID of each
                $existing_attachment_ids = $announcement->attachments()->pluck('id');

                // Get values that have an ID from our collection
                $old_announcements = $all_attachments->pluck('id');

                // Compare $existing_attachment_ids and $old_announcements
                // and delete the ones that are not present in $old_announcements
                $deleted_announcement_ids = $existing_attachment_ids
                    ->diff($old_announcements)
                    ->each(function ($value, $key) use ($announcement) {
                        Attachment::where('id', $value)->delete();
                    });

                // Insert new attachments
                if ($request->input('attachments')) {
                    foreach ($request->file('attachments') as $order => $attachment) {
                        $announcement->attachments()->create([
                            'announcement_id'  => $announcement->id,
                            'filename'         => $attachment->getClientOriginalName(),
                            'content'          => file_get_contents($attachment),
                            'filesize'         => $attachment->getSize(),
                            'mime_type'        => $attachment->getMimeType()
                        ]);
                    }
                }
            }

            // Return the attachment
            return new AnnouncementResource($announcement);
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::guard('api')->check()) {
            // Get single announcement
            $announcement = Announcement::findOrFail($id);

            // Return announcements
            if ($announcement->delete() && $announcement->attachments()->delete()) {
                $announcements = Announcement::orderBy('updated_at', 'desc')->paginate(10);
                return AnnouncementResource::collection($announcements);
            }
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    /**
     * Display events.
     *
     * @return \Illuminate\Http\Response
     */
    public function events()
    {
        $events = Announcement::select('id', 'title', 'event_location', 'gmaps', 'event_start_time', 'event_end_time')
            ->orderBy('updated_at', 'desc')
            ->where('is_event', 1)
            ->paginate(12);

        return $events;
    }

    /**
     * Display pinned announcements.
     *
     * @return array
     */
    public function pinned(Request $request)
    {
        $local_ip = $request->session()->get('local_ip', 0);

        if ($local_ip == 1 or Auth::guard('api')->check()) {
            $pinned = Announcement::where([['is_pinned', '=', 1], ['pinned_until', '>=', (string)date("Y-m-d H:i", time())]])
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'desc')->get(['id', 'title']);
        } else {
            $pinned = Announcement::whereHas('tags', function (Builder $query) {
                $query->where('is_public', '=', 1);
            })
                ->where([['is_pinned', '=', 1], ['pinned_until', '>=', (string)date("Y-m-d H:i", time())]])
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'desc')->get(['id', 'title']);
        }

        return $pinned;
    }

    /**
     * Search based on given tag.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchByTag($id, Request $request)
    {
        $local_ip = $request->session()->get('local_ip', 0);

        if ($local_ip == 1  or Auth::guard('api')->check()) {
            $results = Announcement::whereHas('tags', function (Builder $query) use ($id) {
                $query->where('id', '=', $id);
            })
                ->orderBy('updated_at', 'desc')->paginate(10);
        } else {
            $results = Announcement::whereHas('tags', function (Builder $query) use ($id) {
                $query->where('id', '=', $id);
            })->whereHas('tags', function (Builder $query) {
                $query->where('is_public', '=', 1);
            })
                ->orderBy('updated_at', 'desc')->paginate(10);
        }

        return AnnouncementResource::collection($results);
    }

    /**
     * Search based on given tag.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchByAuthor($id, Request $request)
    {
        $local_ip = $request->session()->get('local_ip', 0);

        if ($local_ip == 1  or Auth::guard('api')->check()) {
            $results = Announcement::where('user_id', $id)
                ->orderBy('updated_at', 'desc')->paginate(10);
        } else {
            $results = Announcement::where('user_id', $id)
                ->whereHas('tags', function (Builder $query) {
                    $query->where('is_public', '=', 1);
                })
                ->orderBy('updated_at', 'desc')->paginate(10);
        }

        return AnnouncementResource::collection($results);
    }

    /**
     * Search based custom input.
     *
     * @return \Illuminate\Http\Response
     */
    public function customSearch(Request $request)
    {
        $local_ip = $request->session()->get('local_ip', 0);

        if ($local_ip == 1  or Auth::guard('api')->check()) {
            $results = Announcement::orderBy('updated_at', 'desc');
        } else {
            $results = Announcement::whereHas('tags', function (Builder $query) {
                $query->where('is_public', '=', 1);
            })
                ->orderBy('updated_at', 'desc');
        }

        $params = json_decode($request->input("q"));

        $results = $results->where(function ($query) use ($params) {
            foreach ($params as $param) {
                $query->orWhere('title', 'LIKE', '%' . $param . '%')
                    ->orWhere('eng_title', 'LIKE', '%' . $param . '%');
            }
        });

        $results = $results->distinct()->paginate(10);
        return AnnouncementResource::collection($results);
    }

    /**
     * FSS feed.
     *
     * @return \Illuminate\Http\Response
     */
    public function rss(Request $request, Announcement $announcement)
    {
        return show($announcement);
    }
}
