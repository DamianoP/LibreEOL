<?php
/**
 * File: Controller.php
 * User: Masterplan
 * Date: 3/19/13
 * Time: 1:07 PM
 * Desc: Your description HERE
 */
class Controller {

    // Default action of all Controllers
    public $defaultAction = 'Index';

    /**
     * @name    getAccess
     * @param   $user       User        User istance
     * @param   $action     String      Name of requested action
     * @param   $rules      Array       Array of Controller access rules
     * @return  boolean     If user have access (true) of not (false)
     * @descr   Verify if user have access to action
     */
    protected function getAccess($user, $action, $rules){
        
        $access = false;
        $role = str_split($user->role);
        $index = 0;
        while(($index < count($rules)) && (!$access)){
            $rule = $rules[$index];
            if((in_array($action, $rule['actions'])) || (in_array('*', $rule['actions']))){
                $index2 = 0;
                while(($index2 < count($role)) && (!$access)){
                    if((in_array($role[$index2], $rule['roles'])) || (in_array('*', $rule['roles']))){
                        if($rule[0] == 'allow'){
                            $access = true;
                        }
                    }
                    $index2++;
                }
            }
            $index++;
        }
        return $access;
    }

    /**
     *  @name   actionGetalllanguages
     *  @descr  Returns all system's languages
     */
    protected function actionGetalllanguages(){
        $db2 = new sqlDB();
        if($langs = $db2->qGetAllLanguages()){
            // echo json_encode($langs, JSON_UNESCAPED_UNICODE);   // For PHP >= 5.4.0
            $json = str_replace('\\/', '/', json_encode($langs));  // Use this for
            echo $json;                                            // PHP < 5.4.0
        }else{
            $langs = ttEDatabase;
        }
        return $langs;
    }

    static public function error($error){
        switch($error){
            case 'ControllerNotFound' :
                echo "Controller not found";
                break;
            case 'ActionNotFound' :
                echo "Action not found";
                break;
            case 'AccessDenied' :
                echo "Access Denied";
                break;
            default :
                echo "Other error";
        }
    }
}
