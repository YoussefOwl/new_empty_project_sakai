<?php

namespace App\Http\Controllers\General;
use stdClass;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\AppParameters\sidebar_buttons;
use App\Models\Devise\devises;
use App\Models\Devise\transactions;
use PHPUnit\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Crypt,Validator,File,Auth};
use App\Models\users\{User,roles};
use App\Models\helpers;
/* ------------------------------ parametrages ------------------------------ */
use App\Models\parametrages\{actions_logs};

class GeneralController extends Controller
{
    public function TelechargerDocument(Request $request)
    {
        try
        {
            $messages = ['fileName' => 'required','from' => 'required'];
            $validator = Validator::make($request->all(),$messages,$messages);
            /* -------------------  Si la validation n'a pas réussie ------------------ */
            if ($validator->fails()){ 
                return helpers::apiResponse(message:'erreur_de_parametres',erreur:$validator->errors());
            } 

            /* ------------------------------ Les variables ----------------------------- */
            $source = $request->input('source') ? $request->input('source') : "private";
            $file = storage_path('app/'.$source.'/'.$request->input('from').'/'.$request->input('fileName'));

            /* ------------------------- On retourne le résultat ------------------------ */
            return File::exists($file)
            ? response()->download(storage_path("app/".$source."/".$request->input('from').'/'.$request->input('fileName')))
            : helpers::apiResponse('fichier_inexistant');
        }

        catch (Exception | QueryException $error)
        {
            return helpers::apiResponse('fichier_inexistant',null,$error);
        }
    }

    public function LoadParamsForList(Request $request)
    {
        try
        {
            /* ------------------------- Création des variables ------------------------- */
            $results = new stdClass();

            /* ---------------------------- les utilisateurs ---------------------------- */
            if($request->input('users'))
            {
                $results->liste_users = User::select(
                    'users.id',
                    DB::raw('CONCAT(users.nom, " ", users.prenom) AS nom_complet'),
                    // alias
                    'users.id as value',
                    DB::raw('CONCAT(users.nom, " ", users.prenom) AS label')
                )
                ->orderBy("nom","asc")
                ->get();
            }
          
            /* -------------------------------- les roles ------------------------------- */
            if($request->input('roles'))
            {
                $results->liste_roles = roles::select(
                    'roles.id',
                    'roles.libelle_role',
                    // les alias
                    'roles.id as value',
                    'roles.libelle_role as label'
                )
                ->orderBy("libelle_role","asc")
                ->get();
            }

            /* ---------------------------------- tables ---------------------------------- */
            if($request->input('tables'))
            {
                $liste_tables = DB::select('SHOW TABLES');
                $results->liste_tables = helpers::map_list_single_key($liste_tables,'Tables_in_database');
            }
            /* -------------------------- sidebar buttons liste ------------------------- */
            if ($request->input('sidebar_buttons')) {
                $selected_roles_routerLink_list = $request->input('selected_roles_routerLink_list') ? (array) $request->input('selected_roles_routerLink_list') : null;
                $results->liste_sidebar_buttons = sidebar_buttons::when($selected_roles_routerLink_list, fn($query)=>(
                    $query->whereNotIn('routerLink', $selected_roles_routerLink_list)
                ))->get();
            }

            /* --------------------------------- devises -------------------------------- */
            if ($request->input('devises')) {
                $results->liste_devises = devises::select(
                    "id as value",
                    'label',
                    'abrv'
                )
                ->orderBy('label', "asc")
                ->get();
            }

            /* -------------------------- transactions_totales -------------------------- */
            if ($request->input('transactions_totales')) {
                $id_devise = helpers::handleValue($request->input("id_devise"));
                $transactions = transactions::select(
                    DB::raw("SUM(CASE WHEN is_entree = 1 THEN prix * taux ELSE 0 END) as total_entree"),
                    DB::raw("SUM(CASE WHEN is_entree = 0 THEN prix * taux ELSE 0 END) as total_sortie")
                )
                ->when($id_devise, fn($query)=> $query->where('id_devise', $id_devise))
                ->first();

                $total_entree = $transactions->total_entree ?? 0;
                $total_sortie = $transactions->total_sortie ?? 0;
                $total_difference = round($total_entree - $total_sortie, 2);

                $results->transactions_totales = [
                    "total_entree_formated" => number_format($total_entree, 2, ",", "."),
                    "total_sortie_formated" => number_format($total_sortie, 2, ",", "."),
                    "total_difference_formated" => number_format($total_difference, 2, ",", "."),
                    "diff_color" => match(true) {
                        $total_difference > 0 => "text-green-600",
                        $total_difference < 0 => "text-red-600",
                        default => null
                    },
                    "diff_icon" => match(true) {
                        $total_difference > 0 => "pi pi-arrow-up",
                        $total_difference < 0 => "pi pi-arrow-down",
                        $total_difference == 0 => 'pi pi-circle-on'
                    },
                ];
            }

            /* ----------------- On retourne le resulat ------------------- */
            $results->api_message = "success";
            return response()->json($results, 200);
        }

        catch (Exception | QueryException $error)
        {
            return helpers::apiResponse(message:'erreur',erreur:$error);
        }
    }

    public function AfficherLogs(Request $request)
    {
        try
        {
            /* ------------------------------ les variables ----------------------------- */
            $skip = $request->input('skip') ? intval($request->input('skip')) : 0;
            $take = $request->input('take') ? intval($request->input('take')) : 10;
            $colone = $request->input('colone') ? $request->input('colone') : "created_at";
            $order = $request->input('order') ? $request->input('order') : "desc";
            $debut = $request->input('debut') ? $request->input('debut') : null;
            $fin = $request->input('fin') ? $request->input('fin') : null;
            $if_excel = $request->input('if_excel') === true;
            $id_user = $request->input('id_user') ? intval($request->input('id_user')) : null;
            $description = $request->input('description') ? trim($request->input('description')) : null;
            $libelle_log = $request->input('libelle_log') ? trim($request->input('libelle_log')) : null;
            $json_log_data = $request->input('json_log_data') ? trim($request->input('json_log_data')) : null;
            $table_name = $request->input('table_name') ? (array)$request->input('table_name') : null;

            /* -------------------------- La requète du select -------------------------- */
            $Liste_logs = actions_logs::join('users', 'actions_logs.id_user', '=', 'users.id')
            ->select(
                'actions_logs.*',
                'users.nom',
                'users.prenom'
            )
            ->when($description, function ($query) use ($description) {
                return $query->where('actions_logs.description', 'like', '%' . $description . '%');
            })
            ->when($json_log_data, function ($query) use ($json_log_data) {
                return $query->where('actions_logs.json_log_data', 'like', '%' . $json_log_data . '%');
            })
            ->when($libelle_log, function ($query) use ($libelle_log) {
                return $query->where('actions_logs.libelle_log', 'like', '%' . $libelle_log . '%');
            })
            ->when($debut, function ($query) use ($debut) {
                return $query->whereDate('actions_logs.created_at', '>=', $debut);
            })
            ->when($fin, function ($query) use ($fin) {
                return $query->whereDate('actions_logs.created_at', '<=', $fin);
            })
            ->when($id_user, function ($query) use ($id_user) {
                return $query->where('actions_logs.id_user', $id_user);
            })
            ->when($table_name, function ($query) use ($table_name) {
                return $query->whereIn('actions_logs.table_name', $table_name);
            })
            ->orderBy($colone,$order);

            $totalRecords = $Liste_logs->count(); // le total

            /* ------------------------------ Finalisation ------------------------------ */

            $Liste_logs = $if_excel
            ? $Liste_logs->get()
            : $Liste_logs->skip($skip)->take($take)->get();

            /* ------------------------------- Traitements ------------------------------ */

            foreach ($Liste_logs as $log)
            {
                $log['_id'] = Crypt::encryptString($log['id']);
                $log['nom_complet'] = $log['nom'].' '.$log['prenom'];
                $log['json_log_data'] = $log['json_log_data'] ? json_decode($log['json_log_data']) : null;
                $log['created_at_formated'] = $log['created_at'] ? Carbon::parse($log['created_at'])->format('d-m-Y H:i') : null;
                $log['updated_at_formated'] = $log['updated_at'] ? Carbon::parse($log['updated_at'])->format('d-m-Y H:i') : null;
                unset($log['id']);
            }

            /* ------------------------- On renvoie le résultat ------------------------- */
            return response()->json(
            [
                'api_message' => 'success',
                'totalRecords' => $totalRecords,
                'data' => $Liste_logs
            ], 200);
            
        }
        catch (Exception | QueryException $error)
        {
            return helpers::apiResponse(message:'erreur',erreur:$error);
        }
    }
}
?>