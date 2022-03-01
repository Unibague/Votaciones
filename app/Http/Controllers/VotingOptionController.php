<?php

namespace App\Http\Controllers;

use App\Models\VotingOption;
use App\Http\Requests\StoreVotingOptionRequest;
use App\Http\Requests\UpdateVotingOptionRequest;
use Inertia\Inertia;

class VotingOptionController extends Controller
{
    /**
     * Show index vue view
     *
     */
    public function indexView(): \Inertia\Response
    {
        return Inertia::render('VotingOptions/Index');
    }

    //From this point, all the routes are used as API

    /**
     * Get all faculties for API
     */
    public function index()
    {
        $faculties = Program::all(['id', 'name', 'code']);
        return response($faculties);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProgramRequest $request
     * @return JsonResponse
     */
    public function store(StoreProgramRequest $request)
    {
        Program::create($request->all());
        return response()->json(['message' => 'Facultad creada exitosamente']);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProgramRequest $request
     * @param Program $program
     * @return JsonResponse
     */
    public function update(UpdateProgramRequest $request, Program $program): JsonResponse
    {
        $program->name = $request->input('name');
        $program->save();
        return response()->json(['message' => 'Facultad actualizada exitosamente', 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteProgramRequest $request
     * @param Program $program
     * @return JsonResponse
     */
    public function destroy(DeleteProgramRequest $request, Program $program)
    {
        $program->delete();
        return response()->json(['message' => 'Facultad eliminada exitosamente']);
    }
}
