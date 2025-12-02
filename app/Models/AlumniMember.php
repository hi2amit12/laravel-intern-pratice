<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'graduation_year',
        'job_title',
        'photo_path'
    ];

    public function committee()
    {
        return $this->hasOne(AlumniCommittee::class);
    }

    public function topAlumni()
    {
        return $this->hasOne(TopAlumni::class);
    }

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return asset('assets/default-avatar.png');
        }
        return asset('storage/' . $this->photo_path);
    }

    protected $appends = ['photo_url'];
}
