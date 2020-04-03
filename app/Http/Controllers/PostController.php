<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{

	public function __construct() {
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
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
    	$post = Post::find($id)->load('category');

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
    			$jwtAuth = new JwtAuth();
    			$token = $request->header('Authorization', null);
    			$user = $jwtAuth->checkToken($token, true);

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
}
