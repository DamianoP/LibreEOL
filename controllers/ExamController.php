<?php
/**
 * File: ExamController.php
 * User: Masterplan
 * Date: 4/19/13
 * Time: 10:04 AM
 * Desc: Your description HERE
 */

class ExamController extends Controller{

    public $defaultAction = 'Exams';

    /**
     *  @name   ExamController
     *  @descr  Create an instance of ExamController class
     */
    public function ExamController(){}

    /**
     * @name    executeAction
     * @param   $action         String      Name of requested action
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

    /********************************************************************
     *                               Exam                               *
     ********************************************************************/

    /**
     *  @name   actionExams
     *  @descr  Show the list of exams
     */
    private function actionExams(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionShowexaminfo
     *  @descr  Shows all infos about requested exam
     */
    private function actionShowexaminfo(){
        global $log, $engine;

        if(isset($_POST['idExam'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdateexaminfo
     *  @descr  Saves edited informations about an exam
     */
    private function actionUpdateexaminfo(){
        global $ajaxSeparator, $config, $log;

        $db = new sqlDB();
        if((isset($_POST['idExam'])) && (isset($_POST['password']))){
            $newPassword = randomPassword(8);

            if($db->qUpdateExamInfo($_POST['idExam'], null, null, null, null, null, null, $newPassword)){
                echo 'ACK'.$ajaxSeparator.$newPassword;
            }else{
                die($db->getError());
            }

            $db->close();
        }elseif((isset($_POST['idExam'])) && (isset($_POST['name'])) && (isset($_POST['datetime'])) &&
                (isset($_POST['desc'])) && (isset($_POST['regStart'])) && (isset($_POST['regEnd'])) && (isset($_POST['rooms']))){
            $db = new sqlDB();
            if(($db->qUpdateExamInfo($_POST['idExam'], $_POST['name'], $_POST['datetime'], $_POST['desc'],
                                     $_POST['regStart'], $_POST['regEnd'], $_POST['rooms'])) && ($examInfo = $db->nextRowAssoc())){
                $statuses = array('w' => array('Waiting', 'Start'),
                                  's' => array('Started', 'Stop'),
                                  'e' => array('Stopped', 'Start'));
                $datetime = new DateTime($examInfo['datetime']);
                $day = $datetime->format("d/m/Y");
                $time = $datetime->format("H:i");
                $manage = '<span class="manageButton edit">
                               <img name="edit" src="'.$config['themeImagesDir'].'edit.png"title="'.ttEdit.'" onclick="showExamInfo(this);">
                           </span>
                           <span class="manageButton students">
                               <img name="students" src="'.$config['themeImagesDir'].'users.png" title="'.ttStudents.'" onclick="showStudentsList(this);">
                           </span>
                           <span class="manageButton action">
                               <img name="action" src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][1].'.png" title="'.constant('tt'.$statuses[$examInfo['status']][1]).'" onclick="changeExamStatus(new Array(true, this));">
                           </span>
                           <span class="manageButton archive">
                               <img name="archive" src="'.$config['themeImagesDir'].'Archive.png" title="'.ttArchive.'" onclick="archiveExam();">
                           </span>
                           ';

                $updatedExam = array(
                    '<img alt="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          title="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][0].'.png">',
                    $day,
                    $time,
                    $examInfo['exam'],
                    $examInfo['subject'],
                    $examInfo['settings'],
                    $examInfo['password'],
                    $manage,
                    $examInfo['idExam'],
                    $examInfo['idSubject'],
                    $examInfo['idTestSetting'],
                    $examInfo['status']
                );
                echo 'ACK'.$ajaxSeparator.str_replace('\\/', '/', json_encode($updatedExam));

            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShowstudentslist
     *  @descr  Shows the list of registered users for requested exam
     */
    private function actionShowregistrationslist(){
        global $log, $engine;

        if(isset($_POST['idExam'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewexam
     *  @descr  Action used to create a new exam
     */
    private function actionNewexam(){
        global $log, $config, $ajaxSeparator;

        if((isset($_POST['name'])) && (isset($_POST['idSubject'])) && (isset($_POST['idTestSettings'])) &&
           (isset($_POST['datetime'])) && (isset($_POST['desc'])) && (isset($_POST['regStart'])) &&
           (isset($_POST['regEnd'])) && (isset($_POST['rooms']))){

            $db = new sqlDB();
            $password = randomPassword(8);
            if(($db->qNewExam($_POST['name'], $_POST['idSubject'], $_POST['idTestSettings'], $_POST['datetime'],
                              $_POST['desc'], $_POST['regStart'], $_POST['regEnd'], $_POST['rooms'], $password)) && ($examInfo = $db->nextRowAssoc())){
                $statuses = array('w' => array('Waiting', 'Start'),
                                  's' => array('Started', 'Stop'),
                                  'e' => array('Stopped', 'Start'));
                $datetime = new DateTime($examInfo['datetime']);
                $day = $datetime->format("d/m/Y");
                $time = $datetime->format("H:i");
                $manage = '<span class="manageButton edit">
                               <img name="edit" src="'.$config['themeImagesDir'].'edit.png"title="'.ttEdit.'" onclick="showExamInfo(this);">
                           </span>
                           <span class="manageButton students">
                               <img name="students" src="'.$config['themeImagesDir'].'users.png" title="'.ttStudents.'" onclick="showStudentsList(this);">
                           </span>
                           <span class="manageButton action">
                               <img name="action" src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][1].'.png" title="'.constant('tt'.$statuses[$examInfo['status']][1]).'" onclick="changeExamStatus(new Array(true, this));">
                           </span>
                           <span class="manageButton archive">
                               <img name="archive" src="'.$config['themeImagesDir'].'Archive.png" title="'.ttArchive.'" onclick="archiveExam(new Array(true, this));">
                           </span>
                           ';

                $newExam = array(
                    '<img alt="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          title="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][0].'.png">',
                    $day,
                    $time,
                    $examInfo['exam'],
                    $examInfo['subject'],
                    $examInfo['settings'],
                    $examInfo['password'],
                    $manage,
                    $examInfo['idExam'],
                    $examInfo['idSubject'],
                    $examInfo['idTestSetting'],
                    $examInfo['status']
                );
                echo 'ACK'.$ajaxSeparator.str_replace('\\/', '/', json_encode($newExam));

            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__.' : Params not set - $_POST = '.var_export($_POST));
        }
    }

    /**
     *  @name   actionDeleteexam
     *  @descr  Deletes selected exam
     */
    private function actionDeleteexam(){
        global $log;

        if(isset($_POST['idExam'])){
            $db = new sqlDB();
            if($db->qDeleteExam($_POST['idExam'])){
                echo 'ACK';
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__.' : Params not set - $_POST = '.var_export($_POST));
        }
    }

    /**
     *  @name   actionChangestatus
     *  @descr  Starts and Stops requested exam
     */
    private function actionChangestatus(){
        global $log;

        if((isset($_POST['idExam'])) && (isset($_POST['action']))){
            $db = new sqlDB();
            if(($db->qSelect('Exams', 'idExam', $_POST['idExam'])) && ($exam = $db->nextRowAssoc())){
                switch($_POST['action']){
                    case 'start' :
                        if($exam['status'] == 'a'){
                            die(ttEExamArchived);
                        }elseif($db->qChangeExamStatus($_POST['idExam'], 's')){
                            echo 'ACK';
                        }else{
                            die($db->getError());
                        } break;
                    case 'stop' :
                        if($exam['status'] == 'w'){
                            die(ttEExamWaiting);
                        }elseif($exam['status'] == 'a'){
                            die(ttEExamArchived);
                        }elseif($db->qChangeExamStatus($_POST['idExam'], 'e')){
                            echo 'ACK';
                        }else{
                            die($db->getError());
                        }break;
                    default :
                        $log->append(__FUNCTION__." : action not set");
                }
            }else{
                die(ttEExamNotFound);
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionArchiveExam
     *  @descr  Archives requested exam
     */
    private function actionArchiveexam(){
        global $log;

        if(isset($_POST['idExam'])){
            $db = new sqlDB();
            if(($db->qSelect('Exams', 'idExam', $_POST['idExam'])) && ($examInfo = $db->nextRowAssoc())){
                if($examInfo['status'] == 'a'){
                    die(ttEExamArchived);
                }elseif(($db->qSelect("Tests", "fkExam", $_POST['idExam'])) && ($tests = $db->getResultAssoc('idTest'))){
                    if(($db->qSelect("TestSettings", "idTestSetting", $examInfo['fkTestSetting'])) && ($examSettings = $db->nextRowAssoc())){
                        $scale = $examSettings['scale'];
                        $allowNegative = ($examSettings['negative'] == 0)? false : true;
                        foreach($tests as $idTest => $testInfo){
                            try{
                                switch($testInfo['status']){
                                    case 'w': break;
                                    case 's': break;
                                    case 'b': if(!$db->qArchiveTest($idTest, $correctScores=array(), $scoreTest=null, $bonus='0', $scoreFinal='0', $scale=0.0, $allowNegative, $status=$testInfo['status'])){
                                                 try{
                                                    $db->qForceArchiveTest($idTest, $correctScores=array(), $scoreTest=null, $bonus='0', $scoreFinal='0', $scale=0.0, $allowNegative, $status=$testInfo['status']);
                                                 }catch(Exception $ex){} 
                                              }
                                              break;
                                    case 'e': if(!$db->qArchiveTest($idTest, $correctScores=array(), $testInfo['scoreTest'], $testInfo['bonus'], $scoreFinal=round($testInfo['scoreTest']+$testInfo['bonus']), $scale, $allowNegative)){
                                                 try{
                                                    $db->qForceArchiveTest($idTest, $correctScores=array(), $testInfo['scoreTest'], $testInfo['bonus'], $scoreFinal=round($testInfo['scoreTest']+$testInfo['bonus']), $scale, $allowNegative); 
                                                 }catch(Exception $ex){} 
                                              }
                                              break;
                                }                                
                            }catch(Exception $ex){
                                try{
                                    $log->append(__FUNCTION__." : errorr ".$ex." archiving ".$idTest);
                                }catch(Exception $ex){

                                }
                            }
                        }
                        if($db->qArchiveExam($_POST['idExam'])){
                            // la actionPrintcertificate controlla se bisogna generare il certificato, se non va 
                            // generato ritorna comunque ACK
                            //if($this->actionPrintcertificate($_POST['idExam'])=="ACK")
                                echo 'ACK';
                            //else 
                            //    echo ttExamArchivedCertNot; 
                        }else{
                            die("Type 3 error - ".$db->getError());
                        }
                    }
                }else{
                    // in questo caso esiste l'esame ma non ha alcuno studente che lo ha svolto -- aggiunta Damiano 28/07/17
                    if($db->qArchiveExam($_POST['idExam'])){
                        echo "ACK";                        
                    }else{
                        echo ttMExamArchived; 
                    }
                }
            }else{
                die(ttEExamNotFound);
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }


// ____________________________________________________________________________

/*
questa è la vecchia funzione che stampava i certificati per tutti gli esami del test, 
non viene più utilizzata
*/
    /**
     *  @name  actionPrintcertificate
     *  @descr Gets the information needed to create the certificate
     */
    /*
    private function actionPrintcertificate($idExam=null){

        global $log,$config;
        $var ="1";
        if($idExam!=null)  
            $_POST['idExam']=$idExam;
        if(isset($_POST['idExam'])){
            $var ="2";
            //$log->append('Entrato exam');
            $db = new sqlDB();
            if(($db->qSelect('Exams', 'idExam', $_POST['idExam'])) && ($examInfo = $db->nextRowAssoc())){
                $var ="3";
                $db->qGetCertificate($_POST['idExam']);
                $examCert = $db->nextRowAssoc();
                if($examCert['certificate'] == 1){
                    $var ="4";
                    if(($db->qSelect("Tests", "fkExam", $_POST['idExam'])) && ($tests = $db->getResultAssoc('idTest'))) {
                        $var ="5";
                        $date = strtotime($examInfo['datetime']);
                        $month = date('F',$date);
                        $year = date('Y',$date);
                        $dateCert = date('F jS, Y', $date);
                        $dateName = date('Ymd',$date);
                        include($config['systemPhpGraphLibDir'] . 'phpgraphlib.php');
                        include($config['systemFpdfDir'] . 'fpdf.php');
                        $db->qGetSubjectExam($_POST['idExam']);
                        $nameSubject = $db->nextRowAssoc();
                        $var ="6";
                        foreach ($tests as $idTest => $testInfo) {
                            if ($testInfo['status'] == 'a') {
                                if(($testInfo['scoreFinal'] != '')&&($testInfo['scoreFinal'] != 0)) {
                                    $result = $this->calcResult($testInfo);
                                    if ($result != 'NOTPASS') {
                                        $db->qGetUserTest($testInfo['idTest']);
                                        $userInfo = $db->nextRowAssoc();
                                        $userSurname = iconv('UTF-8', 'ISO-8859-1', $userInfo['surname']);
                                        $userName = iconv('UTF-8', 'ISO-8859-1', $userInfo['name']);
                                        if (($userName == "")|| ($userSurname == "")){
                                            $userName = iconv('UTF-8', 'ISO-8859-2', $userInfo['name']);
                                            $userSurname = iconv('UTF-8', 'ISO-8859-2', $userInfo['surname']);
                                        }
                                        $subject = trim(substr($nameSubject['name'],0,strpos($nameSubject['name'],'-')-1));


                                        $this->createCertificate($result, $subject, $userName, $userSurname,
                                            $userInfo['email'],$userInfo['NameGroup'],$userInfo['NameSubGroup'], $dateCert,$dateName,$month,$year);
                                        $var=$var."Sono dentro !";
                                    }
                                }
                            }
                        }
                    }
                }

            }else{
                die(ttEExamNotFound);
            }
        }else{
            $log->append(__FUNCTION__." :( Params not set");
        }
        return "ACK";
    }
*/
// ____________________________________________________________________________


    /**
     *  @name  actionPrintcertificate
     *  @descr Gets the information needed to create the certificate
     */
    private function actionPrintcertificate(){
        global $log,$config,$user;
                                
        if(1==2){
        //if($user->email!="damiano.perri@gmail.com"){
                echo json_encode(array("problem","For now, the function is reserved for administrators"));
                return;
        }
        if(!file_exists("../firme/".$user->surname."-".$user->name.".png")){
            echo json_encode(array("problem","Operation not permitted"));
            return;
        }   


        $var ="1";
        if($_POST["idTest"]==null) {
                echo json_encode(array("problem","idTest null"));
                return;
            }
        $db = new sqlDB();
        if(($db->qSelect("Tests", "idTest", $_POST['idTest'])) && ($testInfo = $db->nextRowAssoc())) {
            $var ="5";
            //$date = strtotime($examInfo['datetime']);
            $date = strtotime($testInfo['timeStart']);
            $month = date('F',$date);
            $year = date('Y',$date);
            $dateCert = date('F jS, Y', $date);
            $dateName = date('Ymd',$date);
            include($config['systemPhpGraphLibDir'] . 'phpgraphlib.php');
            include($config['systemFpdfDir'] . 'fpdf.php');
            $db->qGetSubjectExam($testInfo["fkExam"]);
            $nameSubject = $db->nextRowAssoc();

            $var ="6";
            if ($testInfo['status'] == 'a') {
                if(($testInfo['scoreFinal'] != '')&&($testInfo['scoreFinal'] != 0)) {
                    $result = $this->calcResult($testInfo);       
                    if ($result != 'NOTPASS') {
                        $db->qGetUserTest($testInfo['idTest']);
                        $userInfo = $db->nextRowAssoc();
                        
                        $dbU1 = new sqlDB();
                        if(($dbU1->qSelect("Users", "idUser", $user->id)) && ($teacherInfoU1 = $dbU1->nextRowAssoc())){
                            if(!(
                                $userInfo['group']==$teacherInfoU1['group'] 
                                && 
                                $userInfo['idSubGroup']==$teacherInfoU1['subgroup'])
                                ){
                                echo json_encode(array("problem","Operation not permitted"));
                                return;
                                
                            }
                        }
                        else{
                            echo json_encode(array("problem","Operation not permitted"));                            
                            return;
                            
                        }
                        $userName=$userInfo['name'];
                        $userSurname=$userInfo['surname'];
                        if (strpos($nameSubject['name'], ' - ') !== false) {
                            //echo 'true';
                           $subject = trim(substr($nameSubject['name'],0,strpos($nameSubject['name'],'-')-1));
                        }else{
                           $subject = $nameSubject['name'];
                        }                        
                        try{

                            $subjectArrayTEMP = explode(' ', $subject);
                            $subjectTEMP = substr($subjectArrayTEMP[0],0,1).substr($subjectArrayTEMP[1],0,1).$subjectArrayTEMP[2];
                        
                            $fileNameCertificate = $dateName."--".$userName."--".$userSurname."--".$userInfo['email']."--".$subjectTEMP."--".$userInfo['NameGroup']."--".$userInfo['NameSubGroup'].".pdf";
                            $dirNewCertificate = $config['systemViewsDir']."Certificates/new";
                            $fileCertificatePDF=$dirNewCertificate."/".$fileNameCertificate;

                            
                            if (file_exists($fileCertificatePDF)) {
                                if(!copy($fileCertificatePDF, "temp/".$fileNameCertificate)){
                                    echo json_encode(array("problem","Internal server error"));
                                    return;
                                }else{
                                    echo json_encode(array("success","temp/".$fileNameCertificate));
                                    return;
                                }
                                
                                
                                /*
                                if($this->sendEmail($fileCertificatePDF,$userInfo['email'])=="ACK")
                                    die("ACK");
                                else die(ttEmail." problem");
                                */
                            }
                        }catch(Exception $ex){

                        }

                        $pathFile=$this->createCertificate($result, 
                            $subject, 
                            $userName, 
                            $userSurname,
                            $userInfo['email'],
                            $userInfo['NameGroup'],
                            $userInfo['NameSubGroup'], 
                            $dateCert,
                            $dateName,
                            $month,
                            $year,
                            $userInfo['Description']);
                        if(pathFile!="NACK" && pathFile.trim()!=""){
                            echo json_encode(array("success",$pathFile));
                            return;
                            /*if($this->sendEmail($pathFile,$userInfo['email'])=="ACK"){

                            }*/
                            //else die(json_encode(array("success",$fileCertificatePDF)));
                        }
                        else{
                            echo json_encode(array("problem",ttCertificateGeneratedProblem));
                            return;
                        }                        
                    }else{
                        echo json_encode(array("problem",ttCertificatePoint));
                        return;
                    }
                }else{
                    echo json_encode(array("problem",ttCertificatePoint));
                    return;
                }
            }else{
                echo json_encode(array("problem",ttError." - -  the test need to be archieved"));
                return;
            }
        }else{
            echo json_encode(array("problem",ttEExamNotFound));
            return;
        }  
        echo json_encode(array("problem","unexpected error"));   
        return;
    }

    /**
     *  @name  sendMail
     *  @descr sendEmail to student
     */
    private function sendEmail($pathFile,$emailStudente){
        global $config,$user;

        if($pathFile=="" || $emailStudente=="") return "NACK";

        $mailto=$emailStudente;
        $subject="Proficiency Certificate";
        $file=$pathFile;
        $filename="Certificate.pdf";
        $message="Enclosed, you will find the certification of your exam";
        $content = file_get_contents($file);
        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $name = basename($file);        
        
        // header
        $header = "From: ".$user->email."\r\n";
        //$header = 'From: '.$config['systemTitle'].' <'.$config['systemEmail'].'>\r\n';

        $header .= "Reply-To: ".$user->email."\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

        // message & attachment
        $nmessage = "--".$uid."\r\n";
        $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
        $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $nmessage .= $message."\r\n\r\n";
        $nmessage .= "--".$uid."\r\n";
        $nmessage .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
        $nmessage .= "Content-Transfer-Encoding: base64\r\n";
        $nmessage .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
        $nmessage .= $content."\r\n\r\n";
        $nmessage .= "--".$uid."--";
        //die($mailto." ".$subject." ".$file." ".$nmessage." ".$header);
        if (mail($mailto, $subject, $nmessage, $header)) {
            return "ACK"; 
        } else {
          return "NACK";
        }
    }


    /**
     *  @name  calcResult
     *  @descr Calculates the result of a given test
     */
    private function calcResult($testInfo){
        $db = new sqlDB();
        $db->qcalcScore($testInfo['idTest']);
        $maxScore = $db->nextRowAssoc();
        $percentScore = $testInfo['scoreFinal'] / $maxScore['scoreType'] * 100 ;
        if ($percentScore < 30){
            return 'NOTPASS';
        }elseif(($percentScore >= 30) && ($percentScore < 50)){
            return 'PASS';
        }elseif(($percentScore >= 50) && ($percentScore < 70)){
            return 'OPTIMUM';
        }elseif($percentScore >= 70){
            return 'EXCELLENT';
        }
    }

    private function replaceCharacter($word) {
        $word = str_replace("@","%40",$word);
        $word = str_replace("`","%60",$word);
        $word = str_replace("¢","%A2",$word);
        $word = str_replace("£","%A3",$word);
        $word = str_replace("¥","%A5",$word);
        $word = str_replace("|","%A6",$word);
        $word = str_replace("«","%AB",$word);
        $word = str_replace("¬","%AC",$word);
        $word = str_replace("¯","%AD",$word);
        $word = str_replace("º","%B0",$word);
        $word = str_replace("±","%B1",$word);
        $word = str_replace("ª","%B2",$word);
        $word = str_replace("µ","%B5",$word);
        $word = str_replace("»","%BB",$word);
        $word = str_replace("¼","%BC",$word);
        $word = str_replace("½","%BD",$word);
        $word = str_replace("¿","%BF",$word);
        $word = str_replace("À","%C0",$word);
        $word = str_replace("Á","%C1",$word);
        $word = str_replace("Â","%C2",$word);
        $word = str_replace("Ã","%C3",$word);
        $word = str_replace("Ä","%C4",$word);
        $word = str_replace("Å","%C5",$word);
        $word = str_replace("Æ","%C6",$word);
        $word = str_replace("Ç","%C7",$word);
        $word = str_replace("È","%C8",$word);
        $word = str_replace("É","%C9",$word);
        $word = str_replace("Ê","%CA",$word);
        $word = str_replace("Ë","%CB",$word);
        $word = str_replace("Ì","%CC",$word);
        $word = str_replace("Í","%CD",$word);
        $word = str_replace("Î","%CE",$word);
        $word = str_replace("Ï","%CF",$word);
        $word = str_replace("Ð","%D0",$word);
        $word = str_replace("Ñ","%D1",$word);
        $word = str_replace("Ò","%D2",$word);
        $word = str_replace("Ó","%D3",$word);
        $word = str_replace("Ô","%D4",$word);
        $word = str_replace("Õ","%D5",$word);
        $word = str_replace("Ö","%D6",$word);
        $word = str_replace("Ø","%D8",$word);
        $word = str_replace("Ù","%D9",$word);
        $word = str_replace("Ú","%DA",$word);
        $word = str_replace("Û","%DB",$word);
        $word = str_replace("Ü","%DC",$word);
        $word = str_replace("Ý","%DD",$word);
        $word = str_replace("Þ","%DE",$word);
        $word = str_replace("ß","%DF",$word);
        $word = str_replace("à","%E0",$word);
        $word = str_replace("á","%E1",$word);
        $word = str_replace("â","%E2",$word);
        $word = str_replace("ã","%E3",$word);
        $word = str_replace("ä","%E4",$word);
        $word = str_replace("å","%E5",$word);
        $word = str_replace("æ","%E6",$word);
        $word = str_replace("ç","%E7",$word);
        $word = str_replace("è","%E8",$word);
        $word = str_replace("é","%E9",$word);
        $word = str_replace("ê","%EA",$word);
        $word = str_replace("ë","%EB",$word);
        $word = str_replace("ì","%EC",$word);
        $word = str_replace("í","%ED",$word);
        $word = str_replace("î","%EE",$word);
        $word = str_replace("ï","%EF",$word);
        $word = str_replace("ð","%F0",$word);
        $word = str_replace("ñ","%F1",$word);
        $word = str_replace("ò","%F2",$word);
        $word = str_replace("ó","%F3",$word);
        $word = str_replace("ô","%F4",$word);
        $word = str_replace("õ","%F5",$word);
        $word = str_replace("ö","%F6",$word);
        $word = str_replace("÷","%F7",$word);
        $word = str_replace("ø","%F8",$word);
        $word = str_replace("ù","%F9",$word);
        $word = str_replace("ú","%FA",$word);
        $word = str_replace("û","%FB",$word);
        $word = str_replace("ü","%FC",$word);
        $word = str_replace("ý","%FD",$word);
        $word = str_replace("þ","%FE",$word);
        $word = str_replace("ÿ","%FF",$word);        
        $word = str_replace("ő","o",$word);    
        $word = str_replace("Ż","Z",$word);    
        $word = str_replace("ł","l",$word);    
        $word = str_replace("ą","a",$word);
        $word = str_replace("’","'",$word);
        $word = str_replace("ę","e",$word);        
        $word = str_replace("Ś","S",$word);
        $cyr = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
            'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];
        $lat = [
            'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
            'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
        ];
        $word = str_replace($cyr, $lat, $word);
        return $word;
    }



    /**
     *  @name   actionCcertificate
     *  @descr  Creates the certificate
     */
    private function createCertificate($result,$nameSubject,$userName, 
        $userSurname,$email,$group,$subgroup,$date,$dateName,$month,$year,$subgroupDescription){
        global $config,$user;
        //die($userName." ".$userSurname);
        if($group==null) $group="defaultGroup";
        else{
            $group=$group.trim();
        }
        if($subgroup==null) $subgroup="defaultSubGroup";
        else{
            $subgroup=$subgroup.trim();
        }
        try{
            $pdf = new FPDF();
            $pdf->AddPage('L');
            //Logo + Titolo
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,40,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->SetFont('Helvetica','B',30);
            //$pdf->Cell(215,40,ttProfCert,0,1,'C',true);
                $pdf->Cell(215,40,"Proficiency Certificate",0,1,'C',true);
            $pdf->Image("themes/default/images/ECTN2.png",50,15);
            //--------------------------------------
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,7,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->SetFont('Helvetica','',20);
            //$pdf->Cell(215,7,ttCertify,0,1,'C',true);
                $pdf->Cell(215,7,"This is to certify that",0,1,'C',true);
            //---------------------------------------
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,7,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->Cell(215,7,'',0,1,'C',true);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,17,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color

            
            $userSurnameText=strtoupper(urldecode($this->replaceCharacter($userSurname)));
            $userNameText=strtoupper(urldecode($this->replaceCharacter($userName)));
            $pdf->SetFont('Helvetica','B',20);

            $pdf->Cell(215,17,$userSurnameText.' '.$userNameText,0,1,'C',true);
            $pdf->SetFont('Helvetica','',20);
            //---------------------------------------
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,10,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->SetFont('Helvetica','',20);
            //$pdf->Cell(215,10,ttSuccess,0,1,'C',true);
                $pdf->Cell(215,10,"has successfully passed",0,1,'C',true);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,10,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->SetFont('Helvetica','B',20);
            //$pdf->Cell(215,10, ttEvaluation.' '.$result,0,1,'C',true);
                $pdf->Cell(215,10, "with evaluation".' '.$result,0,1,'C',true);
            $pdf->SetFont('Helvetica','',20);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,10,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->SetFont('Helvetica','B',22);

            $nomeIstituzione="EChemTest"; // echemtest o EOL
            if($config['dbName']=="EOL") $nomeIstituzione="LibreEOL";

            $pdf->Cell(215,10,$nomeIstituzione.iconv("UTF-8", "ISO-8859-1", "®")." ".$nameSubject,0,1,'C',true);
	    $pdf->SetFillColor(255,255,255);
            //$pdf->Cell(35,10,'',0,0,'C',true);
           // $pdf->SetFillColor(194,206,255);// background color
            //$pdf->Cell(215,10,'',0,1,'C',true);
            //$pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,10,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->SetFont('Helvetica','',17);
            //$pdf->Cell(215,10,ttAccreditedTestSite.' '.$subgroup,0,1,'C',true);
// AGGIUNTA PER IL TESTO DESCRITTIVO DEL LUOGO 
            $subgroupDescriptionText=urldecode($this->replaceCharacter($subgroupDescription));
            $pdf->Cell(215,10,"in the Accredited Test Site of the",0,1,'C',true);  
            $pdf->SetFillColor(255,255,255);           
            $pdf->Cell(35,10,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color

            if(strlen($subgroupDescriptionText)<=65){                
                $pdf->SetFont('Helvetica','',17);
                $pdf->Cell(215,10,$subgroupDescriptionText,0,1,'C',true); 
            }else{                
                $pdf->SetFont('Helvetica','',17);
                $pdf->Cell(215,10,$subgroupDescriptionText,0,1,'C',true); 
            }            //--------------------------------------
                //$pdf->Cell(215,10,"in the Accredited Test Site of the".' '.$subgroupDescription,0,1,'C',true);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,10,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->SetFont('Helvetica','',17);
            //$pdf->Cell(215,10,ttOn.' '.$date,0,1,'C',true);
                $pdf->Cell(215,10,"on".' '.$date,0,1,'C',true);
	    $pdf->SetFont('Helvetica','',20);

            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,15,'',0,0,'C',true);
            $pdf->SetFillColor(194,206,255);// background color
            $pdf->Cell(215,15,'',0,1,'C',true);
            $pdf->SetFillColor(255,255,255);

            $pdf->SetFillColor(194,206,255);// background color
            
            $pdf->SetFont('Helvetica','',13);
            $pdf->Cell(35);
            $utente=iconv("UTF-8","ISO-8859-1","Prof. Antonio Laganà");
            $pdf->SetY(144); //154
            $pdf->SetX(205);
            $pdf->Cell(50,10,$utente,0,1,'R',true); // scritta di Antonio LAGANA
            $pdf->Cell(35);
            
            
            $pdf->Cell(35);
            $pdf->SetY(144); //154
            $pdf->SetX(52);

            $teacherName=ucfirst(urldecode($this->replaceCharacter($user->name)));
            $teacherSurname=ucfirst(urldecode($this->replaceCharacter($user->surname)));
            $utente="Prof. ".$teacherName." ".$teacherSurname;
            //$utente=iconv("UTF-8","ISO-8859-1","Dott. ".ucfirst ($user->name)." ".ucfirst ($user->surname));
            $pdf->Cell(50,10,$utente,0,1,'L',true); // NOME PROFESSORE
            $pdf->Cell(35);
            $pdf->Cell(215,35,"",0,1,'R',true);
            $pdf->Image("../firme/Lagana-trasp.png",196,157,70,32); // FIRMA DI LAGANA
            $pdf->Image("../firme/".$user->surname."-".$user->name.".png",51,157,60,32); // FIRMA PROFESSORE

            $pdf->SetY(144);
            $pdf->SetX(140);


            $year = date("Y");
            $db = new sqlDB();
            $number=1;
            try{
                if($db->qSelect('Certificates', 'year', $year)){
                    if($db!=null){
                        try{
                            $number=count($db->getResultAssoc('idCertificate'));
                            $number++;
                        }catch(Exception $ex){
                            $number=1;
                        }
                        try{
                            $db = new sqlDB();
                            if(!$db->qInsertCertificateYear($year)){
                                return "NACK";
                            }
                        }catch(Exception $ex){
                                return "NACK";
                        }
                    }else{
                        $number=1;
                    }
                }
            }catch(Exception $ex){
                $number=1;
            }
            if($number<10){
                $number="000".$number;
            }else if($number<100){
                $number="00".$number;
            }else if($number<1000){
                $number="0".$number;
            }
            // qui aggiungere il nuovo codice per carlo manuali
            $stringaContatore="";//"MUP"
            /*try{
                if($group=="ntc.it"){
                    $stringaContatore="MU".strtoupper(substr($subgroup, strpos($subgroup, ".") + 1)[0]);
                }else{
                    $stringaContatore=strtoupper(substr($group, strpos($group, ".") + 1));
                }
            }catch(Exception $ex){
                $stringaContatore="MUP";
            }*/
            try{
                $groupExplode=explode(".", $group)[1];
                $stringaContatore.=$groupExplode;
                $subStrTemp=explode(".", $subgroup);
                if(isset($subStrTemp[1][0])){
                    $stringaContatore.=$subStrTemp[1][0];          
                    if(isset($subStrTemp[1][1])) $stringaContatore.=$subStrTemp[1][1];
                    if(isset($subStrTemp[1][2])) $stringaContatore.=$subStrTemp[1][2];
                }//es: ntc.it and ats.perugia.subperugia.it
                if(isset($subStrTemp[2]) && $subStrTemp[2]!=$groupExplode){
                    if(isset($subStrTemp[2][0])){
                        $stringaContatore.=$subStrTemp[2][0];
                        if(isset($subStrTemp[2][2]))  $stringaContatore.=$subStrTemp[2][2];            
                    }
                }
                $stringaContatore=strtoupper($stringaContatore);
            }catch(Exception $ex){
                $stringaContatore="CODE";
            }
            $pdf->Cell(40,10,$stringaContatore.$number."-".$year,0,1,'L',true); // CODICE PROGRESSIVO

            //$pdf->Cell(40,10,"MUP".$number."-".$year,0,1,'L',true); // CODICE PROGRESSIVO
       

            $pdf->SetY(152); //154            
            $pdf->SetX(195);
            $pdf->Cell(60,6,"ECTN Virtual Education Committee",0,1,'R',true);

            $pdf->SetY(152); //154            
            $pdf->SetX(62);
            $pdf->Cell(60,6,"ECTN Virtual Education Committee",0,1,'R',true);


            //Creates the Certificate folder if not exists
            $dir = $config['systemViewsDir']."Certificates";
            if (file_exists($dir)==false){
                mkdir($config['systemViewsDir']."Certificates");
            }
            //Creates the group subfolder if not exists
            $dir = $config['systemViewsDir']."Certificates/".$group;
            if (file_exists($dir)==false){
                mkdir($config['systemViewsDir']."Certificates/".$group);
            }

            //Creates the subgroup subfolder if not exists
            $dir = $config['systemViewsDir']."Certificates/".$group."/".$subgroup;
            if (file_exists($dir)==false){
                mkdir($config['systemViewsDir']."Certificates/".$group."/".$subgroup);
            }
            //Creates the subject subfolder if not exists
            $dir = $config['systemViewsDir']."Certificates/".$group."/".$subgroup."/".$nameSubject;
            if (file_exists($dir)==false){
                mkdir($config['systemViewsDir']."Certificates/".$group."/".$subgroup."/".$nameSubject);
            }
            //Creates the new subfolder if not exists
            $dirNew = $config['systemViewsDir']."Certificates/new";
            if (file_exists($dirNew)==false){
                mkdir($config['systemViewsDir']."Certificates/new");
            }
            //Creates the subfolder Date
            $dir = $config['systemViewsDir']."Certificates/".$group."/".$subgroup."/".$nameSubject."/".$month.$year;
            if (file_exists($dir)==false){
                mkdir($config['systemViewsDir']."Certificates/".$group."/".$subgroup."/".$nameSubject."/".$month.$year);
            }

            $subjectArray = explode(' ', $nameSubject);
            $subject = substr($subjectArray[0],0,1).substr($subjectArray[1],0,1).$subjectArray[2];
            $fileName = $dateName."--".$userName."--".$userSurname."--".$email."--".$subject."--".$group."--".$subgroup;

            //Creates the certificate
            $pdf->Output($dir."/".$fileName.".pdf","F");
            $pdf->Output($dirNew."/".$fileName.".pdf","F");
            $pdf->Output("temp"."/".$fileName.".pdf","F");
            //return $dirNew."/".$fileName.".pdf";
            return "temp"."/".$fileName.".pdf";
        }catch(Exception $ex){
            return "NACK";
            //$log->append(__FUNCTION__." : ".$this->getError());
        }
    }

    /********************************************************************
     *                          Test Settings                           *
     ********************************************************************/

    /**
     *  @name   actionSettings
     *  @descr  Show the list of settings for selected subject
     */
    private function actionSettings(){
        global $config, $engine,$user;

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
            if($user->role=='e' || $user->role=='er')
                header('Location: index.php?page=subject/index2&r=set');
            else
                header('Location: index.php?page=subject&r=set');

        }
    }

        /**
     *  @name   actionResetSessionTestSettings
     *  @descr  Reset Value
     */
    private function actionResetSessionTestSettings(){
        global $log, $engine,$config, $user;

        unset($_POST['idSubject']);
        unset($_SESSION['idSubject']);

    }



    /**
     *  @name   actionShowsettingsinfo
     *  @descr  Show info about an exam's settings
     */
    private function actionShowsettingsinfo(){
        global $engine, $log;

        if((isset($_POST['idTestSetting'])) && (isset($_POST['action']))){
            $engine->loadLibs();
            $engine->renderPage();

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShownewsettingsinfo
     *  @descr  Show info about an exam's settings
     */
    private function actionShownewsettingsinfo(){
        global $engine, $log;

        if((isset($_POST['idTestSetting'])) && (isset($_POST['action']))){
            $engine->loadLibs();
            $engine->renderPage();

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdatesettingsinfo
     *  @descr  Save edited informations about a test settings
     */
    private function actionUpdatesettingsinfo(){
        global $ajaxSeparator, $log,$user;

        if((isset($_POST['idTestSetting'])) && (isset($_POST['name'])) && (isset($_POST['scoreType'])) &&
            (isset($_POST['scoreMin'])) && (isset($_POST['bonus'])) && (isset($_POST['duration'])) &&
            (isset($_POST['negative'])) &&(isset($_POST['editable'])) && (isset($_POST['certificate'])) &&
            (isset($_POST['questions'])) && (isset($_POST['desc'])) && (isset($_POST['easy'])) &&
            (isset($_POST['medium'])) && (isset($_POST['hard'])) &&
            (isset($_POST['topicQuestionsId'])) && (isset($_POST['topicQuestionsValue'])) &&
            (isset($_POST['mandatQuestionsI'])) &&(isset($_POST['completeUpdate']))){

            $db = new sqlDB();
            if($_POST['completeUpdate'] == 'true'){
                if($db->qGetEditAndDeleteConstraints('edit', 'testsetting', array($_POST['idTestSetting']))){
                    if($db->numResultRows() > 0){
                        die(ttEExamsNotArchivedEditTestSettings);
                    }else{
                        $numQuestions = $_POST['questions'];
                        $numDifficulty = array(
                            0 => $_POST['easy'],
                            1 => $_POST['medium'],
                            2 => $_POST['hard']
                        );
                        $topicQuestionsId = json_decode($_POST['topicQuestionsId'], true);
                        $topicQuestionsValue = json_decode($_POST['topicQuestionsValue'], true);
                        $numMaxE = json_decode($_POST['numMaxE'], true);
                        $numMaxM = json_decode($_POST['numMaxM'], true);
                        $numMaxH = json_decode($_POST['numMaxH'], true);
                        $numTopics = count($topicQuestionsValue);
                        $matrixMaxQuestions = array(array());
                        for($j=0; $j<$numTopics; $j++) $matrixMaxQuestions[$j][0] = $numMaxE[$j];
                        for($j=0; $j<$numTopics; $j++) $matrixMaxQuestions[$j][1] = $numMaxM[$j];
                        for($j=0; $j<$numTopics; $j++) $matrixMaxQuestions[$j][2] = $numMaxH[$j];
                        $mandatQuestionsI = explode('&', $_POST['mandatQuestionsI']);
                        //Gets the informations about mandatory questions selected
                        $db = new sqlDB();
                        $db->getMandatQuestionsInfo($mandatQuestionsI);
                        while ($row = $db->nextRowAssoc()) {
                            $numQuestions--;
                            for ($i= 0; $i<3;$i++){
                                if ($i+1 == $row['difficulty'] ){$numDifficulty[$i]--;}
                            }
                            for ($i = 0; $i<$numTopics; $i++){
                                if ($topicQuestionsId[$i] == $row['fkTopic']){
                                    if ($topicQuestionsValue[$i]>0){$topicQuestionsValue[$i]--;}
                                }
                            }
                        }
                        $topicRequired = false;
                        for ($i = 0; $i < $numTopics;$i++){
                            if ($topicQuestionsValue[$i] > 0 ){
                                $topicRequired = true;
                            }
                        }
                        $matrixDistribution = array(array());
                        for($j=0; $j<3; $j++){
                            for ($i=0; $i<$numTopics; $i++){
                                $matrixDistribution[$i][$j] = 0;
                            }
                        }
                        for($j=0; $j<$numTopics; $j++) $matrixDistribution[$j][3] = $topicQuestionsId[$j];
                        $dCoefficients = array();
                        // Calculates the distribution coefficients
                        $dCoefficients[0] = $numDifficulty[0] / $numQuestions;
                        $dCoefficients[1] = $numDifficulty[1] / $numQuestions;
                        $dCoefficients[2] = $numDifficulty[2] / $numQuestions;

                        if($this->checkIntegrity($numQuestions,$numTopics,$dCoefficients,$topicQuestionsValue,$matrixMaxQuestions)) {
                            if ($topicRequired) {
                                $tempResult = $this->calcTopicDistribution($numQuestions, $numDifficulty, $topicQuestionsId, $topicQuestionsValue, $matrixMaxQuestions, $matrixDistribution, $dCoefficients);
                                $matrixDistribution = $tempResult[0];
                                $matrixMaxQuestions = $tempResult[1];
                                $numDifficulty = $tempResult[3];
                                $numQuestions = $tempResult[4];
                            }
                            if ($numQuestions > 0) {
                                $matrixDistribution = $this->calcFinalDistribution($numQuestions, $numTopics, $numDifficulty, $matrixMaxQuestions, $matrixDistribution, $dCoefficients);
                            }
                            /*
                            $db = new sqlDB();
                            $questionsDistribution = array();
                            if($db->qQuestions($_SESSION['idSubject'], '-1')) {
                                while ($question = $db->nextRowAssoc()) {
                                    array_push($questionsDistribution,$question['idQuestion']);
                                }
                            }
                            */
                            $db->close();
                            $db = new sqlDB();
                            //if($db->qUpdateTestSettingsInfo($questionsDistribution,$_POST['idTestSetting'], $_POST['completeUpdate'],
                            if($db->qUpdateTestSettingsInfo($_POST['idTestSetting'], $_POST['completeUpdate'],
                                $_POST['name'], $_POST['desc'], $_POST['scoreType'], $_POST['scoreMin'],
                                $_POST['bonus'], $_POST['negative'], $_POST['editable'],$_POST['certificate'],
                                $_POST['duration'], $_POST['questions'],$_POST['easy'],$_POST['medium'],$_POST['hard'],
                                $matrixDistribution, $mandatQuestionsI, $numTopics,$user->id,$user->group,$user->subgroup)){
                                echo 'ACK'.$ajaxSeparator.'ACK';
                                
                            }else{
                                echo $db->getError();
                            }
                        }

                    }
                    $db->close();
                }
            }else{
                if($db->qUpdateTestSettingsInfo($_POST['idTestSetting'], $_POST['completeUpdate'],
                                                $_POST['name'], $_POST['desc'])){
                    echo 'ACK'.$ajaxSeparator.'ACK';
                }else{
                    echo $db->getError();
                }
                $db->close();
            }
        }else{
            $log->append(__FUNCTION__.' : Params not set - $_POST = '.var_export($_POST, true));
        }
    }

    /**
     *  @name   actionNewsettings
     *  @descr  Show page to create a new test settings
     */
    private function actionNewsettings(){
        global $engine, $log, $ajaxSeparator,$user;
        $log->append("Inizio creazione nuovo test settings");
        if((isset($_POST['name'])) && (isset($_POST['scoreType'])) &&
            (isset($_POST['scoreMin'])) && (isset($_POST['bonus'])) &&
            (isset($_POST['negative'])) &&(isset($_POST['editable'])) && (isset($_POST['certificate'])) &&
            (isset($_POST['duration'])) && (isset($_POST['questions'])) &&
            (isset($_POST['easy'])) &&(isset($_POST['medium'])) && (isset($_POST['hard'])) &&
            (isset($_POST['topicQuestionsId'])) && (isset($_POST['topicQuestionsValue'])) &&
            (isset($_POST['mandatQuestionsI'])) &&
            (isset($_SESSION['idSubject']))){

            $numQuestions = $_POST['questions'];
            $numDifficulty = array(
                0 => $_POST['easy'],
                1 => $_POST['medium'],
                2 => $_POST['hard']
            );

            $log->append(">>>>>>>>>>>>>>>>>>>> 2");
            $topicQuestionsId = json_decode($_POST['topicQuestionsId'], true);
            $topicQuestionsValue = json_decode($_POST['topicQuestionsValue'], true);
            $numMaxE = json_decode($_POST['numMaxE'], true);
            $numMaxM = json_decode($_POST['numMaxM'], true);
            $numMaxH = json_decode($_POST['numMaxH'], true);
            $numTopics = count($topicQuestionsValue);
            $matrixMaxQuestions = array(array());
            for($j=0; $j<$numTopics; $j++) $matrixMaxQuestions[$j][0] = $numMaxE[$j];
            for($j=0; $j<$numTopics; $j++) $matrixMaxQuestions[$j][1] = $numMaxM[$j];
            for($j=0; $j<$numTopics; $j++) $matrixMaxQuestions[$j][2] = $numMaxH[$j];
            $mandatQuestionsI = explode('&', $_POST['mandatQuestionsI']);
            $log->append(">>>>>>>>>>>>>>>>>>>> 3");
            //Gets the informations about mandatory questions selected
            $db = new sqlDB();
            $db->getMandatQuestionsInfo($mandatQuestionsI);
            while ($row = $db->nextRowAssoc()) {
                $numQuestions--;
                for ($i= 0; $i<3;$i++){
                    if ($i+1 == $row['difficulty'] ){$numDifficulty[$i]--;}
                }
                for ($i = 0; $i<$numTopics; $i++){
                    if ($topicQuestionsId[$i] == $row['fkTopic']){
                        if ($topicQuestionsValue[$i]>0){$topicQuestionsValue[$i]--;}
                    }
                }
            }
            $log->append(">>>>>>>>>>>>>>>>>>>> 4");
            $topicRequired = false;
            for ($i = 0; $i < $numTopics;$i++){
                if ($topicQuestionsValue[$i] > 0 ){
                    $topicRequired = true;
                }
            }
            $log->append(">>>>>>>>>>>>>>>>>>>> 5");
            $matrixDistribution = array(array());
            for($j=0; $j<3; $j++){
                for ($i=0; $i<$numTopics; $i++){
                    $matrixDistribution[$i][$j] = 0;
                }
            }
            $log->append(">>>>>>>>>>>>>>>>>>>> 6");
            for($j=0; $j<$numTopics; $j++) $matrixDistribution[$j][3] = $topicQuestionsId[$j];
            $dCoefficients = array();
            // Calculates the distribution coefficients
            $dCoefficients[0] = $numDifficulty[0] / $numQuestions;
            $dCoefficients[1] = $numDifficulty[1] / $numQuestions;
            $dCoefficients[2] = $numDifficulty[2] / $numQuestions;
            $log->append(">>>>>>>>>>>>>>>>>>>> 7");
            if($this->checkIntegrity($numQuestions,$numTopics,$dCoefficients,$topicQuestionsValue,$matrixMaxQuestions)) {
                if ($topicRequired) {
                    $log->append(">>>>>>>>>>>>>>>>>>>> 8");
                    $tempResult = $this->calcTopicDistribution($numQuestions, $numDifficulty, $topicQuestionsId, $topicQuestionsValue, $matrixMaxQuestions, $matrixDistribution,$dCoefficients);
                    $matrixDistribution = $tempResult[0];
                    $matrixMaxQuestions = $tempResult[1];
                    $numDifficulty = $tempResult[3];
                    $numQuestions = $tempResult[4];
                    $log->append(">>>>>>>>>>>>>>>>>>>> 9");
                }
                if ($numQuestions > 0) {
                    $log->append(">>>>>>>>>>>>>>>>>>>> 10");
                    $matrixDistribution = $this->calcFinalDistribution($numQuestions, $numTopics, $numDifficulty, $matrixMaxQuestions, $matrixDistribution, $dCoefficients);
                    $log->append(">>>>>>>>>>>>>>>>>>>> 11");
                }
                /*
                $db = new sqlDB();
                $questionsDistribution = array();
                $log->append(">>>>>>>>>>>>>>>>>>>> 12");
                if($db->qQuestions($_SESSION['idSubject'], '-1')) {
                    $log->append(">>>>>>>>>>>>>>>>>>>> 13");
                    while ($question = $db->nextRowAssoc()) {
                        array_push($questionsDistribution,$question['idQuestion']);
                    }

                    $log->append(">>>>>>>>>>>>>>>>>>>> 14");
                }
                $db->close();
                */
                $db = new sqlDB();

                $log->append(">>>>>>>>>>>>>>>>>>>> 15");
                //if(($db->qNewSettings($_SESSION['idSubject'], $questionsDistribution, $_POST['name'], $_POST['scoreType'], $_POST['scoreMin'],
                if(($db->qNewSettings($_SESSION['idSubject'],$_POST['name'], $_POST['scoreType'], $_POST['scoreMin'],
                        $_POST['bonus'], $_POST['negative'], $_POST['editable'],$_POST['certificate'], $_POST['duration'],
                        $_POST['questions'],$_POST['easy'],$_POST['medium'],$_POST['hard'],$_POST['desc'],
                        $matrixDistribution, $mandatQuestionsI, $numTopics,$user->id,$user->group,$user->subgroup)) && ($idNewSetting = $db->nextRowEnum())){

                    $log->append(">>>>>>>>>>>>>>>>>>>> 16");
                    echo 'ACK'.$ajaxSeparator.$idNewSetting[0];
                }else{

                    $log->append(">>>>>>>>>>>>>>>>>>>> 17");
                    die($db->getError());
                }
                $db->close();


            }else{
                $log->append(">>>>>>>>>>>>>>>>>>>> 18");echo ttNotEnoughQuestions;};

        }else{
            $log->append(">>>>>>>>>>>>>>>>>>>> 19");
            $log->append(__FUNCTION__.' : Params not set - $_POST: '.var_export($_POST,true));
            echo ttParamNotSet;
        }
        $log->append(">>>>>>>>>>>>>>>>>>>> 20");
    }

    /**
     *  @name   checkIntegrity
     *  @descr  Check the integrity of the input
     */
    private function checkIntegrity($numQuestions,$numTopics, $dCoefficients, $topicQuestionValue,$matrixMaxQuestion){
        $totQuestionsNeeded = array(0,0,0);
        $totQuestionsTopic = 0;
        $totQuestionsOwned = array(0,0,0);
        $finalResult = 0;
        $result = array(0,0,0);
        for($j = 0; $j< 3 ; $j++) {
            for ($i = 0; $i < $numTopics; $i++) {
                $totQuestionsNeeded[$j] += $dCoefficients[$j] * $topicQuestionValue[$i];
                if ($topicQuestionValue[$i] > 0) {
                    $totQuestionsOwned[$j] += $matrixMaxQuestion[$i][$j];
                }
            }
            if (!is_int($totQuestionsNeeded[$j])) {
                $totQuestionsNeeded[$j] = floor($totQuestionsNeeded[$j]);
            }
            $result[$j] = $totQuestionsNeeded[$j] - $totQuestionsOwned[$j];
            if($result[$j]>0){$finalResult += $result[$j];}
        }
        for ($i = 0; $i < $numTopics; $i++) {
            $totQuestionsTopic += $topicQuestionValue[$i];
        }
        if (($finalResult - ($numQuestions - $totQuestionsTopic)) > 0) {
            return false;
        }else{
            return true;
        }
    }
    /**
     *  @name   calcTopicDistribution
     *  @descr  calculates the  matrixdistribution of topics
     */
    private function calcTopicDistribution($numQuestions,$numDifficulty,$topicQuestionsID,$topicQuestionsValue,$matrixMaxQuestions,$matrixDistribution,$dCoefficients){
        $numTopics = count($topicQuestionsID);

        $numT = array(); //vector of questions have to be included for each topic
        $numI = array(); //vector of questions have to be hypothetically included for each topic
        $matrixAnomalies = array(array()); // matrice anomalie
        for($j=0; $j<3; $j++){
            for ($i=0; $i<$numTopics; $i++){
                $matrixAnomalies[$i][$j] = 0;
            }
        }
        //adds to the matrix the questions that must to be added
        for ($i = 0; $i < $numTopics;$i++) {
            for ($j = 0; $j < 3; $j++){
                //calculates the number of questions have to be added divided by difficulty
                $numT[$j] = floor($dCoefficients[$j] * $topicQuestionsValue[$i]);
                for ($k = 0; $k < $numT[$j]; $k++) {
                    if ($matrixMaxQuestions[$i][$j] > 0) {
                        $matrixDistribution [$i][$j]++;
                        $numDifficulty[$j]--;
                        $matrixMaxQuestions[$i][$j]--;
                        $numQuestions--;
                    }else{
                        $matrixAnomalies[$i][$j] ++;
                        $numDifficulty[$j]--;
                        $numQuestions--;
                    }
                    $numI[$i] ++;
                }
            }
        }
        //add the other questions
        for ($i = 0; $i < $numTopics;$i++) {
            $numR = $topicQuestionsValue[$i] - $numI[$i];
            $j = mt_rand(0, 2);
            $contaErrori=0;
            while ($numR > 0) {
                if ($numDifficulty[$j] > 0 && $matrixMaxQuestions[$i][$j] > 0) {
                    $matrixDistribution [$i][$j]++;
                    $matrixMaxQuestions[$i][$j]--;
                    $numDifficulty[$j]--;
                    $numQuestions--;
                    $numR--;
                    $contaErrori=0;
                }else{
                    $contaErrori++;
                    if($contaErrori==3){ // DAMIANO
                        break;
                    }
                }
                $j = ($j + 1) % 3;
            }
        }
        $counterFinale = 0;
        //corrects the anomalies, STEP 1
        for($j=0; $j<3; $j++){
            for ($i=0; $i<$numTopics; $i++){
                $counter = 0;
                for($l = 0; $l<$matrixAnomalies[$i][$j];$l++){
                    $goOn = true;
                    $k = 0;
                    while($k<$numTopics && $goOn){
                        $numQuesIns = $matrixDistribution[$k][0] + $matrixDistribution[$k][1] + $matrixDistribution[$k][2];
                        if($matrixMaxQuestions[$k][$j] > 0 && $numQuesIns < $topicQuestionsValue[$k]){
                            $matrixDistribution[$k][$j]++;
                            $matrixMaxQuestions[$k][$j]--;
                            $counter++;
                            $goOn = false;
                        }
                        $k++;
                    }
                }
                $matrixAnomalies[$i][$j]= $matrixAnomalies[$i][$j]-$counter;
            }
        }

        //corrects the anomalies, STEP 2
        for($j=0; $j<3; $j++){
            for ($i=0; $i<$numTopics; $i++){
                while($matrixAnomalies[$i][$j] > 0){ // DAMIANO
                    $tempDiff1 = null;
                    $tempDiff2 = null;
                    $goOn = true;
                    if($matrixMaxQuestions[$i][($j+1)%3] > 0){
                        $tempDiff1 = ($j+1)%3;
                    }
                    if($matrixMaxQuestions[$i][($j+2)%3] > 0){
                        $tempDiff2 = ($j+2)%3;
                    }
                    $k = 0;
                    while($k<$numTopics && $goOn){
                        if($matrixMaxQuestions[$k][$j] > 0 && $matrixDistribution[$k][$tempDiff1] > 0){
                            $matrixDistribution[$k][$j]++;
                            $matrixMaxQuestions[$k][$j]--;
                            $matrixDistribution[$k][$tempDiff1]--;
                            $matrixMaxQuestions[$k][$tempDiff1]++;
                            $matrixDistribution[$i][$tempDiff1]++;
                            $matrixAnomalies[$i][$j]--;
                            $goOn = false;
                        }else if ($matrixMaxQuestions[$k][$j] > 0 && $matrixDistribution[$k][$tempDiff2] > 0){
                            $matrixDistribution[$k][$j]++;
                            $matrixMaxQuestions[$k][$j]--;
                            $matrixDistribution[$k][$tempDiff2]--;
                            $matrixMaxQuestions[$k][$tempDiff2]++;
                            $matrixDistribution[$i][$tempDiff2]++;
                            $matrixAnomalies[$i][$j]--;
                            $goOn = false;
                        }
                        $k++;
                    }

                }
            }
        }

        return array($matrixDistribution,$matrixMaxQuestions,$matrixAnomalies,$numDifficulty,$numQuestions,$counterFinale);
    }

    /**
     *  @name   calcFinalDistribution
     *  @descr  calculates the matrixdistribution of the testsetting
     */
    private function calcFinalDistribution($numQuestions,$numTopic,$numDifficulty,$matrixMaxQuestions,$matrixDistribution){
        //Calculates the distribution coefficients
        //Adds to each topic a number of questions equal to the coefficient with random difficulty
        $distributionCoefficient = floor($numQuestions/$numTopic);
        for($i = 0; $i<$distributionCoefficient; $i++){
            for ($j = 0; $j<$numTopic;$j++){
                $randomDiffuculty = mt_rand(0,2);
                $k = 0;
                $goOnD = true;
                while ($k<3 && $goOnD) {
                    if ($numDifficulty[$randomDiffuculty] > 0 && $matrixMaxQuestions[$j][$randomDiffuculty] > 0) {
                        $matrixDistribution [$j][$randomDiffuculty]++;
                        $numDifficulty[$randomDiffuculty]--;
                        $matrixMaxQuestions[$j][$randomDiffuculty]--;
                        $goOnD = false;
                    }
                    $randomDiffuculty = ($randomDiffuculty+1)%3;
                    $k++;
                }
            }
        }
        //Adds randomly the others questions
        for ($i = 0;  $i<3; $i++){
            for ($j = 0;  $j<$numDifficulty[$i]; $j++){
                $randomTopic = mt_rand(0,$numTopic-1);
                while($matrixMaxQuestions[$randomTopic][$i] == 0){
                    $randomTopic = ($randomTopic+1)%$numTopic;
                }
                $matrixDistribution[$randomTopic][$i]++;
                $matrixMaxQuestions[$randomTopic][$i]--;
            }
        }
        return $matrixDistribution;
    }


    /**
     * @name    calcQuestionsDistribution
     * @descr   Returns matrix questions assignament per topics
     * @param   $questionsT         Array       Questions distribution per topic
     * @param   $questionsD         Array       Questions distribution per difficulty
     * @param   $totQuestions       String      Number of total questions per test
     * @return  Array
     */
    private function calcQuestionsDistribution($questionsT, $questionsD, $totQuestions){
        global $log;

        $distributionMatrix = $approxMatrix = array();

//        $log->append('********************************* Store Into distributionMatrix *********************************');

        foreach($questionsT as $idTopic => $arrayQuestionsT)
            if($arrayQuestionsT != null)
                foreach(range(1, getMaxQuestionDifficulty()) as $difficulty)
                    $distributionMatrix[$difficulty][$idTopic] = ($arrayQuestionsT['random'] * $questionsD[$difficulty]['random']) / $totQuestions;

//        $log->append('distributionMatrix : '.var_export($distributionMatrix, true));
//        $log->append('********************************* Approx and Update All Matrix **********************************');

        $assignedForDifficulties = array_fill(1, getMaxQuestionDifficulty(), 0);
        $assignedForTopics = array();

        foreach($distributionMatrix as $difficulty => $arrayQuestionsT){
            foreach($arrayQuestionsT as $idTopic => $questionsNum){
                $decimal = $questionsNum - floor($questionsNum);

                if($decimal== 0){
                    $approxMatrix[$difficulty][$idTopic] = 0;
                }else if($decimal > 0 && $decimal <= 0.16){
                    $distributionMatrix[$difficulty][$idTopic] = floor($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = -1;
                }else if($decimal > 0.16 && $decimal <= 0.33){
                    $distributionMatrix[$difficulty][$idTopic] = floor($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = -2;
                }else if($decimal > 0.33 && $decimal <= 0.49){
                    $distributionMatrix[$difficulty][$idTopic] = floor($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = -3;
                }else if($decimal > 0.49 && $decimal <= 0.66){
                    $distributionMatrix[$difficulty][$idTopic] = ceil($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = 3;
                }else if($decimal > 0.66 && $decimal <= 0.82){
                    $distributionMatrix[$difficulty][$idTopic] = ceil($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = 2;
                }else if($decimal > 0.82 && $decimal <= 0.99){
                    $distributionMatrix[$difficulty][$idTopic] = ceil($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = 1;
                }

                $assignedForDifficulties[$difficulty] += $distributionMatrix[$difficulty][$idTopic];
                $assignedForTopics[$idTopic] = isset($assignedForTopics[$idTopic])? $assignedForTopics[$idTopic] + $distributionMatrix[$difficulty][$idTopic] : $distributionMatrix[$difficulty][$idTopic];

            }
        }

//        $log->append('distributionMatrix : '.var_export($distributionMatrix, true));
//        $log->append('approxMatrix : '.var_export($approxMatrix, true));
//        $log->append('assignedForDifficulties : '.var_export($assignedForDifficulties, true));
//        $log->append('assignedForTopics : '.var_export($assignedForTopics, true));
//        $log->append('******************************** Adjust Questions Assignaments **********************************');

        foreach(range(1, getMaxQuestionDifficulty()) as $difficulty){
            $gap = $assignedForDifficulties[$difficulty] - $questionsD[$difficulty]['random'];
//            $log->append('$gap'."$difficulty : $gap");
            if($gap > 0){                               // Too many random questions assigned
                                                        // for this difficulty, so remove some
                arsort($approxMatrix[$difficulty]);
                foreach($approxMatrix[$difficulty] as $idTopic => $approximation){
                    if($gap > 0){
                        if($assignedForTopics[$idTopic] > $questionsT[$idTopic]['random']){
                            $distributionMatrix[$difficulty][$idTopic]--;
                            $assignedForTopics[$idTopic]--;
                            $gap--;
                        }
                    }else break;
                }
            }else if($gap < 0){                         // Too few random questions assigned
                                                        // for this difficulty, so add more
                asort($approxMatrix[$difficulty]);
                foreach($approxMatrix[$difficulty] as $idTopic => $approximation){
                    if($gap < 0){
                        if($assignedForTopics[$idTopic] < $questionsT[$idTopic]['random']){
                            $distributionMatrix[$difficulty][$idTopic]++;
                            $assignedForTopics[$idTopic]++;
                            $gap++;
                        }
                    }
                }
            }
        }

//        $log->append('distributionMatrix : '.var_export($distributionMatrix, true));

        return $distributionMatrix;
    }

    /**
     *  @name   actionDeletesettings
     *  @descr  Delete requested settinga info (if possible)
     */
    private function actionDeletesettings(){
        global $log;

        if(isset($_POST['idTestSetting'])){
            $db = new sqlDB();
            if($db->qSelect('Exams', 'fkTestSetting', $_POST['idTestSetting'])){
                $error = false;
                while(($row = $db->nextRowAssoc()) && (!$error)){
                    if($row['status'] != 'a'){                         // At least one exam isn't archived
                        $error = true;                                 // so, do nothing
                    }
                }
                if($error){
                    die(ttEExamsNotArchivedDeleteTestSettings);
                }else{
                    if($db->qDeleteTestSettings($_POST['idTestSetting'])){
                        echo 'ACK';
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
     * @name    actionTestsettingslist
     * @descr   Shows test settings list for requested subject
     */
    private function actionTestsettingslist() {
        global $log;

        if($_POST['idSubject']){
            $db = new sqlDB();
            if($_POST['idSubject'] == '-1'){
                echo ttSelectSubjectBefore;
            }else{
                if($db->qSelect('TestSettings', 'fkSubject', $_POST['idSubject'], 'name')){
                    if($db->numResultRows() > 0){
                        if($testsetting = $db->nextRowAssoc()){
                            echo '<dt class="writable"><span>'.$testsetting['name'].'<span class="value">'.$testsetting['idTestSetting'].'</span></span></dt>
                                  <dd><ol>
                                  <li>'.$testsetting['name'].'<span class="value">'.$testsetting['idTestSetting'].'</span></li>';
                            while($testsetting = $db->nextRowAssoc()){
                                echo '<li>'.$testsetting['name'].'<span class="value">'.$testsetting['idTestSetting'].'</span></li>';
                            }
                            echo '</ol></dd>';
                        }
                    }else{
                        echo ttNoSettings;
                    }

                }else{
                    die($db->getError());
                }
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /********************************************************************
     *                               Test                               *
     ********************************************************************/

    /**
     *  @name   actionCorrect
     *  @descr  Shows page to correct test or store test details into history table
     */
    private function actionCorrect(){
        global $log, $engine;

        if((isset($_POST['idTest'])) && (isset($_POST['correctScores'])) &&
            (isset($_POST['scoreTest'])) &&
            (isset($_POST['bonus'])) && (isset($_POST['scoreFinal']))){

            $db = new sqlDB();
            if(($db->qTestDetails(null,$_POST['idTest']) && ($testInfo = $db->nextRowAssoc()))){
                $allowNegative = ($testInfo['negative'] == 0)? false : true;

                if($db->qArchiveTest($_POST['idTest'], json_decode(stripslashes($_POST['correctScores']), true),
                    $_POST['scoreTest'], $_POST['bonus'], $_POST['scoreFinal'], $testInfo['scale'], $allowNegative)){
                    echo 'ACK';
                }else{
                    die($db->getError());
                }
            }else{
                die($db->getError());
            }
        }elseif(isset($_POST['idTest'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header("Location: index.php?page=exam/exams");
        }
    }
    /**
     *  @name   actionToggleblock
     *  @descr  Action to block/unblock student's test
     */
    private function actionToggleblock(){
        global $log, $ajaxSeparator;

        if(isset($_POST['idTest'])){
            $db = new sqlDB();
            if(($db->qSelect('Tests', 'idTest', $_POST['idTest'])) && ($test = $db->nextRowAssoc())){
                switch($test['status']){
                    case 'w' :
                    case 's' : echo ($db->qUpdateTestStatus($_POST['idTest'], 'b'))? 'ACK'.$ajaxSeparator.'b' : $db->getError();
                               break;
                    case 'b' : if($test['timeStart'] != ''){
                                   echo ($db->qUpdateTestStatus($_POST['idTest'], 's'))? 'ACK'.$ajaxSeparator.'s' : $db->getError();
                               }else{
                                   echo ($db->qUpdateTestStatus($_POST['idTest'], 'w'))? 'ACK'.$ajaxSeparator.'w' : $db->getError();
                               }
                               break;
                    case 'e' :
                    case 'a' : echo ttETestAlreadyArchived; break;
                }
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionView
     *  @descr  Show page to View test
     */
    private function actionView(){
        global $log, $engine;

        if(isset($_POST['idTest'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header("Location: index.php?page=exam/exams");
        }
    }

    /********************************************************************
     *                               User                               *
     ********************************************************************/

    /**
     *  @name   actionShowaddstudentspanel
     *  @descr  Shows panel to add new registrations to the exam
     */
    private function actionShowaddstudentspanel(){
        global $log, $engine;

        if(isset($_POST['idExam'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionRegisterstudents
     *  @descr  Register students to requested exam
     */
    private function actionRegisterstudents(){
        global $log;
        if((isset($_POST['idExam'])) && (isset($_POST['students']))){
            $db = new sqlDB();
            $students = explode('&', $_POST['students']);
            foreach($students as $student){
                if($db->qCheckRegistration($_POST['idExam'], $student)){
                    if($db->numResultRows() == 0){
                        if($db->qMakeQuestionsSet($_POST['idExam'], $student)){

                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die(ttEDatabase);
                }
            }
            echo "ACK";
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }


    /**
     *  @name   actionSavestudentexam
     *  @descr  Save the student exam if the student dosen't submit the test
     */
    private function actionSavestudentexam(){
        global $log;
        $db = new sqlDB();
        if($_POST['idTest']==null) die($db->getError());
        if($ok=$db->qEndTestByTeacher($_POST['idTest'])){
            //$log->append("Test closed:".$ok);
            echo 'ACK';
        }else{
            die($db->getError());
        }
    }



        /**
     *  @name   actionUpdateArchivedTest
     *  @descr  Shows page to correct test or store test details into history table
     */
    private function actionUpdateArchivedTest(){
        global $log, $engine;
        if( isset($_POST['idTest']) &&
            isset($_POST['scoreFinal'])){
            $db = new sqlDB();
            if($db->qUpdateArchivedTest($_POST['idTest'],$_POST['scoreFinal']) == true)
                echo "ACK";
            else 
                echo "NACK";
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
                'actions' => array('Deleteexam'),
                'roles'   => array('a'),
            ),
            array(
                'allow',
                'actions' => array('Settings', 'Showsettingsinfo','Shownewsettingsinfo', 'Updatesettingsinfo', 'Newsettings','Newsettings2', 'Deletesettings',
                                   'Exams', 'Showexaminfo', 'Testsettingslist', 'Updateexaminfo', 'Newexam', 'Changestatus',
                                   'Showregistrationslist', 'Showaddstudentspanel', 'Registerstudents', 'Toggleblock', 'Correct', 'View', 'Archiveexam','Printcertificate','Savestudentexam','Sendmail','Updatearchivedtest','Resetsessiontestsettings'),
                'roles'   => array('t','e'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }

}



