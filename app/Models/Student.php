<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

     protected $fillable = ['name', 'age', 'class_id'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
