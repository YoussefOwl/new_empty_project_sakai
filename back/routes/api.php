<?php
use Illuminate\Support\Facades\Route;
/* --------------------------- Api sans middleware -------------------------- */
Route::get('/', ['uses' => 'GuideController@Welcome']);
Route::post('LoginUtilisateur', ['uses' => 'Utilisateur\UserController@LoginUtilisateur']);