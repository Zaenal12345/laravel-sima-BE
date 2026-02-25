<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Merk;

class MerkController extends BaseController
{
    public function index(Request $request) {
        try {
            $query = Merk::query();
            $perPage = $request->input('per_page', 10);
            
            if($request->has('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%$search%");
            }

            if($request->has('status')) {
                $status = $request->input('status');
                $query->where('status', $status);
            }

            $merks = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->paginated($merks, 'Merks retrieved successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            $merk = Merk::create($validatedData);

            return $this->success('Merk created successfully.',$merk);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function show($id) {
        try {
            $merk = Merk::find($id);

            if (!$merk) {
                return $this->notFound('Merk not found.');
            }

            return $this->success('Merk retrieved successfully.',$merk);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function update(Request $request, $id) {
        try {
            $merk = Merk::find($id);

            if (!$merk) {
                return $this->notFound('Merk not found.');
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            $merk->update($validatedData);

            return $this->success('Merk updated successfully.',$merk);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $merk = Merk::find($id);

            if (!$merk) {
                return $this->notFound('Merk not found.');
            }

            $merk->delete();

            return $this->success('Merk deleted successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
