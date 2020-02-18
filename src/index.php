<?php
// Mark "ENABLE" as true to allow for proper class execution
define( "ENABLE", true);

// Load composer packages
require("../vendor/autoload.php");

// Load initializer
require("init.php");

// Check request type (GET == frontend, POST == ajax/backend for this project)
switch ( $_SERVER['REQUEST_METHOD'] )
{
    // Frontend request
    case 'GET':
        \SVC\Init::i()->frontend();
        break;

    // Backend request
    case 'POST':
        \SVC\Init::i()->backend();
        break;

    // Unknown request
    default:
        \SVC\System\HTTP::error(405, "Invalid request method!");
        break;
}