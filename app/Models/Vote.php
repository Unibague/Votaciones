<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vote
 *
 * @property int $id
 * @property int $voter_id
 * @property int $candidate_id
 * @property int $voting_option_id
 * @property int|null $user_id
 * @property int|null $table_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Candidate $candidate
 * @property-read \App\Models\Table|null $table
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Voter $voter
 * @property-read \App\Models\VotingOption $votingOption
 * @method static \Illuminate\Database\Eloquent\Builder|Vote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereCandidateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereVoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vote whereVotingOptionId($value)
 * @mixin \Eloquent
 */
class Vote extends Model
{
    use HasFactory;

    // Explicitly list the fields you allow to be mass assigned
    protected $fillable = [
        'voter_id',
        'candidate_id',
        'voting_option_id',
        'user_id',
        'table_id',
        'certificate_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function votingOption()
    {
        return $this->belongsTo(VotingOption::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
}
