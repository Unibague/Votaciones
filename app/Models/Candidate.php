<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    public function votingOption()
    {
        return $this->belongsTo(VotingOption::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}

