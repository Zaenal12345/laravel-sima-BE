<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends BaseController
{
    public function index(Request $request) 
    {
        try {
            $query = Category::query();

            $perPage = $request->perPage ?? 10;

            if($request->has('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            if($request->has('status')) {
                $query->where('status',$request->status);
            }

            $categories = $query->select('id','name','status','description')->orderBy('created_at', 'desc')->paginate($perPage);
            return $this->paginated($categories, 'Categories retrieved successfully');

        } catch (\Throwable $th) {
            return $this->error('Failed to retrieve categories', $th->getMessage(), 500);
        }
    }

    public function show($id) 
    {
        try {
            $category = Category::select('id','name','status','description')->find($id);
            if(!$category) {
                return $this->notFound('Category not found');
            }
            return $this->success('Category retrieved successfully', $category);
        } catch (\Throwable $th) {
            return $this->error('Failed to retrieve category', $th->getMessage(), 500);
        }
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $category = Category::create($validated);
            return $this->success('Category created successfully', $category, 201);
        } catch (\Throwable $th) {
            return $this->error('Failed to create category', $th->getMessage(), 500);
        }
    }

    public function update(Request $request, $id) 
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories,name,'.$id,
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $category = Category::find($id);
            if(!$category) {
                return $this->notFound('Category not found');
            }
            $category->update($validated);
            return $this->success('Category updated successfully', $category);
        } catch (\Throwable $th) {
            return $this->error('Failed to update category', $th->getMessage(), 500);
        }
    }

    public function destroy($id) 
    {
        try {
            $category = Category::find($id);
            if(!$category) {
                return $this->notFound('Category not found');
            }
            $category->delete();
            return $this->success('Category deleted successfully');
        } catch (\Throwable $th) {
            return $this->error('Failed to delete category', $th->getMessage(), 500);
        }
    }
}
