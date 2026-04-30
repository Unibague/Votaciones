<?php

namespace App\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Table
 *
 * @property int $id
 * @property string $name
 * @property int|null $faculty_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote[] $votes
 * @property-read int|null $votes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Table newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Table newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Table query()
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereFacultyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Table extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getVotingReport(): \Illuminate\Support\Collection
    {
        $facultyId = null;
        $programIds = [];

        if ($this->faculty_code) {
            $facultyId = DB::table('faculties')
                ->where('code', $this->faculty_code)
                ->value('id');

            $programIds = DB::table('programs')
                ->whereIn('code', function ($q) {
                    $q->select('program_code')
                        ->from('voters')
                        ->where('faculty_code', $this->faculty_code)
                        ->whereNotNull('program_code')
                        ->distinct();
                })
                ->pluck('id')
                ->toArray();
        }

        return DB::table('votes')
            ->select([
                'candidates.principal_name', 'candidates.substitute_name', 'voting_options.name AS voting_option', DB::raw('COUNT(*) AS total_votes')
            ])
            ->where('votes.table_id', '=', $this->id)
            ->leftJoin('voting_options', 'voting_options.id', '=', 'votes.voting_option_id')
            ->leftJoin('candidates', 'candidates.id', '=', 'votes.candidate_id')
            ->where(function ($query) use ($facultyId, $programIds) {
                $query->where('voting_options.key', 'all');

                if ($facultyId !== null) {
                    $query->orWhere(function ($q) use ($facultyId) {
                        $q->where('voting_options.key', 'faculty')
                          ->where('voting_options.value', $facultyId);
                    });
                }

                if (!empty($programIds)) {
                    $query->orWhere(function ($q) use ($programIds) {
                        $q->where('voting_options.key', 'program')
                          ->whereIn('voting_options.value', $programIds);
                    });
                }
            })
            ->groupBy('candidates.principal_name', 'voting_options.name', 'candidates.substitute_name')
            ->orderByRaw('voting_options.name , "numero votos"')
            ->get();
    }
}
