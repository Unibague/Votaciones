<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    public function voters()
    {
        return $this->hasMany(Voter::class,'faculty_code','code');
    }
}
