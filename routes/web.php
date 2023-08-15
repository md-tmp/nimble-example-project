<?php
/**
 * Web Routes 
 * php version 8.2.4
 * 
 * @category WebRoutes
 * @package  NimbleExampleProject
 * @author   Matt Dunbar <matt@mattdsworld.com>
 * @license  Not for commercial use.
 * @link     https://github.com/md-tmp/nimble-example-project
 */

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\ResultController;

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

Route::get(
    '/', function () {
        return redirect()->route('keywords.index');
    }
);

Route::middleware(
    [
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ]
)->group(
    function () {
        Route::resource('keywords', KeywordController::class)->only(['index', 'show', 'store']);
        
        Route::get(
            '/results/{id}/cache',
            [ResultController::class, 'cache']
        )->name('results.cache');
    }
);
