<?php

namespace App\Http\Controllers;

class GuideController extends Controller
{
	public function Welcome()
	{
        return response()->json(
        [
            'api_message' =>'Access Denied'
        ], 403)->header('Content-Type', 'application/json');
    }
}