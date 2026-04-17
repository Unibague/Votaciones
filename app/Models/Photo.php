<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Photo extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['url'];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function getUrlAttribute()
    {
        if (!$this->path || !Storage::disk('public')->exists($this->path)) {
            return null;
        }

        return Storage::disk('public')->url($this->path);
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
