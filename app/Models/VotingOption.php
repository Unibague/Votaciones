<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VotingOption
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Candidate[] $candidates
 * @property-read int|null $candidates_count
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VotingOption whereValue($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote[] $votes
 * @property-read int|null $votes_count
 */
class VotingOption extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
