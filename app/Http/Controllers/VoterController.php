<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchVoterByIdentificationNumberRequest;
use App\Imports\VotersImport;
use App\Models\Voter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class VoterController extends Controller
{
    public function indexView(): \Inertia\Response
    {
        return Inertia::render('Voters/Index');
    }

    public function indexShow(): \Inertia\Response
    {
        $voters = Voter::with(['program', 'faculty'])
            ->get()
            ->map(function ($voter) {
                return [
                    'identification_number' => $voter->identification_number,
                    'name' => $voter->name,
                    'email' => $voter->email,
                    'program_name' => $voter->program->name ?? 'N/A',
                    'faculty_name' => $voter->faculty->name ?? 'N/A',
                ];
            });

        return Inertia::render('Voters/Show', [
            'voters' => $voters
        ]);
    }

    public function searchByIdentificationNumber(SearchVoterByIdentificationNumberRequest $request)
    {
        $voters = Voter::with(['faculty', 'program'])
            ->where('identification_number', $request->input('identification_number'))
            ->get();

        if ($voters->isEmpty()) {
            return response()->json(['message' => 'Votante no encontrado'], 404);
        }

        $authenticatedUser = auth()->user();

        if ($authenticatedUser->hasRole('admin')) {
            $voter = $voters->first();

            if ($voter->hasVoted()) {
                return response()->json(['message' => 'El usuario ya ha votado'], 403);
            }

            return $voter;
        }

        $table = $authenticatedUser->table;

        if (!$table) {
            return response()->json(['message' => 'El jurado no tiene una mesa asignada.'], 403);
        }

        if (!$table->faculty_code) {
            return response()->json(['message' => 'La mesa actual no tiene una facultad asignada. No se puede autorizar el voto hasta corregir la mesa.'], 403);
        }

        if ($voters->contains(function ($voter) {
            return empty($voter->faculty_code);
        })) {
            return response()->json(['message' => 'El votante no tiene una facultad asignada. No se puede autorizar el voto hasta corregir sus datos.'], 403);
        }

        $voter = $voters->firstWhere('faculty_code', $table->faculty_code);

        if (!$voter) {
            $firstVoter = $voters->first();
            $voterFacultyCode = $firstVoter ? ($firstVoter->faculty_code ?? 'sin facultad') : 'desconocida';
            $tableFacultyCode = $table->faculty_code ?? 'sin facultad';

            return response()->json([
                'message' => "El usuario no está autorizado para votar en esta mesa. Facultad del votante: {$voterFacultyCode}. Facultad de la mesa: {$tableFacultyCode}."
            ], 403);
        }

        if ($voter->hasVoted()) {
            return response()->json(['message' => 'El usuario ya ha votado'], 403);
        }

        return $voter;
    }

    public function store(Request $request)
    {
        $request->validate([
            'voters' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new VotersImport();
            Excel::import($import, $request->file('voters'));

            foreach ($import->voters as $voter) {
                \DB::table('voters')->updateOrInsert(
                    [
                        'identification_number' => $voter['identification_number'],
                        'program_code' => $voter['program_code'],
                    ],
                    [
                        'name' => $voter['name'],
                        'email' => $voter['email'],
                        'faculty_code' => $voter['faculty_code'],
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json([
                'message' => 'Votantes procesados correctamente',
                'imported_count' => $import->voters->count(),
                'skipped' => $import->errors
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al procesar el archivo: ' . $e->getMessage()], 500);
        }
    }
}
