<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('user-registration', 'UserController@registerUser');
Route::post('user-login', 'UserController@loginUser');
// -------------------- [ Auth Tokens ]
Route::group(['middleware' => 'auth:api'], function () {

    Route::get('user-detail', 'UserController@userDetail');
    Route::post('update-user', 'UserController@update');
    Route::delete('delete-user', 'UserController@destroy');
    Route::get('logout-user', 'UserController@logout');

    Route::get('todos', 'TodoController@index');
    Route::post('todos/create', 'TodoController@createTodo');
    Route::get('todos/{id}', 'TodoController@getTodo');
    Route::put('todos/{id}/update', 'TodoController@updateTodo');
    Route::delete('todos/{id}/delete','TodoController@deleteTodo');
});
