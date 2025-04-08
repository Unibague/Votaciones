<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VoteCertificateController extends Controller
{
    /**
     * Display the vote certificate by token.
     *
     * @param  string  $token
     * @return \Inertia\Response|\Illuminate\Http\Response
     */
    public function show($token)
{
    $vote = Vote::with('voter')->where('certificate_token', $token)->firstOrFail();

    return Inertia::render('Votes/CertificateView', [
        'voter' => $vote->voter,
        'voted_at' => $vote->created_at->format('Y-m-d H:i:s'),
    ]);
}

}
