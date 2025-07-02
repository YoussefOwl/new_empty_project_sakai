<?php

namespace App\Http\Middleware;
use Closure;
use PHPUnit\Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\{Response,Config};
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddlewareAdmin extends BaseMiddleware
{
    public function handle($request, Closure $next)
    {
        try
        {
            $unique_claim = JWTAuth::parseToken()->getPayload()->get('unique_claim');
            if($unique_claim==env('unique_claim'))
            {
                // Si c'est un token valide et si l'utilsateur figure sur la base
                
                if (JWTAuth::parseToken()->authenticate())
                {
                    return $next($request);
                }
                else
                {
                    return Response::json(['api_message' => 'Access Denied'],403);
                }
            }
            else
            {
                return Response::json(['api_message' => 'Access Denied invalid unique_claim'],403);
            }
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $ex) {
            return Response::json(['Api_Error_message' => 'Token is Expired JwtMiddlewareAdmin','Error' => $ex], 401);
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $ey) {
            return Response::json(['Api_Error_message' => 'Token is Invalid JwtMiddlewareAdmin','Error' => $ey], 401);
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $ez) {
            return Response::json(['Api_Error_message' => 'Token is not found JwtMiddlewareAdmin','Error' => $ez], 401);
        }
        catch (Exception $e) {
            return Response::json(['Api_Error_message' => 'Error JwtMiddlewareAdmin middleware', 'Error' => $e], 401);
        }
    }
}
