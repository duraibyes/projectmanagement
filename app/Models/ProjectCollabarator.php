<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCollabarator extends Model
{
    use HasFactory;
    protected $fillable = ['project_id', 'user_id', 'is_owner', 'status'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
