<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuarios;
use Illuminate\Http\Response;
use \Validator;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listUsers = Usuarios::count();

        return $listUsers;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true);
        $paramsArray = array_map('trim', $paramsArray);

        if (!empty($paramsArray)) {
            $validate = Validator::make($paramsArray, [
                'user' => 'required|max:30|alpha_num|unique:usuarios',
                'nombre' => 'required|max:30|string',
                'apellidos' => 'required|max:30|string',
                'email' => 'required|email|unique:usuarios',
                'password' => 'required',
                'telefono' => 'required|numeric'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'El usuario no se ha podido crear',
                    'errors' => $validate->errors()
                );
                return response()->json($data, 200);
            } else {
                $pwd = hash('sha256', $paramsArray["password"]);
                $user = new Usuarios();
                $user->user = $paramsArray["user"];
                $user->nombre = $paramsArray["nombre"];
                $user->apellidos = $paramsArray["apellidos"];
                $user->email = $paramsArray["email"];
                $user->password = $pwd;
                $user->telefono = $paramsArray["telefono"];
                $user->save();

                $data = array(
                    'status' => 'Success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente!'
                );
                return response()->json($data, 200);
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos, pero los datos enviados son incorrectos. Vuelve a intentarlo.'
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
    public function show($id)
    {
        $user = Usuarios::find($id);
        if ($user) {
            unset($user->password);
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'user' => $user
            );
            return response()->json($data, 200);
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos, no hemos podido encontrar ningun usuario. Vuelve a intentarlo.'
            );
            return response()->json($data, 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id)
    {
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
                $user = $jwtAuth->checkToken($token, true);
                $userUpdate = Usuarios::find($user->sub);

                if (!empty($userUpdate)) {
                    $userUpdate->user = $paramsArray["user"];
                    $userUpdate->nombre = $paramsArray["nombre"];
                    $userUpdate->apellidos = $paramsArray["apellidos"];
                    $userUpdate->email = $paramsArray["email"];
                    $userUpdate->telefono = $paramsArray["telefono"];
                    $userUpdate->save();

                    $data = array(
                        'status' => 'Success',
                        'code' => 200,
                        'message' => 'El usuario se ha modificado correctamente'
                    );
                    return response()->json($data, 200);
                } else {
                    $data = array(
                        'status' => 'Error',
                        'code' => 404,
                        'message' => 'Lo sentimos, no hemos podido encontrar el usuario. Vuelve a intentarlo.'
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $token = $request->header('Authorization');
        if (!empty($token)) {

            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);
            if ($checkToken) {
                $user = $jwtAuth->checkToken($token, true);

                $userDelete = Usuarios::Where([
                    'email' => $user->email,
                ]);

                if (!empty($userDelete)) {
                    $userDelete->delete();

                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El usuario se ha eliminado correctamente'
                    );
                    return response()->json($data, 200);
                } else {
                    $data = array(
                        'status' => 'Error',
                        'code' => 404,
                        'message' => 'Lo sentimos, no hemos encontrado el usuario que quieres eliminar'
                    );
                    return response()->json($data, 200);
                }
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
     * Login into youre account.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $paramsArray = json_decode($json, true);

        if (!empty($paramsArray)) {
            $validate = Validator::make($paramsArray, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Login incorrecto. Vuelve a intentarlo.',
                    'errors' => $validate->errors()
                );
                return response()->json($data, 200);
            } else {
                $jwtAuth = new \JwtAuth();

                $pwd = hash('sha256', $paramsArray["password"]);

                if (!empty($params->gettoken)) {
                    $data = $jwtAuth->login($paramsArray["email"], $pwd, true);
                } else {
                    $data = $jwtAuth->login($paramsArray["email"], $pwd);
                }
            }
            return response()->json($data, 200);
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos, pero los datos enviados son incorrectos. Vuelve a intentarlo.'
            );
            return response()->json($data, 200);
        }
    }

    public function uploadImage(Request $request)
    {
        $token = $request->header('Authorization');
        if (!empty($token)) {
            $jwtAuth = new \JwtAuth();
            $checkToken = $jwtAuth->checkToken($token);

            if ($checkToken) {
                $image = $request->file('file0');
                $user = $jwtAuth->checkToken($token, true);
                $userImage = Usuarios::find($user->sub);

                if ($image && $userImage) {

                    $validate = Validator::make($request->all(), [
                        'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
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
                        $imageName = time() . $image->getClientOriginalName();
                        \Storage::disk('userimages')->put($imageName, \File::get($image));
                        $userImage->image = $imageName;
                        $userImage->save();
                        $data = array(
                            'status' => 'Succes',
                            'code' => 200,
                            'message' => 'Imagen actualizada correctamente.'
                        );
                        return response()->json($data, 200);
                    }
                } else {
                    if (empty($image) && !empty($userImage)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, pero no hemos encontrado una imagen. Vuelve a intentarlo.'
                        );
                        return response()->json($data, 200);
                    } elseif (!empty($image) && empty($userImage)) {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, pero no hemos encontrado el usuario. Vuelve a intentarlo.'
                        );
                        return response()->json($data, 200);
                    } else {
                        $data = array(
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'Lo sentimos, pero no hemos encontrado ni un usuario ni una imgen. Vuelve a intentarlo.'
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

    public function getImage($filename)
    {
        $exist = \Storage::disk('userimages')->exists($filename);

        if ($exist){
            $file = \Storage::disk('userimages')->get($filename);
            return new Response($file);
        }else{
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Lo sentimos la imagen no exite'
            );
            return response()->json($data, 200);
        }
    }

    public function getnames(){
        $usuarios = Usuarios::all();
        $names = array();

        foreach ($usuarios as $key => $usuario){
            array_push($names, $usuario->user);
        }

        return $names;
    }
}
