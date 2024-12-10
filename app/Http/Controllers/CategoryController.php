<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories|max:255',
            'description' => 'required|max:255',
        ]);

        $category = Category::query()->create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'status' => 'success',
            'data' => $category
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found!'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found!'
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:255',
        ]);

        // Tasvirni yuklab saqlash
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories_images', 'public');
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully!',
            'status' => 'success',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found!'
            ]);
        }
        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully!'
        ]);
    }
}
