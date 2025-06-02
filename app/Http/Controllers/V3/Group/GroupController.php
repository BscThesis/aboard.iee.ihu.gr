<?php

namespace App\Http\Controllers\V3\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V3\Group;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Display a listing of the groups.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $groups = Group::orderBy('name')->get();
        return response()->json($groups);
    }

    /**
     * Display a single group.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $group = Group::findOrFail($id);
        return response()->json($group);
    }

    /**
     * Store a newly created group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $group = Group::create($validated);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Group could not be created.'], 406);
        }

        return response()->json($group, 201);
    }

    /**
     * Update the given group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        $group->update($validated);

        return response()->json($group);
    }

    /**
     * Delete the given group.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Group deleted successfully.']);
    }
}
