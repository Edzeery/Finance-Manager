<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseCategory::query();

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

        return view('expense.categories', compact('categories'));
    }

    public function store(StoreExpenseCategoryRequest $request)
    {
        ExpenseCategory::create(array_merge($request->validated(), [
            'user_id' => auth()->id(),
            'sort_order' => ExpenseCategory::max('sort_order') + 1,
        ]));

        return redirect()->back()->with('success', __('messages.created'));
    }

    public function update(StoreExpenseCategoryRequest $request, ExpenseCategory $category)
    {
        $this->authorize('update', $category);
        $category->update($request->validated());

        return redirect()->back()->with('success', __('messages.updated'));
    }

    public function destroy(ExpenseCategory $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return redirect()->back()->with('success', __('messages.deleted'));
    }
}
