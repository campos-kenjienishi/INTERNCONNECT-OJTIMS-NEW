<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('file_requirements')) {
            Schema::table('file_requirements', function (Blueprint $table) {
                if (!Schema::hasColumn('file_requirements', 'uploader_user_id')) {
                    $table->unsignedBigInteger('uploader_user_id')->nullable()->after('uploadedBy')->index();
                }
            });

            $requirements = DB::table('file_requirements')
                ->whereNull('uploader_user_id')
                ->whereNotNull('uploadedBy')
                ->select('id', 'uploadedBy', 'adviser')
                ->get();

            foreach ($requirements as $req) {
                $ownerId = $this->resolveRequirementOwnerId($req->uploadedBy, $req->adviser);
                if ($ownerId) {
                    DB::table('file_requirements')
                        ->where('id', $req->id)
                        ->update(['uploader_user_id' => $ownerId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('file_requirements')) {
            Schema::table('file_requirements', function (Blueprint $table) {
                if (Schema::hasColumn('file_requirements', 'uploader_user_id')) {
                    $table->dropColumn('uploader_user_id');
                }
            });
        }
    }

    private function resolveRequirementOwnerId(?string $uploadedBy, ?string $adviser): ?int
    {
        if (empty($uploadedBy)) {
            return null;
        }

        $query = DB::table('users')
            ->join('students', 'students.user_id', '=', 'users.id')
            ->where('users.full_name', $uploadedBy);

        if (!empty($adviser)) {
            $query->where('students.adviser_name', $adviser);
        }

        $studentUserId = $query->value('users.id');
        if ($studentUserId) {
            return (int) $studentUserId;
        }

        $user = DB::table('users')
            ->where('full_name', $uploadedBy)
            ->where('role', 1)
            ->first();

        if ($user) {
            return (int) $user->id;
        }

        $fallbackId = DB::table('users')->where('full_name', $uploadedBy)->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }
};
