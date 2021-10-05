<?php
/**
 * File: QuestionController.php
 * User: Masterplan
 * Date: 4/4/13
 * Time: 6:18 PM
 * Desc: Controller for all questions operations
 */

class QuestionController extends Controller{

    /**
     *  @name   QuestionController
     *  @descr  Create an instance of QuestionController class
     */
    public function QuestionController(){}

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
     *  @descr  Show index page
     */
    private function actionIndex(){
        global $config, $engine, $qlog;




        if(isset($_POST['idSubject'])){
            $_SESSION['idSubject'] = $_POST['idSubject'];
            $_SESSION['uploadDir'] = $config['systemUploadDir'].$_POST['idSubject'];
        }
        if(isset($_SESSION['idSubject'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header('Location: index.php?page=subject&r=qstn');
        }
    }



    /**
     *  @name   actionIndex
     *  @descr  Show index page
     */
    private function actionIndex2(){
        global $config, $engine;

        if(isset($_POST['idSubject'])){
            $_SESSION['idSubject'] = $_POST['idSubject'];
            $_SESSION['uploadDir'] = $config['systemUploadDir'].$_POST['idSubject'];
        }
        if(isset($_SESSION['idSubject'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header('Location: index.php?page=subject/index2&r=qstn2');
        }

    }




    /********************************************************************
     *                             Question                             *
     ********************************************************************/

    /**
     *  @name   actionShowquestionpreview
     *  @descr  Show preview about requested question
     */
    private function actionShowquestionpreview(){
        global $log, $engine;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idLanguage'])) && (isset($_POST['type']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
            die(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShowquestionlanguages
     *  @descr  Show languages about requested question
     */
    private function actionShowquestionlanguages(){
        global $log, $engine;

        if(isset($_POST['idQuestion'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
            die(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShowquestioninfo
     *  @descr  Show all details about selected question with relative answers with specific language
     */
    private function actionShowquestioninfo(){  // event double click on question
        global $log, $engine;

        if(((isset($_POST['action'])) && (isset($_POST['idQuestion']))) ||
            (isset($_POST['action'])) && (isset($_POST['type'])) && (isset($_POST['topic']))){
            $engine->loadLibs();
            $engine->renderPage();

        }else{
            $log->append("Params not set in '".__FUNCTION__.'\' function: $_POST = '.var_export($_POST, true));
            die("Params not set in '".__FUNCTION__." function");
        }
    }

    /**
     *  @name   actionShowquestionhistory
     *  @descr  Show all details about question's history
     */
    private function actionShowquestionhistory(){
   /*    global $qlog, $engine;

        if (isset($_POST['idQuestion'])){
            $ActualQuestionId= $_POST['idQuestion'];

            $qlog->append("************************* --> ".$ActualQuestionId);


        } */

    }


    /**
     *  @name   actionUpdatequestioninfo
     *  @descr  If requested duplicates the question and updates all its infos
     */
    private function actionUpdatequestioninfo(){
        global $log, $qlog, $user,$OldquestionID,$NewquestionID , $question_newText;
        $textChanged = false;

        $db = new sqlDB();
        $db->result = null; $db->mysqli = $db->connect();

        if((isset($_POST['idQuestion'])) && (isset($_POST['idTopic'])) && (isset($_POST['difficulty'])) &&
           (isset($_POST['translationsQ'])) && (isset($_POST['shortText'])) && (isset($_POST['extras'])) &&
           (isset($_POST['mainLang']))) {

            $questionID = $_POST['idQuestion'];
            $question_newText = $_POST['shortText'];
            $OldquestionID = $_POST['idQuestion'];
            $usernamesurname = $user->name . " " . $user->surname;

            //query to find old question's text

                $queryOldText = "SELECT shortText FROM Questions WHERE idQuestion= ".$OldquestionID;
                $db->execQuery($queryOldText);
                if ($db->numResultRows()>0){
                    while ($row = mysqli_fetch_array($db->result)){
                        $question_oldText=$row['shortText'];
                    }
                }


                if (trim(strtolower($question_oldText)) != trim(strtolower($question_newText))){
                    $textChanged=true;
                }




            if($textChanged) {


                if ($db->qGetEditAndDeleteConstraints('edit', 'question2', array($_POST['idQuestion']))) { //if question was used

                    if ($db->numResultRows() > 0) {  //if there are results
                        if ($db->qDuplicateQuestion($questionID, true)) {  // if duplicateQuestion return true

                            if ($questionID = $db->nextRowEnum()) { //next free id question
                                $NewquestionID = $questionID[0];

                                if ($OldquestionID != $NewquestionID) { //if question's id change (assigned question was modified)
 				    // AGGIUNTA DAMIANO PER RENDERE CONSISTENTE QUESTIONSDISTRIBUTION
				   // $queryQuestionsDistributions = "UPDATE questionsdistribution SET fkQuestion=".$NewquestionID." WHERE fkQuestion=".$OldquestionID;
                                   // $db->execQuery($queryQuestionsDistributions);
                                    $qlog->append($usernamesurname . " modified question id " . $OldquestionID . " in question id " . $NewquestionID . "\n-"); //write file log

                                    //select to find idparent
                                    $parentQuery = "SELECT FkQuestionParent FROM Questions_History WHERE FkQuestion = " . $OldquestionID;
                                    $db->execQuery($parentQuery);
                                    if ($db->numResultRows() > 0) {  //if query find a result
                                        while ($row = mysqli_fetch_array($db->result)) {
                                            $idParent = $row['FkQuestionParent'];

                                            //INSERT new question's history saving old parent
                                            $query = "INSERT INTO `Questions_History` (`FkQuestion`, `FkQuestionParent`, `WhoChanged`, `Date`)
                                        VALUES ('$NewquestionID', '$idParent', '$usernamesurname', '" . date('Y/m/d') . "')";
                                            $db->execQuery($query);

                                        }

                                    } else {
                                        //INSERT new question's history
                                        $query = "INSERT INTO `Questions_History` (`FkQuestion`, `FkQuestionParent`, `WhoChanged`, `Date`)
                                        VALUES ('$NewquestionID', '$OldquestionID', '$usernamesurname', '" . date('Y/m/d') . "')";
                                        $db->execQuery($query);
                                    }


                                }
                                $questionID = $questionID[0];

                            }
                        } else {
                            die($db->getError());
                        }
                    }
                } else {
                    die($db->getError());
                }
            }
            $translationsQ = json_decode($_POST['translationsQ'], true);
            if ($translationsQ[$_POST['mainLang']]) {
                if ($db->qUpdateQuestionInfo($questionID, $_POST['idTopic'], $_POST['difficulty'], $_POST['extras'], $_POST['shortText'], $translationsQ)) {
                    echo $this->updateQuestionRow($questionID, $_POST['mainLang'], $translationsQ);
                }
            } else {
                die(ttEMainLanguageEmpty);
            }



        }else{
            $log->append("Params not set in '".__FUNCTION__.'\' function: $_POST = '.var_export($_POST, true));
            die("Params not set in '".__FUNCTION__." function");
        }

    }




    /**
     *  @name   updateQuestionRow
     *  @param  $questionID         String          Question's ID
     *  @param  $mainLang           String          Question's main lang
     *  @param  $translationsQ      Array           Question's translations
     *  @return null|String
     *  @descr  Update all infos about question
     */
    private function updateQuestionRow($questionID, $mainLang, $translationsQ){
        global $log, $ajaxSeparator, $config;

        $db = new sqlDB();
        if(($db->qQuestionInfo($questionID, $mainLang)) && ($question = $db->nextRowAssoc()) &&
            ($db->qSelect('Languages')) && ($allLangs = $db->getResultAssoc('idLanguage'))){
            $statuses = array('a' => 'Active',
                              'i' => 'Inactive',
                              'e' => 'Error');
            $languages = '';
            foreach($translationsQ as $idLanguage => $translation){
                if((isset($allLangs[$idLanguage])) && trim($translation) != "" )
                    $languages .= '<img title="'.$allLangs[$idLanguage]['description'].'"
                                        class="flag" alt="'.$allLangs[$idLanguage]['alias'].'"
                                        src="'.$config['themeFlagsDir'].$allLangs[$idLanguage]['alias'].'.gif">';
            }
            $text = $question['shortText'];
            $newQuestion = array(
                '<img title="'.constant('tt'.$statuses[$question['status']]).'"
                      value="'.$question['status'].'" alt="'.$statuses[$question['status']].'"
                      src="'.$config['themeImagesDir'].$statuses[$question['status']].'.png">',
                $text,
                $languages,
                $question['name'],
                constant('ttQT'.$question['type']),
                constant('ttD'.$question['difficulty']),
                $questionID,
                $question['idTopic'],
                $question['type'],
                $_POST['mainLang']
            );
            return 'ACK'.$ajaxSeparator.str_replace('\\/', '/', json_encode($newQuestion));
        }else{
            return $db->getError();
        }
    }

    /**
     *  @name   actionChangestatus
     *  @descr  Action used for change question's status
     */
    private function actionChangestatus(){
        global $log, $engine;
        if((isset($_POST['idQuestion'])) && (isset($_POST['status']))){
            if($TheresNoErrorsInQuestion = true){       // Add function to check if question
                $db = new sqlDB();                      // and it's answers are well formed
                if($db->qChangeQuestionStatus($_POST['idQuestion'], $_POST['status'])){
                    echo 'ACK';
                }else{
                    die($db->getError());
                }
                $db->close();
            }else{
                die("Question/Answers not well formed");
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewquestion
     *  @descr  Action used for create a new question
     */
    private function actionNewquestion(){
        global $log, $config, $ajaxSeparator;

        if((isset($_POST['idTopic'])) && (isset($_POST['type'])) && (isset($_POST['difficulty'])) &&
           (isset($_POST['translationsQ'])) && (isset($_POST['shortText'])) && (isset($_POST['extras'])) &&
           (isset($_POST['mainLang']))){

            $db = new sqlDB();
            $translationsQ = json_decode($_POST['translationsQ'], true);
            if($translationsQ[$_POST['mainLang']]){
                $question = Question::newQuestion($_POST['type'], array('idTopic' => $_POST['idTopic'],
                                                                        'type' => $_POST['type'],
                                                                        'difficulty' => $_POST['difficulty'],
                                                                        'extras' => $_POST['extras'],
                                                                        'shortText' => $_POST['shortText'],
                                                                        'translationsQ' => $translationsQ));
                $idQuestion = $question->createNewQuestion();
                echo $this->updateQuestionRow($idQuestion, $_POST['mainLang'], $translationsQ);
            }else{
                die(ttEMainLanguageEmpty);
            }

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionDeletequestion
     *  @descr  Delete questions and its answers (if possible)
     */
    private function actionDeletequestion(){
        global $log;

        if(isset($_POST['idQuestion'])){
            $db = new sqlDB();

            if(($db->qGetEditAndDeleteConstraints('delete', 'question1', array($_POST['idQuestion']))) && ($db->numResultRows() > 0)){
                $error = ttETestSettingDeleteQuestion;
                while($testsetting = $db->nextRowAssoc()){
                    $error .= ' - '.$testsetting['name'].'</br>';
                }
                die($error);
            }elseif($db->qGetEditAndDeleteConstraints('delete', 'question2', array($_POST['idQuestion']))){
                if($db->qDeleteQuestion($_POST['idQuestion'], ($db->numResultRows() == 0))){
                    echo 'ACK';
                }else{
                    die($db->getError());
                }
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /********************************************************************
     *                              Answer                              *
     ********************************************************************/

    /**
     *  @name   actionShowanswerinfo
     *  @descr  Show all informations about selected answer
     */
    private function actionShowanswerinfo(){
        global $engine, $log;

        if((isset($_SESSION['idSubject'])) && (isset($_POST['action'])) && (isset($_POST['idQuestion'])) &&
            (isset($_POST['type'])) && (isset($_POST['idAnswer']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }
    
    

    /**
     *  @name   actionUpdateanswerinfo
     *  @descr  Update all details about a answer
     */
    private function actionUpdateanswerinfo(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idAnswer'])) && (isset($_POST['type'])) &&
           (isset($_POST['translationsA'])) && (isset($_POST['score'])) && (isset($_POST['mainLang']))){
            $db = new sqlDB();

            $translationsA = json_decode($_POST['translationsA'], true);
            if ($_POST['type']=="FB"){
                foreach ($translationsA as &$element){
                    $element=htmlspecialchars($element,ENT_QUOTES);
                }
                unset($element);
            }
            if($translationsA[$_POST['mainLang']]){
                $updateMandatory = false;
                $questionID = $newQuestionID = $_POST['idQuestion'];
                $answerID = $_POST['idAnswer'];
                if($db->qGetEditAndDeleteConstraints('edit', 'answer1', array($questionID))){
                    $updateMandatory = ($db->numResultRows() > 0) ? true : false;
                }else{
                    die($db->getError());
                }

                if($db->qGetEditAndDeleteConstraints('edit', 'answer2', array($questionID))){
                    if($db->numResultRows() > 0){
                        if($db->qDuplicateQuestion($questionID, $updateMandatory, $answerID)){
                            if($IDs = $db->nextRowEnum()){
                                $newQuestionID = $IDs[0];
                                $answerID = $IDs[1];
                            }
                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

            if($db->qUpdateAnswerInfo($answerID, $_POST['score'], $translationsA)){
                echo 'ACK'.$ajaxSeparator.$newQuestionID;
                if($questionID != $newQuestionID){                      // Question duplicated, reload answers table
                    $question = Question::newQuestion($_POST['type']);
                    echo $ajaxSeparator;
                    $question->printAnswersTable($newQuestionID, $_SESSION['idSubject']);
                }else{                                                  // Reprint only edited answer's row
                    $answer = Answer::newAnswer($_POST['type'], array('score'       =>  $_POST['score'],
                                                                      'translation' =>  $translationsA[$_POST['mainLang']],
                                                                      'idAnswer'    =>  $answerID));
                    echo $ajaxSeparator.str_replace('\\/', '/', json_encode($answer->getAnswerRowInTable()));
                }
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set - ".var_export($_POST, true));
        }
    }
    
    
    private function actionGetanswersubinfo(){
        global $log;
        global $ajaxSeparator;
        $db = new sqlDB();
        $idQuestion = $_POST["idQuestion"];
        $idSubQuestion = $_POST["idSubQuestion"];
        $lang = $_POST["mainLang"];
        $return="";
        if ($db->qSubAnswer($idQuestion,$idSubQuestion,$lang)) {
            $i = 0;
            while ($answer = $db->nextRowAssoc()) {
                $return[$i] = $answer;
                $i++;
            }
            echo 'ACK'.$ajaxSeparator.json_encode($return);
        }else{
            echo "error";
        }

    }
	private function actionUpdatesubinfo(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idAnswer'])) && (isset($_POST['type'])) &&
            (isset($_POST['translationsA'])) && (isset($_POST['score'])) && (isset($_POST['mainLang'])) ){
            $db = new sqlDB();

            $translationsA = json_decode($_POST['translationsA'], true);
            if($translationsA[$_POST['mainLang']]){
                $updateMandatory = false;
                $questionID = $newQuestionID = $_POST['idQuestion'];
                $answerID = $_POST['idAnswer'];
                if($db->qGetEditAndDeleteConstraints('edit', 'answer1', array($questionID))){
                    $updateMandatory = ($db->numResultRows() > 0) ? true : false;
                }else{
                    die($db->getError());
                }

                if($db->qGetEditAndDeleteConstraints('edit', 'answer2', array($questionID))){
                    if($db->numResultRows() > 0){
                        if($db->qDuplicateQuestion($questionID, $updateMandatory, $answerID)){
                            if($IDs = $db->nextRowEnum()){
                                $newQuestionID = $IDs[0];
                                $answerID = $IDs[1];
                            }
                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

            if($db->qUpdateAnswerInfo($answerID, $_POST['score'], $translationsA)){
                echo 'ACK'.$ajaxSeparator.$newQuestionID;
                if($questionID != $newQuestionID){                      // Question duplicated, reload answers table
                    $question = Question::newQuestion($_POST['type']);
                    echo $ajaxSeparator;
                    $question->printAnswersTable($newQuestionID, $_SESSION['idSubject']);
                }else{                                                  // Reprint only edited answer's row
                    $answer = Answer::newAnswer($_POST['type'], array('score'       =>  $_POST['score'],
                        'translation' =>  $translationsA[$_POST['mainLang']],
                        'idAnswer'    =>  $answerID));
                    echo $ajaxSeparator.str_replace('\\/', '/', json_encode($answer->getAnswerRowInTable()));
                }
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set - ".var_export($_POST, true));
        }
    }
	    private function actionShowsubquestionsinfo(){
        global $engine, $log;
        // $log->append(var_export("prova action show ",true));

        if((isset($_SESSION['idSubject'])) && (isset($_POST['action'])) && (isset($_POST['idQuestion'])) &&
            (isset($_POST['type'])) && (isset($_POST['sub_questions']))){
            $engine->loadLibs();
            $engine->renderPage();


        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }
    /**
     *  @name   actionNewanswer
     *  @descr  Action used for create a new answer
     */
    private function actionNewanswer(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['score'])) && (isset($_POST['type'])) &&
           (isset($_POST['translationsA'])) && (isset($_POST['mainLang']))){
            $db = new sqlDB();

            $translationsA = json_decode($_POST['translationsA'], true);
            if($translationsA[$_POST['mainLang']]){

                $updateMandatory = false;
                $questionID = $newQuestionID = $_POST['idQuestion'];
                if($db->qGetEditAndDeleteConstraints('create', 'answer1', array($questionID))){
                    $updateMandatory = ($db->numResultRows() > 0) ? true : false;
                }else{
                    die($db->getError());
                }

                if($db->qGetEditAndDeleteConstraints('create', 'answer2', array($questionID))){
                    if($db->numResultRows() > 0){
                        if($db->qDuplicateQuestion($questionID, $updateMandatory)){
                            $resultSet = $db->nextRowEnum();
                            $newQuestionID = $resultSet[0];
                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die($db->getError());
                }

                if($db->qNewAnswer($newQuestionID, $_POST['score'], $translationsA)){
                    $resultSet = $db->nextRowEnum();
                    $answerID = $resultSet[0];
                    echo 'ACK'.$ajaxSeparator.$newQuestionID;
                    if($questionID != $newQuestionID){                      // Question duplicated, reload answers table
                        $question = Question::newQuestion($_POST['type']);
                        echo $ajaxSeparator;
                        $question->printAnswersTable($newQuestionID, $_SESSION['idSubject']);
                    }else{                                                  // Reprint only edited answer's row
                        $answer = Answer::newAnswer($_POST['type'], array('score'       =>  $_POST['score'],
                                                                          'translation' =>  $translationsA[$_POST['mainLang']],
                                                                          'idAnswer'    =>  $answerID));
                        echo $ajaxSeparator.str_replace('\\/', '/', json_encode($answer->getAnswerRowInTable()));
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }
	 /**
     *  @name   actionNewsub
     *  @descr  Action used for create a new pull down list answer
     */
    private function actionNewanswersub(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['subQuestion'])) && (isset($_POST['score'])) && (isset($_POST['type'])) &&
            (isset($_POST['translationsA'])) && (isset($_POST['mainLang']))){
            $db = new sqlDB();
            $translationsA = json_decode($_POST['translationsA'], true);
            if($translationsA[$_POST['mainLang']]){

                $updateMandatory = false;
                $questionID = $newQuestionID = $_POST['idQuestion'];

                $subID  = $_POST['subQuestion'];
                if($db->qGetEditAndDeleteConstraints('create', 'answer1', array($questionID))){
                    $updateMandatory = ($db->numResultRows() > 0) ? true : false;
                }else{
                    die($db->getError());
                }

                if($db->qGetEditAndDeleteConstraints('create', 'answer2', array($questionID))){
                    if($db->numResultRows() > 0){
                        if($db->qDuplicateQuestion($questionID, $updateMandatory)){
                            $resultSet = $db->nextRowEnum();
                            $newQuestionID = $resultSet[0];
                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die($db->getError());
                }

                if($db->qNewAnswerPL($subID,$newQuestionID, $_POST['score'],$translationsA)){
                    $resultSet = $db->nextRowEnum();
                    $answerID = $resultSet[0];
                    echo 'ACK'.$ajaxSeparator.$newQuestionID;
                    if($questionID != $newQuestionID){                      // Question duplicated, reload answers table
                        $question = Question::newQuestion($_POST['type']);
                        echo $ajaxSeparator;
                        $question->printAnswersTable($subID,$_SESSION['idSubject']);
                    }else{                                                  // Reprint only edited answer's row
                        $answer = Answer::newAnswer($_POST['type'], array('score'       =>  $_POST['score'],
                            'translation' =>  $translationsA[$_POST['mainLang']],
                            'idAnswer'    =>  $answerID));
                        echo $ajaxSeparator.str_replace('\\/', '/', json_encode($answer->getAnswerRowInTable()));
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }
    private function actionDeletesubquestion(){
        $db = new sqlDB();
        if(isset($_POST["idSubQuestion"])){
            if($db->qDeleteSubQuestion($_POST["idSubQuestion"])){
                echo "ACK";
            }else{
                echo "error";
            }
        }
    }

	/**
     *  @name   actionNewanswer
     *  @descr  Action used for create a new answer
     */
    private function actionNewsubquestion(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['score'])) && (isset($_POST['type'])) &&
            (isset($_POST['translationsA'])) && (isset($_POST['mainLang']))){
            $db = new sqlDB();

            $translationsA = json_decode($_POST['translationsA'], true);
            if($translationsA[$_POST['mainLang']]){

                $updateMandatory = false;
                $questionID = $newQuestionID = $_POST['idQuestion'];
                if($db->qGetEditAndDeleteConstraints('create', 'answer1', array($questionID))){
                    $updateMandatory = ($db->numResultRows() > 0) ? true : false;
                }else{
                    die($db->getError());
                }

                if($db->qGetEditAndDeleteConstraints('create', 'answer2', array($questionID))){
                    if($db->numResultRows() > 0){
                        if($db->qDuplicateQuestion($questionID, $updateMandatory)){
                            $resultSet = $db->nextRowEnum();
                            $newQuestionID = $resultSet[0];
                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die($db->getError());
                }

                if($db->qnewSubquestions($newQuestionID, $_POST['score'],$translationsA)){
                    $resultSet = $db->nextRowEnum();
                    $answerID = $resultSet[0];
                    echo 'ACK'.$ajaxSeparator.$newQuestionID;
                    if($questionID != $newQuestionID){                      // Question duplicated, reload answers table
                        $question = Question::newQuestion($_POST['type']);
                        echo $ajaxSeparator;
                        $question->printSubquestionsTable($newQuestionID);
                    }else{                                                  // Reprint only edited answer's row
                        $answer = Answer::newAnswer($_POST['type'], array('score'       =>  $_POST['score'],
                            'translation' =>  $translationsA[$_POST['mainLang']],
                            'idAnswer'    =>  $answerID));
                        echo $ajaxSeparator.str_replace('\\/', '/', json_encode($answer->getAnswerRowInTable()));
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }


    /**
     *  @name   actionDeleteanswer
     *  @descr  Delete answer and its translation (if possible)
     */
    private function actionDeleteanswer(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idAnswer'])) && (isset($_POST['type']))){
            $db = new sqlDB();

            $updateMandatory = false;
            $questionID = $newQuestionID = $_POST['idQuestion'];
            $answerID = $_POST['idAnswer'];
            if($db->qGetEditAndDeleteConstraints('delete', 'answer1', array($questionID))){
                $updateMandatory = ($db->numResultRows() > 0) ? true : false;
            }else{
                die($db->getError());
            }

            if($db->qGetEditAndDeleteConstraints('delete', 'answer2', array($questionID))){
                if($db->numResultRows() > 0){
                    if($db->qDuplicateQuestion($questionID, $updateMandatory, $answerID)){
                        if($IDs = $db->nextRowEnum()){
                            $newQuestionID = $IDs[0];
                            $answerID = $IDs[1];
                        }
                    }else{
                        die($db->getError());
                    }
                }
            }else{
                die($db->getError());
            }

            if($db->qDeleteAnswer($answerID)){
                echo 'ACK'.$ajaxSeparator.$newQuestionID.$ajaxSeparator;
                if($questionID != $newQuestionID){                      // Question duplicated, reload answers table
                    $question = Question::newQuestion($_POST['type']);
                    $question->printAnswersTable($newQuestionID, $_SESSION['idSubject']);
                }
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
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
                'actions' => array('Index', 'Showtopics', 'Showquestionpreview','Showquestionpreviewpdl', 'Showquestioninfo', 'Showquestionhistory',
                                   'Newquestion', 'Showquestionlanguages','Showsubquestionsinfo',
                                   'Updatequestioninfo', 'Deletequestion','Deletesubquestion', 'Changestatus',
                                   'Showanswerinfo', 'Updateanswerdetails',
                                   'Updateanswerinfo','Getanswersubinfo','Updatesubinfo', 'Newanswer','Newanswersub', 'Deleteanswer','Newsubquestion'),
                'roles'   => array('t'),
            ),
            array(
                'allow',
                'actions' => array('Index2', 'Showtopics', 'Showquestionpreview', 'Showquestioninfo', 'Showquestionhistory',
                    'Showquestionlanguages',
                    'Changestatus',
                    'Showanswerinfo'),
                'roles'   => array('e'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }

}
