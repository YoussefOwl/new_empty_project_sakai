<?php
use App\Models\helpers;
use Illuminate\Support\Facades\Route;
Route::get('/', ['uses' => 'GuideController@Welcome']);
Route::group(['middleware' => 'jwt.verify'], function () use ($router) {
    /* --------------------------------- devises -------------------------------- */
    $router->post('AfficherDeviseParameter', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\DeviseController@AfficherDeviseParameter',
    ]);
    $router->post('AjouterDeviseParameter', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\DeviseController@AjouterDeviseParameter',
    ]);
    $router->post('ModifierDeviseParameter', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\DeviseController@ModifierDeviseParameter',
    ]);
    $router->post('SuppressionDeviseParameter', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\DeviseController@SuppressionDeviseParameter',
    ]);
    
    /* ------------------------------- transaction ------------------------------ */
    $router->post('AfficherTransaction', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\TransactionController@AfficherTransaction',
    ]);
    $router->post('AjouterTransaction', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\TransactionController@AjouterTransaction',
    ]);
    $router->post('ModifierTransaction', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\TransactionController@ModifierTransaction',
    ]);
    $router->post('SuppressionTransaction', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_devises'),
        'uses' => 'Devises\TransactionController@SuppressionTransaction',
    ]);
});