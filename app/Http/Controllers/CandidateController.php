<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteCandidateRequest;
use App\Http\Requests\UpdateVotingOptionRequest;
use App\Models\Candidate;
use App\Http\Requests\StoreCandidateRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

/**
 *
 */
class CandidateController extends Controller
{
    /**
     * Show index vue view
     *
     */
    public function indexView(): \Inertia\Response
    {
        return Inertia::render('Candidates/Index');
    }

    //From this point, all the routes are used as API

    /**
     * Get all candidates for API
     */
    public function index()
    {
        $candidates = Candidate::all();
        return response($candidates);
    }


    /**
     * @param StoreCandidateRequest $request
     * @return JsonResponse
     */
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        unset($request->id);
        Candidate::create($request->all());
        return response()->json(['message' => 'Candidato creado exitosamente']);

    }


    /**
     * @param UpdateVotingOptionRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function update(UpdateVotingOptionRequest $request, Candidate $candidate): JsonResponse
    {
        $candidate->update($request->all());
        return response()->json(['message' => 'Candidato actualizado exitosamente', 200]);
    }


    /**
     * @param DeleteCandidateRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function destroy(DeleteCandidateRequest $request, Candidate $candidate): JsonResponse
    {
        $candidate->delete();
        return response()->json(['message' => 'Candidato eliminado exitosamente']);
    }
}
