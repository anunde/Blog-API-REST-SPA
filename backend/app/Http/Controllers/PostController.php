<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{

	public function __construct() {
		$this->middleware('api.auth', ['except' => ['index', 'show', 'getImage', 'getPostsByCategory', 'getPostsByUser']]);
	}

    public function index() {
    	$posts = Post::all()->load('category');

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'posts' => $posts
    	], 200);
    }

    public function show($id) {
    	$post = Post::find($id)->load('category')->load('user');

    	if(is_object($post)) {
    		$data = array(
    			'code' => 200,
    			'status' => 'success',
    			'post' => $post
    		);
    	} else {
    		$data = array(
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'No existe entrada con ese ID'
    		);
    	}

    	return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
    	//Recoger los datos por POST
    		$json = $request->input('json', null);
    		$params = json_decode($json);
    		$params_array = json_decode($json, true);

    		if(!empty($params_array)) {
    			//Conseguir usuario identificado
    			$user = $this->getIdentity($request);

    			//Validar datos
    			$validate = \Validator::make($params_array, [
    				'title' => 'required',
    				'content' => 'required',
    				'category_id' => 'required',
    				'image' => 'required'
    			]);

    			if($validate->fails()) {
    				$data = array(
    					'code' => 400,
    					'status' => 'error',
    					'message' => 'Faltan datos necesarios para crear la entrada'
    				);
    			} else {
    				//Guardar post
    				$post = new Post();
    				$post->user_id = $user->sub;
    				$post->title = $params_array['title'];
    				$post->content = $params_array['content'];
    				$post->category_id = $params_array['category_id'];
    				$post->image = $params_array['image'];
    				$post->save();

    				$data = array(
    					'code' => 200,
    					'status' => 'success',
    					'message' => 'Se ha creado la nueva entrada correctamente',
    					'post' => $post
    				);
    			}
    		} else {
    			$data = array(
    				'code' => 400,
    				'status' => 'error',
    				'message' => 'Los datos no están llegando'
    			);
    		}

    	//Delvolver una respuesta
    	return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
    	//Recoger datos obtenidos por POST
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	//Conseguir usuario identificado
    	$user = $this->getIdentity($request);

    	//Comprobamos que esa entrada existe
    	$post = Post::where('id', $id)->where('user_id', $user->sub)->first();

    	if(!empty($params_array) && is_object($post)) {
    		//Validar datos
    		$validate = \Validator::make($params_array, [
    			'title' => 'required',
    			'content' => 'required',
    			'category_id' => 'required',
    			'image' => 'required'
    		]);

    		if ($validate->fails()) {
    			$data = [
    			'code' => 400,
    			'status' => 'error',
    			'message' => 'Los datos introducidos no son válidos'
    			];
    		} else {
    			//Eliminar datos que no queremos modificar
    			unset($params_array['id']);
    			unset($params_array['user_id']);
    			unset($params_array['created_at']);
    			unset($params_array['user']);

    			//Actualizar los datos
    			$where = [
    				'id' => $id,
    				'user_id' => $user->sub
    			];
    			$post = Post::updateOrCreate($where, $params_array);

    			$data = [
    				'code' => 200,
    				'status' => 'success',
    				'message' => 'La entrada se ha actualizado correctamente',
    				'post' => $post,
    				'changes' => $params_array
    			];

    		}

    	} else {
    		$data = [
    			'code' => 400,
    			'status' => 'error',
    			'message' => 'No has podido modificar esta entrada'
    		];
    	}
    	
    	//Devolver una respuesta
    	return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request) {
    	//Conseguir usuario identificado
    	$user = $this->getIdentity($request);

    	//Conseguir el post
    		$post = Post::where('id', $id)->where('user_id', $user->sub)->first();

    		if(is_object($post)) {
    	//Borrar post
    		$post->delete();

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'message' => 'La entrada se ha borrado de la base de datos',
    			'post' => $post
    		];

    	} else {
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'Esa entrada no existe en la base de datos'
    		];
		}

    	//Devolver algo
		return response()->json($data, $data['code']);
    }

    private function getIdentity(Request $request) {
    	//Conseguir usuario identificado
    	$jwtAuth = new JwtAuth();
    	$token = $request->header('Authorization', null);
    	$user = $jwtAuth->checkToken($token, true);

    	return $user;
    }

    public function upload(Request $request) {
    	//Recoger la imagen de la petición
    	$image = $request->file('file0');

    	if ($image) {
    		//Validar la imagen
    		$validate = \Validator::make($request->all(), [
    			'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
    		]);

    		if ($validate->fails()) {
    			$data = [
    				'code' => 400,
    				'status' => 'error',
    				'message' => 'La imagen no es válida'
    			];
    		} else {
    			//Guardar la imagen en el disco
    			$image_name = time().$image->getClientOriginalName();

    			\Storage::disk('images')->put($image_name, \File::get($image));

    			$data = [
    				'code' => 200,
    				'status' => 'success',
    				'message' => 'La imagen se ha guardado correctamente',
    				'image' => $image_name
    			];
    		}
    	} else {
    		$data = [
    			'code' => 400,
    			'status' => 'error',
    			'message' => 'No ha llegado ninguna imagen'
    		];
    	}
    	
    	//Devolver los datos
    	return response()->json($data, $data['code']);
    }


    public function getImage($filename) {
    	//Comprobar si existe el fichero
    	$isset = \Storage::disk('images')->exists($filename);

    	if ($isset) {
    		//Conseguir imagen
    		$file = \Storage::disk('images')->get($filename);

    		//Devolver imagen
    		return new Response($file, 200);
    	} else {
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'La imagen no existe'
    		];
    	}
    	
    	return response()->json($data, $data['code']);
    }

    public function getPostsByCategory($id) {
    	$posts = Post::where('category_id', $id)->get();

    	return response()->json([
    		'status' => 'success',
    		'posts' => $posts
    	], 200);
    }

    public function getPostsByUser($id) {
    	$posts = Post::where('user_id', $id)->get();

    	return response()->json([
    		'status' => 'success',
    		'posts' => $posts 
    	], 200);
    }
}
