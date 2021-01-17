<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LeagueController;
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

Route::get('/', function (){		
    return view('main');
});


Route::post('/up-teams', [LeagueController::class, 'upload_teams']);
Route::post('/get-table', [LeagueController::class, 'get_table']);
Route::post('/week', [LeagueController::class, 'next_week']);
Route::post('/all', [LeagueController::class, 'play_all']);
Route::post('/reset', [LeagueController::class, 'reset']);
Route::post('/reset-all', [LeagueController::class, 'reset_all']);

Route::group(['middleware' => 'web'], function () {

	

});
