<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
    protected $fillable = ['name', 'status', 'order'];
}
