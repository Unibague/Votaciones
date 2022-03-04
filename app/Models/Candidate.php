<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Candidate
 *
 * @property int $id
 * @property string $principal_name
 * @property string $principal_faculty
 * @property string $principal_program
 * @property string $substitute_name
 * @property string $substitute_faculty
 * @property string $substitute_program
 * @property int $voting_option_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote[] $votes
 * @property-read int|null $votes_count
 * @property-read \App\Models\VotingOption $votingOption
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate wherePrincipalFaculty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate wherePrincipalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate wherePrincipalProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate whereSubstituteFaculty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate whereSubstituteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate whereSubstituteProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Candidate whereVotingOptionId($value)
 * @mixin \Eloquent
 */
class Candidate extends Model
{
    protected $guarded = ['id'];

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

