<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Usuarios;
use PHPUnit\Framework\UnexpectedValueException;


class JwtAuth
{

    public $key;

    public function __construct()
    {
        $this->key = 'Esta_es_una_clave_super_secreta-98752448';
    }

    public function login($email, $password, $getToken = null)
    {
        $user = Usuarios::Where([
            'email' => $email,
            'password' => $password,
        ])->first();

        $login = false;
        if (is_object($user)) {
            $login = true;
        }
        if ($login) {
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'user' => $user->user,
                'name' => $user->nombre,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60),
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            if (is_null($getToken)) {
                $data = $jwt;
            } else {
                $data = $decoded;
            }

        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login Incorrecto',
            );
        }
        return $data;
    }

    public function checkToken($jwt, $getId = false)
    {
        $auth = false;
        try {
            $jwt = str_replace('"', "", $jwt);
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        if (!empty($decode) && is_object($decode) && isset($decode->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getId) {
            return $decode;
        }
        return $auth;
    }
}
