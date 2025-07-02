<?php
namespace App\Traits\Globlal;

trait ErrorHandling {
    /* --------------------------- SUCCESS API RESPOSE -------------------------- */
    public function success(array $data = [], int $code = 200) 
    {
        return response()->json([
            'api_message' => 'success', ...$data
        ], $code);
    }

    /* ---------------------------- ERROR API RESPOSE --------------------------- */
    public function error(array $data = [], int $code = 200) 
    {
        return response()->json([
            'api_message' => 'erreur', ...$data // dictionnary merging
        ], $code);
    }

    /* ------------------------------ OTHER RESPOSE ----------------------------- */
    public function api_message(string $api_message = 'erreur_de_parametres' , array $data = [],  int $code = 200)
    {
        return response()->json([
            'api_message' => $api_message,
            ...$data
        ], $code);
    }
}