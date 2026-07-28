<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class CategoryController extends Controller
{
    abstract protected function getModelClass(): string;

    abstract protected function getValidationRules(): array;

    abstract protected function getStoreView(): string;

    abstract protected function getIndexRoute(): string;

    abstract protected function getUpdateRoute(): string;

    abstract protected function getDestroyRoute(): string;

    public function create()
    {
        return redirect()->route($this->getIndexRoute());
    }

    public function edit(Model $category)
    {
        return redirect()->route($this->getIndexRoute());
    }

    public function index(Request $request)
    {
        $modelClass = $this->getModelClass();
        $query = $modelClass::query();

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

        return view($this->getStoreView(), compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());

        $modelClass = $this->getModelClass();
        $modelClass::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'sort_order' => $modelClass::max('sort_order') + 1,
        ]));

        return redirect()->back()->with('success', __('messages.created'));
    }

    public function update(Request $request, Model $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate($this->getValidationRules());

        $category->update($validated);

        return redirect()->back()->with('success', __('messages.updated'));
    }

    public function destroy(Model $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return redirect()->back()->with('success', __('messages.deleted'));
    }
}
