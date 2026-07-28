<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OJTInformation extends Model
{
    use HasFactory;

    protected $table = 'o_j_t_information';

    protected $fillable = [
        'studentNum',
        'company_name',
        'company_address',
        'nature_of_bus',
        'nature_of_link',
        'level',
        'assigned_department',
        'student_role',
        'start_date',
        'finish_date',
        'report_time',
        'contact_name',
        'contact_position',
        'contact_number',
        'status',
    ];
}
