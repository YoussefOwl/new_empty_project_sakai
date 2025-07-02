<?php
use App\Models\helpers;
use Illuminate\Support\Facades\Route;
Route::get('/', ['uses' => 'GuideController@Welcome']);
Route::group(['middleware' => 'jwt.verify'], function () use ($router) {
    /* -------------------------------------------------------------------------- */
    /*                          Api pour les utilisateurs                         */
    /* -------------------------------------------------------------------------- */
    // le nom de la route par example ici AfficherUtilisateur <=> URL/api/AfficherUtilisateur
    $router->post('AfficherUtilisateur', [
        'middleware' => 'jwt.role', // middleware spécifique de la route
        'roles' => helpers::getRoleGroup('group_roles_for_api_user'), // les rôles autorisé d'utiliser la route
        'uses' => 'Utilisateur\UserController@AfficherUtilisateur', // Nom_controlleur@methode_dans_le_controleur
    ]);
    $router->post('SupprimerUtilisateur', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_user'),
        'uses' => 'Utilisateur\UserController@SupprimerUtilisateur',
    ]);
    $router->post('AjouterUtilisateur', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_user'),
        'uses' => 'Utilisateur\UserController@AjouterUtilisateur',
    ]);
    $router->post('ModifierParmsUtilisateur', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_user'),
        'uses' => 'Utilisateur\UserController@ModifierParmsUtilisateur',
    ]);
    /* -------------------------------------------------------------------------- */
    /*                               SELF MANEGEMENT                              */
    /* -------------------------------------------------------------------------- */
    $router->post('ModifierUtilisateur', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'Utilisateur\UserController@ModifierUtilisateur',
    ]);
    $router->post('ChangePassword', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'Utilisateur\UserController@ChangePassword',
    ]);
    $router->post('GetMyInfo', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'Utilisateur\UserController@GetMyInfo',
    ]);
    $router->post('AfficherMonImage', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'Utilisateur\UserController@AfficherMonImage',
    ]);
    $router->post('ModifierMonImage', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'Utilisateur\UserController@ModifierMonImage',
    ]);
    $router->post('SupprimerMonImage', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'Utilisateur\UserController@SupprimerMonImage',
    ]);
    /* -------------------------------------------------------------------------- */
    /*                            Les menus du sidebar                            */
    /* -------------------------------------------------------------------------- */
    $router->post('AjouterSidebarButtons', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'AppParameters\SidebarButtonsController@AjouterSidebarButtons',
    ]);
    $router->post('AfficherSidebarButtons', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'AppParameters\SidebarButtonsController@AfficherSidebarButtons',
    ]);
    $router->post('ModifierSidebarButtons', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'AppParameters\SidebarButtonsController@ModifierSidebarButtons',
    ]);
    $router->post('SupprimerSidebarButtons', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'AppParameters\SidebarButtonsController@SupprimerSidebarButtons',
    ]);
    /* -------------------------------------------------------------------------- */
    /*                                    ROLES                                   */
    /* -------------------------------------------------------------------------- */
    $router->post('AjoutGroupRole', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'AppParameters\GroupRoleSettingsController@AjoutGroupRole',
    ]);
    $router->post('ModifierGroupRole', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'AppParameters\GroupRoleSettingsController@ModifierGroupRole',
    ]);
    $router->post('AfficherGroupRole', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_manage_account'),
        'uses' => 'AppParameters\GroupRoleSettingsController@AfficherGroupRole',
    ]);
}); // fin Route group
