<?php

use Illuminate\Http\Request;
use App\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::post('/register', 'Api\AuthContorller@register');
Route::post('/login', 'Api\AuthContorller@login');
Route::put('/updatephone', 'Api\AuthContorller@updatePhone')->middleware('auth:api');
Route::post('/complete-registration', 'Api\AuthContorller@completeRegistration')->middleware('auth:api');
Route::post('/profile/update', 'Api\ProfileController@update')->middleware('auth:api');
Route::get('/profile/{id}', 'Api\ProfileController@index')->middleware('auth:api');
Route::get('/follow/{user}', 'Api\FollowsController@store')->middleware('auth:api');
Route::get('/search/{user}', 'Api\UserController@search')->middleware('auth:api');
Route::get('/check-username/{username}', 'Api\UserController@checkUsername')->middleware('auth:api');
Route::get('/not-following', 'Api\UserController@index')->middleware('auth:api');
Route::get('/chats', 'Api\ChatController@index')->middleware('auth:api');
Route::get('/chat/{id}', 'Api\ChatController@show')->middleware('auth:api');
Route::post('/send-chat', 'Api\ChatController@store')->middleware('auth:api');