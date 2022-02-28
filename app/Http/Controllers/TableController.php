<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteTableRequest;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    /**
     * Show index vue view
     *
     */
    public function indexView(): \Inertia\Response
    {
        return Inertia::render('Tables/Index');
    }

    //From this point, all the routes are used as API

    /**
     * Get all faculties for API
     */
    public function index()
    {
        $tables = Table::all(['id', 'name', 'faculty_code']);
        return response($tables);
    }

    /**
     * @param StoreTableRequest $request
     * @return JsonResponse
     */
    public function store(StoreTableRequest $request): JsonResponse
    {
        Table::create($request->all());
        return response()->json(['message' => 'Mesa creada exitosamente']);

    }


    public function update(UpdateTableRequest $request, Table $table): JsonResponse
    {
        $table->name = $request->input('name');
        $table->faculty_code = $request->input('faculty_code');
        $table->save();
        return response()->json(['message' => 'Mesa actualizada exitosamente', 200]);
    }


    public function destroy(DeleteTableRequest $request, Table $table)
    {
        $table->delete();
        return response()->json(['message' => 'Mesa eliminada exitosamente']);
    }
}
