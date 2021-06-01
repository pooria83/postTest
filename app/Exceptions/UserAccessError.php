<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class UserAccessError extends Exception
{
    public function render($request)
    {

        return  new JsonResponse([
            'status' => false,
            'result' => "User access error"
        ], Response::HTTP_UNAUTHORIZED );
    }
}
