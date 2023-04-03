<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    public function collabarators()
    {
        return $this->hasMany(ProjectCollabarator::class, 'project_id', 'id');
    }
    
}
