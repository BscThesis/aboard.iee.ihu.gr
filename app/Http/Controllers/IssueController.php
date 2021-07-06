<?php

namespace App\Http\Controllers;

use App\Issue;
use Illuminate\Http\Request;
use App\Http\Requests\StoreIssue;
use App\Http\Resources\Issue as IssueResource;

class IssueController extends Controller
{

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth('api')->user()->is_admin) {
            $issues = Issue::orderBy('created_at')->get();
            return IssueResource::collection($issues);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIssue $request)
    {
        $validated = $request->validated();
        return auth('api')->user()->issues()->create($validated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function destroy(Issue $issue)
    {
        //
    }
}
