<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Illuminate\Http\Request;
use App\Usuarios;
use App\Adopcion;
use mysql_xdevapi\Exception;
use \Validator;
use function foo\func;


class AdopcionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Adopcion::all();
        return $list;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                    $validate = Validator::make($paramsArray, [
                        'tipo' => 'required|max:30|string',
                        'edad' => 'required|max:30',
                        'raza' => 'required|max:30|string',
                        'ciudad' => 'required|max:30|string',
                        'provincia' => 'required|max:30|string',
                        'detalles' => 'required|max:200',
                        'nombre' => 'required|max:30|string',
                        'sexo' => 'required|max:30|string',
                    ]);
                    if ($validate->fails()) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => $validate->errors(),
                        );
                        return response()->json($data, 200);
                    } else {
                        $adp = new Adopcion();
                        $adp->usuarios_id = $user->user;
                        $adp->tipo = $paramsArray["tipo"];
                        $adp->edad = $paramsArray["edad"];
                        $adp->raza = $paramsArray["raza"];
                        $adp->sexo = $paramsArray["sexo"];
                        $adp->nombre = $paramsArray["nombre"];
                        $adp->cuidad = $paramsArray["ciudad"];
                        $adp->provincia = $paramsArray["provincia"];
                        $adp->detalles = $paramsArray["detalles"];
                        $adp->save();

                        $data = array(
                            'status' => 'Success',
                            'code' => 200,
                            'message' => 'Adopcion creada.',
                            'adp' => $adp
                        );
                        return response()->json($data, 200);
                    }
                } else {
                    if (empty($user)) {
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
                            'message' => 'Lo sentimos, pero los datos enviados no son correctos o existentes. Vuelve a intentarlo'
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
    public
    function show($id)
    {
        $adopcion = Adopcion::find($id);
        if ($adopcion) {
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'adopcion' => $adopcion
            );
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'No hemos encontrado ninguna adopcion. Vuelve a intentarlo,'
            );
        }
        return response()->json($data, 200);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function newadp(Request $request)
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
                    $adp = Adopcion::find($paramsArray["adpid"]);
                    $ownerAdp = Usuarios::find($user->sub);
                    if ($adp && $ownerAdp) {
                        if ($adp->usuarios_id == $ownerAdp->user) {
                            $adp->adoptedby_id = $paramsArray["adp_usr_id"];
                            $adp->archived = 1;
                            $adp->save();
                            $data = array(
                                'status' => 'Success',
                                'code' => 200,
                                'message' => 'Enhorabuena! ya has dado en adopcion a tu animal.'
                            );
                            return response()->json($data, 200);
                        } else {
                            $data = array(
                                'status' => 'Error',
                                'code' => 404,
                                'message' => 'Lo sentimos, lo que estas intentando modificar no es tuyo.'
                            );
                            return response()->json($data, 200);
                        }
                    } else {
                        if (empty($adp)) {
                            $data = array(
                                'status' => 'Error',
                                'code' => 404,
                                'message' => 'Lo sentimos pero no hemos encontrado ninguna adopcion. Vuelve a intentarlo.'
                            );
                            return response()->json($data, 200);
                        } elseif (empty($ownerAdp)) {
                            $data = array(
                                'status' => 'Error',
                                'code' => 404,
                                'message' => 'Lo sentimos pero no hemos encontrado tu usuario. Vuelve a intentarlo.'
                            );
                            return response()->json($data, 200);
                        } else {
                            $data = array(
                                'status' => 'Error',
                                'code' => 404,
                                'message' => 'Lo sentimos, pero no hemos encontrado ni un usuario ni una adopcion. Vuelve a intentarlo.'
                            );
                            return response()->json($data, 200);
                        }
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $token = $request->header('Authorization');

        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $json = $request->input('json', null);
                $paramsArray = json_decode($json, true);
                $image1 = $request->file('file0');
                $image2 = $request->file('file1');
                $image3 = $request->file('file2');
                $user = $jwtAuth->checkToken($token, true);

                if (!empty($paramsArray) && !empty($user)) {
                    $validate = Validator::make($paramsArray, [
                        'tipo' => 'required|max:30|alpha_num',
                        'edad' => 'required|max:30',
                        'raza' => 'required|max:30|alpha_num',
                        'ciudad' => 'required|max:30|alpha_num',
                        'provincia' => 'required|max:30|alpha_num',
                        'detalles' => 'required|max:200',
                        'nombre' => 'required|max:30|string',
                        'sexo' => 'required|max:30|string',
                    ]);
                    if ($validate->fails()) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => $validate->errors(),
                        );
                        return response()->json($data, 200);
                    }

                    $validate = Validator::make($request->all(), [
                        'file0' => 'image|mimes:jpg,jpeg,png,gif',
                        'file1' => 'image|mimes:jpg,jpeg,png,gif',
                        'file2' => 'image|mimes:jpg,jpeg,png,gif',
                    ]);

                    if ($validate->fails()) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'La imagen es incorrecta.',
                            'errors' => $validate->errors()
                        );
                        return response()->json($data, 200);
                    } else {
                        $adp = Adopcion::find($paramsArray["id"]);
                        if (!empty($adp)) {
                            if ($adp->usuarios_id == $user->sub) {
                                $images = array(
                                    'image1' => $image1,
                                    'image' => $image2,
                                    'image' => $image3
                                );
                                foreach ($images as $key => $image) {
                                    if ($image != null) {
                                        $imageName = time() . $image->getClientOriginalName();
                                        \Storage::disk('userimages')->put($imageName, \File::get($image));
                                        $adp->image_ . [$key] = $imageName;
                                    }
                                }
                                $adp->tipo = $paramsArray["tipo"];
                                $adp->edad = $paramsArray["edad"];
                                $adp->raza = $paramsArray["raza"];
                                $adp->sexo = $paramsArray["sexo"];
                                $adp->nombre = $paramsArray["nombre"];
                                $adp->cuidad = $paramsArray["cuidad"];
                                $adp->provincia = $paramsArray["provincia"];
                                $adp->detalles = $paramsArray["detalles"];
                                $adp->save();


                                $data = array(
                                    'status' => 'Success',
                                    'code' => 200,
                                    'message' => 'Adopcion modificada correctamente.'
                                );
                                return response()->json($data, 200);
                            }
                        } else {
                            $data = array(
                                'status' => 'Error',
                                'code' => 404,
                                'message' => 'Lo sentimos, no hemos encontrado ninguna adopcion con ese ID.'
                            );
                            return response()->json($data, 200);
                        }
                    }
                } else {
                    if (empty($user)) {
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
                            'message' => 'Lo sentimos, pero los datos enviados no son correctos o existentes. Vuelve a intentarlo'
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
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public
    function destroy(Request $request, $id)
    {
        $token = $request->header('Authorization');

        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $user = $jwtAuth->checkToken($token, true);
                $userAdp = Usuarios::find($user->sub);
                $adp = Adopcion::find($id);

                if (!empty($userAdp && !empty($adp))) {
                    if ($adp->usuarios_id == $userAdp->id) {
                        $adp->delete();
                        $data = array(
                            'status' => 'Succes',
                            'code' => 200,
                            'message' => 'La adopcion se ha eliminado correctamente.'
                        );
                        return response()->json($data, 200);
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, pero lo que estas intentando borrar no es tuyo.'
                        );
                        return response()->json($data, 200);
                    }
                } else {
                    if (!empty($userAdp) && empty($adp)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, no hemos encontrado una adopcion con ese ID.'
                        );
                    } elseif (!empty($adp) && empty($userAdp)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, no hemos encontrado un usuario con el ID.'
                        );
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, no hemos encontrado un usuario con el ID ni una adopcion con ese ID.'
                        );
                    }
                    return response()->json($data, 200);
                }
            } else {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Lo sentimos, para hacer esta peticion necesitas estar logeado'
                );
                return response()->json($data, 200);
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => '200',
                'message' => 'Lo sentimos, pero no has incluido la cabecera de autorizacion, introducela por favor.'
            );
            return response()->json($data, 200);
        }
    }

    public function countadp()
    {
        return Adopcion::count();
    }

    public function noadp()
    {
        $adp = Adopcion::where([
            'archived' => 0
        ])->get();
        if ($adp) {
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'adp' => $adp
            );
            return response()->json($data, 200);
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'No hemos encontrado nada, lo sentimos.'
            );
            return response()->json($data, 200);
        }
    }

    public function adped()
    {
        $adp = Adopcion::where([
            'archived' => 1
        ])->get();
        if ($adp) {
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'adp' => $adp
            );
            return response()->json($data, 200);
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'No hemos encontrado nada, lo sentimos.'
            );
            return response()->json($data, 200);
        }
    }

    public function images(Request $request, $id)
    {

        $token = $request->header('Authorization');
        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $image1 = $request->file('file0');
                $image2 = $request->file('file1');
                $image3 = $request->file('file2');
                $user = $jwtAuth->checkToken($token, true);
                $adp = Adopcion::find($id);

                if (!empty($user) && !empty($adp)) {
                    if ($user->user == $adp->usuarios_id) {
                        $validate = Validator::make($request->all(), [
                            'file0' => 'image|mimes:jpg,jpeg,png,gif',
                            'file1' => 'image|mimes:jpg,jpeg,png,gif',
                            'file2' => 'image|mimes:jpg,jpeg,png,gif'
                        ]);
                        if ($validate->fails()) {
                            $data = array(
                                'status' => 'Error',
                                'code' => 404,
                                'message' => 'La imagen es incorrecta.',
                                'errors' => $validate->errors()
                            );
                            return response()->json($data, 200);
                        } else {
                            if ($image1 != null) {
                                $imageName = time() . $image1->getClientOriginalName();
                                \Storage::disk('adpimages')->put($imageName, \File::get($image1));
                                $adp->image_1 = $imageName;
                            }
                            if ($image2 != null) {
                                $imageName = time() . $image2->getClientOriginalName();
                                \Storage::disk('adpimages')->put($imageName, \File::get($image2));
                                $adp->image_2 = $imageName;
                            }
                            if ($image3 != null) {
                                $imageName = time() . $image3->getClientOriginalName();
                                \Storage::disk('adpimages')->put($imageName, \File::get($image3));
                                $adp->image_3 = $imageName;
                            }
                            $adp->save();
                            $data = array(
                                'status' => 'Success',
                                'code' => 200,
                                'message' => 'Tus imagenes se han guardado correctamente.',
                            );
                            return response()->json($data, 200);

                        }
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, pero lo que estas intentando modificar no es tuyo.'
                        );
                        return response()->json($data, 200);
                    }
                } else {
                    $data = array(
                        'status' => 'Error',
                        'code' => 404,
                        'message' => 'No hemos encontrado una adopcion o usuario. Vuelve a intentarlo.'
                    );
                    return response()->json($data, 200);
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

    public function getadpimages($filename)
    {
        $exist = \Storage::disk('adpimages')->exists($filename);

        if ($exist) {
            $file = \Storage::disk('adpimages')->get($filename);
            return \Response($file);
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos la imagen no exite'
            );
            return response()->json($data, 200);
        }
    }

    public function filter(Request $request)
    {
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true);

        $adps = Adopcion::where([
            'cuidad' => $paramsArray["comunidad"]
        ]);

        if (!empty($adps)) {
            $data = array(
                'status' => 'Succes',
                'code' => 200,
                'adp' => $adps
            );
            return \response()->json($data, 200);
        } else {
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'vacio' => 1,
                'adp' => 'Lo sentimos, no hemos encontrado ninguna adopcion que mostrar.'
            );
            return \response()->json($data, 200);
        }
    }
}
