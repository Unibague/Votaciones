<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    use HasFactory;

    public function votes(){
        return $this->hasMany(Vote::class);
    }
    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }
    public function program(){
        return $this->belongsTo(Program::class);
    }
}
