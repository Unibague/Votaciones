<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Voter
 *
 * @property int $id
 * @property string $name
 * @property string $identification_number
 * @property int|null $faculty_code
 * @property int|null $program_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Faculty|null $faculty
 * @property-read \App\Models\Program|null $program
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote[] $votes
 * @property-read int|null $votes_count
 * @method static \Database\Factories\VoterFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Voter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereFacultyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereProgramCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Voter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Voter extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function hasVoted(): bool
    {
        return count($this->votes) !== 0;
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_code', 'code');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_code', 'code');
    }

    public function getVotingOptions()
{
    // Guarda la consulta en la variable
    $VotingOptionsQuery = VotingOption::with(['candidates.principalPhoto', 'candidates.substitutePhoto'])
        ->where(function ($query) {
            $query->where('key', '=', 'faculty')
                  ->where('value', '=', optional($this->faculty)->id);
        })
        ->orWhere(function ($query) {
            $query->where('key', '=', 'program')
                  ->where('value', '=', optional($this->program)->id);
        })
        ->orWhere('key', 'all');

    // Si existe otro votante con el mismo número de identificación
    $secondUser = self::where('identification_number', $this->identification_number)
        ->where('id', '!=', $this->id)
        ->first();

    if ($secondUser !== null) {
        $VotingOptionsQuery->orWhere(function ($query) use ($secondUser) {
            $query->where('key', '=', 'program')
                  ->where('value', '=', optional($secondUser->program)->id);
        })->orWhere(function ($query) use ($secondUser) {
            $query->where('key', '=', 'faculty')
                  ->where('value', '=', optional($secondUser->faculty)->id);
        });
    }

    return $VotingOptionsQuery->get();
}
}