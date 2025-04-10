<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteCandidateRequest;
use App\Models\Candidate;
use App\Http\Requests\StoreCandidateRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateCandidateRequest;



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
        $candidates = Candidate::with(['principalPhoto', 'substitutePhoto'])->get(); 
        return response($candidates);
    }


    /**
     * @param StoreCandidateRequest $request
     * @return JsonResponse
     */
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        unset($request->id);

        $candidate = Candidate::create($request->except(['photo', 'substitute_photo']));

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $candidate->principalPhoto()->create(['path' => $path, 'type' => 'principal']);
        }

        if ($request->hasFile('substitute_photo')) {
            $path = $request->file('substitute_photo')->store('photos', 'public');
            $candidate->substitutePhoto()->create(['path' => $path, 'type' => 'substitute']);
        }

        return response()->json(['message' => 'Candidato creado exitosamente']);
    }

    

    /**
     * @param UpdateVotingOptionRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function update(UpdateCandidateRequest $request, Candidate $candidate): JsonResponse
    {
        $candidate->update($request->except(['photo', 'substitute_photo']));

        // Foto del principal
        if ($request->hasFile('photo')) {
            if ($candidate->principalPhoto) {
                Storage::disk('public')->delete($candidate->principalPhoto->path);
                $candidate->principalPhoto->delete();
            }

            $path = $request->file('photo')->store('photos', 'public');
            $candidate->principalPhoto()->create([
                'path' => $path,
                'type' => 'principal',
            ]);
        }

        // Foto del suplente
        if ($request->hasFile('substitute_photo')) {
            if ($candidate->substitutePhoto) {
                Storage::disk('public')->delete($candidate->substitutePhoto->path);
                $candidate->substitutePhoto->delete();
            }

            $path = $request->file('substitute_photo')->store('photos', 'public');
            $candidate->substitutePhoto()->create([
                'path' => $path,
                'type' => 'substitute',
            ]);
        }

        return response()->json(['message' => 'Candidato actualizado exitosamente']);
    }



    /**
     * @param DeleteCandidateRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function destroy(DeleteCandidateRequest $request, Candidate $candidate): JsonResponse
    {
        // Foto del principal
        if ($candidate->principalPhoto) {
            Storage::disk('public')->delete($candidate->principalPhoto->path);
            $candidate->principalPhoto->delete();
        }

        // Foto del suplente
        if ($candidate->substitutePhoto) {
            Storage::disk('public')->delete($candidate->substitutePhoto->path);
            $candidate->substitutePhoto->delete();
        }

        $candidate->delete();

        return response()->json(['message' => 'Candidato eliminado exitosamente']);
    }
}
