<?php

namespace App\Http\Controllers\Goal;

use App\Contracts\Repositories\GoalRepositoryInterface;
use App\Enums\GoalStatus;
use App\Http\Controllers\BaseCrudController;
use App\Http\Requests\Goal\StoreGoalRequest;
use App\Http\Requests\Goal\UpdateGoalRequest;
use App\Models\FinancialGoal;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class FinancialGoalController extends BaseCrudController
{
    protected string $model = FinancialGoal::class;

    public function __construct(
        private GoalRepositoryInterface $goalRepo,
        private NotificationService $notificationService,
    ) {}

    protected function getModelClass(): string
    {
        return FinancialGoal::class;
    }

    protected function getRoutePrefix(): string
    {
        return 'goal';
    }

    protected function getViewPrefix(): string
    {
        return 'goal';
    }

    protected function getLangPrefix(): string
    {
        return 'goal';
    }

    protected function getIcon(): string
    {
        return 'bi-flag-fill';
    }

    protected function getRepositoryInterface(): string
    {
        return GoalRepositoryInterface::class;
    }

    protected function getRepository()
    {
        return $this->goalRepo;
    }

    public function index(Request $request)
    {
        $this->resetBreadcrumbs()->resourceBreadcrumbs('general.goal', 'goal.index', 'bi-flag-fill');

        $tab = $request->input('tab', $request->boolean('trashed') ? 'trashed' : 'all');
        $filters = array_merge(
            $request->only(['search', 'per_page']),
            ['trashed' => $tab === 'trashed']
        );
        if ($tab === GoalStatus::InProgress->value) {
            $filters['status'] = GoalStatus::InProgress->value;
        } elseif ($tab === GoalStatus::Completed->value) {
            $filters['status'] = GoalStatus::Completed->value;
        }

        $goals = $this->goalRepo->forUser(filters: $filters);

        $tabs = $this->buildTabs([
            'all' => ['label' => __('general.all'), 'scope' => null],
            'in_progress' => ['label' => __('goal.in_progress'), 'scope' => fn ($q) => $q->where('status', GoalStatus::InProgress->value)],
            'completed' => ['label' => __('goal.completed'), 'scope' => fn ($q) => $q->where('status', GoalStatus::Completed->value)],
            'trashed' => ['label' => __('general.trash'), 'scope' => fn ($q) => $q->onlyTrashed()],
        ]);

        return view('goal.index', $this->withBreadcrumbs(compact('goals', 'tab', 'tabs')));
    }

    public function create()
    {
        return view('goal.create');
    }

    public function store(StoreGoalRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['current_amount'] ??= 0;

        if ($data['status'] === GoalStatus::Completed->value) {
            $data['completed_at'] = now();
            $data['current_amount'] = $data['target_amount'];
        }

        $goal = $this->goalRepo->create($data);

        if ($goal->status === GoalStatus::Completed) {
            $this->notificationService->goalAchieved(
                auth()->id(),
                $goal->{'name_'.app()->getLocale()}
            );
        }

        return redirect()->route('goal.index')
            ->with('success', __('messages.goal_created'));
    }

    public function edit(FinancialGoal $goal)
    {
        $this->authorize('update', $goal);

        return view('goal.edit', compact('goal'));
    }

    public function update(UpdateGoalRequest $request, FinancialGoal $goal)
    {
        $this->authorize('update', $goal);

        $data = $request->validated();
        $wasCompleted = $goal->status === GoalStatus::Completed;

        if ($data['status'] === GoalStatus::Completed->value) {
            $data['completed_at'] = $goal->completed_at ?? now();
            $data['current_amount'] = $data['target_amount'];
        } else {
            $data['completed_at'] = null;
        }

        $this->goalRepo->update($goal, $data);

        if ($data['status'] === GoalStatus::Completed->value && ! $wasCompleted) {
            $this->notificationService->goalAchieved(
                auth()->id(),
                $goal->{'name_'.app()->getLocale()}
            );
        }

        return redirect()->route('goal.index')
            ->with('success', __('messages.goal_updated'));
    }

    public function restore($id)
    {
        $goal = $this->goalRepo->withTrashedFindOrFail($id);
        $this->authorize('restore', $goal);

        if ($goal->status === GoalStatus::Completed) {
            return redirect()->route('goal.index', ['tab' => 'trashed'])
                ->with('error', __('messages.cannot_restore_completed'));
        }

        $this->goalRepo->restore($goal);

        return redirect()->route('goal.index')
            ->with('success', __('messages.goal_restored'));
    }
}
