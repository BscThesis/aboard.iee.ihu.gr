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
        $local_ip = $request->session()->get('local_ip', 0);
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
        if ($local_ip == 1 or Auth::guard('web')->check()) {
            $tags = Tag::with('childrenRecursive')->where('parent_id',1)->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('title', '')),
                    json_decode(request()->input('body', ''))
                );
            }])->orderBy('title', 'asc')->get();
        } else {
            $tags = Tag::with('childrenRecursive')->where('parent_id',1)->where('is_public',1)->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('title', '')),
                    json_decode(request()->input('body', ''))
                );
            }])->orderBy('title', 'asc')->get();
        }    
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
        $validated = $request->validated();
	return Tag::create($validated);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
        $tag = Tag::find($id);
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
