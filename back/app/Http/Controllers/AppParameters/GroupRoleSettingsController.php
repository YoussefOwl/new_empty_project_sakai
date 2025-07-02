<?php

namespace App\Http\Controllers\AppParameters;

use App\Http\Controllers\Controller;
use App\Models\AppParameters\{config_can_access, config_can_access_keys};
use App\Models\helpers;
use App\Traits\Globlal\ErrorHandling;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Crypt, DB,  Validator};

use PHPUnit\Util\Exception;

class GroupRoleSettingsController extends Controller
{
    use ErrorHandling;

    public function AfficherGroupRole(Request $request)
    {
        try {
            $skip = $request->input('skip') ? intval($request->input('skip')) : 0;
            $take = $request->input('take') ? intval($request->input('take')) : 10;
            $colone = $request->input('colone') ? $request->input('colone') : "key_label";
            $order = $request->input('order') ? $request->input('order') : "asc";
            $getAll = $request->input('getAll', false);

            /* --------------------------------- filter --------------------------------- */
            $key_label = $request->input('key_label') ? trim($request->input('key_label')) : null;
            $id_role = $request->input('id_role') ? intval($request->input('id_role')) : null;
            $description = $request->input('description') ? trim($request->input('description')) : null;

            /* -------------------------------- afficher -------------------------------- */
            $liste_config_can_access_keys = config_can_access_keys::with('roles')
            ->when($key_label, fn($query)=> $query->where('key_label', 'like', "%$key_label%"))
            ->when($description, fn($query)=> $query->where('description', 'like', "%$description%"))
            ->when($id_role, fn($query)=> (
                $query->whereHas('roles', function($q) use ($id_role) {
                    $q->where('roles.id', $id_role);
                })
            ))
            ->orderBy($colone, $order);

            $totalRecords = $liste_config_can_access_keys->get()->count();

            $liste_config_can_access_keys = ($getAll ? $liste_config_can_access_keys : $liste_config_can_access_keys->skip($skip)->take($take));
            $liste_config_can_access_keys = $liste_config_can_access_keys->get()->map(function($item) {
                $item->_id = Crypt::encryptString($item->id);
                $item->created_at_formated = date("d-m-Y H:i", strtotime($item->created_at));
                $item->updated_at_formated = $item->updated_at ? date("d-m-Y H:i", strtotime($item->updated_at)) : null;
                return $item;
            });

            return $this->success([
                'data' => $liste_config_can_access_keys,
                'totalRecords' => $totalRecords
            ]);

        } catch (Exception | QueryException $exp) {
            return $this->error(['erreur' => $exp]);
        }
    }

    public function AjoutGroupRole(Request $request)
    {
        try {
            DB::beginTransaction();
            helpers::SqlListen();
            
            $validtors = Validator::make($request->all(), ['key_label' => 'required']);
            if ($validtors->fails()) return $this->api_message(data: [$validtors->errors()]);

            /* --------------------------------- values --------------------------------- */
            $key_label = helpers::handleValue($request->input('key_label'));
            $description = helpers::handleValue($request->input('description'));
            $roles = $request->input('roles') ? (array) $request->input('roles') : null;

            /* --------------------------------- ajouter -------------------------------- */
            $config_can_access_keys = config_can_access_keys::firstOrCreate(
            [
                'key_label' => $key_label,
            ], 
            [
                'key_label' => $key_label,
                'description' => $description,
            ]);

            if(!$config_can_access_keys->wasRecentlyCreated) return $this->api_message('exists_deja');

            /* ----------------------- if some roles are affected ----------------------- */
            if ($roles) {
                $roles = array_map(fn($item)=> [
                    'id_can_access_key' => $config_can_access_keys->id,
                    'id_role' => $item['id']
                ], $roles);
                /* ---------------------------- affectation roles --------------------------- */
                config_can_access::insert($roles);
            }

            /* -------------------------------------------------------------------------- */
            /*                                 action logs                                */
            /* -------------------------------------------------------------------------- */
            $description_log = "Ajout d'un nouveau group de roles, identifiant : $config_can_access_keys->id";
            helpers::Log_action('config_can_access_keys',Auth::user()->id, "Ajout d'un nouveau group de roles",$description_log,$request->all());
            DB::commit();
            return $this->success();
        } catch (Exception | QueryException $exp) {
            DB::rollBack();
            return $this->error(['erreur' => $exp]);
        }
    }

    public function ModifierGroupRole(Request $request)
    {
        try {
            DB::beginTransaction();
            $validtors = Validator::make($request->all(), [
                '_id' => 'required'
            ]);
            if ($validtors->fails()) return $this->api_message(data: [$validtors->errors()]);

            /* --------------------------------- values --------------------------------- */
            $_id = intval(Crypt::decryptString($request->input('_id')));
            $description = helpers::handleValue($request->input('description'));
            $roles = $request->input('roles') ? (array) $request->input('roles') : null;

            $config_can_access_keys = config_can_access_keys::find($_id);
            $old_description = (clone $config_can_access_keys)->description;
            $config_can_access_keys->description = $description;
            $config_can_access_keys->save();

            /* -------------------------------------------------------------------------- */
            /*                                   update                                   */
            /* -------------------------------------------------------------------------- */
            $config_can_access_keys = config_can_access::where('id_can_access_key', $_id)
            ->when($roles, fn($item) => $item->whereNotIn('id_role', $roles));
            
            /* ----------------------------- save old value ----------------------------- */
            $old_value = (clone $config_can_access_keys)->get();
            /* --------------------------------- delete --------------------------------- */
            $config_can_access_keys = $config_can_access_keys->delete();
            /* --------------------------------- ajouter -------------------------------- */
            if ($roles) {
                /* ---------------------------- affectation roles --------------------------- */
                foreach ($roles as $role) {
                    config_can_access::firstOrCreate([
                        'id_can_access_key' => $_id,
                        'id_role' => $role
                    ]);
                }
            }

            /* ------------------------------- LOG ACTION ------------------------------- */
            $description_log = "Modification d'un group de roles, identifiant sur la base : $_id";
            helpers::Log_action('config_can_access',Auth::user()->id, "Modification d'un group de roles",$description_log,[
                'request_all' => $request->all(),
                'old_value' => [
                    $old_description,
                    ...$old_value
                ]
            ]);
            DB::commit();
            return $this->success();
        } catch (Exception | QueryException $exp) {
            return $this->error(['erreur' => $exp]);
        }
    }
}
