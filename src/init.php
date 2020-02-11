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
     * @var LoaderInterface
     */
    public static $loader;

    /**
     * Twig environment. Declared statically to allow for calls from anywhere
     *
     * @global \SVC\Init::$twig
     * @var Environment
     */
    public static $twig;

    /**
     * Create a temporary instance from a static deceleration and run required prerequisites
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
     * @TODO Segregate child calls to separate classes, frontend to Init->Frontend and backend into Init->Backend
     *
     * @return void
     * @throws \ReflectionException
     */
    public function frontend(): void
    {
        // Gather and validate view
        $view = \SVC\System\Request::i()->view ?? "dashboard";

        if ( method_exists( new \SVC\System\Template(), $view ) )
        {
            $ref = new \ReflectionMethod( new \SVC\System\Template(), $view );
            if ( $ref->isPublic() )
            {
                // Clear output buffer before displaying if not in debug
                ! \SVC\Config::$debug ? ob_clean() : null;

                $view = \SVC\System\Template::$view();
            }
        }
        $view = is_array( $view ) && $view[0] ? $view[1] : \SVC\System\Template::dashboard()[1];

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
                    'callback'=>'reportGenerate',
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
        if ( \SVC\System\Request::i()->isAjax() )
        {
            switch( \SVC\System\Request::i()->do )
            {
                case 'template':
                    if ( !is_null( $callback = \SVC\System\Request::i()->callback ) )
                    {
                        if ( method_exists( new \SVC\System\Template(), $callback ) )
                        {
                            $ref = new \ReflectionMethod( new \SVC\System\Template(), $callback );
                            if ( $ref->isPublic() )
                            {
                                // Clear output buffer before displaying if not in debug
                                ! \SVC\Config::$debug ? ob_clean() : null;
                                $callback = \SVC\System\Template::$callback();
                                if ( $callback[0] )
                                {
                                    die( $callback[1] );
                                }
                            }
                        }
                    }
                    \SVC\System\HTTPError::i(405, "Method not allowed");
                    break;

                case 'push':
                    if ( !is_null( $callback = \SVC\System\Request::i()->callback ) && !is_null( $data = \SVC\System\Request::i()->data ) )
                    {
                        if ( method_exists( new \SVC\System\Template(), $callback ) )
                        {
                            $ref = new \ReflectionMethod(new \SVC\System\Push(), $callback );
                            if ($ref->isPublic())
                            {
                                die( \SVC\System\Push::i()->$callback( $data ) );
                            }
                            \SVC\System\HTTPError::i(405, "Method not allowed");
                        }
                    }
                    \SVC\System\HTTPError::i(400, "Invalid parameters");
                    break;

                default:
                    \SVC\System\HTTPError::i(400, "Invalid parameters");
                    break;
            }
        }
        else
        {
            \SVC\System\HTTPError::i(400, "Bad Request");
        }
    }
}