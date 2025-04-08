<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Photo extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    protected static function booted()
{
    static::deleting(function ($photo) {
        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }
    });
}
}
