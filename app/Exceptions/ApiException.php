<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiException extends HttpResponseException
{
    public function __construct($message, $code, $errors = [])
    {
        $res = [
            'error' => [
                'code'    => $code,
                'message' => $message,
            ]
        ];
        if($errors) $res['error']['errors'] = $errors;

        parent::__construct(response($res, $code));
    }
}
