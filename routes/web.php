<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Bienvenido');
})->name('default');

Route::get('/redirect', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('programs.index');
    }

})->middleware('auth')->name('handleRoleRedirect');

/*
|--------------------------------------------------------------------------
| Faculties
|--------------------------------------------------------------------------
*/

Route::apiResource('api/faculties', \App\Http\Controllers\FacultyController::class, [
    'as' => 'api'
])->middleware('auth');

Route::get('/faculties', [\App\Http\Controllers\FacultyController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('faculties.index');

/* >>>>>Faculties routes <<<<<< */

/*
|--------------------------------------------------------------------------
| Programs
|--------------------------------------------------------------------------
*/

Route::apiResource('api/programs', \App\Http\Controllers\ProgramController::class, [
    'as' => 'api'
])->middleware('auth');

Route::get('/programs', [\App\Http\Controllers\ProgramController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('programs.index');

/*
|--------------------------------------------------------------------------
| Voting options
|--------------------------------------------------------------------------
*/
Route::apiResource('api/votingOptions', \App\Http\Controllers\VotingOptionController::class, [
    'as' => 'api'
])->middleware('auth');

Route::get('/votingOptions', [\App\Http\Controllers\VotingOptionController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('votingOptions.index');


/*
|--------------------------------------------------------------------------
| Candidates
|--------------------------------------------------------------------------
*/
Route::apiResource('api/candidates', \App\Http\Controllers\CandidateController::class, [
    'as' => 'api'
])->middleware('auth');

Route::get('/candidates', [\App\Http\Controllers\CandidateController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('candidates.index');


/*
|--------------------------------------------------------------------------
| Tables
|--------------------------------------------------------------------------
*/

Route::apiResource('api/tables', \App\Http\Controllers\TableController::class, [
    'as' => 'api'
])->middleware('auth');

//Get all programs (view)
Route::get('/tables', [\App\Http\Controllers\TableController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('tables.index');

/*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
*/
Route::get('/roles', [\App\Http\Controllers\Roles\RoleController::class, 'index'])->middleware(['auth', 'isAdmin'])->name('roles.index');
//Roles api
Route::resource('api/roles', \App\Http\Controllers\Roles\ApiRoleController::class, [
    'as' => 'api'
])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/
//users api
Route::resource('api/users', \App\Http\Controllers\Users\ApiUserController::class, [
    'as' => 'api'
])->middleware('auth');

//Get all users
Route::get('/users', [\App\Http\Controllers\Users\UserController::class, 'index'])->middleware(['auth', 'isAdmin'])->name('users.index');
//Update user role
Route::patch('/api/users/{user}/roles', [\App\Http\Controllers\Users\ApiUserController::class, 'updateUserRole'])->middleware('auth')->name('api.users.roles.update');
Route::get('/api/users/{user}/roles', [\App\Http\Controllers\Users\ApiUserController::class, 'getUserRole'])->middleware('auth')->name('api.users.roles.show');

/*
|--------------------------------------------------------------------------
| Jurors
|--------------------------------------------------------------------------
*/

Route::apiResource('api/jurors', \App\Http\Controllers\JuryController::class, [
    'as' => 'api'
])->middleware('auth');

Route::get('/jurors', [\App\Http\Controllers\JuryController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('jurors.index');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::get('login', [\App\Http\Controllers\AuthController::class, 'redirectGoogleLogin'])->name('login');
Route::get('/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);

