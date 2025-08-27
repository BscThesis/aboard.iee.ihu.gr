<?php

namespace App\Http\Controllers\V3\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V3\Group;
use Illuminate\Validation\ValidationException;

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
        // normalize name
        $request->merge(['name' => trim((string) $request->input('name'))]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:groups,name',
                'description' => 'nullable|string|max:1000',
                'parent_group' => 'nullable|exists:groups,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

        // Optional: extra case-insensitive existence guard
        $existing = \App\Models\V3\Group::whereRaw('LOWER(name) = ?', [mb_strtolower($validated['name'])])->first();
        if ($existing) {
            return response()->json([
                'message' => 'Group already exists.',
                'group'   => $existing,
            ], 409);
        }

        $group = \App\Models\V3\Group::create($validated);
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
            'parent_group' => 'nullable|exists:groups,id',
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
