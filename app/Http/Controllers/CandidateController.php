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
    $candidates = Candidate::with('photo')->get(); 
    return response($candidates);
}

    /**
     * @param StoreCandidateRequest $request
     * @return JsonResponse
     */
    public function store(StoreCandidateRequest $request): JsonResponse
    {
        unset($request->id);
    
        $candidate = Candidate::create($request->all());
    
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $candidate->photo()->create(['path' => $path]);
        }
    
        return response()->json(['message' => 'Candidato creado exitosamente']);
    }
    

    /**
     * @param UpdateVotingOptionRequest $request
     * @param Candidate $candidate
     * @return JsonResponse
     */
    public function update(UpdateCandidateRequest $request, Candidate $candidate)
    {
        // Actualizar datos básicos
        $candidate->update($request->except('photo'));
    
        // Verificar si hay una nueva imagen
        if ($request->hasFile('photo')) {
            // Si ya tenía una imagen, eliminarla del disco y de la base de datos
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo->path);
                $candidate->photo->delete();
            }
    
            // Guardar nueva foto
            $file = $request->file('photo');
            $path = $file->store('photos', 'public');
    
            $candidate->photo()->create([
                'path' => $path
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
        // Eliminar archivo físico y relación
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo->path); // Elimina imagen del disco
            $candidate->photo->delete(); // Elimina registro en tabla photos
        }
    
        $candidate->delete(); // Elimina el candidato
    
        return response()->json(['message' => 'Candidato eliminado exitosamente']);
    }

}
