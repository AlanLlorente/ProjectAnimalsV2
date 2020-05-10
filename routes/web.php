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
//listar todos los nombres de usuarios
Route::GET('users/list/users', 'UsuariosController@getnames')->name('usuarios.listnames');

//listamos tooodas las adopciones
Route::GET('adp', 'AdopcionesController@index')->name('adp.listall');
//Crear una adopcion
Route::POST('adp', 'AdopcionesController@store')->name('adp.create');
//Modificar una adopcion
Route::PUT('adp', 'AdopcionesController@update')->name('adp.listar');
//Borrar una adopcion
Route::DELETE('adp', 'AdopcionesController@destroy')->name('adp.destroy');
//Mostrar un registro
Route::GET('adp/{id}', 'AdopcionesController@show')->name('adp.show');
//listar numero de registros
Route::GET('count/adp', 'AdopcionesController@countadp')->name('adp.count');
//Adoptar algo
Route::POST('adp/new', 'AdopcionesController@newadp')->name('adp.adp');
//listar todos los animales NO adoptados
Route::GET('noadp/adp', 'AdopcionesController@noadp')->name('adp.noadp');
//listar todos los animales PENDIENTES de adoptar
Route::GET('yadp/adp', 'AdopcionesController@adped')->name('adp.adped');
//subir imagenes
Route::POST('adp/new/img/{id}', 'AdopcionesController@images')->name('ado.images');
//get de las imagenes
Route::GET('adp/img/{filename}', 'AdopcionesController@getadpimages')->name('adp.getimages');
//filtros
Route::GET('filter/adp', 'AdopcionesController@filter')->name('filter.adp');

//enviar un mensaje
Route::POST('msj', 'MensajesController@store')->name('msj.new');
//recoger todos los mensajes que tenga
Route::GET('msj/user', 'MensajesController@index')->name('msj.get');
//borrar un mensaje
Route::PUT('msj', 'AdopcionesController@adped')->name('adp.adped');
//Mostrar un solo mensaje
Route::GET('msj/{id}', 'Mensajescontroller@show')->name('adp.leer');
