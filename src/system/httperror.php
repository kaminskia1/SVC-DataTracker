<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class HTTPError
{
    static public function i( int $http_code, string $message = "" ): void
    {
        // Search for the provided HTTP Error code
        switch ( $http_code ) {
            case 401:
                header("HTTP/1.1 401 Unauthorized");
                http_response_code(401);
                exit;
            case 403:
                header("HTTP/1.1 403 Forbidden");
                http_response_code(404);
                exit;
            case 404:
                header("HTTP/1.1 401 Not Found");
                http_response_code(404);
                exit;
            default:
                http_response_code($http_code);
                echo $message;
                exit;
        }

    }
}