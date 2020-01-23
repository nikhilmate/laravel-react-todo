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

Route::middleware('auth:api', 'verified')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('user-registration', 'UserController@registerUser');
Route::post('user-login', 'UserController@loginUser');

Route::get('forgot-password', 'UserController@forgotPassword');
// -------------------- [ Auth Tokens ]
Route::middleware('auth:api', 'verified')->group(function () {

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

Route::get('email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
Route::get('password/find/{token}', 'PasswordResetController@find');
Route::post('password/create', 'PasswordResetController@create');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');
    Route::post('password/reset', 'PasswordResetController@reset');
});
