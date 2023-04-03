<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCollabarator extends Model
{
    use HasFactory;

    protected $table = 'task_collabarators';
    protected $fillable = [
        'task_id',
        'user_id',
        'is_owner',
        'status'     //'active', 'inactive'
    ];
}
