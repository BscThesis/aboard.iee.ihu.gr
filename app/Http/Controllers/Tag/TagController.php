<?php

namespace App\Http\Controllers\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\StoreTag;
use App\Models\Tag;
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
        $this->middleware('auth:api')->only('store', 'update', 'destroy');

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
        if (auth('api')->user()->is_admin) {
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
