<?php

namespace App\Http\Controllers\AppParameters;

use App\Http\Controllers\Controller;
use App\Traits\Globlal\ErrorHandling;
use App\Models\AppParameters\sidebar_buttons;
use Illuminate\Support\Facades\{Auth, Crypt, Validator};
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use PHPUnit\Util\Exception;
use App\Models\helpers;

class SidebarButtonsController extends Controller
{
    use ErrorHandling;
    public function AjouterSidebarButtons(Request $request)
    {
        try 
        {
            $messages = [
                'routerLink' => 'required|unique:sidebar_buttons,routerLink',
                'title' => 'required',
                'icon' => 'required'
            ];
            $validator = Validator::make($request->all(),$messages,$messages);
            if ($validator->fails()) return $this->api_message();

            /* -------------------------------- variables ------------------------------- */
            $routerLink = helpers::remove_spaces($request->input('routerLink'));
            $title = trim($request->input('title'));
            $icon = trim($request->input('icon'));

            /* ---------------------------------- ajout --------------------------------- */
            $sidebar_buttons = sidebar_buttons::firstOrCreate([
                'routerLink' => $routerLink,
            ],
            [
                'title' => $title,
                'icon' => $icon
            ]);

            if(!$sidebar_buttons->wasRecentlyCreated) return $this->api_message('link_exists');

            /* ------------------------------- LOG ACTION ------------------------------- */
            $description_log = "Ajout d'un nouveau menu sidebar, identifiant : $sidebar_buttons->id";
            helpers::Log_action('sidebar_buttons',Auth::user()->id, "Ajout d'un nouveau menu sidebar",$description_log,$request->all());

            /* ------------------------------ api response ------------------------------ */
            return $this->success();
        } 
        catch (Exception | QueryException $exp) {
            return $this->error(["erreur" => $exp]);
        }
    }

    public function AfficherSidebarButtons(Request $request)
    {
        try 
        {
            $skip = $request->input('skip') ? intval($request->input('skip')) : 0;
            $take = $request->input('take') ? intval($request->input('take')) : 10;
            $colone = $request->input('colone') ? $request->input('colone') : "title";
            $order = $request->input('order') ? $request->input('order') : "asc";
            $get_all = $request->input('get_all', false);

            /* --------------------------------- filter --------------------------------- */
            $routerLink = helpers::handleValue($request->input('routerLink'));
            $title = helpers::handleValue($request->input('title'));

            /* -------------------------------- affichage ------------------------------- */
            $liste_of_data = sidebar_buttons::when($routerLink, fn($query)=> $query->where("routerLink", 'like',"%$routerLink%"))
            ->when($title, fn($query)=> $query->where("title",'like', "%$title%"))
            ->orderBy($colone, $order);

            $totalRecords = $liste_of_data->count();
            
            /* ------------------------------ finalisations ----------------------------- */
            $liste_of_data = ($get_all ? $liste_of_data : $liste_of_data->skip($skip)->take($take))
            ->get()
            ->map(function($item) {
                $item->_id = Crypt::encryptString($item->id);
                /* ---------------------------------- dates --------------------------------- */
                $item->created_at_formated = $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : null;
                $item->updated_at_formated = $item->updated_at ? date('d/m/Y H:i', strtotime($item->updated_at)) : null;
                return $item;
            });
            
            /* ------------------------------ api response ------------------------------ */
            return $this->success([
                'data' => $liste_of_data,
                'totalRecords' => $totalRecords
            ]);

        } catch (Exception | QueryException $exp) {
            return $this->error(["erreur" => $exp]);
        }
    }

    public function ModifierSidebarButtons(Request $request)
    {
        try 
        {
            $messages = [
                '_id' => 'required',
                'routerLink' => 'required',
                'title' => 'required',
                'icon' => 'required'
            ];
    
            $validator = Validator::make($request->all(),$messages,$messages);
    
            if ($validator->fails()) return $this->api_message();

            /* -------------------------------- variables ------------------------------- */
            $_id = intval(Crypt::decryptString($request->input('_id')));
            $routerLink = helpers::remove_spaces($request->input('routerLink'));
            $title = trim($request->input('title'));
            $icon = trim($request->input('icon'));

            /* ---------------- check if routerLink already exists --------------- */
            $sidebar_buttons = sidebar_buttons::where([['id','!=', $_id], ['routerLink', $routerLink]])->exists();
            if($sidebar_buttons) return $this->api_message('duplicate_tentative');

            /* ---------------------------------- find ---------------------------------- */
            $sidebar_buttons = sidebar_buttons::find($_id);
            $old_value = clone $sidebar_buttons;

            /* --------------------------------- modifer -------------------------------- */
            $sidebar_buttons->routerLink = $routerLink;
            $sidebar_buttons->title = $title;
            $sidebar_buttons->icon = $icon;
            
            if(!$sidebar_buttons->save()) return $this->api_message('non_modifier');

            $description_log = "Modification d'un menu sidebar, identifiant sur la base : $_id";
            /* ------------------------------- LOG ACTION ------------------------------- */
            helpers::Log_action('sidebar_buttons',Auth::user()->id, "Modification d'un menu sidebar",$description_log,[
                'request_all' => $request->all(),
                'old_value' => $old_value
            ]);

            return $this->success();

        } 
        catch (Exception | QueryException $exp) {
            return $this->error(["erreur" => $exp]);
        }
    }

    public function SupprimerSidebarButtons(Request $request)
    {
        try 
        {
            $messages = ['_id' => 'required'];
            $validator = Validator::make($request->all(),$messages,$messages);
            if ($validator->fails()) return $this->api_message();

            $_id = intval(Crypt::decryptString($request->input('_id')));

            /* ---------------------------------- find ---------------------------------- */
            $sidebar_buttons = sidebar_buttons::find($_id);
            $old_value = clone $sidebar_buttons;

            /* --------------------------------- delete --------------------------------- */
            if(!$sidebar_buttons->delete()) return $this->api_message("non_supprimee");
            
            $description_log = "Suppression d'un menu sidebar, identifiant sur la base : $_id";
            helpers::Log_action('sidebar_buttons',Auth::user()->id, "Suppression d'un menu sidebar",$description_log,[
                'old_value' => $old_value
            ]);

            return $this->success();
        } 
        catch (Exception | QueryException $exp) {
            return $this->error(["erreur" => $exp]);
        }
    }
}
