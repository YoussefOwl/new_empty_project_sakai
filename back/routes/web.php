<?php
use Illuminate\Support\Facades\Route;
Route::get('/', ['uses' => 'GuideController@Welcome']);