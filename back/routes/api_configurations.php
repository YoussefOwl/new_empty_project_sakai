<?php
use App\Models\helpers;
use Illuminate\Support\Facades\Route;
Route::get('/', ['uses' => 'GuideController@Welcome']);
Route::group(['middleware' => 'jwt.verify'], function () use ($router) {
    /* -------------------------------------------------------------------------- */
    /*                             Api pour les rôles                             */
    /* -------------------------------------------------------------------------- */
    $router->post('AfficherRole', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_roles_view'),
        'uses' => 'Roles\RolesController@AfficherRole',
    ]);
    $router->post('AjouterRole', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_roles_manage'),
        'uses' => 'Roles\RolesController@AjouterRole',
    ]);
    $router->post('ModifierRole', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_roles_manage'),
        'uses' => 'Roles\RolesController@ModifierRole',
    ]);
    /* -------------------------------------------------------------------------- */
    /*                              Les apis des logs                             */
    /* -------------------------------------------------------------------------- */
    $router->post('AfficherLogs', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api_logs'),
        'uses' => 'General\GeneralController@AfficherLogs',
    ]);
}); // fin Route group
