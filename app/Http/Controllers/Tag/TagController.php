<?php

namespace App\Http\Controllers\Tag;

use Session;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\StoreTag;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
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
        $this->middleware('admin.ip.check')->only('returnUsers');

        $this->middleware('auth:web')->only('store', 'update', 'destroy');

        $this->middleware('api.id.check')->only('show', 'update', 'destroy');

        $this->middleware('api.tag.check')->only('show', 'update', 'destroy');
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
       
        if(Auth::guard('web')->check() && auth()->user()->is_author && $extra_fields == 'self_stats'){
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
    public function indexForFiltering(Request $request)
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
            } catch (\Throwable $e) {
                Auth('web')->logout();
                Session::flush();
                if ($user == null)
                    return response()->json(['message' => 'User not found! Login to our website first in order to activate your account.'], 404);
                return response()->json(['message' => $e->getMessage()], 401);
            }
        }
        // If user is logged in or inside university's wifi return tags, filtering and then counting every announcement each one has with their children
        $local_ip = $request->session()->get('local_ip', 0);
        if ($local_ip == 1 or Auth::guard('web')->check()) {
            $tags = Tag::with('childrenRecursive')->where('parent_id',1)->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('title', '')),
                    json_decode(request()->input('body', '')),
                    json_decode(request()->input('updatedAfter', '')),
                    json_decode(request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('title', 'asc')->get();
        } 
        // Else return tags filtering and then counting every public announcement each one has with their children
        else {
            $tags = Tag::with('childrenRecursive')->where([['parent_id',1],['is_public',1]])->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('title', '')),
                    json_decode(request()->input('body', '')),
                    json_decode(request()->input('updatedAfter', '')),
                    json_decode(request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('title', 'asc')->get();
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
        if (auth('web')->user()->is_admin) {
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
