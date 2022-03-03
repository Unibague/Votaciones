<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteVotingOptionRequest;
use App\Models\VotingOption;
use App\Http\Requests\StoreVotingOptionRequest;
use App\Http\Requests\UpdateVotingOptionRequest;
use Illuminate\Support\Facades\DB;
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
     * Get all votingOptions for API
     */
    public function index()
    {
        $votingOptions = DB::table('voting_options')->select(['id', 'name', 'key', 'value'])->get();
        foreach ($votingOptions as $votingOption) {
            if ($votingOption->key === 'faculty') {
                $faculty = DB::table('faculties')->where('id', '=', $votingOption->value)->first();
                $votingOption->entityName = $faculty->name ?? 'No figura';
            }
            if ($votingOption->key === 'program') {
                $program = DB::table('programs')->where('id', '=', $votingOption->value)->first();
                $votingOption->entityName = $program->name ?? 'No figura';
            }
            if ($votingOption->key === 'all') {
                $votingOption->entityName = 'Todos';
            }
        }
        return response($votingOptions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreVotingOptionRequest $request
     * @return JsonResponse
     */
    public function store(StoreVotingOptionRequest $request)
    {
        VotingOption::create($request->all());
        return response()->json(['message' => 'Opción de votación creada exitosamente']);

    }


    public function update(UpdateVotingOptionRequest $request, VotingOption $votingOption): \Illuminate\Http\JsonResponse
    {
        $votingOption->name = $request->input('name');
        $votingOption->key = $request->input('key');
        $votingOption->value = $request->input('value');
        $votingOption->save();
        return response()->json(['message' => 'Opción de votación actualizada exitosamente', 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteVotingOptionRequest $request
     * @param VotingOption $votingOption
     * @return JsonResponse
     */
    public function destroy(DeleteVotingOptionRequest $request, VotingOption $votingOption)
    {
        $votingOption->delete();
        return response()->json(['message' => 'Opción de votación eliminada exitosamente']);
    }
}
