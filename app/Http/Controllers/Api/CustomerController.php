<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Customer; 

class CustomerController extends BaseController
{
    public function index(Request $request) {
        try {
            $query = Customer::query();
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

            $customers = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->paginated($customers,'Customers retrieved successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:customers,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            $customer = Customer::create($validatedData);

            return $this->success('Customer created successfully.',$customer);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function show($id) {
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return $this->notFound('Customer not found.');
            }

            return $this->success('Customer retrieved successfully.',$customer);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function update(Request $request, $id) {
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return $this->notFound('Customer not found.');
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:customers,email,' . $customer->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            $customer->update($validatedData);

            return $this->success('Customer updated successfully.',$customer);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return $this->notFound('Customer not found.');
            }

            $customer->delete();

            return $this->success('Customer deleted successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
