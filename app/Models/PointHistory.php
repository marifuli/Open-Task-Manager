<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_point_id',
        'task_id',
        'points',
        'reason',
    ];


    public function userPoint()
    {
        return $this->belongsTo(UserPoint::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
