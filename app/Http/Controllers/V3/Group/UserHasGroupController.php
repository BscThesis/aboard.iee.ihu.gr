<?php

namespace App\Http\Controllers\V3\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserHasGroupController extends Controller
{
    /**
     * Canonical roles allowed by DB enum (must match exactly).
     */
    private const ALLOWED_ROLES = ['student', 'staff', 'admin'];

    public function index(Request $request)
    {
        $query = DB::table('user_has_group')
            ->join('users', 'user_has_group.user_id', '=', 'users.id')
            ->join('groups', 'user_has_group.group_id', '=', 'groups.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'groups.id as group_id',
                'groups.name as group_name',
                'user_has_group.role'
            );

        if ($request->filled('user_id')) {
            $query->where('user_has_group.user_id', $request->input('user_id'));
        }
        if ($request->filled('group_id')) {
            $query->where('user_has_group.group_id', $request->input('group_id'));
        }

        return response()->json($query->get());
    }

    public function show($user_id, $group_id)
    {
        $row = DB::table('user_has_group')
            ->join('users', 'user_has_group.user_id', '=', 'users.id')
            ->join('groups', 'user_has_group.group_id', '=', 'groups.id')
            ->where('user_has_group.user_id', $user_id)
            ->where('user_has_group.group_id', $group_id)
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'groups.id as group_id',
                'groups.name as group_name',
                'user_has_group.role'
            )
            ->first();

        if (!$row) {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        return response()->json($row);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'user_id'  => 'required|exists:users,id',
                'group_id' => 'required|exists:groups,id',
                'role'     => ['required', Rule::in(self::ALLOWED_ROLES)],
            ],
            [
                'role.in' => 'Invalid role. Allowed roles are: ' . implode(', ', self::ALLOWED_ROLES) . '.',
            ]
        );

        $exists = DB::table('user_has_group')
            ->where('user_id', $validated['user_id'])
            ->where('group_id', $validated['group_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This user is already assigned to the group.'
            ], 200);
        }

        DB::table('user_has_group')->insert([
            'user_id'    => $validated['user_id'],
            'group_id'   => $validated['group_id'],
            'role'       => $validated['role'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'User added to group successfully.'
        ], 201);
    }

    public function update(Request $request, $user_id, $group_id)
    {
        $validated = $request->validate(
            ['role' => ['required', Rule::in(self::ALLOWED_ROLES)]],
            ['role.in' => 'Invalid role. Allowed roles are: ' . implode(', ', self::ALLOWED_ROLES) . '.']
        );

        $row = DB::table('user_has_group')
            ->where('user_id', $user_id)
            ->where('group_id', $group_id)
            ->first();

        if (!$row) {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        if ($row->role === $validated['role']) {
            return response()->json(['message' => 'No changes. Role is already set to this value.'], 200);
        }

        DB::table('user_has_group')
            ->where('user_id', $user_id)
            ->where('group_id', $group_id)
            ->update([
                'role'       => $validated['role'],
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Role updated successfully.'], 200);
    }

    public function destroy($user_id, $group_id)
    {
        $deleted = DB::table('user_has_group')
            ->where('user_id', $user_id)
            ->where('group_id', $group_id)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'User removed from group.']);
        }

        return response()->json(['error' => 'Record not found.'], 404);
    }
}
