<?php namespace Regulus\Exterminator;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

use Regulus\Exterminator\Exterminator as Dbg;

Route::get('debug/{code?}', function()
{
    return Dbg::e();
});