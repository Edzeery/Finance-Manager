<?php

namespace App\Http\Controllers\Income;

use App\Http\Controllers\Controller;
use App\Http\Requests\Income\StoreIncomeCategoryRequest;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomeCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_fr', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 15), config('finance.per_page_max', 100));
        $categories = $query->orderBy('sort_order')->paginate($perPage);

        return view('income.categories', compact('categories'));
    }

    public function store(StoreIncomeCategoryRequest $request)
    {
        IncomeCategory::create(array_merge($request->validated(), [
            'user_id' => auth()->id(),
            'sort_order' => IncomeCategory::max('sort_order') + 1,
        ]));

        return redirect()->back()->with('success', __('messages.created'));
    }

    public function update(StoreIncomeCategoryRequest $request, IncomeCategory $category)
    {
        $this->authorize('update', $category);
        $category->update($request->validated());

        return redirect()->back()->with('success', __('messages.updated'));
    }

    public function destroy(IncomeCategory $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return redirect()->back()->with('success', __('messages.deleted'));
    }
}
