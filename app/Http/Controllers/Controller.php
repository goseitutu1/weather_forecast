<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function failedResponse($message){
        return response()->json([
            'code' => 400,
            'msg' => $message
        ]);
    }

    protected function successResponse($message){
        return response()->json([
            'code' => 200,
            'msg' => $message
        ]);
    }
}
