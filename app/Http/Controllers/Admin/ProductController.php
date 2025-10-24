<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * HAPUS CONSTRUCTOR - tidak perlu middleware di controller
     * Gunakan manual check di setiap method
     */

    public function index()
    {
        // MANUAL ADMIN CHECK
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        // MANUAL ADMIN CHECK
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        $validated['is_active'] = $request->has('is_active');

        // Upload main image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Upload gallery images
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        // MANUAL ADMIN CHECK
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($product->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        }

        $validated['is_active'] = $request->has('is_active');

        // Update main image
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Update gallery images
        if ($request->hasFile('gallery')) {
            // Delete old gallery images
            if ($product->gallery) {
                foreach ($product->gallery as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            
            $galleryPaths = [];
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // MANUAL ADMIN CHECK
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}