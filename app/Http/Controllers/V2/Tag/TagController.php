<?php

namespace App\Http\Controllers\V2\Tag;

use Session;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\V1;
use App\Http\Requests\V1\StoreTag;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Tag;
use App\User;
use App\Http\Resources\Tag as TagResource;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        // Get every tag as a Json

        $extra_fields = json_decode(request()->input('extra_fields', ''));
       
        if(auth('api_v2')->check() && auth('api_v2')->user()->is_author && $extra_fields == 'self_stats'){
            $results = DB::select( DB::raw(
                "SELECT announcementCounter, T.id, T.is_public, T.maillist_name, T.parent_id, T.title
                 FROM tags T
                 LEFT JOIN (SELECT tag_id, COUNT(*) AS announcementCounter
                 FROM announcement_tag INNER JOIN (SELECT id AS announcement_id FROM `announcements`  WHERE user_id=".auth()->user()->id." ORDER BY updated_at DESC LIMIT 20) AS A USING(announcement_id)                  
                 GROUP BY tag_id ) AS Tag_stats ON T.id=Tag_stats.`tag_id`
                 WHERE T.deleted_at IS NULL
                 ORDER BY announcementCounter DESC, title"
                 ) );
            return $results;
        }
        $tags = Tag::orderBy('title', 'asc')->get();
        return TagResource::collection($tags);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexMostUsed()
    {   
        // Get every tag as a Json
       
        if(auth('api_v2')->check() && auth('api_v2')->user()->is_author){
            $results = DB::select( DB::raw(
                "SELECT tags.*, SUM(UNIX_TIMESTAMP(announcements.created_at) / 100000000) AS weight FROM abroad.tags 
                JOIN announcement_tag ON announcement_tag.tag_id = tags.id
                JOIN abroad.announcements ON announcements.id  = announcement_tag.announcement_id
                WHERE announcements.user_id = 1
                AND tag_id != tags.parent_id
                GROUP BY tags.id
                ORDER BY weight DESC
                LIMIT 5"
                 ) );
            return $results;
        }
        
        return [];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexForFiltering(Request $request)
    {
        // If user is logged in or inside university's wifi return tags, filtering and then counting every announcement each one has with their children
        $local_ip = $request->session()->get('local_ip', 0);
        if ($local_ip == 1 or auth('api_v2')->check()) {
            $tags = Tag::with('childrenRecursive')->where('parent_id',1)->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    (request()->input('title', '')),
                    (request()->input('body', '')),
                    (request()->input('updatedAfter', '')),
                    (request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('title', 'asc')->get();
        } 
        // Else return tags filtering and then counting every public announcement each one has with their children
        else {
            $tags = Tag::with('childrenRecursive')->where([['parent_id',1],['is_public',1]])->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    (request()->input('title', '')),
                    (request()->input('body', '')),
                    (request()->input('updatedAfter', '')),
                    (request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('title', 'asc')->get();
        }    
        return $tags;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexForAnnouncementCreation(Request $request)
    {
        // If user is logged in or inside university's wifi return tags, filtering and then counting every announcement each one has with their children
        $tags = [];
        if (auth('api_v2')->check()) {
            $tags = Tag::with('childrenRecursive')->where('parent_id',1)->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    (request()->input('title', '')),
                    (request()->input('body', '')),
                    (request()->input('updatedAfter', '')),
                    (request()->input('updatedBefore', '')),
                );
            }])->orderBy('title', 'asc')->get();
        } 

        return $tags;
    }

     /*
     * 
     * Display all listing tags.
     * 
     */
    public function basicIndexing(Request $request)
    {
        $tags = Tag::with('childrensubRecursive')->where('parent_id', null)->orderBy('title','asc')->get();
        return $tags;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreTag  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTag $request)
    {
        // Check if the request's params is properly form for Tag Model and create a new Tag
    	try {
            $validated = $request->validated();
            $tag = Tag::create($validated);
        }
        catch (\Exception $exception) {
            return response()->json(["error" => "Record not created successfully!"], 406);
        }
        return $tag;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Show Tag with an id of $id
        $tag = Tag::findOrFail($id);
        return new TagResource($tag);
    }

    /**
     * Returns all users that subed to specific tag
     *
     * @return void
     */
    public function returnUsers($id, Request $request)
    {
        $admin_ip = $request->session()->get('admin_ip', 0);
        if($admin_ip){
            $tag = Tag::findOrFail($id);
            if($tag === null){
                return response()->json(['message' => 'No tag was found ..'], 404);
            }else{
		        return $tag->users()->get();
            }
        }else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\StoreTag  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTag $request, $id)
    {
        // Find a Tag with an id of $id
        $tag = Tag::find($id);
        // Check if the request's params is properly form for Tag Model and update an existing Tag
        $validated = $request->validated();
	    $tag->update($validated);
        return new TagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // If user is admin find the Tag with an id of $id and try to delete it then return every Tag as Json
        if (auth('api_v2')->user()->is_admin) {
            $tag = Tag::find($id);
            if ($tag->delete()) {
                $tags = Tag::orderBy('id', 'desc')->get();
                return TagResource::collection($tags);
            }
        } else {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }
    }
}
