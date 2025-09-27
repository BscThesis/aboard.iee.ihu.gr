<?php

namespace App\Http\Controllers\V3\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V3\Group;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::orderBy('name')->get();
        return response()->json($groups);
    }

    public function show($id)
    {
        $group = Group::findOrFail($id);
        return response()->json($group);
    }

    public function store(Request $request)
    {
        // normalize
        $request->merge([
            'name'         => trim((string) $request->input('name')),
            'parent_group' => $request->input('parent_group') ?: null,
            // accept raw expressions as-is; just trim empty to null
            'is_user'      => $this->normalizeExpr($request->input('is_user')),
            'is_author'    => $this->normalizeExpr($request->input('is_author')),
        ]);

        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255|unique:groups,name',
                'description'   => 'nullable|string|max:1000',
                'parent_group'  => 'nullable|exists:groups,id',
                'is_user'       => 'nullable|string',   // PHP-like expression string
                'is_author'     => 'nullable|string',   // PHP-like expression string
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

        // extra case-insensitive guard on name
        $existing = Group::whereRaw('LOWER(name) = ?', [mb_strtolower($validated['name'])])->first();
        if ($existing) {
            return response()->json([
                'message' => 'Group already exists.',
                'group'   => $existing,
            ], 409);
        }

        $group = Group::create($validated);
        return response()->json($group, 201);
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        // normalize (fallback to existing values if not sent)
        $request->merge([
            'name'         => $request->has('name') ? trim((string) $request->input('name')) : $group->name,
            'parent_group' => $request->input('parent_group') ?: null,
            'is_user'      => $request->has('is_user')   ? $this->normalizeExpr($request->input('is_user'))     : $group->is_user,
            'is_author'    => $request->has('is_author') ? $this->normalizeExpr($request->input('is_author'))   : $group->is_author,
        ]);

        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:groups,name,' . $id,
            'description'   => 'nullable|string|max:1000',
            'parent_group'  => 'nullable|exists:groups,id',
            'is_user'       => 'nullable|string',
            'is_author'     => 'nullable|string',
        ]);

        $group->update($validated);

        return response()->json($group);
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Group deleted successfully.']);
    }

    /**
     * Trim to null; keep case and content intact (expressions).
     */
    private function normalizeExpr($val): ?string
    {
        if ($val === null) return null;
        $v = trim((string) $val);
        return $v === '' ? null : $v;
    }
}
