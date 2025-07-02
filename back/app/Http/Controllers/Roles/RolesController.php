<?php

namespace App\Http\Controllers\Roles;
use App\Http\Controllers\Controller;
use PHPUnit\Exception;
use App\Models\users\roles;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB,Validator,Crypt};
use App\Models\helpers;

class RolesController extends Controller
{
    public function AjouterRole(Request $request)
    {
        try
        {
            $messages = [
                'libelle_role' => 'required|min:3',
                "sidebar" => 'required',
                'acronym_role' => 'required'
            ];
    
            $validator = Validator::make($request->all(),$messages,$messages);
    
            if ($validator->fails()) 
            {
                return helpers::apiResponse(message:'erreur_de_parametres',erreur:$validator->errors());
            } 

            else 
            {
                /* ------------------------------ les variables ----------------------------- */
                $libelle_role = trim($request->input('libelle_role'));
                $sidebar = $request->input('sidebar');
                $acronym_role = trim($request->input('acronym_role'));
                
                // Chercher si le role est existant
                $deuplicate = DB::table('roles')
                ->where('libelle_role', mb_strtolower($libelle_role))
                ->orWhere('libelle_role', mb_strtoupper($libelle_role))
                ->orWhere('acronym_role',$acronym_role)
                ->first();

                if($deuplicate) {
                    return helpers::apiResponse("existant");
                } 

                else 
                {
                    $query_ajouter = roles::create([
                        'libelle_role' => $libelle_role,
                        'acronym_role' => $acronym_role,
                        'sidebar' => json_encode($sidebar),
                        'description' => $request->input('description') ? trim($request->input('description')) : null
                    ]);

                    $if_saved = $query_ajouter->save();

                    if($if_saved) 
                    {
                        /* -------------------------------------------------------------------------- */
                        /*                                 LOG ACTION                                 */
                        /* -------------------------------------------------------------------------- */
                        $description_log = 'Création du role qui a comme identifiant : '.$query_ajouter->id;
                        helpers::Log_action('roles',helpers::get_id_logger(),"Création d'un nouveau rôle",$description_log,$request->all());
                    }
                    return helpers::apiResponse($if_saved ? 'ajouter' : 'non_ajouter');
                }
            }
        } // fin try

        catch (Exception | QueryException $error)
        {
            return helpers::apiResponse(message:'erreur',erreur:$error);
        }
    }

    public function AfficherRole(Request $request)
    {
        try
        {
            /* ------------------------------ Les variables ----------------------------- */
            $skip = $request->input('skip') ? intval($request->input('skip')) : 0;
            $take = $request->input('take') ? intval($request->input('take')) : 10;
            $colone = $request->input('colone') ? $request->input('colone') : "libelle_role";
            $order = $request->input('order') ? $request->input('order') : "asc";
            $if_excel = $request->input('if_excel') ? $request->input('if_excel') : null;
            $libelle_role = $request->input('libelle_role') ? trim($request->input('libelle_role')) : null;
            $description = $request->input('description') ? trim($request->input('description')) : null;
            $acronym_role = $request->input('acronym_role') ? trim($request->input('acronym_role')) : null;
            $sidebar = $request->input('sidebar') ? trim($request->input('sidebar')) : null;

            /* ----------------------- La requête de récupération ----------------------- */
            $listes_roles = roles::select('roles.*')
            ->when($libelle_role, function ($query, $libelle_role) {
                return $query->where('libelle_role', 'like', '%' . $libelle_role . '%');
            })
            ->when($description, function ($query, $description) {
                return $query->where('description', 'like', '%' . $description . '%');
            })
            ->when($acronym_role, function ($query, $acronym_role) {
                return $query->where('acronym_role', 'like', '%' . $acronym_role . '%');
            })
            ->when($sidebar, function ($query, $sidebar) {
                return $query->where('sidebar', 'like', '%' . $sidebar . '%');
            })
            ->orderBy($colone,$order);
    
            $totalRecords = $listes_roles->count(); // le total

            $listes_roles = $if_excel // pagination
            ? $listes_roles->get()
            : $listes_roles->skip($skip)->take($take)->get();

            /* -------------------------------------------------------------------------- */
            /*                           Traitement des données                           */
            /* -------------------------------------------------------------------------- */

            foreach ($listes_roles as $rec) 
            {
                $rec['_id'] = Crypt::encryptString($rec['id']);
                $rec['id_base64_encode'] = base64_encode($rec['id']);
                $rec['id_md5_base64_encode'] = md5(base64_encode($rec['id']));
                $rec['sidebar'] = json_decode($rec['sidebar']);
                $rec['created_at_formated'] = $rec['created_at'] ? Carbon::parse($rec['created_at'])->format('d-m-Y H:i') : null;
                $rec['updated_at_formated'] = $rec['updated_at'] ? Carbon::parse($rec['updated_at'])->format('d-m-Y H:i') : null;
            }

            return response()->json(
            [
                'api_message' =>'success',
                'totalRecords' => $totalRecords,
                'data' => $listes_roles
            ], 200)->header('Content', 'application/json');
        }

        catch (Exception | QueryException $error)
        {
            return helpers::apiResponse(message:'erreur',erreur:$error);
        }
    }

    public function ModifierRole(Request $request)
    {
        try
        {
            $messages = [
                '_id' => 'required',
                'libelle_role' => 'required',
                'sidebar' => 'required',
                'acronym_role' => 'required'
            ];
    
            $validator = Validator::make($request->all(),$messages,$messages);
    
            if ($validator->fails()) 
            {
                return helpers::apiResponse(message:'erreur_de_parametres',erreur:$validator->errors());
            } 
            
            else
            {
                /* ------------------------------ les variables ----------------------------- */
                $_id = intval(Crypt::decryptString(trim($request->input('_id'))));
                $libelle_role = trim($request->input('libelle_role'));
                $acronym_role = trim($request->input('acronym_role'));
                $sidebar = $request->input('sidebar');

                /* -------------------------------------------------------------------------- */
                /*                           Recherche si ça existe                           */
                /* -------------------------------------------------------------------------- */

                $if_dublicate_tentative = DB::table('roles')
                ->where([
                    ['libelle_role',mb_strtolower($libelle_role)],
                    ['id','!=',$_id]
                ])
                ->orWhere([
                    ['libelle_role',mb_strtoupper($libelle_role)],
                    ['id','!=',$_id]
                ])
                ->orWhere([
                    ['acronym_role',$acronym_role],
                    ['id','!=',$_id]
                ])
                ->first();

                if ($if_dublicate_tentative)
                {
                    return helpers::apiResponse("existant");
                }

                else
                {
                    /* ----------------------- La requête de modification ----------------------- */
                    $query_update = DB::table('roles')
                    ->where('id',$_id)
                    ->update(
                        array(
                        'updated_at' => date("Y-m-d H:i:s"),
                        'libelle_role' => $libelle_role,
                        'acronym_role' => $acronym_role,
                        'sidebar' => json_encode($sidebar),
                        'description' => $request->input('description') ? trim($request->input('description')) : null
                    ));
        
                    if ($query_update)
                    {
                        /* -------------------------------------------------------------------------- */
                        /*                                 LOG ACTION                                 */
                        /* -------------------------------------------------------------------------- */
                        $description_log = 'Modification du rôle qui a comme identifiant : '.$_id;
                        helpers::Log_action('roles',helpers::get_id_logger(),"Modification d'un rôle",$description_log,$request->all());
                    }
                    return helpers::apiResponse($query_update ? 'modifier' : 'non_modifier');
                }
            }
        }

        catch (Exception | QueryException $error)
        {
            return helpers::apiResponse(message:'erreur',erreur:$error);
        }
    }

}