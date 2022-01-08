<?php

namespace App\Http\Controllers\Issue;

use App\Http\Controllers\Controller;
use App\Models\Issue;
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
        $this->middleware('auth:web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth('web')->user()->is_admin) {
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
        return auth('web')->user()->issues()->create($validated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth('web')->user()->is_admin) {
            $issue = Issue::findOrFail($id);
            if ($issue->delete()) {
                $issues = issue::orderBy('id', 'desc')->get();
                return issueResource::collection($issues);
            }
        } else {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }
    }
}
