<?php

namespace App\Models;

use App\Models\AppParameters\config_can_access_keys;
use Illuminate\Support\Facades\File;
use PHPUnit\Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth; //use this library
use Illuminate\Support\Facades\{Auth, Crypt,Response,Schema,Storage,DB, Log};
use App\Models\parametrages\actions_logs;
use Carbon\Carbon;

/* --------------------- To use a function in other class -------------------- */
// use App\Models\helpers;
// self::function_name(params)

class helpers
{
    public function __construct() {
        // si on veut initialiser des variables globales
    }

    /* ------------------------ Retourne une image base64 ----------------------- */
    
    public static function get_image($image, $source)
    {
        $fullPathAvatar = storage_path('app/private/' . $source . '/' . $image);
        $image = File::exists($fullPathAvatar)
        ? "data:image/png" . ";base64," . base64_encode(file_get_contents($fullPathAvatar))
        : null;
        return $image;
    }

    public static function getMonthName($value, $liste_mois) {
        foreach ($liste_mois as $month) {
            if (intval($month["value"]) == $value) {
                return $month["label"];
            }
        }
        return null; // Return null if the value is not found
    }

    public static function Get_badge_restant($value)
    {
        if ($value > 0) {
            return "text-success";
        } else {
            return "text-danger";
        }
    }

    public static function Get_badge_solde($value)
    {
        if ($value > 0) {
            return "bg-vert";
        } 
        else if ($value == 0) {
            return "bg-jaune";
        }
        else {
            return "bg-rouge";
        }
    }

    public static function get_id_logger()
    {
        try
        {
            $token = JWTAuth::getToken();
            $token_decoded = JWTAuth::getPayload($token)->toArray();
            if (empty($token_decoded['api_id_user'])) {
                return Response::json(['api_message' => 'Access Denied invalid api_id_user'],403);
                exit();
            }
            return intval(Crypt::decryptString($token_decoded['api_id_user']));
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $ex) {
            return Response::json(['Api_Error_message' => 'Token is Expired helpers','Error' => $ex], 401);
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $ey) {
            return Response::json(['Api_Error_message' => 'Token is Invalid helpers','Error' => $ey], 401);
        }
        catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $ez) {
            return Response::json(['Api_Error_message' => 'Token is not found helpers','Error' => $ez], 401);
        }
        catch (Exception $e) {
            return Response::json(['Api_Error_message' => 'Error helpers', 'Error' => $e], 401);
        }
    }

    public static function get_id_role_base_64_md5()
    {
        return md5(base64_encode(DB::table('users')->where('id',self::get_id_logger())->value('id_role')));
    }

    public static function if_user_is_admin()
    {
       return Auth::user()->is_admin;
    }

    /* ---------------------- Conversion Nombre en lettres ---------------------- */

    public static function nombre_en_lettre($Montant)
    {
        $grade = array(0 => "Zero ", 1 => " Milliards ", 2 => " Millions ", 3 => " Mille ");
        $Mon = array(0 => " Dirham", 1 => " Dirhams", 2 => " Centime", 3 => " Centimes");

        // Mise au format pour les chéques et le SWI 
        $Montant = number_format($Montant, 2, ".", "");

        if ($Montant == 0) 
        {
            $result = $grade[0] . $Mon[0];
        } 
        else
        {
            $result = "";
            // Calcule des Unités 
            $montant = intval($Montant);

            // Calcul des Centimes 
            $Centime = round(($Montant * 100) - ($montant * 100), 0);

            // Traitement pour les Milliards 
            $nombre = $montant / 1000000000;
            $nombre = intval($nombre);
            if ($nombre > 0) {
                if ($nombre > 1) {
                    $result = $result . self::Convertion($nombre) . $grade[1];
                } else {
                    $result = $result . " Un " . $grade[1];
                    $result = substr($result, 0, 13) . " ";
                }
                $montant = $montant - ($nombre * 1000000000);
            }

            // Traitement pour les Millions 
            $nombre = $montant / 1000000;
            $nombre = intval($nombre);
            if ($nombre > 0) {
                if ($nombre > 1) {
                    $result = $result . self::Convertion($nombre) . $grade[2];
                } else {
                    $result = $result . " Un " . $grade[2];
                    $result = substr($result, 0, 12) . " ";
                }
                $montant = $montant - ($nombre * 1000000);
            }

            // Traitement pour les Milliers 
            $nombre = $montant / 1000;
            $nombre = intval($nombre);
            if ($nombre > 0) {
                if ($nombre > 1) {
                    $result = $result . self::Convertion($nombre) . $grade[3];
                } else {
                    $result = $result . $grade[3];
                }
                $montant = $montant - ($nombre * 1000);
            }

            // Traitement pour les Centaines & Centimes 
            $nombre = $montant;
            if ($nombre > 0) {
                $result = $result . self::Convertion($nombre);
            }
            // Traitement si le montant = 1 
            if ((substr($result, 0, 7) == " Et Un " and strlen($result) == 7)) {
                $result = substr($result, 3, 3);
                $result = $result . $Mon[0];
                if (intval($Centime) != 0) {
                    $differ = self::Convertion(intval($Centime));
                    if (substr($differ, 0, 7) == " Et Un ") {
                        if ($result == "") {
                            $differ = substr($differ, 3);
                        }
                        $result = $result . " " . $differ . $Mon[2];
                    } else {
                        $result = $result . " Et " . $differ . $Mon[3];
                    }
                }
                // Traitement si le montant > 1 ou = 0 
            } else {
                if ($result != "") {
                    $result = $result . $Mon[1];
                }
                if (intval($Centime) != 0) {
                    $differ = self::Convertion(intval($Centime));
                    if (substr($differ, 0, 7) == " Et Un ") {
                        if ($result == "") {
                            $differ = substr($differ, 3);
                        }
                        $result = $result . " " . $differ . $Mon[2];
                    } else {
                        if ($result != "") {
                            $result = $result . " Et " . $differ . $Mon[3];
                        } else {
                            $result = $differ . $Mon[3];
                        }
                    }
                }
            }
        }
        return trim(mb_strtoupper(str_replace("  "," ",$result)));
    }

    public static function Convertion($Valeur)
    {
        $code = "";
        //texte en clair 
        $SUnit = array(1 => "Et Un ", 2 => "Deux ", 3 => "Trois ", 4 => "Quatre ", 5 => "Cinq ", 6 => "Six ", 7 => "Sept ", 8 => "Huit ", 9 => "Neuf ", 10 => "Dix ", 11 => "Onze ", 12 => "Douze ", 13 => "Treize ", 14 => "Quatorze ", 15 => "Quinze ", 16 => "Seize ", 17 => "Dix-Sept ", 18 => "Dix-Huit ", 19 => "Dix-Neuf ");
        $sDiz = array(20 => "Vingt ", 30 => "Trente ", 40 => "Quarante ", 50 => "Cinquante ", 60 => "Soixante ", 70 => "Soixante Dix ", 80 => "Quatre Vingt ", 90 => "Quatre Vingt Dix ");

        if ($Valeur > 99) {
            $N1 = intval($Valeur / 100);
            if ($N1 > 1) {
                $code = $code . $SUnit[$N1];
            }
            $Valeur = $Valeur - ($N1 * 100);
            if ($code != "") {
                if ($Valeur == 0) {
                    $code = $code . " Cents ";
                } else {
                    $code = $code . " Cent ";
                }
            } else {
                $code = " Cent ";
            }
        }
        if ($Valeur != 0) {

            if ($Valeur > 19) {
                $N1 = intval($Valeur / 10) * 10;
                if ((($Valeur > 70) and ($Valeur < 80) or ($Valeur > 90)) && $Valeur - $N1 != 0) {
                    $code = $code . $sDiz[$N1 - 10];
                    if ($Valeur > 70 && $Valeur < 80 && $Valeur - $N1 == 1)
                        $code = $code . " Et ";
                } else
                    $code = $code . $sDiz[$N1];
                if (($Valeur > 70) and ($Valeur < 80) or ($Valeur > 90))
                    $Valeur = $Valeur + 10;
                $Valeur = $Valeur - $N1;
            }
            if ($Valeur > 0) {
                $code = $code . " " . $SUnit[$Valeur];
            }
        }
        return $code;
    }

    public static function GetLabels($value_de_comparaison, $liste, $la_cle_voulue = "label", $compare_key = "value")
    {
        $label = null;
        
        // Check if the list exists in the configuration file
        if (!config()->has('config_arrays.' . $liste)) 
        {
            return null; // List does not exist
        }
        
        $my_list = config('config_arrays.' . $liste);

        if ($value_de_comparaison!==null) 
        {
            foreach ($my_list as $key) 
            {
                // Check if the specified keys exist in the current element of the list
                if (isset($key[$compare_key]) && $value_de_comparaison == $key[$compare_key] && isset($key[$la_cle_voulue])) {
                    $label = $key[$la_cle_voulue];
                    break;
                }
            }
        }
        return $label;
    }

    public static function Log_action($table_name,$id_user,$libelle,$description=null,$json_log_data=null)
    {
        if($table_name && $id_user && $libelle)
        {
            if(Schema::hasTable($table_name))
            {
                $query_log = actions_logs::create([
                    'id_user' => intval($id_user),
                    'table_name' => $table_name,
                    'libelle_log' => trim($libelle),
                    'json_log_data' => $json_log_data ? json_encode($json_log_data) : null,
                    'description' => $description ? trim($description) : null
                ]);
                $query_log->save();
            }
        }
    }
    
    public static function getRoleGroup($key_label = null, $value_only = true) 
    {
        $config_can_access_keys = config_can_access_keys::select(
            'config_can_access_keys.*',
            'config_can_access_keys.key_label as key', 
            'config_can_access.id_role as value'
        )
        ->when($key_label, fn($query)=> $query->where('config_can_access_keys.key_label', $key_label))
        ->join('config_can_access', 'config_can_access.id_can_access_key', 'config_can_access_keys.id');

        $config_can_access_keys = $value_only 
        ? $config_can_access_keys->get()->map(fn($query)=> md5(base64_encode($query->value)))->toArray()
        : $config_can_access_keys->groupBy('config_can_access_keys.id')
        ->get()->map(fn($item) => [
            'key' => $item->key,
            'value' => $item?->roles?->map(fn($role)=> md5(base64_encode(intval($role->id))))
        ])->toArray();

        return $config_can_access_keys;
    }   

    # Create a new array with a specific value of a chosen key from the original array of objects

    public static function map_list_single_key($source_list, $wanted_key)
    {
        $new_array = [];
        if($source_list && $wanted_key)
        {
            $new_array = array_map(function($item) use ($wanted_key) 
            {
                if(gettype($item) == "object")
                {
                    return isset($item->$wanted_key) ? $item->$wanted_key : null;
                }
                else if(gettype($item) == "array")
                {
                    return isset($item[$wanted_key]) ? $item[$wanted_key] : null;
                }
            }, $source_list);
        }
        return $new_array;
    }

    public static function moveFile($fileName,$content,$disk) {
        $etat = Storage::disk($disk)->put($fileName, $content);
        return $etat;
    }

    // Helper function to handle value assignment
    public static function handleValue($value, $type = 'string') {
        // Simplified check for null-like values
        if (in_array($value, [null, 'null', '', '0', 0], true)) {
            return in_array($type, ['double', 'bool', 'int']) ? 0 : null;
        }
        // Handling based on type
        return match($type) {
            'double' => doubleval($value),
            'int' => intval($value),
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== false ? 1 : 0,
            default => trim($value)
        };
    }

    public static function remove_spaces($string)
    {
        return preg_replace('/\s+/','',$string);
    }

    public static function is_decimal($value) {
        if (is_numeric($value)) {
            return (float)$value != (int)$value;
        }
        return false;
    }

    public static function removeAllButNumbers($string) {
        return preg_replace('/\D/', '', $string);
    }

    public static function apiResponse($message, $data = null, $erreur = null, $http_code=200,$totalRecords = null)
    {
        $response = ['api_message' => $message];
        if ($erreur) {$response['erreur'] = $erreur; }
        if ($data) {$response['data'] = $data; }
        if ($totalRecords) {$response['totalRecords'] = $totalRecords; }
        return response()->json($response,$http_code);
    }

    public static function SumOneKey($ma_liste, $wanted_key)
    {
        $sum = 0;
        // Quick return if list is empty
        if (empty($ma_liste)) {
            return $sum;
        }
        foreach ($ma_liste as $ligne) {
            // Combine isset and !empty checks, and use is_numeric for type checking
            if (!empty($ligne[$wanted_key]) && is_numeric($ligne[$wanted_key])) {
                $sum += $ligne[$wanted_key];
            }
        }
        return $sum;
    }

    public static function convertToStandardDateFormat($date)
    {
        // Essayer de convertir en utilisant différents formats
        try {
            // Format ISO 8601
            if (Carbon::hasFormat($date, 'Y-m-d\TH:i:s.v\Z')) {
                return Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $date)->format('Y-m-d');
            }
            // Format ISO 8601 (sans millisecondes)
            if (Carbon::hasFormat($date, 'Y-m-d\TH:i:s\Z')) {
                return Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $date)->format('Y-m-d');
            }
            // Format jour/mois/année
            if (Carbon::hasFormat($date, 'd/m/Y')) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }
            if (Carbon::hasFormat($date, 'd-m-Y')) {
                return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
            }
            // Autres formats peuvent être ajoutés ici
            // Si aucune correspondance de format n'est trouvée, essayer la conversion générale
            return Carbon::parse($date)->format('Y-m-d');
        } catch (Exception $e) {
            // Gérer les erreurs ou retourner null si la conversion échoue
            return null;
        }
    }

    public static function isImage($extension) {
        return match(true) {
            $extension != null =>in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff']),
            default => false
        };
    }

    public static function SqlListen()
    {
        DB::listen(function ($query) {
            $datetime = date('d-m-Y H:i:s');
            Log::channel("sql_logs")->info(
            "
            [ -------------------------------------------------------------------------- ]
            [                                 $datetime                        ]
            [ -------------------------------------------------------------------------- ]"
            );
            Log::channel("sql_logs")->info('SQL QUERY : ' . $query->sql);
            Log::channel("sql_logs")->info('DATA : ' . implode(', ', $query->bindings ?? null));
            Log::channel("sql_logs")->info('EXECUTION TIME : ' . $query->time . ' ms');
        });
    }

}