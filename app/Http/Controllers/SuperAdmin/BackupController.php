<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Http\Controllers\Controller;
use App\Services\AdminNotificationService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    use HasBreadcrumbs;

    public function index()
    {
        $this->resetBreadcrumbs()
            ->addBreadcrumb(__('super-admin.super_dashboard'), route('super.admin.dashboard'), 'bi-shield-shaded')
            ->addBreadcrumb(__('super-admin.backups'));

        $backups = collect();
        $disk = Storage::disk('local');

        try {
            $allDirs = $disk->directories('/');

            foreach ($allDirs as $dir) {
                $files = $disk->files($dir);
                foreach ($files as $f) {
                    if (str_ends_with($f, '.zip')) {
                        $backups->push([
                            'path' => $f,
                            'name' => basename($f),
                            'directory' => $dir,
                            'size' => $disk->size($f),
                            'last_modified' => $disk->lastModified($f),
                        ]);
                    }
                }
            }

            $backups = $backups->sortByDesc('last_modified')->values();
        } catch (\Exception $e) {
            Log::error('Failed to list backups: '.$e->getMessage());
        }

        return view('super-admin.backups', $this->withBreadcrumbs(compact('backups')));
    }

    public function create(AdminNotificationService $notificationService)
    {
        try {
            $exitCode = Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);

            if ($exitCode === 0) {
                $notificationService->backupCompleted('backup-'.now()->format('Y-m-d-His'));

                return redirect()->route('super.admin.backups.index')
                    ->with('success', __('super-admin.backup_created'));
            }

            Log::error('Backup failed with exit code: '.$exitCode, ['output' => Artisan::output()]);

            return redirect()->route('super.admin.backups.index')
                ->with('error', __('super-admin.backup_create_error'));
        } catch (\Exception $e) {
            Log::error('Backup exception: '.$e->getMessage());

            return redirect()->route('super.admin.backups.index')
                ->with('error', __('super-admin.backup_create_error'));
        }
    }

    public function download(string $directory, string $filename)
    {
        $disk = Storage::disk('local');
        $path = $this->sanitizeBackupPath($directory, $filename);

        abort_if($path === null || ! $disk->exists($path), 404);

        return $disk->download($path);
    }

    public function restore(string $directory, string $filename)
    {
        $disk = Storage::disk('local');
        $path = $this->sanitizeBackupPath($directory, $filename);

        abort_if($path === null || ! $disk->exists($path), 404);

        $tempDir = storage_path('app'.DIRECTORY_SEPARATOR.'backup-restore-temp');

        try {
            if (is_dir($tempDir)) {
                (new Filesystem)->cleanDirectory($tempDir);
            } else {
                mkdir($tempDir, 0755, true);
            }

            $zip = new ZipArchive;
            if ($zip->open($disk->path($path)) !== true) {
                throw new \RuntimeException('Cannot open zip archive');
            }
            $zip->extractTo($tempDir);
            $zip->close();

            $sqlFile = null;
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($files as $file) {
                if ($file->getExtension() === 'sql') {
                    $sqlFile = $file->getRealPath();
                    break;
                }
            }

            if (! $sqlFile) {
                throw new \RuntimeException('No SQL file found in backup archive');
            }

            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            $dumpPath = config('database.connections.mysql.dump.dump_binary_path', '');
            $mysqlBin = $dumpPath ? rtrim($dumpPath, '\\/').DIRECTORY_SEPARATOR.'mysql.exe' : 'mysql';

            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s %s %s < "%s"',
                $mysqlBin,
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                $dbPass ? '--password='.escapeshellarg($dbPass) : '',
                escapeshellarg($dbName),
                $sqlFile
            );

            Log::info('Restoring backup', ['file' => $filename, 'db' => $dbName]);

            exec($command.' 2>&1', $output, $exitCode);

            if ($exitCode !== 0) {
                throw new \RuntimeException('mysql import failed: '.implode("\n", $output));
            }

            return redirect()->route('super.admin.backups.index')
                ->with('success', __('super-admin.backup_restored'));
        } catch (\Exception $e) {
            Log::error('Backup restore failed', [
                'file' => $filename,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('super.admin.backups.index')
                ->with('error', __('super-admin.backup_restore_error'));
        } finally {
            if (is_dir($tempDir)) {
                (new Filesystem)->deleteDirectory($tempDir);
            }
        }
    }

    public function destroy(string $directory, string $filename)
    {
        $disk = Storage::disk('local');
        $path = $this->sanitizeBackupPath($directory, $filename);

        abort_if($path === null || ! $disk->exists($path), 404);

        $disk->delete($path);

        return redirect()->route('super.admin.backups.index')
            ->with('success', __('super-admin.backup_deleted'));
    }

    private function sanitizeBackupPath(string $directory, string $filename): ?string
    {
        $dir = basename($directory);
        $file = basename($filename);
        $path = $dir.'/'.$file;

        $disk = Storage::disk('local');
        $root = $disk->path('');
        $absolute = realpath($root.'/'.$path);

        if ($absolute === false || ! str_starts_with($absolute, realpath($root))) {
            return null;
        }

        return $path;
    }
}
