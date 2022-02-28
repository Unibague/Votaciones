<?php

use App\Models\Voter;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Bienvenido');
})->name('default');

Route::get('/redirect', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('programs.index');
    }

})->middleware('auth')->name('handleRoleRedirect');

/* >>>>>Faculties routes <<<<<< */

//Faculties api
Route::apiResource('api/faculties', \App\Http\Controllers\FacultyController::class, [
    'as' => 'api'
])->middleware('auth');

//Get all faculties (view)
Route::get('/faculties', [\App\Http\Controllers\FacultyController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('faculties.index');

/* >>>>>Faculties routes <<<<<< */

/* >>>>>Program routes <<<<<< */

//programs api
Route::apiResource('api/programs', \App\Http\Controllers\ProgramController::class, [
    'as' => 'api'
])->middleware('auth');

//Get all programs (view)
Route::get('/programs', [\App\Http\Controllers\ProgramController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('programs.index');

/* >>>>>Program routes <<<<<< */

/* >>>>>Table routes <<<<<< */

//programs api
Route::apiResource('api/tables', \App\Http\Controllers\TableController::class, [
    'as' => 'api'
])->middleware('auth');

//Get all programs (view)
Route::get('/tables', [\App\Http\Controllers\TableController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('tables.index');

/* >>>>>Table routes <<<<<< */


/* >>>>>Roles routes <<<<<< */

//Get all roles
Route::get('/roles', [\App\Http\Controllers\Roles\RoleController::class, 'index'])->middleware(['auth', 'isAdmin'])->name('roles.index');
//Roles api
Route::resource('api/roles', \App\Http\Controllers\Roles\ApiRoleController::class, [
    'as' => 'api'
])->middleware('auth');

/* >>>>>User routes <<<<<< */

//Get all users
Route::get('/users', [\App\Http\Controllers\Users\UserController::class, 'index'])->middleware(['auth', 'isAdmin'])->name('users.index');
//users api
Route::resource('api/users', \App\Http\Controllers\Users\ApiUserController::class, [
    'as' => 'api'
])->middleware('auth');
//Update user role
Route::patch('/api/users/{user}/roles', [\App\Http\Controllers\Users\ApiUserController::class, 'updateUserRole'])->middleware('auth')->name('api.users.roles.update');
Route::get('/api/users/{user}/roles', [\App\Http\Controllers\Users\ApiUserController::class, 'getUserRole'])->middleware('auth')->name('api.users.roles.show');

/* >>>>>User routes <<<<<< */

/* >>>>>Jurors routes <<<<<< */

//Faculties api
Route::apiResource('api/jurors', \App\Http\Controllers\JuryController::class, [
    'as' => 'api'
])->middleware('auth');

//Get all faculties (view)
Route::get('/jurors', [\App\Http\Controllers\JuryController::class, 'indexView'])->middleware(['auth', 'isAdmin'])->name('jurors.index');

/* >>>>>Jurors routes <<<<<< */


//Auth routes
Route::get('login', [\App\Http\Controllers\AuthController::class, 'redirectGoogleLogin'])->name('login');
Route::get('/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);

