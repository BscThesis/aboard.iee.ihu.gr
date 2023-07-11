<?php

namespace App\Http\Controllers\V2\Announcement;

use Session;
use App\Http\Controllers\V2\AuthorController;
use Illuminate\Http\Request;
use App\Http\Requests\V2;
use App\Http\Requests\V2\StoreAnnouncement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\V2\Announcement;
use App\Models\V2\Attachment;
use App\Models\V2\Tag;
use App\Http\Resources\AnnouncementV2 as AnnouncementResource;
use App\Http\Resources\DeletedAnnouncement;
use App\Events\V2\NewAnnouncementWasCreatedEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends AuthorController
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $per_page = request()->input('perPage',10);
        $sort_id = request()->input('sortId',0);
        $page = request()->input('page', 1);
        $only_deleted = request()->input('only_deleted', 0);

        $content_type = request()->header('Content-Type', '');
        $is_ical = $content_type == 'text/calendar';

        // If user is logged in or inside university's wifi return all filtered announcements
        $local_ip = $request->session()->get('local_ip', 0);

        if ($local_ip == 1 or auth('api_v2')->check()) {

            list($announcements, $count_total) = $this->create_announcements_query_build(
                [
                    'users' => request()->input('users', []),
                    'tags' => request()->input('tags',[]),
                    'title' => request()->input('title', ''),
                    'body' => request()->input('body', ''),
                    'updatedAfter' => request()->input('updatedAfter',''),
                    'updatedBefore' => request()->input('updatedBefore',''),
                    'is_ical' => $is_ical,
                    'fetch_public' => false,
                    'only_deleted' => $only_deleted
                ],
                $page, $per_page, $sort_id
            );
            
        } 
        else { // Else return only public filtered announcements
            list($announcements, $count_total) = $this->create_announcements_query_build(
                [
                    'users' => request()->input('users', []),
                    'tags' => request()->input('tags',[]),
                    'title' => request()->input('title', ''),
                    'body' => request()->input('body', ''),
                    'updatedAfter' => request()->input('updatedAfter',''),
                    'updatedBefore' => request()->input('updatedBefore',''),
                    'is_ical' => $is_ical,
                    'fetch_public' => true,
                    'only_deleted' => $only_deleted
                ],
                $page, $per_page, $sort_id
            );
        }    
        
        $announcements = $announcements->get();

        if ($only_deleted == 1) {
            return DeletedAnnouncement::collection($announcements);
        }
        else {
            $announcements_collection = AnnouncementResource::collection($announcements);
        }
        
        
        if ($is_ical) {
            header('Content-type: text/calendar; charset=utf-8');
            echo $this->collection_to_ical($announcements_collection);
            exit;
        }

        
        return $this->create_paginate_collection($announcements_collection, $page, $per_page, $count_total);
    }

    protected function create_paginate_collection($collection, $page, $per_page, $count_total) 
    {
        $total_pages = ceil($count_total / $per_page);
        $ret = collect([
            'data' => $collection,
            'meta' => [
                "current_page" => $page,
                "from" => 1,
                "last_page" => $total_pages,
                "path" => env("APP_URL") ."/api/v2/announcements",
                "per_page" => $per_page,
                "to" => $count_total,
                "total" => $count_total
            ]
        ]);

        return $ret;
    }
    protected function create_announcements_query_build($args, $page, $per_page, $sort_id) 
    {
        extract($args);

        $offset = $per_page * ($page - 1);
        
        if (!isset($only_deleted)) {
            $only_deleted = 0;
        }

        if ($only_deleted == 1) {
            $announcements = Announcement::onlyTrashed()->withFilters(
                $users,
                $tags,
                $title,
                $body,
                $updatedAfter,
                $updatedBefore,
                $is_ical,
                $fetch_public
            )
            ->select('announcements.*')
            ->orderByRaw(Announcement::SORT_VALUES[$sort_id])
            ->skip($offset)->take($per_page);
            // ->toSql();
    
            // echo $announcements;
            // exit;
    
            $count_total = Announcement::onlyTrashed()->withFilters(
                $users,
                $tags,
                $title,
                $body,
                $updatedAfter,
                $updatedBefore,
                $is_ical,
                $fetch_public
            )
            ->select(DB::raw('distinct IFNULL(count(announcements.id) OVER(), 0) as total'))
            ->get(0)
            ->first();
    
            if ($count_total) {
                $count_total = $count_total->total;
            }
            else {
                $count_total = 0;
            }
    
            return [$announcements, $count_total];
        }
        $announcements = Announcement::withFilters(
            $users,
            $tags,
            $title,
            $body,
            $updatedAfter,
            $updatedBefore,
            $is_ical,
            $fetch_public
        )
        ->select('announcements.*')
        ->orderByRaw(Announcement::SORT_VALUES[$sort_id])->whereNull('announcements.deleted_at')
        ->skip($offset)->take($per_page);
        // ->toSql();

        // echo $announcements;
        // exit;

        $count_total = Announcement::withFilters(
            $users,
            $tags,
            $title,
            $body,
            $updatedAfter,
            $updatedBefore,
            $is_ical,
            $fetch_public
        )
        ->select(DB::raw('distinct IFNULL(count(announcements.id) OVER(), 0) as total'))
        ->whereNull('announcements.deleted_at')
        ->get(0)
        ->first();

        if ($count_total) {
            $count_total = $count_total->total;
        }
        else {
            $count_total = 0;
        }

        return [$announcements, $count_total];
    }

    /**
     * Transform announcements collection to ical
     * 
     * @return String 
     */
    protected function collection_to_ical($collection) 
    {
        
        $ret = "BEGIN:VCALENDAR\n";
        $ret .= "VERSION:2.0\n";
        $ret .= "PRODID:-//it/iee//ihu v1.0\n";
        

        foreach ($collection as $item) {
            $DTSTAMP = date('Ymd\THis\Z', strtotime($item->created_at));
            $DTSTART = date('Ymd\THis\Z', strtotime($item->event_start_time));
            $DTEND = date('Ymd\THis\Z', strtotime($item->event_end_time));
            $description = $this->get_ical_complied_string($item->body);
            
            $preview = strip_tags($item->body);
            $preview_complied = $this->get_ical_complied_string($preview);
            $event_location = $this->get_ical_complied_string($item->event_location);

           
            $ret .= "BEGIN:VEVENT\n";
            $ret .= "UID:{$item->id}-iee-ihu\n";
            $ret .= "DTSTAMP:{$DTSTAMP}\n";
            $ret .= "DTSTART:{$DTSTART}\n";
            $ret .= "DTEND:{$DTEND}\n";
            $ret .= "NAME:{$item->title}\n";
            $ret .= "DESCRIPTION:{$preview_complied}\n";
            $ret .= "X-ALT-DESC;FMTTYPE=text/html:<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 3.2//EN\">
            <HTML><BODY>\n{$description}\n</BODY></HTML>\n";
            $ret .= "LOCATION:{$event_location}\n";
            $ret .= "END:VEVENT\n";
        }
        $ret .= "END:VCALENDAR\n";

        return $ret;
    }

    private function get_ical_complied_string($ogString) {
        ini_set('memory_limit', '2024M');
        $compliedArray = [];
        $str_len = strlen($ogString);
        for($i = 0; $i < $str_len; $i += 75) {
            $compliedArray[] = mb_substr($ogString, $i, 75, 'utf8');
        }

        return implode("\n", $compliedArray);
    }

    /**
     * Display a listing of the current user's announcements.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_announcements(Request $request)
    {
        
        if (! $user = auth('api_v2')->user()) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        if (!$user->is_author && !$user->is_admin) {
            return response()->json(['message' => 'You are not an author'], 401);
        }

        $per_page = request()->input('perPage',10);
        $sort_id = request()->input('sortId',0);
        $page = request()->input('page', 1);
        
        $content_type = request()->header('Content-Type', '');
        $is_ical = $content_type == 'text/calendar';

        $sort_id = request()->input('sortId',0);
        list($announcements, $count_total) = $this->create_announcements_query_build(
            [
                'users' => [$user->id],
                'tags' => request()->input('tags',[]),
                'title' => request()->input('title', ''),
                'body' => request()->input('body', ''),
                'updatedAfter' => request()->input('updatedAfter',''),
                'updatedBefore' => request()->input('updatedBefore',''),
                'is_ical' => $is_ical,
                'fetch_public' => false
            ],
            $page, $per_page, $sort_id
        );
        
        $announcements = $announcements->get();
        $announcements_collection = AnnouncementResource::collection($announcements);
        
        
        if ($is_ical) {
            header('Content-type: text/calendar; charset=utf-8');
            echo $this->collection_to_ical($announcements_collection);
            exit;
        }

        
        return $this->create_paginate_collection($announcements_collection, $page, $per_page, $count_total);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\StoreAttachment  $request
     * @return \Illuminate\Http\Response[]
     */
    public function store(StoreAnnouncement $request)
    {
        // var_dump($request); exit;
        if (!auth('api_v2')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if (!auth('api_v2')->check() || (!auth('api_v2')->user()->is_author && !auth('api_v2')->user()->is_admin)) {
            return response()->json(['message' => 'You are not an author'], 401);
        }
        if (!$request) {
            return response()->json(['message' => 'Request body seems to be empty'], 400);
        }
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
            if (auth('api_v2')->user()->is_admin) {
                $announcement->user_id = $request->user_id;
            } else {
                return response()->json(['message' => 'Not permitted to upload as other person'], 401);
            }
        } 
        // Else just set the user as the author.
        else {
            $announcement->user_id = auth('api_v2')->user()->id;
        }

        // If saving the Announcement Instance completes successfully
        if ($announcement->save()) {
            // Set Announcement's Tags if they exist.
            // Array of tags
            $tags = array();
            foreach ($request->input('tags') as $id) {
                $tag = Tag::findOrFail($id);
                array_push($tags, $tag->id);
                $parent = $tag->parent_id;

                while (!is_null($parent)) {
                    // if (!is_null($tag->parent_id)) {}
                    $tag = Tag::findOrFail($parent);    
                    array_push($tags, $tag->id);
                    $parent = $tag->parent_id;                            
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
        } else {
            return response()->json(['message' => 'Announcement did not save'], 401);
        }

        // Raise an event that a new Announcement was created
        event(new NewAnnouncementWasCreatedEvent($announcement));

        // Return the announcement
        
        return new AnnouncementResource($announcement);
        
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
        try {
            $announcement = Announcement::findOrFail($id);
            return new AnnouncementResource($announcement);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Announcement not found'], 404);
        }

        // Return announcement
        
        
    }

    /**
     * Display the specified resource if the user is the author.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showForEdit($id)
    {
        if (! $user = auth('api_v2')->user()) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        if (!$user->is_author && !$user->is_admin) {
            return response()->json(['message' => 'You are not an author'], 401);
        }

        // Get single announcement
        try {
            $announcement = Announcement::findOrFail($id);
            if ($announcement->user_id !== $user->id && !$user->is_admin) {
                return response()->json(['message' => 'You are not the author'], 401);
            }
            return new AnnouncementResource($announcement);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Announcement not found'], 404);
        }

        // Return announcement
        
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAnnouncement $request, $id)
    {
        
        $announcement = $this->get_user_announcement($id);

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
            if (auth('api_v2')->user()->is_admin) {
                $announcement->user_id = $request->user_id;
            } else {
                return response()->json(['message' => 'Not permitted to upload as other person'], 401);
            }
        } 

        $announcement->touch();

        // If update is successful
        if ($announcement->update()) {
            // Add tags to announcement
            // Array of tags []
            $tags = array();
            foreach ($request->input('tags') as $id) {
                $tag = Tag::findOrFail($id);
                array_push($tags, $tag->id);
                $parent = $tag->parent_id;	    
                
                while (!is_null($parent)) {
                    // if (!is_null($tag->parent_id)) {}
                    $tag = Tag::findOrFail($parent); 
                    array_push($tags, $tag->id);
                    $parent = $tag->parent_id;			  
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
        if (!auth('api_v2')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if (!$user = auth('api_v2')->user()) {
            return response()->json(['message' => 'Something went wrong. Can\'t fetch user data. Please logout and login'], 401);
        }
        // Get single announcement
        $announcement = Announcement::find($id);

        if (!$announcement || ($user->id !== $announcement->user_id && !$user->is_admin)) {
            return response()->json(['message' => 'You can\'t edit this announcement'], 401);
        }

        // Return announcements after deleting the announcement with an id of $id
        if ($announcement->delete()) {
            $announcements = Announcement::orderBy('updated_at', 'desc')->paginate(10);
            // return new AnnouncementResource($old_announcement);

            return new AnnouncementResource($announcement);
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
