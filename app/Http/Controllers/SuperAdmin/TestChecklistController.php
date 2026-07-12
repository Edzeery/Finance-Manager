<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasBreadcrumbs;
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
            ['id' => 'environment', 'label' => 'super-admin.tab_environment', 'categories' => ['1. البيئة والإعدادات']],
            ['id' => 'auth', 'label' => 'super-admin.tab_auth', 'categories' => ['2. التسجيل والمصادقة', '3. الإعداد الأولي']],
            ['id' => 'payments', 'label' => 'super-admin.tab_payments', 'categories' => ['4.1 Chargily', '4.2 Stripe', '4.3 PayPal', '4.4 Noest', '4.5 Manual', '5. الاشتراكات والفواتير']],
            ['id' => 'assets', 'label' => 'super-admin.tab_assets', 'categories' => ['6. إدارة الأصول', '7. الإيرادات والمصروفات', '8. الميزانيات', '9. الديون']],
            ['id' => 'zakat', 'label' => 'super-admin.tab_zakat', 'categories' => ['10. الزكاة', '11. لوحة التحكم والتقارير', '12. البحث العام']],
            ['id' => 'settings', 'label' => 'super-admin.tab_settings', 'categories' => ['13. إعدادات المستخدم', '14. الصلاحيات والأدوار']],
            ['id' => 'infrastructure', 'label' => 'super-admin.tab_infrastructure', 'categories' => ['15. مهام الخلفية', '16. الأمان', '17. البريد الإلكتروني', '18. النسخ الاحتياطي', '19. الأداء والتوافق']],
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
        if (!file_exists($path)) {
            return redirect()->route('super.admin.test-checklist.index')
                ->with('error', __('super-admin.test_checklist_import_not_found'));
        }

        $content = file_get_contents($path);
        $lines = explode("\n", $content);

        $updated = 0;
        foreach ($lines as $line) {
            if (!preg_match('/^- \[(x|~|-| )\] (.+)$/', $line, $matches)) {
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

        $lines = [];
        $lines[] = '# 📋 FMZS — قائمة التحقق اليدوي (Admin)';
        $lines[] = '';
        $lines[] = '> **الهدف:** التحقق من جاهزية المنصة للإطلاق من منظور الإدارة.';
        $lines[] = '> يتم تنفيذ القائمة كاملة على بيئة **staging** ثم **production**.';
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '';

        $categoryTotals = [];
        $categoryPassed = [];

        foreach ($groups as $category => $catItems) {
            $isSubCategory = preg_match('/^\d+\.\d+/', $category);

            if ($isSubCategory) {
                $lines[] = "### {$category}";
            } else {
                $lines[] = "## ✅ {$category}";
            }
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

            $mainCategory = $isSubCategory ? preg_replace('/\..*/', '', $category) : null;

            if ($mainCategory) {
                $categoryTotals[$mainCategory] = ($categoryTotals[$mainCategory] ?? 0) + $catItems->count();
                $categoryPassed[$mainCategory] = ($categoryPassed[$mainCategory] ?? 0) + $catItems->where('status', 'passed')->count();
            } else {
                $categoryTotals[$category] = $catItems->count();
                $categoryPassed[$category] = $catItems->where('status', 'passed')->count();
            }
        }

        $lines[] = '---';
        $lines[] = '';
        $lines[] = '## 🏁 جدول النتائج';
        $lines[] = '';
        $lines[] = '| القسم | العلامة |';
        $lines[] = '|-------|---------|';

        $grandTotal = 0;
        $grandPassed = 0;
        $categoryLabels = [
            '1. البيئة والإعدادات' => '1. البيئة',
            '2. التسجيل والمصادقة' => '2. التسجيل',
            '3. الإعداد الأولي' => '3. الإعداد الأولي',
            '4' => '4. بوابات الدفع',
            '5. الاشتراكات والفواتير' => '5. الاشتراكات',
            '6. إدارة الأصول' => '6. الأصول',
            '7. الإيرادات والمصروفات' => '7. الإيرادات والمصروفات',
            '8. الميزانيات' => '8. الميزانيات',
            '9. الديون' => '9. الديون',
            '10. الزكاة' => '10. الزكاة',
            '11. لوحة التحكم والتقارير' => '11. لوحة التحكم',
            '12. البحث العام' => '12. البحث',
            '13. إعدادات المستخدم' => '13. الإعدادات',
            '14. الصلاحيات والأدوار' => '14. الصلاحيات',
            '15. مهام الخلفية' => '15. المهام',
            '16. الأمان' => '16. الأمان',
            '17. البريد الإلكتروني' => '17. البريد',
            '18. النسخ الاحتياطي' => '18. النسخ الاحتياطي',
            '19. الأداء والتوافق' => '19. الأداء',
        ];

        foreach ($categoryTotals as $catKey => $total) {
            $passed = $categoryPassed[$catKey] ?? 0;
            $label = $categoryLabels[$catKey] ?? $catKey;
            $lines[] = "| {$label} | {$passed}/{$total} |";
            $grandTotal += $total;
            $grandPassed += $passed;
        }

        $lines[] = '| **المجموع** | **' . $grandPassed . '/' . $grandTotal . '** |';
        $lines[] = '';
        $lines[] = '**النتيجة النهائية:**';

        $overallPercent = $grandTotal > 0 ? round(($grandPassed / $grandTotal) * 100) : 0;

        if ($overallPercent >= 90) {
            $lines[] = '- ✅ **جاهز للإطلاق** — ≥ 90% (' . $grandPassed . '+' . ' نقطة)';
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
