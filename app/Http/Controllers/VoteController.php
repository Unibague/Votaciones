<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizeVoteRequest;
use App\Models\Vote;
use App\Http\Requests\StoreVoteRequest;
use App\Models\Voter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 *
 */
class VoteController extends Controller
{

    /**
     * @param Voter $voter
     * @return Builder[]|Collection
     */
    public function getVoterVotingOptions(Voter $voter)
    {
        return $voter->getVotingOptions();
    }

    /**
     * @param AuthorizeVoteRequest $request
     * @return Response
     */
    public function authorizeVote(AuthorizeVoteRequest $request): Response
    {
        return Inertia::render('Votes/Authorize.vue');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function vote(Request $request): Response
    {
        $voter = Voter::findOrFail($request->input('voterId'));
        return Inertia::render('Votes/Cast.vue', [
            'voter' => $voter,
        ]);
    }

    /**
     * @param StoreVoteRequest $request
     * @return JsonResponse
     */
    public function store(StoreVoteRequest $request): JsonResponse
    {
        //verify if the user already voted
        $voter = Voter::findOrFail($request->input('userVotes')[0]['voter_id']);
        if ($voter->hasVoted()) {
            return response()->json(['message' => 'Ha ocurrido un problema: Ya existen votos registrados a tu nombre'], 403);
        }

        $userVotes = $request->input('userVotes');
        foreach ($userVotes as $userVote) {
            Vote::create([
                'voter_id' => $userVote['voter_id'],
                'voting_option_id' => $userVote['voting_option_id'],
                'candidate_id' => $userVote['candidate_id'] === 0 ? null : $userVote['candidate_id'],
                'user_id' => auth()->user()->id,
                'table_id' => auth()->user()->table->id,
            ]);
        }
        return response()->json(['message' => 'Voto registrado exitosamente']);
    }

}
