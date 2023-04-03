<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 
        'name',
        'description',
        'status' // 'pending', 'completed', 'incompleted'
    ];

    public function collabarators()
    {
        return $this->hasMany(TaskCollabarator::class, 'task_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
