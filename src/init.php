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
     * Current user session.
     *
     * @global \SVC\Init::$session
     * @var Session
     */
    public static $session;

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

        // Declare Twig loader and environment
        static::$loader = new FilesystemLoader(\SVC\Config::$assetDirectory . "template" );
        static::$twig = new Environment( static::$loader, [
            'debug'            => \SVC\Config::$debug,
            'strict_variables' => true,
        ] );

        // Check if session provided
        if ( !is_null( \SVC\System\Request::i()->session ) && ( \mb_strlen( \SVC\System\Request::i()->session ) == 32 ) && \SVC\System\Session::verify( \SVC\System\Request::i()->session ) )
        {
            // Grab preexisting session
            static::$session = \SVC\System\Session::grabSession( \SVC\System\Request::i()->session );

            // Refresh session cookie
            \SVC\System\Request::setCookie( 'session', static::$session->token );
            \SVC\System\Request::i()->setCookie( 'username', "", time() - 3600 );
            \SVC\System\Request::i()->setCookie( 'profile', "", time() - 3600 );
        }
        else if ( !is_null( \SVC\System\Request::i()->username ) && !is_null( \SVC\System\Request::i()->username ) && \SVC\System\API::i()->verifyProfileAndUsername( \SVC\System\Request::i()->username, \SVC\System\Request::i()->profile ) )
        {
            // Create new session
            static::$session = \SVC\System\Session::createSession( \SVC\System\API::i()->usernameToPrettyUsername( \SVC\System\Request::i()->username ), \SVC\System\Request::i()->profile );

            // Bind session id to cookie
            \SVC\System\Request::setCookie( 'session', static::$session->token );
            \SVC\System\Request::i()->setCookie( 'username', "", time() - 3600 );
            \SVC\System\Request::i()->setCookie( 'profile', "", time() - 3600 );
        }
        return new self();
    }


    /**
     * Frontend call manager
     *
     * @TODO Segregate child calls to separate classes, frontend to Init->Frontend and backend into Init->Backend
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @return void
     */
    public function frontend(): void
    {        // Clear output buffer before displaying if not in debug
        ! \SVC\Config::$debug ? ob_clean() : null;
        echo static::$twig->load("body.twig")->render([
            'view'=>'dashboard.twig',
            'navbar' => [

                [
                    'callback'=>'_title',
                    'title'=>"Main",
                ],
                [
                    'icon'=>'fa fa-lock',
                    'callback'=>'dashboard',
                    'public_name'=>"Dashboard",
                ],

                [
                    'callback'=>'_title',
                    'title'=>"People",
                ],
                [
                    'icon'=>'fa fa-cogs',
                    'callback'=>'personList',
                    'public_name'=>'View People',
                ],
                [
                    'icon'=>'fa fa-sign-out',
                    'callback'=>'personAdd',
                    'public_name'=>"Add Person",
                ],

                [
                    'callback'=>'_title',
                    'title'=>"Aid",
                ],
                [
                    'icon'=>'fa fa-cogs',
                    'callback'=>'aidView',
                    'public_name'=>'View Aid',
                ],
                [
                    'icon'=>'fa fa-sign-out',
                    'callback'=>'aidAdd',
                    'public_name'=>"Add Aid",
                ],

                [
                    'callback'=>'_title',
                    'title'=>"Report",
                ],
                [
                    'icon'=>'fa fa-cogs',
                    'callback'=>'reportList',
                    'public_name'=>'View Reports',
                ],
                [
                    'icon'=>'fa fa-sign-out',
                    'callback'=>'reportGenerate',
                    'public_name'=>"Generate Report",
                ],

            ]
        ]);
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

                                if ( \SVC\System\Template::$callback() )
                                {
                                    exit;
                                }
                            }
                        }
                    }
                    \SVC\System\HTTPError::i(405, "Method not allowed");
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