<?php

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

//listamos los usuarios registrados
Route::GET('users', 'UsuariosController@index')->name('usuarios.index');
//mostramos solo un usuario
Route::GET('users/{id}', 'UsuariosController@show')->name('usuarios.show');
//creamos un nuevo usuario
Route::POST('users', 'UsuariosController@store')->name('usuarios.create');
//modificamos los datos de un usuario
Route::PUT('users', 'UsuariosController@update')->name('usuarios.update');
//Eliminamos un usuario
Route::DELETE('users', 'UsuariosController@destroy')->name('usuarios.destroy');
//Login de un usuario
Route::POST('users/login', 'UsuariosController@login')->name('usuarios.login');


