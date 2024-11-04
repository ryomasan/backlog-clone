<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function blog()
    {
        return $this->belongsToMany(Tag::class,'task_tag');
    }
    protected $fillable = ['name'];
}
