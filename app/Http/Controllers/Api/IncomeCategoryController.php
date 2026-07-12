<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IncomeCategory\StoreIncomeCategoryRequest;
use App\Http\Requests\Api\IncomeCategory\UpdateIncomeCategoryRequest;
use App\Models\IncomeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = IncomeCategory::orderBy('sort_order')->get();

        return response()->json($categories);
    }

    public function store(StoreIncomeCategoryRequest $request): JsonResponse
    {
        $category = IncomeCategory::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'workspace_id' => $request->user()->current_workspace_id,
        ]);

        return response()->json($category, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $category = IncomeCategory::find($id);

        if (!$category) {
            return response()->json(['message' => __('messages.not_found')], 404);
        }

        return response()->json($category);
    }

    public function update(UpdateIncomeCategoryRequest $request, int $id): JsonResponse
    {
        $category = IncomeCategory::find($id);

        if (!$category) {
            return response()->json(['message' => __('messages.not_found')], 404);
        }

        $category->update($request->validated());

        return response()->json($category);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $category = IncomeCategory::find($id);

        if (!$category) {
            return response()->json(['message' => __('messages.not_found')], 404);
        }

        $category->delete();

        return response()->json(['message' => __('messages.deleted')]);
    }
}
