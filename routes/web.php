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
//Subir una imagen de perfil
Route::POST('users/image', 'UsuariosController@uploadImage')->name('usuarios.photo');
//Mostrar imagen de perfil
Route::GET('users/getimage/{filename}', 'UsuariosController@getImage')->name('usuarios.getImage');

//listamos tooodas las adopciones
Route::GET('adp', 'AdopcionesController@index')->name('adp.listall');
//Crear una adopcion
Route::POST('adp', 'AdopcionesController@create')->name('adp.create');
//Modificar una adopcion
Route::PUT('adp', 'AdopcionesController@update')->name('adp.listar');
//Borrar una adopcion
Route::DELETE('adp/{id}', 'AdopcionesController@destroy')->name('adp.destroy');
//Mostrar un registro
Route::GET('adp/{id}', 'AdopcionesController@show')->name('adp.show');
//listar numero de registros
Route::GET('/adp/list', 'AdopcionesController@listadp')->name('adp.count');
//Adoptar algo
Route::POST('adp/new', 'AdopcionesController@newadp')->name('adp.adp');

