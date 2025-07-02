<?php

namespace App\Http\Controllers\Devises;

use App\Http\Controllers\Controller;
use App\Models\Devise\devises;
use App\Models\helpers;
use App\Traits\Globlal\ErrorHandling;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Crypt,Validator};
use PHPUnit\Util\Exception;

class DeviseController extends Controller
{
    use ErrorHandling;
    public function AfficherDeviseParameter(Request $request) {
        try {
            $skip = $request->input('skip') ? intval($request->input('skip')) : 0;
            $take = $request->input('take') ? intval($request->input('take')) : 10;
            $colone = $request->input('colone') ? $request->input('colone') : "id";
            $order = $request->input('order') ? $request->input('order') : "asc";
            $getAll = $request->input('getAll', false);

            /* --------------------------------- filter --------------------------------- */
            $label = helpers::handleValue($request->input('label'));
            $abrv = helpers::handleValue($request->input('abrv'));
            $description = helpers::handleValue($request->input('description'));

            /* -------------------------------- afficher -------------------------------- */
            $liste_devises = devises::select("devises.*")
            ->when($abrv, fn($query)=> $query->where('abrv', $abrv))
            ->when($label, fn($query)=> $query->where('label','like', "%$label%"))
            ->when($description, fn($query)=> $query->where('description','like', "%$description%"))
            ->orderBy($colone, $order);

            $totalRecords = $liste_devises->count();

            $liste_devises = ($getAll ? $liste_devises : $liste_devises->skip($skip)->take($take))
            ->get()
            ->map(function($item) {
                $item->_id = Crypt::encryptString($item->id);
                $item->created_at_formated = date("m-d-Y H:i:s" ,strtotime($item->created_at));
                $item->updated_at_formated = $item->updated_at ? date("m-d-Y H:i:s" ,strtotime($item->updated_at)) : null;
                return $item;
            });

            return $this->success([
                'data' => $liste_devises,
                'totalRecords' => $totalRecords
            ]);
        } catch (Exception | QueryException $exp) {
            return $this->error(['erreur' => $exp]);
        }
    }

    public function AjouterDeviseParameter(Request $request)
    {
        try {
            $validators = Validator::make($request->all(), [
                'liste_devise' => "required",
                'liste_devise.*.label' => 'required',
                'liste_devise.*.abrv' => 'required'
            ]);
            if ($validators->fails()) return $this->api_message(data: [$validators->errors()]);

            /* ---------------------------------- vars ---------------------------------- */
            $liste_devise = (array) $request->input("liste_devise");

            /* -------------------------------- add query ------------------------------- */
            $liste_devise = array_map(fn($item) =>[
                'label' => helpers::handleValue($item['label']),
                'abrv' => helpers::handleValue($item['abrv']),
                'description' => isset($item['description'])? trim($item['description']) : null,
                'created_at' => now()
            ],$liste_devise);
            
            /* -------------------------- check if devis exists ------------------------- */
            $devise_exists = devises::whereIn('label', array_column($liste_devise, 'label'))
            ->orWhereIn('abrv', array_column($liste_devise, 'abrv'))
            ->exists();
            if($devise_exists) return $this->api_message("devise_exists");

            $query_ajouter = devises::insert($liste_devise);
            
            if(!$query_ajouter) return $this->api_message("non_ajoutee");

            /* ------------------------------- action logs ------------------------------ */
            $description_log = "Création d'une nouvelle devise";
            helpers::Log_action('devises', Auth::user()->id,"Création d'une nouvelle devise",$description_log,$request->all());

            return $this->success();
        } catch (Exception | QueryException $exp) {
            return $this->error(['erreur' => $exp]);
        }
    }

    public function ModifierDeviseParameter(Request $request)
    {
        try {
            $validators = Validator::make($request->all(), [
                '_id' => 'required',
                'label' => 'required',
                'abrv' => 'required'
            ]);
            if ($validators->fails()) return $this->api_message(data: [$validators->errors()]);
            /* ---------------------------------- vars ---------------------------------- */
            $_id = Crypt::decryptString($request->input("_id"));
            $label = helpers::handleValue($request->input("label"));
            $abrv = helpers::handleValue($request->input("abrv"));
            $description = helpers::handleValue($request->input("description"));

            $devise_exists = devises::where([
                ['id', '!=', $_id],
                ['label', $label],
                ['abrv', $abrv],
            ])
            ->exists();
            if($devise_exists) return $this->api_message("devise_exists");

            /* --------------------------------- update --------------------------------- */
            $devises = devises::find($_id);
            $old_value = clone $devises;
            $devises = $devises->update([
                'label' => $label,
                'abrv' => $abrv,
                'description' => $description,
            ]);

            if(!$devises) return $this->api_message("non_modifee");

            /* ------------------------------- action logs ------------------------------ */
            $description_log = "Modification du devise, ID : ".$_id;
            helpers::Log_action('devises', Auth::user()->id,"Modification d'une devise",$description_log,[
                'new_value' => $request->all(),
                'old_value' => $old_value
            ]);

            return $this->success();
        } catch (Exception | QueryException $exp) {
            return $this->error(['erreur' => $exp]);
        }
    }

    public function SuppressionDeviseParameter(Request $request) {
        try {
            $validators = Validator::make($request->all(), ['_id' => 'required']);
            if ($validators->fails()) return $this->api_message(data: [$validators->errors()]);

            /* ---------------------------------- vars ---------------------------------- */
            $_id = Crypt::decryptString($request->input("_id"));
            $devises = devises::find($_id);

            /* ------------------------ check if has transaction ------------------------ */
            if ($devises?->transactions?->isNotEmpty()) return $this->api_message("used_devise");
            $old_value = clone $devises;

            if(!$devises->delete()) return $this->api_message("not_deleted");

            /* ------------------------------- action logs ------------------------------ */
            $description_log = "Suppression du devise, ID : ".$_id;
            helpers::Log_action('devises', Auth::user()->id,"Suppression d'une devise",$description_log,[
                'old_value' => $old_value
            ]);
            return $this->success();
        } catch (Exception | QueryException $exp) {
            return $this->error(['erreur' => $exp]);
        }
    }
}
