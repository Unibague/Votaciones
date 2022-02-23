<?php

namespace App\Http\Controllers;

use App\Models\VotingOption;
use App\Http\Requests\StoreVotingOptionRequest;
use App\Http\Requests\UpdateVotingOptionRequest;

class VotingOptionController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVotingOptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVotingOptionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VotingOption  $votingOption
     * @return \Illuminate\Http\Response
     */
    public function show(VotingOption $votingOption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VotingOption  $votingOption
     * @return \Illuminate\Http\Response
     */
    public function edit(VotingOption $votingOption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVotingOptionRequest  $request
     * @param  \App\Models\VotingOption  $votingOption
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVotingOptionRequest $request, VotingOption $votingOption)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VotingOption  $votingOption
     * @return \Illuminate\Http\Response
     */
    public function destroy(VotingOption $votingOption)
    {
        //
    }
}
