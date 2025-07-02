<?php
namespace App\Http\Middleware;
use Closure;
use PHPUnit\Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\{Response,Config,Crypt};
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class CheckRole  extends BaseMiddleware
{
    public function handle($request, Closure $next)
    {
        try
        {
            /* ----------------------- Le role défini sur la route ---------------------- */
            $actions = $request->route()->getAction('roles');
            /* --------------------- Front end user id_role_for_api_middleware in token -------------------- */
            $id_role_for_api_middleware = Crypt::decryptString(JWTAuth::parseToken()->getPayload()->get('id_role_for_api_middleware'));
            $role_to_test = md5(base64_encode($id_role_for_api_middleware));
            /* ---------------------- Récupération du claim unique ---------------------- */
            $unique_claim = JWTAuth::parseToken()->getPayload()->get('unique_claim');

            if (in_array($role_to_test, $actions) && ($unique_claim==env('unique_claim'))) 
            {
                return $next($request);
            } 
            else 
            {
                return Response::json(['api_message' => 'Access Denied'], 403);
            }
        } 
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $ex) {
            return Response::json(['Api_Error_message' => 'Token is Expired CheckRole','Error' => $ex], 401);
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $ey) {
            return Response::json(['Api_Error_message' => 'Token is Invalid CheckRole','Error' => $ey], 401);
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $ez) {
            return Response::json(['Api_Error_message' => 'Token is not found CheckRole','Error' => $ez], 401);
        }
        catch (Exception $e) {
            return Response::json(['Api_Error_message' => 'Error CheckRole middleware', 'Error' => $e], 401);
        }
    }
}