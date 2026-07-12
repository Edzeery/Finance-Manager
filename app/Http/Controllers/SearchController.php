<?php

namespace App\Http\Controllers;

use App\Contracts\Services\SearchServiceInterface;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use HasBreadcrumbs;

    public function __construct(
        private SearchServiceInterface $searchService,
    ) {}

    public function search(Request $request)
    {
        $this->resetBreadcrumbs()->addBreadcrumb(__('general.search_results'));

        $q = $request->input('q');
        if (!$q || strlen($q) < 2) {
            return redirect()->back()->withErrors(['q' => __('messages.search_min_length')]);
        }

        $workspace = config('app.current_workspace');
        $results = $this->searchService->search(query: $q, workspaceId: $workspace?->id);

        return view('search.results', $this->withBreadcrumbs(compact('results', 'q')));
    }
}
