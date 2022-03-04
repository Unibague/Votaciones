<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizeVoteRequest;
use App\Models\Vote;
use App\Http\Requests\StoreVoteRequest;
use App\Http\Requests\UpdateVoteRequest;
use App\Models\Voter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getVoterVotingOptions(Voter $voter)
    {
        $votingOptions = $voter->getVotingOptions();
        return $votingOptions;
    }

    public function authorizeVote(AuthorizeVoteRequest $request)
    {
        return Inertia::render('Votes/Authorize.vue');
    }

    public function vote(Request $request)
    {
        $voter = Voter::findOrFail($request->input('voterId'));
        return Inertia::render('Votes/Cast.vue', [
            'voter' => $voter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(StoreVoteRequest $request)
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
                'candidate_id' => $userVote['candidate_id'],
                'user_id' => auth()->user()->id,
                'table_id' => auth()->user()->table->id,
            ]);
        }
        return response()->json(['message' => 'Voto registrado exitosamente']);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Vote $vote
     * @return \Illuminate\Http\Response
     */
    public function show(Vote $vote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Vote $vote
     * @return \Illuminate\Http\Response
     */
    public function edit(Vote $vote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateVoteRequest $request
     * @param \App\Models\Vote $vote
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVoteRequest $request, Vote $vote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Vote $vote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vote $vote)
    {
        //
    }
}
