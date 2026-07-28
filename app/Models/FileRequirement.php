<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'fileName',
        'file',
        'status',
        'uploadedBy',
        'adviser',
        'denial_reason',
        'uploader_user_id',
        'professor_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploader_user_id');
    }

    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query;
        }

        $userId = is_numeric($user) ? (int) $user : ($user->id ?? null);
        $userName = is_object($user) ? ($user->full_name ?? null) : null;

        return $query->where(function ($q) use ($userId, $userName) {
            if ($userId) {
                $q->where('uploader_user_id', $userId);
                if (!empty($userName)) {
                    $q->orWhere(function ($sub) use ($userName) {
                        $sub->whereNull('uploader_user_id')
                            ->where('uploadedBy', $userName);
                    });
                }
            } elseif (!empty($userName)) {
                $q->where('uploadedBy', $userName);
            }
        });
    }
}
