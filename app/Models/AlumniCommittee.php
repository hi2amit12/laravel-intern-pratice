<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniCommittee extends Model
{
    use HasFactory;

    protected $table = 'alumni_committee';

    protected $fillable = [
        'alumni_member_id',
        'position',
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
