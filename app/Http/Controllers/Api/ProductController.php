<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Storage;

use App\Models\Product;
use App\Models\Variant;

class ProductController extends BaseController
{
    public function index(Request $request) {
        try {
            $query = Product::with(['category', 'merk', 'supplier']);
            $perPage = $request->input('per_page', 10);
            
            if($request->has('search')) {
                $search = $request->input('search');
                $query->where('name', 'like', "%$search%");
            }

            if($request->has('category_id')) {
                $categoryId = $request->input('category_id');
                $query->where('category_id', $categoryId);
            }

            if($request->has('merk_id')) {
                $merkId = $request->input('merk_id');
                $query->where('merk_id', $merkId);
            }

            if($request->has('supplier_id')) {
                $supplierId = $request->input('supplier_id');
                $query->where('supplier_id', $supplierId);
            }

            if($request->has('status')) {
                $status = $request->input('status');
                $query->where('status', $status);
            }

            $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->paginated($products, 'Products retrieved successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'merk_id' => 'required|exists:merks,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'basic_price' => 'required|numeric',
                'seller_price' => 'nullable|numeric',
                'status' => 'required|in:active,inactive',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'is_have_variant' => 'required|boolean',
                'stock' => 'required_if:is_have_variant,false|integer|min:0',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $validatedData['image'] = $imagePath;
            }

            $product = Product::create($validatedData);
            $productId = $product->id;

            if($product->is_have_variant) {
                $variants = $request->input('variants', []);
                foreach ($variants as $variantData) {
                    $variant = new Variant();
                    $variant->product_id = $productId;
                    $variant->name = $variantData['name'];
                    $variant->additional_price = $variantData['additional_price'] ?? 0;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->save();
                }
            }

            return $this->success('Product created successfully.',$product);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function show($id) {
        try {
            $product = Product::with(['category', 'merk', 'supplier'])->find($id);
            if (!$product) {
                return $this->notFound('Product not found.');
            }
            return $this->success('Product retrieved successfully.', $product);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function update(Request $request, $id) {
        try {
            $product = Product::find($id);
            if (!$product) {
                return $this->notFound('Product not found.');
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'merk_id' => 'required|exists:merks,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'basic_price' => 'required|numeric',
                'seller_price' => 'nullable|numeric',
                'status' => 'required|in:active,inactive',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'is_have_variant' => 'required|boolean',
                'stock' => 'required_if:is_have_variant,false|integer|min:0',
            ]);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $validatedData['image'] = $imagePath;
            }

            $product->update($validatedData);

            if($product->is_have_variant) {
                $variants = $request->input('variants', []);
                Variant::where('product_id', $product->id)->delete();
                foreach ($variants as $variantData) {
                    $variant = new Variant();
                    $variant->product_id = $product->id;
                    $variant->name = $variantData['name'];
                    $variant->additional_price = $variantData['additional_price'] ?? 0;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->save();
                }
            }

            return $this->success('Product updated successfully.', $product);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $product = Product::find($id);
            if (!$product) {
                return $this->notFound('Product not found.');
            }

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            if($product->is_have_variant) {
                Variant::where('product_id', $product->id)->delete();
            }

            $product->delete();

            return $this->success('Product deleted successfully.');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
