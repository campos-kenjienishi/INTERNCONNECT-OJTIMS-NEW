<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('uploaded_files')) {
            Schema::table('uploaded_files', function (Blueprint $table) {
                if (!Schema::hasColumn('uploaded_files', 'uploader_user_id')) {
                    $table->unsignedBigInteger('uploader_user_id')->nullable()->after('uploader_name')->index();
                }
            });

            $uploadedFiles = DB::table('uploaded_files')->select('id', 'uploader_name')->get();
            foreach ($uploadedFiles as $file) {
                $ownerId = $this->resolveUploadedFileOwnerId((int) $file->id, $file->uploader_name);
                if ($ownerId) {
                    DB::table('uploaded_files')
                        ->where('id', $file->id)
                        ->update(['uploader_user_id' => $ownerId]);
                }
            }
        }

        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (!Schema::hasColumn('announcements', 'announcer_user_id')) {
                    $table->unsignedBigInteger('announcer_user_id')->nullable()->after('announcer')->index();
                }
            });

            $announcements = DB::table('announcements')->select('id', 'announcer')->get();
            foreach ($announcements as $announcement) {
                $ownerId = $this->resolveAnnouncementOwnerId((int) $announcement->id, $announcement->announcer);
                if ($ownerId) {
                    DB::table('announcements')
                        ->where('id', $announcement->id)
                        ->update(['announcer_user_id' => $ownerId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (Schema::hasColumn('announcements', 'announcer_user_id')) {
                    $table->dropColumn('announcer_user_id');
                }
            });
        }

        if (Schema::hasTable('uploaded_files')) {
            Schema::table('uploaded_files', function (Blueprint $table) {
                if (Schema::hasColumn('uploaded_files', 'uploader_user_id')) {
                    $table->dropColumn('uploader_user_id');
                }
            });
        }
    }

    private function resolveUploadedFileOwnerId(int $fileId, ?string $uploaderName): ?int
    {
        if (empty($uploaderName)) {
            return null;
        }

        $query = DB::table('users')->where('full_name', $uploaderName);

        if (Schema::hasColumn('uploaded_files', 'class_id')) {
            $file = DB::table('uploaded_files')->select('class_id')->where('id', $fileId)->first();
            if ($file && !empty($file->class_id)) {
                $query->where('role', 2);
            } else {
                $query->where('role', 1);
            }
        }

        $ownerId = $query->value('id');

        if ($ownerId) {
            return (int) $ownerId;
        }

        $fallbackId = DB::table('users')->where('full_name', $uploaderName)->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }

    private function resolveAnnouncementOwnerId(int $announcementId, ?string $announcerName): ?int
    {
        if (empty($announcerName)) {
            return null;
        }

        $query = DB::table('users')->where('full_name', $announcerName);

        if (Schema::hasColumn('announcements', 'audience')) {
            $announcement = DB::table('announcements')
                ->select('audience', 'target_course', 'target_room')
                ->where('id', $announcementId)
                ->first();

            $isProfessorAnnouncement = $announcement
                && (
                    $announcement->audience === 'class'
                    || !empty($announcement->target_course)
                    || !empty($announcement->target_room)
                );

            $query->where('role', $isProfessorAnnouncement ? 2 : 1);
        }

        $ownerId = $query->value('id');

        if ($ownerId) {
            return (int) $ownerId;
        }

        $fallbackId = DB::table('users')->where('full_name', $announcerName)->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }
};
