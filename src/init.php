<?php

namespace SVC;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

use SVC\System\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class Init
{

    /**
     * Twig environment loader. Declared statically to allow for calls from anywhere
     *
     * @global \SVC\Init::$loader
     * @var \Twig\Loader\LoaderInterface
     */
    public static $loader;

    /**
     * Twig environment. Declared statically to allow for calls from anywhere
     *
     * @global \SVC\Init::$twig
     * @var \Twig\Environment
     */
    public static $twig;

    /**
     * Create a temporary instance from a static deceleration and run required prerequisites
     *
     * @todo Migrate template css <link>s from custom templates to body.twig
     *
     * @return self
     */
    public static function i(): self
    {
        // Declare autoloader
        spl_autoload_register( function( $class ) {
            require( __DIR__ . "\\" . mb_substr($class, 4) . ".php");
        });

        // Declare database
        \SVC\System\PDO::assign( "sqlite:" . \SVC\Config::$assetDirectory . \SVC\Config::$database );
    
        // Declare Twig loader and environment
        static::$loader = new FilesystemLoader(\SVC\Config::$assetDirectory . "template" );
        static::$twig = new Environment( static::$loader, [
            'debug'            => \SVC\Config::$debug,
            'strict_variables' => true,
        ] );

        return new self();
    }


    /**
     * Frontend call manager
     *
     * @return void
     * @throws \ReflectionException
     */
    public function frontend(): void
    {
        // Set view
        $view = \SVC\System\Request::i()->view ?? "dashboard";

        // Check that provided view exists
        if ( method_exists( new \SVC\System\Template(), $view ) )
        {
            // Check that provided view is public
            $ref = new \ReflectionMethod( new \SVC\System\Template(), $view );
            if ( $ref->isPublic() )
            {
                // Clear output buffer before displaying if not in debug
                ! \SVC\Config::$debug ? ob_clean() : null;

                // Set view to custom provided
                $view = \SVC\System\Template::$view();
            }
        }

        // Set review to provided if successful, fallback to dashboard if not
        $view = is_array( $view ) && $view[0] ? $view[1] : \SVC\System\Template::dashboard()[1];

        // Output frontend template with included view
        die( static::$twig->load("body.twig")->render([
            'view'=> $view,
            'navbar' => [
                [
                    'callback'=>'_title',
                    'title'=>"Main",
                ],
                [
                    'icon'=>'fa fa-home',
                    'callback'=>'dashboard',
                    'public_name'=>"Dashboard",
                ],

                [
                    'callback'=>'_title',
                    'title'=>"People",
                ],
                [
                    'icon'=>'fa fa-users',
                    'callback'=>'personList',
                    'public_name'=>'View People',
                ],
                [
                    'icon'=>'fa fa-plus',
                    'callback'=>'personAdd',
                    'public_name'=>"Add Person",
                ],

                [
                    'callback'=>'_title',
                    'title'=>"Aid",
                ],
                [
                    'icon'=>'fa fa-money',
                    'callback'=>'aidView',
                    'public_name'=>'View Aid',
                ],
                [
                    'icon'=>'fa fa-plus',
                    'callback'=>'aidAdd',
                    'public_name'=>"Add Aid",
                ],

                [
                    'callback'=>'_title',
                    'title'=>"Report",
                ],
                [
                    'icon'=>'fa fa-file',
                    'callback'=>'reportList',
                    'public_name'=>'View Reports',
                ],
                [
                    'icon'=>'fa fa-plus',
                    'callback'=>'reportAdd',
                    'public_name'=>"Generate Report",
                ],

            ]
        ]));
    }




    /**
     * Backend call manager
     *
     * @throws \ReflectionException
     * @return void
     */
    public function backend(): void
    {
        // Check that incoming request is through Ajax
        if ( \SVC\System\Request::i()->isAjax() )
        {
            // Check the request type against available options
            switch( \SVC\System\Request::i()->do )
            {
                case 'connection':
                    die( (bool)\SVC\System\HTTP::internetConnection() );
                case 'template':
                    // Check that callback is provided
                    if ( !is_null( $callback = \SVC\System\Request::i()->callback ) )
                    {
                        // Check that provided callback exists
                        if ( method_exists( new \SVC\System\Template(), $callback ) )
                        {
                            // Check that provided callback is public
                            $ref = new \ReflectionMethod( new \SVC\System\Template(), $callback );
                            if ( $ref->isPublic() )
                            {
                                // Clear output buffer before displaying if not in debug
                                ! \SVC\Config::$debug ? ob_clean() : null;

                                // Run callback function
                                $callback = \SVC\System\Template::$callback();
                                if ( $callback[0] )
                                {
                                    // Run template callback and output response
                                    die( $callback[1] );
                                }
                            }
                        }
                        // Invalid callback provided, return 405: Method Not Allowed
                        \SVC\System\HTTP::error(405, "Method Not Allowed");
                    }
                    // No callback provided, return 400: Bad Request
                    \SVC\System\HTTP::error(400, "Bad Request");
                    break;

                case 'push':
                    // Check that callback is provided
                    if ( !is_null( $callback = \SVC\System\Request::i()->callback ) && !is_null( $data = \SVC\System\Request::i()->data ) )
                    {
                        // Check that provided callback exists
                        if ( method_exists( new \SVC\System\Template(), $callback ) )
                        {
                            // Check that provided callback is public
                            $ref = new \ReflectionMethod(new \SVC\System\Push(), $callback );
                            if ($ref->isPublic())
                            {
                                // Clear output buffer before displaying if not in debug
                                ! \SVC\Config::$debug ? ob_clean() : null;

                                // Run push callback and output response
                                die( \SVC\System\Push::i()->$callback( $data ) );
                            }
                            // Invalid callback provided, return 405: Method Not Allowed
                            \SVC\System\HTTP::error(405, "Method Not Allowed");
                        }
                    }
                    // No callback provided, return 400: Bad Request
                    \SVC\System\HTTP::error(400, "Bad Request");
                    break;

                default:
                    // No request type provided, return 400: Bad Request
                    \SVC\System\HTTP::error(400, "Bad Request");
                    break;
            }
        }
        else
        {
            // Nothing provided, return 400: Bad Request
            \SVC\System\HTTP::error(400, "Bad Request");
        }
    }
}