<?php
use App\Models\helpers;
use Illuminate\Support\Facades\Route;
/* --------------------------- Un groupe de route --------------------------- */
Route::group(['middleware' => 'jwt.verify'], function () use ($router) {
    $router->post('TelechargerDocument', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api'),
        'uses' => 'General\GeneralController@TelechargerDocument',
    ]);
    $router->post('LoadParamsForList', [
        'middleware' => 'jwt.role',
        'roles' => helpers::getRoleGroup('group_roles_for_api'),
        'uses' => 'General\GeneralController@LoadParamsForList',
    ]);
});
