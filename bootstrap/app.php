<?php

session_cache_limiter(false);
session_start();

define('ROOT_PATH'  , __DIR__ . '/../');
define('VENDOR_PATH', __DIR__ . '/../vendor/');
define('APP_PATH'   , __DIR__ . '/../app/');
define('PUBLIC_PATH', __DIR__ . '/../public/');

require VENDOR_PATH .'autoload.php';

/** Load the configuration  */
$config = [
    'path.root'     => ROOT_PATH,
    'path.public'   => PUBLIC_PATH,
    'path.app'      => APP_PATH
];

$config['slim'] = [
    'settings' => [
        'responseChunkSize'         => 4096,
        // Debugging
        'displayErrorDetails'       => true,  //false in production
        // HTTP
        'httpVersion'               => '1.1',
        'addContentLengthHeader'    => true,
        'routerCacheFile'           => false, //ROOT_PATH . 'storage/cache/router.php', 
        "determineRouteBeforeAppMiddleware" => true,
        //View
        'viewTemplatesDirectory'    => APP_PATH . 'Views',
    ]
];

$config['db']['host']   = 'localhost';
$config['db']['user']   = 'user';
$config['db']['pass']   = 'password';
$config['db']['dbname'] = 'exampleapp';

$config['twig'] = [
    'debug'         => true,
    'optimizations' => -1,
    //'cache'         => ROOT_PATH . 'storage/cache'
];

/** Initialize Slim application  */
$app = new \Slim\App($config['slim']);
$container = $app->getContainer();


/** Register component on container */
$container['csrf'] = function($c){
    $csrf = new \Slim\Csrf\Guard;
    $csrf->setPersistentTokenMode(true);
    return $csrf;
};

$container['view'] = function ($c) use ($config) {
    $view = new \Slim\Views\Twig([$c['settings']['viewTemplatesDirectory']],$config['twig']);
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($c->get('router'), $uri));
    $view->addExtension(new \App\Extensions\CrsfExtension($c->get('csrf')));
    return $view;
};

$container['db'] = function($c){
    $db = new \App\Database\DB();
    return $db;
};


/** Register Middleware Globals */
// give back errors
$app->add(new \App\Middelware\ValidationErrorsMiddelware($container));

// give back the old input
//$app->add(new \App\Middelware\OldInputMiddelware($container));

// give back a csrf generated key
$app->add(new \App\Middelware\CsrfViewMiddelware($container));

// run the crsf check global
//$app->add($container->csrf);


/** Start the route */
require APP_PATH . 'routes.php';
