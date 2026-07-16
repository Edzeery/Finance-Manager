<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataController extends Controller
{
    public function export(Request $request, string $entity, string $format)
    {
        $allowed = ['income', 'expense', 'transactions', 'debt', 'asset', 'budget', 'goal', 'zakat'];
        $allowedFormats = ['xlsx', 'csv'];

        abort_unless(in_array($entity, $allowed), 404);
        abort_unless(in_array($format, $allowedFormats), 404);

        $exportPerm = $entity === 'transactions' ? 'transaction.export' : $entity.'.export';
        abort_unless(auth()->user()->hasPermission($exportPerm, 'workspace'), 403);

        $filters = $request->only(['category', 'type', 'date_from', 'date_to', 'search', 'status']);
        $exportClass = 'App\\Exports\\'.ucfirst($entity).'Export';
        $export = new $exportClass($filters);

        $filename = $entity.'_'.now()->format('Y-m-d_Hi').'.'.$format;

        return Excel::download($export, $filename);
    }

    public function import(Request $request, string $entity)
    {
        $allowed = ['income', 'expense'];
        abort_unless(in_array($entity, $allowed), 404);

        abort_unless(auth()->user()->hasPermission($entity.'.import', 'workspace'), 403);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ]);

        $importClass = 'App\\Imports\\'.ucfirst($entity).'Import';
        $import = new $importClass(auth()->id(), auth()->user()->currentWorkspace->id);

        Excel::import($import, $request->file('file'));

        $failures = $import->getFailures();
        $count = $import->getImportedCount();

        if (! empty($failures)) {
            $msg = __('messages.import_partial', ['count' => $count]);
            foreach ($failures as $failure) {
                $msg .= ' '.__('messages.import_row_error', ['row' => $failure->row(), 'error' => implode(', ', $failure->errors())]);
            }

            return redirect()->back()->with('warning', $msg);
        }

        return redirect()->back()->with('success', __('messages.import_success', ['count' => $count]));
    }

    public function template(string $entity)
    {
        $allowed = ['income', 'expense'];
        abort_unless(in_array($entity, $allowed), 404);

        $headers = match ($entity) {
            'income' => ['date', 'description', 'category', 'amount', 'recurring', 'notes'],
            'expense' => ['date', 'description', 'category', 'amount', 'recurring', 'notes'],
        };

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, [date('Y-m-d'), 'Sample entry', '', '100.00', 'no', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$entity.'_template.csv"',
        ]);
    }
}
