<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function getRoleNumber(string $roleName): int
    {
        $selectedRole = self::where('name', '=', $roleName)->first();
        if ($selectedRole === null) {
            throw new \RuntimeException('El rol buscado no existe');
        }
        return $selectedRole->customId;
    }



    public function users () {
        return $this->hasMany(User::class,'role_id','id');
    }
}
