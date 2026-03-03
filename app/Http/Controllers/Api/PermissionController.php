<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Module filter
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $permissions = $query->paginate($perPage);

        return response()->json([
            'data' => PermissionResource::collection($permissions),
            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'nullable|string',
            'module' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission = Permission::create($request->only([
            'name',
            'display_name',
            'module',
            'description',
            'status',
        ]));

        return response()->json([
            'data' => new PermissionResource($permission),
            'message' => 'Permission created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return response()->json([
            'data' => new PermissionResource($permission->load('roles')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'display_name' => 'nullable|string',
            'module' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permission->update($request->only([
            'name',
            'display_name',
            'module',
            'description',
            'status',
        ]));

        return response()->json([
            'data' => new PermissionResource($permission),
            'message' => 'Permission updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission has roles
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete permission assigned to roles',
            ], 422);
        }

        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully',
        ]);
    }

    /**
     * Get all modules.
     */
    public function modules()
    {
        $modules = Permission::select('module')
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->distinct()
            ->pluck('module')
            ->sort()
            ->values();

        return response()->json([
            'data' => $modules,
        ]);
    }
}
