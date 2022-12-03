<?php

namespace App\Http\Controllers\V2\Issue;

use App\Http\Controllers\Controller;
use App\Models\V2\Issue;
use Illuminate\Http\Request;
use App\Http\Requests\V2\StoreIssue;
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
        $this->middleware('auth:web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // If user is admin get every Issue
        if (auth('api_v2')->user()->is_admin) {
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
        // Check if the request's params is properly form for Issue Model and create a new Issue
        $validated = $request->validated();
        return auth('api_v2')->user()->issues()->create($validated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // If user is admin find the Issue with an id of $id and try to delete it then return every Issue as Json
        if (auth('api_v2')->user()->is_admin) {
            $issue = Issue::findOrFail($id);
            if ($issue->delete()) {
                $issues = Issue::orderBy('id', 'desc')->get();
                return IssueResource::collection($issues);
            }
        } else {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }
    }
}
