<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Models\TestChecklistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TestChecklistController extends Controller
{
    use HasBreadcrumbs;

    public function index(): View
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.test_checklist'));

        $items = TestChecklistItem::with('tester')->orderBy('sort_order')->get();
        $groups = $items->groupBy('category');

        $stats = [
            'total' => $items->count(),
            'passed' => $items->where('status', 'passed')->count(),
            'failed' => $items->where('status', 'failed')->count(),
            'skipped' => $items->where('status', 'skipped')->count(),
            'pending' => $items->where('status', 'pending')->count(),
        ];

        $categoryProgress = $groups->map(function ($groupItems) {
            $total = $groupItems->count();
            $passed = $groupItems->where('status', 'passed')->count();
            $failed = $groupItems->where('status', 'failed')->count();
            $done = $passed + $failed;

            return [
                'total' => $total,
                'passed' => $passed,
                'failed' => $failed,
                'done' => $done,
                'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        });

        $tabs = self::getTabDefinitions();
        $tabStats = [];
        foreach ($tabs as $tab) {
            $tabItems = collect();
            foreach ($tab['categories'] as $category) {
                if (isset($groups[$category])) {
                    $tabItems = $tabItems->merge($groups[$category]);
                }
            }
            $total = $tabItems->count();
            $passed = $tabItems->where('status', 'passed')->count();
            $failed = $tabItems->where('status', 'failed')->count();
            $done = $passed + $failed;
            $tabStats[$tab['id']] = [
                'total' => $total,
                'passed' => $passed,
                'failed' => $failed,
                'done' => $done,
                'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        }

        return view('super-admin.test-checklist', $this->withBreadcrumbs(
            compact('groups', 'stats', 'categoryProgress', 'tabs', 'tabStats')
        ));
    }

    public static function getTabDefinitions(): array
    {
        return [
            [
                'id' => 'admin',
                'label' => 'super-admin.tab_admin_panel',
                'categories' => [
                    'لوحة الإدارة | 1. البيئة والإعدادات',
                    'لوحة الإدارة | 2. إدارة المستخدمين',
                    'لوحة الإدارة | 3. إدارة مساحات العمل',
                    'لوحة الإدارة | 4. إدارة الباقات والخطط',
                    'لوحة الإدارة | 5. إدارة الاشتراكات',
                    'لوحة الإدارة | 6. إدارة المدفوعات',
                    'لوحة الإدارة | 7. إدارة بوابات الدفع',
                    'لوحة الإدارة | 8. إدارة الكوبونات',
                    'لوحة الإدارة | 9. إدارة الضرائب والرسوم',
                    'لوحة الإدارة | 10. الأدوار والصلاحيات',
                    'لوحة الإدارة | 11. الإعدادات العامة',
                    'لوحة الإدارة | 12. الإعلانات',
                    'لوحة الإدارة | 13. النسخ الاحتياطي',
                    'لوحة الإدارة | 14. الصفحة التعريفية',
                ],
            ],
            [
                'id' => 'user',
                'label' => 'super-admin.tab_user_panel',
                'categories' => [
                    'لوحة المستخدم | 1. التسجيل والمصادقة',
                    'لوحة المستخدم | 2. الإعداد الأولي',
                    'لوحة المستخدم | 3. لوحة التحكم والتقارير',
                    'لوحة المستخدم | 4. الأصول',
                    'لوحة المستخدم | 5. الإيرادات',
                    'لوحة المستخدم | 6. المصروفات',
                    'لوحة المستخدم | 7. الفئات',
                    'لوحة المستخدم | 8. الميزانيات',
                    'لوحة المستخدم | 9. الديون',
                    'لوحة المستخدم | 10. الأهداف المالية',
                    'لوحة المستخدم | 11. الزكاة',
                    'لوحة المستخدم | 12. المعاملات',
                    'لوحة المستخدم | 13. البحث العام',
                    'لوحة المستخدم | 14. الاشتراكات والفواتير',
                    'لوحة المستخدم | 15. بوابات الدفع',
                    'لوحة المستخدم | 16. الأعضاء والصلاحيات',
                    'لوحة المستخدم | 17. إعدادات المستخدم',
                    'لوحة المستخدم | 18. إعدادات مساحة العمل',
                    'لوحة المستخدم | 19. سجل النشاط',
                    'لوحة المستخدم | 20. مهام الخلفية',
                ],
            ],
        ];
    }

    public function update(Request $request, TestChecklistItem $item): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:passed,failed,skipped,pending'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $item->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $item->notes,
            'tested_by' => in_array($validated['status'], ['passed', 'failed']) ? $request->user()->id : null,
            'tested_at' => in_array($validated['status'], ['passed', 'failed']) ? now() : null,
        ]);

        $item->load('tester');

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'status' => $item->status,
                'notes' => $item->notes,
                'tested_by' => $item->tester?->name,
                'tested_at' => $item->tested_at?->diffForHumans(),
            ],
        ]);
    }

    public function stats(): JsonResponse
    {
        $items = TestChecklistItem::all();
        $groups = $items->groupBy('category');
        $tabs = self::getTabDefinitions();

        $stats = [
            'total' => $items->count(),
            'passed' => $items->where('status', 'passed')->count(),
            'failed' => $items->where('status', 'failed')->count(),
            'skipped' => $items->where('status', 'skipped')->count(),
            'pending' => $items->where('status', 'pending')->count(),
        ];

        $tabStats = [];
        foreach ($tabs as $tab) {
            $tabItems = collect();
            foreach ($tab['categories'] as $category) {
                if (isset($groups[$category])) {
                    $tabItems = $tabItems->merge($groups[$category]);
                }
            }
            $total = $tabItems->count();
            $passed = $tabItems->where('status', 'passed')->count();
            $failed = $tabItems->where('status', 'failed')->count();
            $done = $passed + $failed;
            $tabStats[$tab['id']] = [
                'total' => $total,
                'passed' => $passed,
                'failed' => $failed,
                'done' => $done,
                'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        }

        return response()->json(compact('stats', 'tabStats'));
    }

    public function reset(): RedirectResponse
    {
        TestChecklistItem::query()->update([
            'status' => 'pending',
            'notes' => null,
            'tested_by' => null,
            'tested_at' => null,
        ]);

        return redirect()->route('super.admin.test-checklist.index')
            ->with('success', __('super-admin.test_checklist_reset'));
    }

    public function updateNotes(Request $request, TestChecklistItem $item): JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $item->update(['notes' => $validated['notes']]);

        return response()->json(['success' => true]);
    }

    public function importMarkdown(): RedirectResponse
    {
        $path = base_path('docs/test_manual.md');
        if (! file_exists($path)) {
            return redirect()->route('super.admin.test-checklist.index')
                ->with('error', __('super-admin.test_checklist_import_not_found'));
        }

        $content = file_get_contents($path);
        $lines = explode("\n", $content);

        $updated = 0;
        foreach ($lines as $line) {
            if (! preg_match('/^- \[(x|~|-| )\] (.+)$/', $line, $matches)) {
                continue;
            }

            $checkbox = $matches[1];
            $description = trim($matches[2]);

            $status = match ($checkbox) {
                'x' => 'passed',
                '~' => 'failed',
                '-' => 'skipped',
                default => 'pending',
            };

            $item = TestChecklistItem::where('description', $description)->first();
            if ($item && $item->status !== $status) {
                $item->update(['status' => $status]);
                $updated++;
            }
        }

        return redirect()->route('super.admin.test-checklist.index')
            ->with('success', __('super-admin.test_checklist_imported', ['count' => $updated]));
    }

    public function exportMarkdown(): RedirectResponse
    {
        $items = TestChecklistItem::orderBy('sort_order')->get();
        $groups = $items->groupBy('category');
        $tabs = self::getTabDefinitions();

        $lines = [];
        $lines[] = '# 📋 FMZS — قائمة التحقق اليدوي';
        $lines[] = '';
        $lines[] = '> **الهدف:** التحقق من جاهزية المنصة للإطلاق من منظور الإدارة والمستخدم.';
        $lines[] = '> يتم تنفيذ القائمة كاملة على بيئة **staging** ثم **production**.';
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        foreach ($tabs as $tab) {
            $lines[] = "## 🗂️ {$tab['label']}";
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';

            foreach ($tab['categories'] as $category) {
                $catItems = $groups[$category] ?? collect();

                if ($catItems->isEmpty()) {
                    continue;
                }

                $lines[] = "### ✅ {$category}";
                $lines[] = '';

                foreach ($catItems as $item) {
                    $checkbox = match ($item->status) {
                        'passed' => 'x',
                        'failed' => '~',
                        'skipped' => '-',
                        default => ' ',
                    };
                    $lines[] = "- [{$checkbox}] {$item->description}";
                    if ($item->details) {
                        $lines[] = "    > {$item->details}";
                    }
                }
                $lines[] = '';
            }

            $tabItems = collect();
            foreach ($tab['categories'] as $category) {
                if (isset($groups[$category])) {
                    $tabItems = $tabItems->merge($groups[$category]);
                }
            }
            $tabTotal = $tabItems->count();
            $tabPassed = $tabItems->where('status', 'passed')->count();

            $lines[] = '---';
            $lines[] = '';
            $lines[] = "### 🏁 جدول النتائج — {$tab['label']}";
            $lines[] = '';
            $lines[] = '| القسم | العلامة |';
            $lines[] = '|-------|---------|';

            $grandTabTotal = 0;
            $grandTabPassed = 0;

            foreach ($tab['categories'] as $category) {
                $catItems = $groups[$category] ?? collect();
                if ($catItems->isEmpty()) {
                    continue;
                }
                $total = $catItems->count();
                $passed = $catItems->where('status', 'passed')->count();
                $shortName = preg_replace('/^لوحة (الإدارة|المستخدم) \| /', '', $category);
                $lines[] = "| {$shortName} | {$passed}/{$total} |";
                $grandTabTotal += $total;
                $grandTabPassed += $passed;
            }

            $lines[] = '| **المجموع** | **'.$grandTabPassed.'/'.$grandTabTotal.'** |';
            $lines[] = '';
        }

        $grandTotal = $items->count();
        $grandPassed = $items->where('status', 'passed')->count();
        $overallPercent = $grandTotal > 0 ? round(($grandPassed / $grandTotal) * 100) : 0;

        $lines[] = '---';
        $lines[] = '';
        $lines[] = '## 🏆 النتيجة الإجمالية';
        $lines[] = '';
        $lines[] = '| التبويب | العلامة |';
        $lines[] = '|---------|---------|';

        foreach ($tabs as $tab) {
            $tabItems = collect();
            foreach ($tab['categories'] as $category) {
                if (isset($groups[$category])) {
                    $tabItems = $tabItems->merge($groups[$category]);
                }
            }
            $tabTotal = $tabItems->count();
            $tabPassed = $tabItems->where('status', 'passed')->count();
            $lines[] = "| {$tab['label']} | {$tabPassed}/{$tabTotal} |";
        }

        $lines[] = '| **المجموع الكلي** | **'.$grandPassed.'/'.$grandTotal.'** |';
        $lines[] = '';
        $lines[] = '**النتيجة النهائية:**';

        if ($overallPercent >= 90) {
            $lines[] = '- ✅ **جاهز للإطلاق** — ≥ 90%';
        } elseif ($overallPercent >= 70) {
            $lines[] = '- 🔄 **تحسينات قبل الإطلاق** — 70%-89%';
        } else {
            $lines[] = '- ❌ **غير جاهز** — < 70%';
        }

        $content = implode("\n", $lines);

        try {
            file_put_contents(base_path('docs/test_manual.md'), $content);
        } catch (\Throwable $e) {
            Log::error('Failed to export test checklist markdown', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('super.admin.test-checklist.index')
                ->with('error', __('super-admin.test_checklist_export_failed'));
        }

        return redirect()->route('super.admin.test-checklist.index')
            ->with('success', __('super-admin.test_checklist_exported'));
    }
}
