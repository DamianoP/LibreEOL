<?php
/**
 * File: Engine.php
 * User: Masterplan
 * Date: 3/15/13
 * Time: 11:24 AM
 * Desc: Class witch load and show all requested modules
 */

class Engine {

    public $controller;
    public $action;

    /**
     * @name    Engine
     * @descr   Creates an istance of graphics Engine class
     */
/*    public function Engine(){}

    /**
     * @name    getController
     * @return  Controller      Requested controller is exists, NULL otherwise
     * @descr   Get the requested controller
     */
    public function getController(){

        global $config, $user, $log;

        // Set default controller
        $this->controller = 'Login';

        // Get requested controller (if it's set)
        if(isset($_GET['page'])){
            $req = explode('/', $_GET['page']);

            // Get requested controller from url
            $reqController = ucfirst(strtolower($req[0]));

            // Create an istance of requested controller (if exists)
            if($reqController != ''){
                if(file_exists($config['systemControllersDir'].$reqController.'Controller.php')){
                    $controllerName = $reqController.'Controller';
                    $this->controller = $reqController;
                }else{
                    Controller::error('ControllerNotFound');
                    $this->controller = 'Error';
                }
            }
        }elseif($user->role != '?'){
            $this->controller = $config['controller'][$user->role];
        }
    }

    /**
     * @name    getAction
     * @param   $controller     Controller      Istance of requested Controller
     * @descr   Get the requested action
     */
    public function getAction($controller){
        global $log;

        // Set default controller's action
        $this->action = $controller->defaultAction;

        // Get requested action (if it's set)
        if(isset($_GET['page'])){
            $req = explode('/', $_GET['page']);
            if(count($req) > 1){

                // Get requested action from url
                $reqAction = ucfirst(strtolower($req[1]));

                // Verify if requested action exists
                if($reqAction != ''){
                    if(method_exists($controller, 'action'.$reqAction)){
                        $this->action = $reqAction;
                    }else{
                        Controller::error('ActionNotFound');
                        $this->action = 'Error';
                        $action = null;
                    }
                }
            }
        }
    }

    /**
     * @name    renderDoctype
     * @descr   Add DOCTYPE on page
     */
    public function renderDoctype(){
        global $config;

        require_once($config['themeDir'].'doctype.php');
    }

    /**
     * @name    renderHeader
     * @descr   Add and Header on page
     */
    public function renderHeader(){
        global $config;

        require_once($config['themeDir'].'header.php');
    }

    /**
     * @name    render
     * @descr   If exists load file for requested module
     */
    public function renderPage(){

        global $config;
        require_once($config['systemViewsDir'].$this->controller.'/'.strtolower($this->action).'.php');

    }

    /**
     * @name    renderFooter
     * @descr   Add Footer on page
     */
    public function renderFooter(){
        global $config;

        require_once($config['themeDir'].'footer.php');
    }

    /**
     * @name    loadLibrary
     * @descr   If exists import Javascript scripts for requested module
     */
    public function loadLibs() {

        global $config;

        // Import Javascript script (if exists)
        if(file_exists($config['systemLibsDir'].$this->controller.'/'.$this->controller.'.js')){
            echo '<script type="text/javascript" src="'.$config['systemLibsDir'].$this->controller.'/'.$this->controller.'.js'.'"></script>';
        }
        if(file_exists($config['systemLibsDir'].$this->controller.'/'.$this->action.'.js')){
            echo '<script type="text/javascript" src="'.$config['systemLibsDir'].$this->controller.'/'.$this->action.'.js'.'"></script>';
        }

    }
}
