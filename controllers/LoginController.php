<?php
/**
 * File: LoginController.php
 * User: Masterplan
 * Date: 3/19/13
 * Time: 10:45 AM
 * Desc: Controller for all login operations
 */

class LoginController extends Controller{

    /**
     *  @name   LoginController
     *  @descr  Create an instance of LoginController class
     */
    public function LoginController(){}

    /**
     * @name    executeAction
     * @param   $action     String      Name of requested action
     * @descr   Execute action (if exists and if user is allowed)
     */
    public function executeAction($action){
        global $user, $log;

        if ($this->getAccess($user, $action, $this->accessRules())) {
            $action = 'action'.$action;
            $this->$action();
        }else{
            Controller::error('AccessDenied');
        }
    }

    /**
     *  @name   actionIndex
     *  @descr  Show login index page
     */
    private function actionIndex(){
        global $engine;
        global $user;

        if($user->role == '?'){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            header('Location: index.php');
        }
    }

    /**
     *  @name   actionLogin
     *  @descr  Perform a login action
     */
    private function actionLogin(){
        global $config, $log;

        if((isset($_POST['email'])) && (isset($_POST['password']))){
            $db = new sqlDB();
            $result = $db->qLogin($_POST['email'], sha1($_POST['password']));
            if($result != null){
                $_SESSION['logged'] = true;
                $_SESSION['user'] = serialize(new User($result));
                echo "ACK";
            }else
                echo "NACK";
        }else{
            header('Location: index.php?page=login/index');
        }
    }

    /**
     *  @name   actionLogout
     *  @descr  Perform a logout action
     */
    private function actionLogout(){
        global $user;
        global $config;

        // Destroy 'logged' session
        unset($_SESSION['logged']);

        // Destroy User, idSet, idSubject, uploadDir
        $user = new User();
        unset($_SESSION['user']);
        if(isset($_SESSION['idSet']))
            unset($_SESSION['idSet']);
        if(isset($_SESSION['idSubject']))
            unset($_SESSION['idSubject']);
        if(isset($_SESSION['uploadDir']))
            unset($_SESSION['uploadDir']);

        // Bring user to login idnex page
        header('Location: index.php?page=login');
    }

    /**
     *  @name   accessRules
     *  @descr  Returns all access rules for Login controller's actions:
     *  array(
     *     array(
     *       (allow | deny),                                     Parameter
     *       'actions' => array('*' | 'act1', ['act2', ....]),   Actions
     *       'roles'   => array('*' | '?' | 'a' | 't' | 's')     User's Role
     *     ),
     *  );
     */
    private function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }

}