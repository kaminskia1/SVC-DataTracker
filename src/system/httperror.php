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
                break;
            case 403:
                header("HTTP/1.1 403 Forbidden");
                break;
            case 404:
                header("HTTP/1.1 401 Not Found");
                break;
            default:
                break;
        }
        http_response_code($http_code);
        echo $message;
        exit;

    }
}