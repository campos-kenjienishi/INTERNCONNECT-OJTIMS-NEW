<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileCategory extends Model
{
    use HasFactory;

    protected $fillable = ['fileName', 'uploadedBy', 'professor_id', 'phase'];

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }
}
