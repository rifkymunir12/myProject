<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Contracts\Response;
use Exception;
use Illuminate\Http\Request;

class LogoutController extends Controller
{

    public function logout(Request $request)
    {
        try {    
            $request->user()->token()->revoke();
            // foreach ($request->user()->tokens as $token){
                //$token->revoke();
            // }
            return Response::json([
                'message' => 'Berhasl logout!',
            ]);


        } catch (Exception $e) {
       
            return response()->json([
                'error' => [
                    'msg' => $e->getMessage(),
                    'code' => $e->getCode(),
                ],
            ], 500);
        }
    }
}