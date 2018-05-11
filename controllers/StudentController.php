<?php
/**
 * File: StudentController.php
 * User: Masterplan
 * Date: 5/2/13
 * Time: 12:04 PM
 * Desc: Your description HERE
 */

class StudentController extends Controller{

    /**
     *  @name   StudentController
     *  @descr  Create an instance of StudentController class
     */
    public function StudentController(){}

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
     *  @descr  Show student index page
     */
    private function actionIndex(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /********************************************************************
     *                               Exam                               *
     ********************************************************************/

    /**
     *  @name   actionRegister
     *  @descr  Adds student to an exam
     */
    private function actionRegister(){
        global $user, $log;
        if(isset($_POST['idExam'])){
            $db = new sqlDB();
            if($db->qCheckRegistration($_POST['idExam'], $user->id)){
                if($db->numResultRows() > 0){
                    die(ttEAlreadyRegistered);
                }else{
                    if($db->qMakeQuestionsSet($_POST['idExam'], $user->id)){
                        echo "ACK";
                    }else{
                        die($db->getError());
                    }
                }
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionCheckexamstatus
     *  @descr  Checks if exam is started
     */
    private function actionCheckexamstatus(){
        global $log;

        if(isset($_POST['idExam'])){
            $db = new sqlDB();
            if($db->qSelect('Exams', 'idExam', $_POST['idExam'])){
                if($row = $db->nextRowAssoc()){
                    if($row['status'] == 's'){
                        echo "ACK";
                    }else{
                        die(ttEExamNotStarted);
                    }
                }else{
                    die(ttEExamNotFound);
                }
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /********************************************************************
     *                               Test                               *
     ********************************************************************/

    /**
     *  @name   actionLogintest
     *  @descr  Show login form for exam start
     */
    private function actionLogintest(){
        global $log, $engine;

        if(isset($_SESSION['idSet'])){
            header("Location: index.php?page=student/test");
        }else{
            if(isset($_POST['idExam'])){
                $engine->renderDoctype();
                $engine->loadLibs();
                $engine->renderPage();
            }else{
                $log->append(__FUNCTION__." : Params not set");
                header("Location: index.php");
            }
        }
    }

    /**
     *  @name   actionStarttest
     *  @descr  Starts to execute test
     */
    private function actionStarttest(){
        global $log, $user;

        if(isset($_SESSION['idSet'])){
            header("Location: index.php?page=student/test");        // Student has been already authenticated
        }else{
            if((isset($_POST['idExam'])) && (isset($_POST['password']))){
                $db = new sqlDB();
                if($db->qSelect('Exams', 'idExam', $_POST['idExam'])){
                    if($exam = $db->nextRowAssoc()){
                        if($exam['password'] == $_POST['password']){
                            if(($db->qAssignSet($_POST['idExam'], $user->id)) && ($idSet = $db->nextRowEnum())){
                                $_SESSION['idSet'] = $idSet[0];
                                echo 'ACK';
                            }
                        }else{
                            die(ttEPassword);
                        }
                    }else{
                        die(ttEExamNotFound);
                    }
                }else{
                    die($db->getError());
                }
            }else{
                header("Location: index.php");
            }
        }
    }

    /**
     *  @name   actionSubmittest
     *  @descr  Saves all test's answers and close test if is requestd
     */
    private function actionSubmittest(){
        global $log;
        if(isset($_SESSION['idSet'])){
            $db = new sqlDB();
            if(($db->qSelect('Tests', 'fkSet', $_SESSION['idSet'])) && ($testInfo = $db->nextRowAssoc())){
//                if(($testInfo['status'] != 'w') && ($testInfo['status'] != 's')){

                $db2 = new sqlDB();
                if(($db2->qSelect('Users', 'idUser', $testInfo ['fkUser'])) && ($testInfo2 = $db2->nextRowAssoc())) {
                    $idLang = $testInfo2['fkLanguage'];    //prendo la lingua dalla tabella users
                }else{
                    die($db->getError());
                }

                if($testInfo['status'] != 's'){
                    die(ttETestBlockedSubmitted);               // Test has been blocked or already submitted
                }
            }else{
                die($db->getError());
            }

            if((isset($_POST['questions'])) && (isset($_POST['answers']))){
                $questions = json_decode($_POST['questions'], true);
                $answers = json_decode($_POST['answers'], true);

                if($db->qUpdateTestAnswers($_SESSION['idSet'],$idLang, $questions, $answers)){
                    if((isset($_POST['submit'])) && ($_POST['submit'] == "true")){      // Close test
                        if($ok=$db->qEndTest($_SESSION['idSet'])){
                            unset($_SESSION['idSet']);
                            //$log->append("Test closed:".$ok);
                            echo 'ACK';
                        }else{
                            die($db->getError());
                        }
                    }else{                                                              // Leave test open
                        echo 'ACK';
                    }
                }else{
                    $log->append("qSelect2");
                    die($db->getError());
                }
            }else{
                $log->append(__FUNCTION__." : Params not set");
            }
        }else{
            die(ttETestAlreadySubmitted);
        }
    }



/**
     *  @name   actionSubmittest
     *  @descr  Saves all test's answers and close test if is requestd
     */
    private function actionUpdateAnswer(){
        global $log;
        if(isset($_SESSION['idSet'])){
            $db = new sqlDB();
            if(($db->qSelect('Tests', 'fkSet', $_SESSION['idSet'])) && ($testInfo = $db->nextRowAssoc())){
//                if(($testInfo['status'] != 'w') && ($testInfo['status'] != 's')){

                $db2 = new sqlDB();
                if(($db2->qSelect('Users', 'idUser', $testInfo ['fkUser'])) && ($testInfo2 = $db2->nextRowAssoc())) {
                    $idLang = $testInfo2['fkLanguage'];    //prendo la lingua dalla tabella users
                }else{
                    die($db->getError());
                }

                if($testInfo['status'] != 's'){
                    die(ttETestBlockedSubmitted);               // Test has been blocked or already submitted
                }
            }else{
                die($db->getError());
            }
            if((isset($_POST['question'])) && (isset($_POST['answer']))){
                $questions = json_decode($_POST['question'], true);
                $answers = json_decode($_POST['answer'], true);
                if($db->qUpdateTestAnswer($_SESSION['idSet'],$idLang, $question, $answer)){                                                           // Leave test open
                        echo 'ACK';
                }else{
                    $log->append("qSelect2");
                    die($db->getError());
                }
            }else{
                $log->append(__FUNCTION__." : Params not set");
            }
        }else{
            die(ttETestAlreadySubmitted);
        }
    }



    /**
     *  @name   actionTest
     *  @descr  Show student's test
     */
    private function actionTest(){
        global $engine;

        if(isset($_SESSION['idSet'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header("Location: index.php?page=student");
        }
    }


    /**
     *  @name   actionCheckExamAvailability
     *  @descr  check if the teacher submitted the test
     */
    private function actionCheckExamAvailability(){
        global $log;
        if(isset($_SESSION['idSet'])){
            $db = new sqlDB();
            if(($db->qSelect('Tests', 'fkSet', $_SESSION['idSet'])) && ($testInfo = $db->nextRowAssoc())){
                if($testInfo['status'] != 's'){
                    echo "SUBMITTED";               // Test has been blocked or already submitted
                }else{
                    echo "ACK";
                }
            }else{
                die($db->getError());
            }
        }else{
            die(ttETestAlreadySubmitted);
        }
    }


    /**
     *  @name   actionCloseTest
     *  @descr  remove set from session
     */
    private function actionCloseTest(){
        global $log;
        unset($_SESSION['idSet']);
        echo "ACK";
    }
        /**
     *  @name   actionProfile
     *  @descr  Shows profile page of user's account
     */
    private function actionVotes(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

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
                'actions' => array('Index','Votes', 'Checkexamstatus', 'Logintest',
                                   'Starttest', 'Test', 'Submittest', 'Register','Checkexamavailability','Closetest'),
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
