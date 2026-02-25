<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends BaseController
{
    public function index(Request $request) {
        try {
            $query = Supplier::query();
            $perPage = $request->input('per_page', 10);
            
            if($request->has('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%");
            }

            if($request->has('status')) {
                $status = $request->input('status');
                $query->where('status', $status);
            }

            $suppliers = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->paginated($suppliers, 'Suppliers retrieved successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:suppliers,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            $supplier = Supplier::create($validatedData);

            return $this->success('Supplier created successfully.',$supplier);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function show($id) {
        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return $this->notFound('Supplier not found.');
            }

            return $this->success('Supplier retrieved successfully.',$supplier);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function update(Request $request, $id) {
        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return $this->notFound('Supplier not found.');
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            $supplier->update($validatedData);

            return $this->success('Supplier updated successfully.',$supplier);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return $this->notFound('Supplier not found.');
            }

            $supplier->delete();

            return $this->success('Supplier deleted successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
