<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function register(Request $request) {

    	//Recoger los datos del usuario por POST

    	$json = $request->input('json', null);

    	//Decodificar JSON string para extraer las variables
    	
    	$params = json_decode($json);//Decodificando en un Objeto
    	$params_array = json_decode($json, true);//Decodificando en un array


    	if (!empty($params) && !empty($params_array)) {
    		//Limpiar datos
    		$params_array = array_map('trim', $params_array);

    		//Validar datos

    		$validate = \Validator::make($params_array, [
    			'name' => 'required|alpha',
    			'surname' => 'required|alpha',
    			'email' => 'required|email|unique:users',
    			'password' => 'required'
    		]);

    		if($validate->fails()) {
    			$data = array(
    				'status' => 'error',
    				'code' => 404,
    				'message' => 'El usuario no se ha creado',
    				'errors' => $validate->errors()
    			);
    		} else {
    			//Cifrar contraseña
    			$pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);
    			//Comprobar si el usuario ya esta registrado, hecho con Validator
    			//Crear usuario
    			$user = new User();
    			$user->name = $params_array['name'];
    			$user->surname = $params_array['surname'];
    			$user->email = $params_array['email'];
    			$user->password = $pwd;
    			$user->role = 'ROLE_USER';

    			//Guardar el usuario
    			$user->save();

    			$data = array(
    				'status' => 'success',
    				'code' => 200,
    				'message' => 'El usuario se ha creado correctamente',
    				'user' => $user
    			);
    		}
    	} else {
    		$data = array(
    			'status' => 'error',
    			'code' => 404,
    			'message' => 'Los datos enviados no son correctos'
    		);
    	}

    	//Devolvemos JSON
    	return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
    	return "Accion de login de usuario";
    }
}
