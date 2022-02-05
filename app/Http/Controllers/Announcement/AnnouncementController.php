<?php

namespace App\Http\Controllers\Announcement;

use Session;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\StoreAnnouncement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\Attachment;
use App\Models\Tag;
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
        $this->middleware('auth:web')->only('store', 'update', 'destroy');
        
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
        // Check if header has authorization and if user is not logged in and then try to log in from the token
        if ($request->headers->has('authorization') && !Auth::guard('web')->check()) {
            try{
                $socialiteUser = Socialite::driver('iee')->userFromToken($request->bearerToken());
                $user = User::where('uid', $socialiteUser['uid'])->first();
                Auth('web')->login($user);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                Auth('web')->logout();
                Session::flush();
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
        }
        $per_page = request()->input('perPage',10);
        $sort_id = request()->input('sortId',0);
        // If user is logged in or inside university's wifi return all filtered announcements
        $local_ip = $request->session()->get('local_ip', 0);
        if ($local_ip == 1 or Auth::guard('web')->check()) {
            $announcements = Announcement::withFilters( request()->input('users', []),request()->input('tags',[]),json_decode(request()->input('title', '')),json_decode(request()->input('body', '')))
            ->orderByRaw(Announcement::SORT_VALUES[$sort_id])->whereNull('deleted_at');
        } 
        // Else return only public filtered announcements
        else {
            $announcements = Announcement::withFilters( request()->input('users', []),request()->input('tags',[]),json_decode(request()->input('title', '')),json_decode(request()->input('body', '')))
            ->whereHas('tags', function (Builder $query) {
                $query->where('is_public', 1);
            })->orderByRaw(Announcement::SORT_VALUES[$sort_id])->whereNull('deleted_at');
        }    
        // Return announcements as a json and paginated by $per_page value
        $announcements = $announcements->distinct()->paginate($per_page);
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
        // If user is logged in create a new Announcement Instance
        if (Auth::guard('web')->check()) {
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

            // If a user_id exists in request set it as the author only if the logged in user is an admin
            if (isset($request->user_id)) {
                if (auth('web')->user()->is_admin) {
                    $announcement->user_id = $request->user_id;
                } else {
                    return response()->json(['message' => 'Not permitted to upload as other person'], 401);
                }
            } 
            // Else just set the user as the author.
            else {
                $announcement->user_id = auth('web')->user()->id;
            }

            // If saving the Announcement Instance completes successfully
            if ($announcement->save()) {
                // Set Announcement's Tags if they exist.
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

                // If the request has attachments create new Attachment instance and save them
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

            // Raise an event that a new Announcement was created
            event(new NewAnnouncementWasCreatedEvent($announcement));

            // Return the announcement
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
        // If user is logged in
        if (Auth::guard('web')->check()) {
            // Find the announcement and set the request params
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

            // If update is successful
            if ($announcement->update()) {
                // Add tags to announcement
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
        // If user is logged in 
        if (Auth::guard('web')->check()) {
            // Get single announcement
            $announcement = Announcement::findOrFail($id);

            // Return announcements after deleting the announcement with an id of $id
            if ($announcement->delete() && $announcement->attachments()->delete()) {
                $announcements = Announcement::orderBy('updated_at', 'desc')->paginate(10);
                return AnnouncementResource::collection($announcements);
            }
        } else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    /**
     * RSS feed.
     *
     * @return \Illuminate\Http\Response
     */
    public function rss(Request $request, Announcement $announcement)
    {
        return show($announcement);
    }
}
