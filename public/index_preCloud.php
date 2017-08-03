<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/15/13
 * Time: 11:24 AM
 * Desc: Main index.php which perform all http request
 */

ob_start();
error_reporting(E_ERROR | E_PARSE);
/*error_reporting(E_ALL);*/
ini_set('display_errors', 1);


// Include ALL essential PHP files, classes, functions, ...
require_once('../includes/essential.php');

global $config;

// Set timezone for system's functions and logs
date_default_timezone_set($config['systemTimeZone']);

// Create new log element
$log = new Log($config);

// Initialize session's vars
session_start();

// Unserialise user istance (if exists)
$user = new User();

if(isset($_SESSION['user']))
    $user = unserialize($_SESSION['user']);
//print_r($user);

// Include PHP lang file
require_once $config['systemLangsDir'].$user->lang.'/lang.php';

// Create Engine object
$engine = new Engine();

// Get requested Controller (if exists)
$engine->getController();

if($engine->controller != 'Error'){

    // Create an istance of requested controller
    $controllerName = $engine->controller.'Controller';
    require_once($config['systemControllersDir'].$controllerName.'.php');
    $controller = new $controllerName();

    // Get requested Action (if exists)
    $engine->getAction($controller);

    // Execute action
    if($engine->action != 'Error')
        $controller->executeAction($engine->action);
}

ob_end_flush();
// Prevent including/requesting of this file
die();
