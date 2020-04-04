<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Comprobar si el usuario esta identificado:

        //Recogemos token
        $token = $request->header('Authorization');
        //Instanciamos Objeto de provider
        $jwtAuth = new \JwtAuth();
        //Comprobamos token con información correcta
        $checkToken = $jwtAuth->checkToken($token);

        if ($checkToken) {
            return $next($request);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no esta identificado correctamente'
            );

            return response()->json($data, $data['code']);
        }

    }
}
