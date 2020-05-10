<?php

namespace App\Http\Controllers;

use App\Usuarios;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Mensajes;

class MensajesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $token = $request->header('Authorization');
        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $user = $jwtAuth->checkToken($token, true);
                $msj = Mensajes::Where([
                    'to_users_id' => $user->subm,
                    'borrar' => 0
                ])->get();

                $data = array(
                    'statuss' => 'Success',
                    'code' => 200,
                    'msj' => $msj
                );

                return \response()->json($data, 200);

            } else {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Lo sentimos, para hacer esta peticion primero necesitas iniciar sesion.'
                );
                return response()->json($data, 200);
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos, pero no has incluido la cabecera de autorizacion, introducela por favor.'
            );
            return response()->json($data, 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $token = $request->header('Authorization');

        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $json = $request->input('json', null);
                $paramsArray = json_decode($json, true);
                $user = $jwtAuth->checkToken($token, true);

                if (!empty($paramsArray) && !empty($user)) {
                    $validate = \Validator::make($paramsArray, [
                        'to_users_id' => 'required',
                        'titulo' => 'required',
                        'contenido' => 'required'
                    ]);
                    if ($validate->fails()) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, no hemos podido enviar el mensaje.',
                            'errors' => $validate->errors()
                        );
                        return response()->json($data, 200);
                    } else {
                        $msj = new Mensajes();
                        $msj->from_users_id = $user->sub;
                        $msj->to_users_id = $paramsArray["to_users_id"];
                        $msj->titulo = $paramsArray["titulo"];
                        $msj->contenido = $paramsArray["contenido"];

                        $msj->save();

                        $data = array(
                            'status' => 'Success',
                            'code' => 200,
                            'message' => 'Mensaje enviado.'
                        );
                        return \response()->json($data, 200);
                    }
                } else {
                    if (empty($paramsArray)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, pero los datos enviados son incorrectos. Vuelve a intentarlo.'
                        );
                        return response()->json($data, 200);
                    } elseif (empty($user)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, pero no hemos encontrado tu usuario. Vuelve a intentarlo.'
                        );
                        return response()->json($data, 200);
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos pero no hemos encontrado ni tu usuario ni nos han llegado mas datos. Vuelve a intetarlo'
                        );
                        return response()->json($data, 200);
                    }
                }
            } else {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Lo sentimos, para hacer esta peticion primero necesitas iniciar sesion.'
                );
                return response()->json($data, 200);
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos, pero no has incluido la cabecera de autorizacion, introducela por favor.'
            );
            return response()->json($data, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $token = $request->header('Authorization');

        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $user = $jwtAuth->checkToken($token, true);
                $msj = Mensajes::find($id);
                if (!empty($user) && !empty($msj)) {
                    if ($user->sub == $msj->to_users_id) {
                        $msj->leido = 1;
                        $msj->save();
                        $data = array(
                            'status' => 'Success',
                            'code' => 200,
                            'msj' => $msj
                        );
                        return \response()->json($data, 200);
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'El mensaje al que estas intentando acceder no es tuyo, lo sentimos.'
                        );
                        return \response()->json($data, 200);
                    }
                } else {
                    if (empty($user)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'No hemos encontrado un usuario, vuelve a intentarlo.'
                        );
                        return \response()->json($data, 200);
                    } elseif (empty($msj)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'No hemos encontrado un mensaje, vuelve a intentarlo.'
                        );
                        return \response()->json($data, 200);
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'No hemos encontrado un mensaje ni un usuario. Vuelve a intentarlo.'
                        );
                        return \response()->json($data, 200);
                    }
                }
            } else {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Lo sentimos, para hacer esta peticion primero necesitas iniciar sesion.'
                );
                return response()->json($data, 200);
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos, pero no has incluido la cabecera de autorizacion, introducela por favor.'
            );
            return response()->json($data, 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $token = $request->header('Authorization');

        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $user = $jwtAuth->checkToken($token, true);
                $msj = Mensajes::find($id);
                if (!empty($user) && !empty($msj)) {
                    if ($user->sub == $msj->to_users_id) {
                        $msj->borrar = 0;
                        $msj->save();
                        $data = array(
                            'status' => 'Success',
                            'code' => 200,
                            'msj' => $msj
                        );
                        return \response()->json($data, 200);
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'El mensaje al que estas intentando acceder no es tuyo, lo sentimos.'
                        );
                        return \response()->json($data, 200);
                    }
                } else {
                    if (empty($user)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'No hemos encontrado un usuario, vuelve a intentarlo.'
                        );
                        return \response()->json($data, 200);
                    } elseif (empty($msj)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'No hemos encontrado un mensaje, vuelve a intentarlo.'
                        );
                        return \response()->json($data, 200);
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'No hemos encontrado un mensaje ni un usuario. Vuelve a intentarlo.'
                        );
                        return \response()->json($data, 200);
                    }
                }
            } else {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Lo sentimos, para hacer esta peticion primero necesitas iniciar sesion.'
                );
                return response()->json($data, 200);
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos, pero no has incluido la cabecera de autorizacion, introducela por favor.'
            );
            return response()->json($data, 200);
        }
    }
}
