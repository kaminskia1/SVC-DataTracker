<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class HTTP
{
    /**
     * Throw a HTTP error
     *
     * @param int $http_code
     * @param string $message
     */
    static public function error( int $http_code, string $message = "" ): void
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

        // Set response code
        http_response_code( $http_code );

        // Output message and kill execution
        die( $message );
    }

    /**
     * Check internet connection status
     *
     * @return bool
     */
    static public function internetConnection(): bool
    {
        // Generate a temporary socket to check connection
        return @fsockopen('www.google.com', 80, $num, $error, 2) != false ? true : false;
    }

}