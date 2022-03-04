<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchVoterByIdentificationNumberRequest;
use App\Models\Voter;

class VoterController extends Controller
{

    public function searchByIdentificationNumber(SearchVoterByIdentificationNumberRequest $request)
    {
        $voter = Voter::with(['faculty', 'program'])->where('identification_number', $request->input('identification_number'))
            ->firstOrFail();

        if ($voter->faculty_code !== auth()->user()->table->faculty_code) {
            return response()->json(['message' => 'El usuario no está autorizado para votar en esta mesa'], 403);
        }
        //Check if already voted
        if ($voter->hasVoted()) {
            return response()->json(['message' => 'El usuario ya ha votado'], 403);
        }

        //Return the voter faculty and program
        return $voter;
    }


}
