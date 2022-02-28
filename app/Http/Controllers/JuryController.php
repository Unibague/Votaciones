<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateJuryRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 *
 */
class JuryController extends Controller
{

    /**
     * Show index vue view
     *
     */
    public function indexView(): \Inertia\Response
    {
        return Inertia::render('Jurors/Index');
    }

    /**
     * Get all faculties for API
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $jurorRole = Role::where('name', '=', 'jurado')->firstOrFail();
        $users = $jurorRole->users()->with('table')->get();
        return response()->json($users);
    }


    /**
     * @param UpdateJuryRequest $request
     * @param User $juror
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateJuryRequest $request, User $juror): \Illuminate\Http\JsonResponse
    {
        $juror->table_id = $request->input('tableId');
        $juror->save();
        return response()->json(['message' => 'Mesa actualizada exitosamente', 200]);

    }

}
