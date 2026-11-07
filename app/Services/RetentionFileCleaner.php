<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class RetentionFileCleaner
{
    public function validatePath(string $disk, string $path): void
    {
        if ($disk !== 'legacy' && config('filesystems.disks.'.$disk.'.driver') !== 'local') {
            throw new RuntimeException('Retention cleanup requires a local storage disk.');
        }
        $filename = basename(str_replace('\\', '/', $path));
        if (! preg_match('/^[A-Za-z0-9][A-Za-z0-9_. -]*\.(pdf|doc|docx|xls|xlsx|png|jpe?g|gif|webp)$/i', $filename)
            || str_contains($path, '..') || str_contains($path, '\\')) {
            throw new RuntimeException('Unsafe retention file path.');
        }
        $valid = match ($disk) {
            'legacy' => $path === $filename,
            'public' => $path === 'profile_photos/'.$filename,
            (string) config('upload_security.disk', 'secure_uploads') => in_array($path, ['requirements/'.$filename, 'moa/'.$filename, 'completion-certificates/'.$filename], true),
            default => false,
        };
        if (! $valid) {
            throw new RuntimeException('Unsupported retention destination.');
        }
    }

    public function process(int $disposal): void
    {
        if (! config('retention.disposal_enabled')) {
            return;
        }
        $ids = DB::table('retention_file_deletions')->where('disposal_id', $disposal)->whereIn('status', ['pending', 'failed'])->pluck('id');
        foreach ($ids as $id) {
            DB::transaction(function () use ($id) {
                $job = DB::table('retention_file_deletions')->where('id', $id)->lockForUpdate()->first();
                if (! $job || ! in_array($job->status, ['pending', 'failed'], true)) {
                    return;
                }
                try {
                    $this->validatePath($job->disk, $job->path);
                    if ($this->referenced($job->path)) {
                        $status = 'retained_shared';
                    } else {
                        $this->deletePhysicalFile($job->disk, $job->path);
                        $status = 'deleted';
                        if ($job->disk !== 'legacy' && Schema::hasTable('upload_security_records')) {
                            DB::table('upload_security_records')->where('storage_disk', $job->disk)->where('storage_path', $job->path)->delete();
                        }
                    }
                    DB::table('retention_file_deletions')->where('id', $id)->update([
                        'status' => $status, 'path' => null, 'last_error' => null,
                        'attempts' => $job->attempts + 1, 'updated_at' => now(),
                    ]);
                } catch (Throwable $exception) {
                    DB::table('retention_file_deletions')->where('id', $id)->update([
                        'status' => 'failed', 'last_error' => 'File deletion failed; retry required.',
                        'attempts' => $job->attempts + 1, 'updated_at' => now(),
                    ]);
                }
            });
        }
        $pending = DB::table('retention_file_deletions')->where('disposal_id', $disposal)->whereIn('status', ['pending', 'failed'])->exists();
        DB::table('retention_disposals')->where('id', $disposal)->update(['status' => $pending ? 'file_cleanup_pending' : 'completed', 'updated_at' => now()]);
    }

    private function referenced(string $path): bool
    {
        $filename = basename($path);
        foreach (['file_requirements' => 'file', 'companies' => 'file', 'completion_certificates' => 'file', 'uploaded_files' => 'file', 'vouchers' => 'filename'] as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column) && DB::table($table)->whereRaw('LOWER('.$column.') = ?', [mb_strtolower($filename)])->exists()) {
                return true;
            }
        }
        if (Schema::hasTable('moa_renewals') && DB::table('moa_renewals')->where(function ($query) use ($filename) {
            $query->whereRaw('LOWER(previous_file) = ?', [mb_strtolower($filename)])->orWhereRaw('LOWER(new_file) = ?', [mb_strtolower($filename)]);
        })->exists()) {
            return true;
        }
        foreach (['users', 'students'] as $table) {
            if (Schema::hasColumn($table, 'profile_photo') && DB::table($table)->where('profile_photo', $path)->exists()) {
                return true;
            }
        }

        return false;
    }

    private function deletePhysicalFile(string $disk, string $path): void
    {
        $root = $disk === 'legacy' ? config('upload_security.legacy_directory') : Storage::disk($disk)->path('');
        if (! is_string($root) || trim($root) === '') {
            throw new RuntimeException('Storage root is not configured.');
        }
        $rootPath = realpath((string) $root);
        if ($rootPath === false) {
            if (file_exists((string) $root)) {
                throw new RuntimeException('Unresolved storage root.');
            }
            return;
        }
        $absolute = $rootPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
        $parent = $rootPath;
        foreach (explode('/', $path) as $part) {
            $parent .= DIRECTORY_SEPARATOR.$part;
            if (is_link($parent)) {
                throw new RuntimeException('Symlink disposal is not allowed.');
            }
        }
        if (! file_exists($absolute)) {
            return;
        }
        $resolved = realpath($absolute);
        if ($resolved === false || ! str_starts_with($resolved, $rootPath.DIRECTORY_SEPARATOR) || ! is_file($resolved)) {
            throw new RuntimeException('File is outside its storage root.');
        }
        if ($disk === 'legacy') {
            if (! unlink($absolute)) {
                throw new RuntimeException('Legacy deletion failed.');
            }
        } elseif (! Storage::disk($disk)->delete($path)) {
            throw new RuntimeException('Storage deletion failed.');
        }
        clearstatcache(true, $absolute);
        if (file_exists($absolute)) {
            throw new RuntimeException('File still exists after deletion.');
        }
    }
}
