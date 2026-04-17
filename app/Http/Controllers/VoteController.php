<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizeVoteRequest;
use App\Http\Requests\StoreVoteRequest;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use App\Mail\VoteCertificateMail;
use Barryvdh\DomPDF\Facade\Pdf;

class VoteController extends Controller
{
    /**
     * Get the voting options for a given voter.
     */
    public function getVoterVotingOptions(Voter $voter)
    {
        return $voter->getVotingOptions();
    }

    /**
     * Render the vote authorization screen.
     */
    public function authorizeVote(AuthorizeVoteRequest $request): Response
    {
        return Inertia::render('Votes/Authorize.vue');
    }

    /**
     * Render the vote screen for a specific voter.
     */
    public function vote(Request $request): Response
    {
        $voter = Voter::findOrFail($request->input('voterId'));

        return Inertia::render('Votes/Cast.vue', [
            'voter' => $voter,
        ]);
    }

    /**
     * Store the vote.
     */
public function store(StoreVoteRequest $request): JsonResponse
{
    // Retrieve the voter and their existing votes
    $voter = Voter::with('votes')->findOrFail($request->input('userVotes')[0]['voter_id']);
    $tableId = optional(auth()->user()->table)->id;

    // Prevent double voting
    if ($voter->hasVoted()) {
        return response()->json(['message' => 'An error occurred: votes are already registered under your name'], 403);
    }

    // Generate a unique token for the voting certificate
    $token = \Str::random(32);

    // Save all the votes from the frontend
    foreach ($request->input('userVotes') as $userVote) {
        Vote::create([
            'voter_id' => $userVote['voter_id'],
            'voting_option_id' => $userVote['voting_option_id'],
            'candidate_id' => $userVote['candidate_id'] === 0 ? null : $userVote['candidate_id'],
            'user_id' => auth()->user()->id,
            'table_id' => $tableId,
            'certificate_token' => $token,
        ]);
    }

    $pdf = Pdf::loadView('certificate.certificate-image', [
        'voter' => $voter,
        'voted_at' => now()->format('d/m/Y H:i')
    ]);

    $binaryPdf = $pdf->output();

    $mailSent = false;
    $mailMessage = 'No hay correo registrado para este votante.';

    if ($voter->email) {
        try {
            Mail::to($voter->email)->send(
                (new VoteCertificateMail($voter->name))
                    ->attachData($binaryPdf, 'certificado.pdf')
            );
            $mailSent = true;
            $mailMessage = 'El comprobante fue enviado al correo registrado.';
        } catch (\Throwable $exception) {
            Log::error('Failed to send vote certificate email.', [
                'voter_id' => $voter->id,
                'email' => $voter->email,
                'message' => $exception->getMessage(),
            ]);
            $mailMessage = 'El voto fue registrado, pero no fue posible enviar el comprobante al correo.';
        }
    }

    return response()->json([
        'message' => 'Voto registrado exitosamente.',
        'mail_sent' => $mailSent,
        'mail_message' => $mailMessage,
    ]);
}
}
