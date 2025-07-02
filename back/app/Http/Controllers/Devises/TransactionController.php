<?php
namespace App\Http\Controllers\Devises;
use App\Http\Controllers\Controller;
use App\Models\Devise\transactions;
use App\Models\helpers;
use App\Traits\Globlal\ErrorHandling;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Crypt, DB, Validator};
use PHPUnit\Util\Exception;

class TransactionController extends Controller
{
    use ErrorHandling;
    public function AfficherTransaction(Request $request) {
        try {
            $skip = $request->input('skip') ? intval($request->input('skip')) : 0;
            $take = $request->input('take') ? intval($request->input('take')) : 10;
            $colone = $request->input('colone') ? $request->input('colone') : "id";
            $order = $request->input('order') ? $request->input('order') : "asc";
            $getAll = $request->input('getAll', false);
            /* --------------------------------- filter --------------------------------- */
            $id_user_createur = helpers::handleValue($request->input('id_user_createur'));
            $reference = helpers::remove_spaces(helpers::handleValue($request->input('reference')));
            $id_devise = helpers::handleValue($request->input('id_devise'));
            $name_client = helpers::handleValue($request->input('name_client'));
            $description = helpers::handleValue($request->input('description'));
            $date_debut = $request->input('date_debut') ? Carbon::parse()->startOfDay($request->input('date_debut')) : null; // 2025-01-01 00:00:00
            $date_fin = $request->input('date_fin') ? Carbon::parse($request->input('date_fin'))->endOfDay() : null; 

            /* -------------------------------- afficher -------------------------------- */
            $liste_transactions = transactions::select(
                "transactions.*",
                DB::raw("CONCAT('TR_',transactions.id) as reference"),
                DB::raw("CONCAT(users.nom,' ',users.prenom) as nom_complet"),
                "devises.label as label_devise",
                "devises.abrv as abrv_devise"
            )
            ->join("users", 'users.id', "transactions.id_user_createur")
            ->join("devises", 'devises.id', "transactions.id_devise")
            ->when($id_user_createur, fn($query)=> $query->where('transactions.id_user_createur', $id_user_createur))
            ->when($date_debut, fn($query)=> $query->where('transactions.created_at', '>=', $date_debut))
            ->when($date_fin, fn($query)=> $query->where('transactions.created_at', '<=', $date_fin))
            ->when($id_devise, fn($query)=> $query->where('transactions.id_devise', $id_devise))
            ->when($reference, fn($query)=> $query->whereRaw("CONCAT('TR_',transactions.id) like ?", ["%$reference%"]))
            ->when($name_client, fn($query)=> $query->whereRaw('transactions.name_client like ?', ["%$name_client%"]))
            ->when($description, fn($query)=> $query->whereRaw('transactions.description like ?', ["%$description%"]))
            ->orderBy($colone, $order);
            $totalRecords = $liste_transactions->count();

            $liste_transactions = ($getAll ? $liste_transactions : $liste_transactions->skip($skip)->take($take))
            ->get()
            ->map(function($item) {
                $item->_id = Crypt::encryptString($item->id);
                $item->created_at_formated = date("d-m-Y H:i:s",strtotime($item->created_at));
                $item->updated_at_formated = $item->updated_at ? date("d-m-Y H:i:s",strtotime($item->updated_at)) : $item->updated_at;
                $item->prix_formated = number_format($item->prix,2,",",".");
                $item->taux_formated = number_format($item->taux,2,",",".");
                $item->is_entree = boolval($item->is_entree);
                unset($item->id);
                return $item;
            });

            return $this->success([
                'data' => $liste_transactions,
                'totalRecords' => $totalRecords
            ]);
        } catch (Exception | QueryException $exp) {
            return $this->error(["erreur"=>$exp]);
        }
    }

    public function AjouterTransaction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'liste_transactions' => 'required',
                'liste_transactions.*.id_devise' => "required",
                'liste_transactions.*.prix' => "required",
                'liste_transactions.*.taux' => "required",
            ]);
            if($validator->fails()) return $this->api_message(data: [$validator->errors()]);

            /* -------------------------------- variables ------------------------------- */
            $liste_transactions = (array) $request->input("liste_transactions");
            $id_user_createur = Auth::user()->id;

            $liste_transactions = array_map(fn($item)=> [
                'id_user_createur' => $id_user_createur,
                'id_devise' => helpers::handleValue($item['id_devise'], 'int'),
                'prix' => helpers::handleValue($item['prix'], 'double'),
                'taux' => helpers::handleValue($item['taux'], 'double'),
                'is_entree' => helpers::handleValue($item['is_entree'], 'bool'),
                'name_client' => isset($item['name_client'])? trim($item['name_client']) : null,
                'description' => isset($item['description'])? trim($item['description']) : null,
                'created_at' => now()
            ] ,$liste_transactions);

            $add_query = transactions::insert($liste_transactions);
            if (!$add_query) return $this->api_message("non_ajoutee");

            /* ------------------------------- action logs ------------------------------ */
            $description_log = "Création d'une nouvelle transaction";
            helpers::Log_action('devises', $id_user_createur, "Création d'une nouvelle transaction", $description_log, $request->all());
           
            return $this->success();
        } catch (Exception | QueryException $exp) {
            return $this->error(["erreur"=>$exp]);
        }
    }

    public function ModifierTransaction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                '_id' => "required",
                'id_devise' => "required",
                'prix' => "required",
                'taux' => "required",
                'is_entree' => "required"
            ]);
            if($validator->fails()) return $this->api_message(data: [$validator->errors()]);

            /* --------------------------------- values --------------------------------- */
            $_id = Crypt::decryptString($request->input('_id')); 
            $id_devise = helpers::handleValue($request->input('id_devise'));
            $prix = helpers::handleValue($request->input('prix'));
            $taux = helpers::handleValue($request->input('taux'));
            $is_entree = helpers::handleValue($request->input('is_entree'), 'bool');
            $name_client = helpers::handleValue($request->input("name_client"));
            $description = helpers::handleValue($request->input("description"));

            /* ------------------------------- add values ------------------------------- */
            $transactions = transactions::find($_id);
            $old_value = clone $transactions;
            $transactions = $transactions->update([
                "id_devise" => $id_devise,
                "prix" => $prix,
                "taux" => $taux,
                "is_entree" => $is_entree,
                "name_client" => $name_client,
                "description" => $description
            ]);
            if (!$transactions) return $this->api_message('non_modifiee');

            /* ------------------------------- action logs ------------------------------ */
            $description_log = "Modification du transaction, ID : ".$_id;
            helpers::Log_action('devises', Auth::user()->id,"Modification d'une transaction",$description_log,[
                'new_value' => $request->all(),
                'old_value' => $old_value
            ]);

            return $this->success();
        } catch (Exception | QueryException $exp) {
            return $this->error(["erreur"=>$exp]);
        }
    }

    public function SuppressionTransaction(Request $request) {
        try {
            $validator = Validator::make($request->all(), ['_id' => "required"]);
            if($validator->fails()) return $this->api_message(data: [$validator->errors()]);

            /* -------------------------------- variables ------------------------------- */
            $_id = Crypt::decryptString($request->input('_id')); 

            /* ------------------------------- add values ------------------------------- */
            $transactions = transactions::find($_id);
            $old_value = clone $transactions;
            if (!$transactions->delete()) return $this->api_message('non_supprimee');

            /* ------------------------------- action logs ------------------------------ */
            $description_log = "Suppression du transaction, ID : ".$_id;
            helpers::Log_action('devises', Auth::user()->id,"Suppression d'une transaction",$description_log,[
                'old_value' => $old_value
            ]);

            return $this->success();
        } catch (Exception | QueryException $exp) {
            return $this->error(["erreur"=>$exp]);
        }
    }
}
