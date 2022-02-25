<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    public function voters(){
        return $this->hasMany(Voter::class,'program_code','code');
    }
}
