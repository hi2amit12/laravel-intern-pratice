<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopAlumni extends Model
{
    use HasFactory;

    protected $table = 'top_alumni';

    protected $fillable = [
        'alumni_member_id',
        'achievement',
        'display_order'
    ];

    protected $casts = [
        'display_order' => 'integer'
    ];

    public function alumniMember()
    {
        return $this->belongsTo(AlumniMember::class);
    }
}
