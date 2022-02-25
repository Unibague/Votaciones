<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteFacultyRequest;
use App\Models\Faculty;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;

class FacultyController extends Controller
{
    /**
     * Show index vue view
     *
     */
    public function indexView(): \Inertia\Response
    {
        return Inertia::render('Faculties/Index');
    }

    //From this point, all the routes are used as API

    /**
     * Get all faculties for API
     */
    public function index()
    {
        $faculties = Faculty::all(['id', 'name', 'code']);
        return response($faculties);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreFacultyRequest $request
     * @return Response
     */
    public function store(StoreFacultyRequest $request)
    {
        Faculty::create($request->all());
        return response()->json(['message' => 'Facultad creada exitosamente']);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFacultyRequest $request
     * @param Faculty $faculty
     * @return JsonResponse
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        $faculty->name = $request->input('name');
        $faculty->save();
        return response()->json(['message' => 'Facultad actualizada exitosamente', 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Faculty $faculty
     * @return JsonResponse
     */
    public function destroy(DeleteFacultyRequest $request, Faculty $faculty)
    {
        $faculty->delete();
        return response()->json(['message' => 'Facultad eliminada exitosamente']);
    }
}
