<?php

namespace App\Http\Controllers\V3\Group;

use App\Http\Controllers\Controller;
use App\Models\V3\GroupTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GroupTagController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('group_tag')
            ->join('groups', 'group_tag.group_id', '=', 'groups.id')
            ->join('tags', 'group_tag.tag_id', '=', 'tags.id')
            ->select(
                'group_tag.id',
                'group_tag.group_id',
                'groups.name as group_name',
                'group_tag.tag_id',
                'tags.title as tag_title',
                'group_tag.created_at',
                'group_tag.updated_at'
            );

        if ($request->filled('group_id')) {
            $query->where('group_tag.group_id', $request->input('group_id'));
        }

        if ($request->filled('tag_id')) {
            $query->where('group_tag.tag_id', $request->input('tag_id'));
        }

        return response()->json($query->orderBy('group_tag.id')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'tag_id'   => ['required', 'exists:tags,id'],
        ]);

        $exists = DB::table('group_tag')
            ->where('group_id', $validated['group_id'])
            ->where('tag_id', $validated['tag_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This tag is already associated with the group.'
            ], 200);
        }

        GroupTag::create($validated);

        return response()->json([
            'message' => 'Tag assigned to group successfully.'
        ], 201);
    }

    public function show($id)
    {
        $record = DB::table('group_tag')
            ->join('groups', 'group_tag.group_id', '=', 'groups.id')
            ->join('tags', 'group_tag.tag_id', '=', 'tags.id')
            ->select(
                'group_tag.id',
                'group_tag.group_id',
                'groups.name as group_name',
                'group_tag.tag_id',
                'tags.title as tag_title',
                'group_tag.created_at',
                'group_tag.updated_at'
            )
            ->where('group_tag.id', $id)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Association not found.'], 404);
        }

        return response()->json($record);
    }

    public function destroy($group_id, $tag_id)
    {
        $deleted = DB::table('group_tag')
            ->where('group_id', $group_id)
            ->where('tag_id', $tag_id)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Association not found.'], 404);
        }

        return response()->json(['message' => 'Tag removed from group.']);
    }
}
