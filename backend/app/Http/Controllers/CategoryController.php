<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;


class CategoryController extends Controller
{
	public function __construct() {
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index() {
    	$categories = Category::all();

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'categories' => $categories
    	]);
    }

    public function show($id) {
    	$category = Category::find($id);

    	if (is_object($category)) {
    		$data = array(
    			'code' => 200,
    			'status' => 'success',
    			'categories' => $category
    		);
    	} else {
    		$data = array(
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'Esa categoria no existe...'
    		);
    	}

    	return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
    	//Recoger los datos por POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);

    	if (!empty($params_array)) {

    		//Validar los datos
    		$validate = \Validator::make($params_array, [
    			'name' => 'required'
    		]);

    		//Guardar la categoría
    		if ($validate->fails()) {
    			$data = [
    				'code' => 400,
    				'status' => 'error',
    				'message' => 'No se ha guardado la categoría'
    			];
    		} else {
    			$category = new Category();
    			$category->name = $params->name;
    			$category->save();

    			$data = [
    				'code' => 200,
    				'status' => 'success',
    				'message' => 'La categoría se ha guardado satisfactoriamente',
    				'category' => $params_array
    			];
    		}

    	}else {
    		$data = [
    				'code' => 400,
    				'status' => 'error',
    				'message' => 'No han llegado datos'
    		];
    	}
    	//Devolver el resultado
    	return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
    	//Recoger datos que llegan por POST
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if (!empty($params_array)) {

    	//Validar los datos
    		$validate = \Validator::make($params_array, [
    			'name' => 'required'
    		]);
    	//Quitar lo que no quierop actualizar
    		unset($params_array['id']);
    		unset($params_array['created_at']);

    	//Actualizar el registro
    		$category = Category::where('id', $id)->update($params_array);

    		$data = [
    				'code' => 200,
    				'status' => 'success',
    				'message' => 'La categoría se ha actualizado correctamente',
    				'category' => $params_array
    		];

    	} else {
    		$data = [
    				'code' => 400,
    				'status' => 'error',
    				'message' => 'No han llegado datos'
    		];
    	}

    	//Devolver respuesta
    	return response()->json($data, $data['code']);
    }	
}
