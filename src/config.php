<?php

namespace SVC;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Config
{
    /**
     * Debugging state
     *
     * @var bool
     */
    public static $debug = true;

    /**
     * Hypixel API key to be used
     *
     * @var string
     */
    public static $HypixelAPIKey = "7ced4734-1dd0-4a9c-b416-f86a1e387841";

    /**
     * Location of the asset directory
     *
     * @var string
     */
    public static $assetDirectory = "assets\\";

    /**
     * Location to store session files
     *
     * @var string
     */
    public static $sessionDirectory = "session\\";

    /**
     * Link to Mojang's profile location (Used for resolving player UUID's)
     *
     * @var string
     */
    public static $mojangProfileUrl = "https://api.mojang.com/users/profiles/minecraft/";

    /**
     * Link to Hypixel's API
     *
     * @var string
     */
    public static $hypixelAPIUrl = "https://api.hypixel.net/";

}

