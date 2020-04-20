<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdopcionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $adopcion = AdopcionesController::find($id);
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //recoger el auth

        //comprobar el auth

        //comprobar que el auth id es el que pertenece a la adopcion

        //borramos
    }
}
