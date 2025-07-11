<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    use HasFactory;

    protected $fillable = [

        'task_id',
        'action',
        'changed_field',
    ];

    protected $casts = [
        'changed_field' => 'array',
    ];
}
