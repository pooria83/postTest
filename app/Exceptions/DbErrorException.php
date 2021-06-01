<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DbErrorException extends Exception
{
    public $error;
    public function __construct($err)
    {
        $this->error = $err;
    }
    public function render($request)
    {
        return  new JsonResponse([
            'status' => false,
            'result' => $this->error
        ], Response::HTTP_INTERNAL_SERVER_ERROR );
    }
}
