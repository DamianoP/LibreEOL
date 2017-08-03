<?php
/**
 * File: SubjectController.php
 * User: Masterplan
 * Date: 3/25/13
 * Time: 12:39 AM
 * Desc: Controller for all subjects operations
 */
class SubjectController extends Controller{

    public $defaultAction = 'Index';

    /**
     *  @name   SubjectController
     *  @descr  Create an instance of SubjectController class
     */
    public function SubjectController(){}

    /**
     * @name    executeAction
     * @param   $action     String      Name of requested action
     * @descr   Execute action (if exists and if user is allowed)
     */
    public function executeAction($action){
        global $user;

        // If have necessary privileges execute action
        if ($this->getAccess($user, $action, $this->accessRules())) {
            $action = 'action'.$action;
            $this->$action();
            // Else, if user is not logged bring him the to login page
        }elseif($user->role == '?'){
            header('Location: index.php?page=login');
            // Otherwise: Access denied
        }else{
            Controller::error('AccessDenied');
        }
    }

    /**
     *  @name   actionIndex
     *  @descr  Show subject index page
     */
    private function actionIndex(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionIndex
     *  @descr  Show subject index page
     */
    private function actionIndex2(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /********************************************************************
     *                              Subject                             *
     ********************************************************************/

    /**
     *  @name   actionShowsubjectinfo
     *  @descr  Show info about a subject
     */
    private function actionShowsubjectinfo(){
        global $engine, $user, $log;

        if((isset($_POST['action'])) && (isset($_POST['idSubject']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   showsubjectinfoandexams
     *  @descr  Shows informations about requested subject and the list of all available exams
     */
    private function actionShowsubjectinfoandexams(){
        global $engine, $log;

        if(isset($_POST['idSubject'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdatesubjectinfo
     *  @descr  Save edited informations about a subject
     */
    private function actionUpdatesubjectinfo(){
        global $log;

        if((isset($_POST['idSubject'])) && (isset($_POST['subjectName'])) &&
           (isset($_POST['subjectDesc'])) && (isset($_POST['teachers']))){
            $teachers = explode('&', $_POST['teachers']);
            $log->append(var_export($teachers, true));
            $db = new sqlDB();
            if($db->qUpdateSubjectInfo($_POST['idSubject'], $_POST['subjectName'], $_POST['subjectDesc'], $teachers)){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewsubject
     *  @descr  Show page to create a new subject
     */
    private function actionNewsubject(){
        global $log;

        if((isset($_POST['subjectName'])) && (isset($_POST['subjectLang']))){
            $db = new sqlDB();
            $desc = isset($_POST['subjectDesc']) ? $_POST['subjectDesc'] : "";
            if(($db->qNewSubject($_POST['subjectName'], $desc, $_POST['subjectLang'],$_POST['subjectVers'])) && ($subjectID = $db->nextRowEnum())){
                echo $subjectID[0];
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
            die(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionDeletesubject
     *  @descr  Delete requested subject
     */
    private function actionDeletesubject(){
        global $log;

        if(isset($_POST['idSubject'])){
            $db = new sqlDB();
            if($db->qDeleteSubject($_POST['idSubject'])){
                echo "ACK";
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /********************************************************************
     *                              Topic                               *
     ********************************************************************/

    /**
     *  @name   actionShowtopicinfo
     *  @descr  Show info about a topic or show empty infos for new topic
     */
    private function actionShowtopicinfo(){
        global $engine, $log;

        if((isset($_POST['action'])) && (isset($_POST['idTopic']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdatetopicinfo
     *  @descr  Save edited informations about a topic
     */
    private function actionUpdatetopicinfo(){
        global $log;

        if((isset($_POST['idTopic'])) && (isset($_POST['topicName']))){
            $desc = '';
            if(isset($_POST['topicDesc']))
                $desc = $_POST['topicDesc'];
            $db = new sqlDB();
            if($db->qUpdateTopicInfo($_POST['idTopic'], $_POST['topicName'], $desc)){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewtopic
     *  @descr  Show page to create a new topic
     */
    private function actionNewtopic(){
        global $log;

        if((isset($_SESSION['idSubject'])) && (isset($_POST['topicName']))){
            $desc = '';
            if(isset($_POST['topicDesc']))
                $desc = $_POST['topicDesc'];
            $db = new sqlDB();
            if($db->qNewTopic($_SESSION['idSubject'], $_POST['topicName'], $desc)){
                if($row = $db->nextRowEnum()){
                    echo $row[0];
                }
            }else{
                die($db->getError());
            }
            $db->close();

        }else{
            $log->append(__FUNCTION__." : Params not set");
            die(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionDeletetopic
     *  @descr  Delete topic and its related questions and answers (if possible)
     */
    private function actionDeletetopic(){
        global $log;

        if(isset($_POST['idTopic'])){
            $db = new sqlDB();

            if(($db->qGetEditAndDeleteConstraints('delete', 'topic', array($_POST['idTopic']))) && ($db->numResultRows() > 0)){
                $error = ttETestSettingDeleteTopic;
                while($testsetting = $db->nextRowAssoc()){
                    $error .= ' - '.$testsetting['name'].'</br>';
                }
                die($error);
            }elseif($db->qDeleteTopic($_POST['idTopic'])){
                echo 'ACK';
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__.' : Params not set');
            die(__FUNCTION__.' : Params not set');
        }
    }

    /**
     *  @name   accessRules
     *  @descr  Returns all access rules for Home controller's actions:
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
                'actions' => array('Showtopicinfo', 'Updatetopicinfo', 'Newtopic', 'Deletetopic'),
                'roles'   => array('t'),
            ),
            array(
                'allow',
                'actions' => array('Index2','Showsubjectinfo','Showtopicinfo'),
                'roles'   => array('e'),
            ),
            array(
                'allow',
                'actions' => array('Index', 'Updatesubjectinfo', 'Deletesubject', 'Newsubject'),
                'roles'   => array('a', 't'),
            ),
            array(
                'allow',
                'actions' => array('Showsubjectinfo'),
                'roles'   => array('a', 't'),
            ),
            array(
                'allow',
                'actions' => array('Showsubjectinfoandexams'),
                'roles'   => array('s'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }
}
