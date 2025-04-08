<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Mail\VoteCertificateMail;


Route::get('/', function () {
    return Inertia::render('Bienvenido');
})->name('default');

Route::get('/redirect', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('programs.index');
    }

    if (auth()->user()->hasRole('juror')) {
        return redirect()->route('votes.authorize');
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
| Votes
|--------------------------------------------------------------------------
*/
Route::apiResource('api/votes', \App\Http\Controllers\VoteController::class, [
    'as' => 'api'
])->middleware('auth');

Route::get('api/votes/getVoterVotingOptions/{voter}', [\App\Http\Controllers\VoteController::class, 'getVoterVotingOptions'])->name('api.votes.getVoterVotingOptions');

Route::get('/vote', [\App\Http\Controllers\VoteController::class, 'vote'])->middleware('auth')->name('votes.vote');
Route::get('/votes/authorize', [\App\Http\Controllers\VoteController::class, 'authorizeVote'])->middleware('auth')->name('votes.authorize');


/*
|--------------------------------------------------------------------------
| Votes
|--------------------------------------------------------------------------
*/
Route::get('api/voter/searchByIdentificationNumber', [\App\Http\Controllers\VoterController::class, 'searchByIdentificationNumber'])
    ->middleware('auth')->name('api.voters.searchByIdentificationNumber');

    Route::put('api/voter/updateEmail', [\App\Http\Controllers\VoterController::class, 'updateEmail'])
    ->middleware('auth')
    ->name('api.voters.updateEmail');

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

//Get table voting results
Route::get('/tables/{table}/report', [\App\Http\Controllers\TableController::class, 'generateReport'])->middleware(['auth', 'isAdmin'])->name('tables.report');

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

/*
|--------------------------------------------------------------------------
| certificate
|--------------------------------------------------------------------------
*/

Route::get('/certificate/{token}', [\App\Http\Controllers\VoteCertificateController::class, 'show'])
    ->name('votes.certificate');

    use App\Models\Vote;

    Route::get('/test-email', function () {
        $vote = Vote::latest()->first(); 
        $name = $vote->voter->name;
        $token = $vote->certificate_token;
        $certificateUrl = route('votes.certificate', ['token' => $token]);
        $qr = 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(200)->generate($certificateUrl));
    
        Mail::to('practicantes.g3@unibague.edu.co')->send(new VoteCertificateMail($name, $certificateUrl, $qr));
    
        return 'Correo enviado con éxito ';
    });
    