<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchVoterByIdentificationNumberRequest;
use App\Models\Vote;
use App\Models\Voter;
use App\Http\Requests\StoreVoterRequest;
use App\Http\Requests\UpdateVoterRequest;

class VoterController extends Controller
{

    public function searchByIdentificationNumber(SearchVoterByIdentificationNumberRequest $request)
    {
        $voter = Voter::with(['faculty', 'program'])->where('identification_number', $request->input('identification_number'))->firstOrFail();

        //Check if already voted
        $votes = Vote::where('voter_id', '=', $voter->id)->first();
        if ($votes !== null) {
            return response()->json(['message' => 'El usuario ya ha votado'], 403);
        }
        return $voter;
    }


}
