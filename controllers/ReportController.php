<?php
/**
 * File: ReportController.php
 * User: Masterplan
 * Date: 4/19/13
 * Time: 10:04 AM
 * Desc: Controller for all Report operations
 */
class ReportController extends Controller{
    /**
     *  @name   ReportController
     *  @descr  Creates an instance of ReportController class
     */
    public function ReportController(){}
    /**
     * @name    executeAction
     * @param   $action         String      Name of requested action
     * @descr   Executes action (if exists and if user is allowed)
     */
    public function executeAction($action){
        global $user, $log;
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
     *  @descr  Shows report index page
     */
    private function actionIndex(){
        global $engine;
               //, $user;
        //$user->role = 'a';
       // $_SESSION['user'] = serialize($user);
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }
    /**
     *  @name   actionAoreport
     *  @descr  Shows AOreport home page
     */
    private function actionAoreport(){
        global $engine;
               //, $user;
        //$user->role = 'a';
        //$_SESSION['user'] = serialize($user);
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }
    /**
     *  @name   actionShowassesments
     *  @descr  Shows report index page
     */
    private function actionShowassesments(){
        $db=new sqlDB();
        if(!($db->qShowExams($_POST['letter'],$_POST["idUser"]))){
            echo "errore query";
        }
    }
    /**
     *  @name   actionShowgroups
     *  @descr  Shows report index page
     */
    private function actionShowgroups(){
       
        $db=new sqlDB();
        if(!($db->qShowGroups($_POST['letter'],$exams=json_decode($_POST['exams']),$_POST['minscore'],$_POST['maxscore'],$_POST['datein'],$_POST['datefn']))){
            echo "query error check the log file";
        }
    }
    /**
     *  @name   actionShowpartecipant
     *  @descr  Shows partecipant div
     */
    private function actionShowpartecipant(){
        global $engine;
        $engine->loadLibs();
        $engine->renderPage();
    }
    /**
     *  @name   actionShowstudent
     *  @descr  Shows report index page
     */
    private function actionShowstudent(){
       
        $db=new sqlDB();
        $groups=json_decode($_POST['groups']);
        if (($groups[0]!="") or ($groups[0]!=null)){
            if(!($db->qShowStudentGroup($groups,$exams=json_decode($_POST['exams']),$_POST['minscore'],$_POST['maxscore'],$_POST['datein'],$_POST['datefn']))){
                echo "query error check the log file";
            }
        }
        else{
            if(!($db->qShowStudent($exams=json_decode($_POST['exams']),$_POST['minscore'],$_POST['maxscore'],$_POST['datein'],$_POST['datefn']))){
                echo "query error check the log file";
            }
        }
    }
    /**
     *  @name   actionAddstudent
     *  @descr  Shows report index page
     */
    private function actionAddstudent(){
       
        $db=new sqlDB();
        $userid=$_POST['iduser'];
        if(!($db->qAddStudent($userid))){
            echo "errore query";
        }
    }
    /**
     *  @name   actionShowparticipantdetails
     *  @descr  Shows partecipant div
     */
    private function actionShowparticipantdetails(){
        global $engine;
        $engine->loadLibs();
        $engine->renderPage();
    }
    /**
     *  @name   actionPrintparticipantdetails
     *  @descr  Shows report index page
     */
    private function actionPrintparticipantdetails(){
       
        $db=new sqlDB();
        $userid=$_POST['iduser'];
        if(!($db->qShowStudentDetails($userid))){
            echo "errore query";
        }
    }
    /**
     *  @name   actionAoreportparameters
     *  @descr  Set parameters for AOreport
     */
    private function actionAoreportparameters(){
       
        $_SESSION['userparam']=$_POST['iduser'];
        $_SESSION['examsparam']=json_decode($_POST['exams']);
        $_SESSION['groupsparam']=json_decode($_POST['groups']);
        $_SESSION['minscoreparam']=$_POST['minscore'];
        $_SESSION['maxscoreparam']=$_POST['maxscore'];
        $_SESSION['datein']=$_POST['datein'];
        $_SESSION['datefn']=$_POST['datefn'];
    }
    /**
     *  @name   actionAoreporttemplate
     *  @descr  Shows report template for AOReport
     */
    private function actionAoreporttemplate(){
        global $engine;
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }
    /**
     *  @name   actionAoreportresult
     *  @descr  Shows the report
     */
private function actionResultstudent(){
        global $config,$user,$log;
        $idExam = $_POST["idExam"];
        $subject = $_POST["subject"];
        $subject = str_replace(" ", "_", $subject);        
	$subject = str_replace("/", "_", $subject);
        $date = $_POST["date"];
        $date = explode("/", $date);
        $date = $date[0]."-".$date[1]."-".$date[2];

        $dir = $config['systemViewsDir']."Report/generated_report/RatingExam";
        if (file_exists($dir)==false){
            mkdir($config['systemViewsDir']."Report/generated_report/RatingExam");
        }
        $dir = $config['systemViewsDir']."Report/generated_report/RatingExam/".$subject;
        if (file_exists($dir)==false){
            mkdir($config['systemViewsDir']."Report/generated_report/RatingExam/".$subject);
        }
        if (file_exists("temp")==false){
            mkdir("temp");
        }
        $path = $dir."/".$date.".csv";
	$path2 = "temp/".$subject."_".$date.".csv";
        $path2 = "temp/".$date.".csv";
        $file = fopen($path,"w");
        $file2 = fopen($path2,"w");
        chmod($path,0777);
        chmod($path2,0777);
        $db=new sqlDB();
        if ($db->qGetRatingExam($idExam)) { 
            if($config['dbName'] == 'EOL')
	        $a = ttName."#".ttSurname."#".ttEmail."#".ttTimeStart."#".ttTimeEnd."#".ttTimeUsed."#".ttScoreTest."#".ttFinalScore;
	    else           
            	$a = ttName."#".ttSurname."#Certificate#".ttEmail."#".ttTimeStart."#".ttTimeEnd."#".ttTimeUsed."#".ttScoreTest."#".ttFinalScore;
            $db2=new sqlDB();
            $db2->getTopicByExam($idExam);
            $h=0;
            $listaTopic=array();
            while ($topic = $db2->nextRowAssoc()) {
                $a.="#".$topic["Topics"];
                $listaTopic[$h]=$topic["Topics"];
                $h++;
            }
            $title = array($a);
            fputcsv($file,explode("#", $title[0]));
            fputcsv($file2,explode("#", $title[0]));
            while ($info = $db->nextRowAssoc()) {
                $start = strtotime($info['timeStart']);
                $end = strtotime($info['timeEnd']);
                $diff = $end - $start;
                $arr['days']=floor($diff/(60*60*24));
                $diff=$diff-(60*60*24*$arr['days']);
                $arr['hours']=floor($diff/(60*60));
                $diff=$diff-(60*60*$arr['hours']);
                $arr['minutes']=floor($diff/60);
                $diff=$diff-(60*$arr['minutes']);
                $arr['seconds']=$diff;

                //if (date('d',$diff)> 0) {
                if ($arr['days']> 0) {
                    $time = '> 24 h';
                } else {
                    //$time = $diff->format("%H:%I:%S");
                    $time = date("H:i:s",mktime($arr['hours'],$arr['minutes'],$arr['seconds']));
                }
                $info["timeDiff"] = $time;
                $db3=new sqlDB();
                $db3->getReportDetailed($info["idTest"]);
                $scoreArray=array();
                while($scoreTemp = $db3->nextRowAssoc()){
                    $scoreArray[$scoreTemp["Topics"]]["punteggio"]=$scoreTemp["punteggio"];
                    $scoreArray[$scoreTemp["Topics"]]["MaxScore"]=$scoreTemp["MaxScore"];
                }
                $count=0;
                while($count<$h){
                    $valore=0;
                    $risultato="";
                    if(isset($scoreArray[$listaTopic[$count]]))
                        $valore=$scoreArray[$listaTopic[$count]]["punteggio"];
                    if($scoreArray[$listaTopic[$count]]["MaxScore"]>0){
                        $percentage=$scoreArray[$listaTopic[$count]]["MaxScore"]/100*1.1;
                        if($valore>=($scoreArray[$listaTopic[$count]]["MaxScore"]-$percentage))
                            $risultato="100%";
                        else
                            $risultato=round((($valore/$scoreArray[$listaTopic[$count]]["MaxScore"])*100),3)."%";
                    }
                    $info[$count]=$risultato;
                    $count++;
                }
                $info["name"]=urldecode($this->replaceCharacter($info["name"]));
                $info["surname"]=urldecode($this->replaceCharacter($info["surname"]));
		if($info["privacy"]==1){
			$info["privacy"]="Requested";
		}else{
			$info["privacy"]="Not requested";
		}
		if($config['dbName'] == 'EOL')
			unset($info["privacy"]);
                unset($info["idTest"]);
                fputcsv($file,$info);
                fputcsv($file2,$info);
            }
            echo json_encode(array("success",$path2));

            try{
                if($config['dbName'] == 'echemtest'){ // LEVA QUESTA COSA !!!

                    $dbGroup=new sqlDB();
                    $dbGroup->qGetGroupAndSubgroupName($user->group,$user->subgroup);
                    $result=$dbGroup->nextRowAssoc();
                    $emailGroup=$result['NameGroup'];
                    $emailSubGroup=$result['NameSubGroup'];

                    exec("curl --user ".$config['usernameCertificate'].":".$config['passwordCertificate']." -F data=@".$path2." ".$config['urlCertificate']." >/dev/null 2>/dev/null &" );
                

		try{
                        $filename=$subject."_".$date.".csv";
                        $emailTo = "echemtest@master-up.it";
                        $emailSubject = "Automatic notification from echemTest";
                        $emailMessage =  "File sent to the server: ".$filename.
                                  " \r\n The certificate was created by the user: ".$user->email.
                                  " \r\n Exam date: ".$date.
                                  " \r\n Group: ".$emailGroup.
                                  " \r\n SubGroup: ".$emailSubGroup;
                        $emailHeaders = 'from:' . $user->email . "\r\n" .
                            'Reply-To:' . $user->email . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                        if (!mail($emailTo, $emailSubject, $emailMessage, $emailHeaders)) {
                            $log->append("Error, the email has not been sent. Filename:".$filename." by ".$user->email." for the date:".$date);
                        }
		}catch(Exception $ex){}




		}
            }catch(Exception $ex){
                echo $ex;
            }

	}else{
            echo json_encode(array("success","error"));
        }
        fclose($file);
        fclose($file2);
        //echo json_encode(array("success",$path2));
    }
    private function actionAoreportresult(){
        global $config,$user;
        include($config['systemPhpGraphLibDir'].'phpgraphlib.php');
        include($config['systemFpdfDir'].'fpdf.php');
        $db=new sqlDB();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica','B',22);
        $pdf->Image("themes/default/images/eol.png");
        $pdf->Cell(0,20,ttAssesmentOverview,1,1,'C',false);
        $pdf->Cell(0,8,"",0,1);
        // participant is selected
        if ($_SESSION['userparam']!=""){
            $pdf->SetLeftMargin(30);
            $pdf->SetFont('Helvetica','B',13);
            $pdf->Cell(85,10,ttStudent,0,0);
            $pdf->SetFont('Helvetica','',13);
            $pdf->Cell(85,10,$db->qLoadStudent($_SESSION['userparam']),0,1);
            $pdf->SetFont('Helvetica','B',13);
            $pdf->Cell(85,10,ttStudentDetail,0,0);
            $pdf->SetFont('Helvetica','',13);
            $pdf->Cell(85,10,$_SESSION['userparam'],0,1);
            $pdf->SetLeftMargin(10);
            $pdf->Cell(0,5,"",0,1);
            if (($_SESSION['examsparam'][0]!="") or ($_SESSION['examsparam'][0]!=null)){ // case assesment are selected in the main!!
                $i=0;
                while(($_SESSION['examsparam'][$i]!="") or ($_SESSION['examsparam'][$i]!=null)){
                    if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))){
                        if ($i>0){
                            $pdf->AddPage();
                        }
                        $pdf->SetFont('Helvetica','B',16);
                        $pdf->Cell(0,10,ttReportAssessmentInformation,1,1,'L',false);
                        $pdf->Cell(0,10,"",0,1);
                    }
                    //print assesment name
                    if (isset($_POST['assesmentName'])) {
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentName,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$_SESSION['examsparam'][$i],0,1);
                    }
                    //print assesment ID
                    if (isset($_POST['assesmentID'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentID,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentID($_SESSION['examsparam'][$i]),0,1);
                    }
                    //print assesment author
                    if (isset($_POST['assesmentAuthor'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentAuthor,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentAuthor($_SESSION['examsparam'][$i]),0,1);
                    }
                    //print assesment DATA/TIME FIRST TAKEN
                    if (isset($_POST['assesmentDateTimeFirst'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentDateTimeFirst,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentDateTimeFirstTaken($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment DATA/TIME LAST TAKEN
                    if (isset($_POST['assesmentDateTimeLast'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentDateTimeLast,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentDateTimeLastTaken($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment number of times started
                    if (isset($_POST['assesmentNumberStarted'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentNumberStarted,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentNumberStarted($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print exam number of times not finished
                    if (isset($_POST['assesmentNumberNotFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentNumberNotFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentNumberNotFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment number of times finished
                    if (isset($_POST['assesmentNumberFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentNumberFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentNumberFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment min score finished
                    if (isset($_POST['assesmentMinscoreFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMinscoreFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMinScoreFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment max score finished
                    if (isset($_POST['assesmentMaxscoreFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMaxcoreFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMaxScoreFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment medium score finished
                    if (isset($_POST['assesmentMediumFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMediumFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMedScoreFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment least time finished
                    if (isset($_POST['assesmentLeastTimeFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentLeastTimeFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentLeastTimeFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment most time finished
                    if (isset($_POST['assesmentMostTimeFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMostTimeFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMostTimeFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment medium time finished
                    if (isset($_POST['assesmentMediumTimeFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMediumTimeFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(15,10,$db->qShowAssesmentMediumTimeFinished($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,0);
                        $pdf->Cell(20,10,ttMinutes,0,1);
                    }
                    //print assesment std deviation
                    if (isset($_POST['assesmentStdDeviation'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentStdDeviation,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentStdDeviation($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //now load all the topics relative to selected student
                    $usertopics=$db->qLoadTopicUser($_SESSION['examsparam'][$i],$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']);
                    //print all statistics relative to each topics loaded before
                    foreach($usertopics as $topic){
                        if ((isset($_POST['topicAverageScore'])) or (isset($_POST['topicMinimumScore'])) or (isset($_POST['topicMaximumScore'])) or (isset($_POST['topicStdDeviation']))){
                            $pdf->SetLeftMargin(10);
                            if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))){
                                $pdf->AddPage();
                            }
                            else{
                                if ($i>0){
                                    $pdf->AddPage(); // add a page only from second assesment analysis
                                }
                            }
                            $pdf->SetFont("Helvetica","B",16);
                            $pdf->Cell(0,10,ttReportTopicInformation,1,1,'L',false);
                            $pdf->Cell(0,5,"",0,1);
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicName,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$topic,0,1);
                        }
                        //print topic medium score
                        if (isset($_POST['topicAverageScore'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicAverageScore,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicMedScore($topic,$_SESSION['userparam']),0,1);
                        }
                        //print topic min score
                        if (isset($_POST['topicMinimumScore'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicMinimumScore,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicMinScore($topic,$_SESSION['userparam']),0,1);
                        }
                        //print topic max score
                        if (isset($_POST['topicMaximumScore'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicMaximumScore,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicMaxScore($topic,$_SESSION['userparam']),0,1);
                        }
                        //print topic std deviation
                        if (isset($_POST['topicStdDeviation'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicStandardDeviation,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicStdDeviation($topic,$_SESSION['userparam']),0,1);
                        }
                        $pdf->SetLeftMargin(10);
                    }
                    if ((isset($_POST['graphicHistogram']))or(isset($_POST['graphicTopicScore']))){
                        $pdf->SetFont("Helvetica","B",16);
                        $pdf->Cell(0,5,"",0,1);
                        $pdf->Cell(0,10,ttReportGraphicalDsiplays,1,1,'L',false);
                        $pdf->Cell(0,1,"",0,1);
                    }
                    //draw assesments Histograms if selected
                    if (isset($_POST['graphicHistogram'])) {
                        $graphdata=$db->qLoadAssesmentScores($_SESSION['examsparam'][$i], $_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']);
                        ${'graph'.$i} = new PHPGraphLib(700,350, "../views/Report/generated_graphs/assesmentsgraph".$i.".png");
                        ${'graph'.$i}->addData($graphdata);
                        ${'graph'.$i}->setTitle("Assesments Scores");
                        ${'graph'.$i}->setTextColor("black");
                        ${'graph'.$i}->setXValuesHorizontal(true);
                        ${'graph'.$i}->setBarColor("#6da2ff");
                        ${'graph'.$i}->setDataValues(true);
                        ${'graph'.$i}->setDataValueColor("red");
                        ${'graph'.$i}->createGraph();
                        $pdf->Image("../views/Report/generated_graphs/assesmentsgraph".$i.".png");
                    }
                    //draw topics Histograms if selected
                    if (isset($_POST['graphicTopicScore'])) {
                        $graphdatatopic=$db->qLoadTopicScores($usertopics, $_SESSION['examsparam'][$i], $_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']);
                        ${'topics'.$i} = new PHPGraphLib(700,350, "../views/Report/generated_graphs/topicsgraph".$i.".png");
                        ${'topics'.$i}->addData($graphdatatopic);
                        ${'topics'.$i}->setTitle("Topics Scores");
                        ${'topics'.$i}->setTextColor("black");
                        ${'topics'.$i}->setXValuesHorizontal(true);
                        ${'topics'.$i}->setBarColor("green");
                        ${'topics'.$i}->setDataValues(true);
                        ${'topics'.$i}->setDataValueColor("red");
                        ${'topics'.$i}->createGraph();
                        $pdf->Image("../views/Report/generated_graphs/topicsgraph".$i.".png");
                    }
                    $i++;//counter for assesments
                }
            }
            else{
                //case exams are not selected
                $allexams=$db->qLoadExams();
                $i=0;
                foreach($allexams as $exam){
                    if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))){
                        if ($i>0){
                            $pdf->AddPage(); // add a page only from second assesment analysis
                        }
                        $pdf->SetFont('Helvetica','B',16);
                        $pdf->Cell(0,10,ttReportAssessmentInformation,1,1,'L',false);
                        $pdf->Cell(0,10,"",0,1);
                    }
                    //print assesment name
                    if (isset($_POST['assesmentName'])) {
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentName,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$exam,0,1);
                    }
                    //print assesment ID
                    if (isset($_POST['assesmentID'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentID,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentID($exam),0,1);
                    }
                    //print assesment author
                    if (isset($_POST['assesmentAuthor'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentAuthor,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentAuthor($exam),0,1);
                    }
                    //print assesment DATA/TIME FIRST TAKEN
                    if (isset($_POST['assesmentDateTimeFirst'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentDateTimeFirst,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentDateTimeFirstTaken($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment DATA/TIME LAST TAKEN
                    if (isset($_POST['assesmentDateTimeLast'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentDateTimeLast,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentDateTimeLastTaken($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment number of times started
                    if (isset($_POST['assesmentNumberStarted'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentNumberStarted,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentNumberStarted($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print exam number of times not finished
                    if (isset($_POST['assesmentNumberNotFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentNumberNotFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentNumberNotFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment number of times finished
                    if (isset($_POST['assesmentNumberFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentNumberFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentNumberFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment min score finished
                    if (isset($_POST['assesmentMinscoreFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMinscoreFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMinScoreFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment max score finished
                    if (isset($_POST['assesmentMaxscoreFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMaxcoreFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMaxScoreFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment medium score finished
                    if (isset($_POST['assesmentMediumFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMediumFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMedScoreFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment least time finished
                    if (isset($_POST['assesmentLeastTimeFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentLeastTimeFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentLeastTimeFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment most time finished
                    if (isset($_POST['assesmentMostTimeFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMostTimeFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentMostTimeFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //print assesment medium time finished
                    if (isset($_POST['assesmentMediumTimeFinished'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentMediumTimeFinished,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(15,10,$db->qShowAssesmentMediumTimeFinished($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,0);
                        $pdf->Cell(20,10,ttMinutes,0,1);
                    }
                    //print assesment std deviation
                    if (isset($_POST['assesmentStdDeviation'])){
                        $pdf->SetFont("Helvetica","B",13);
                        $pdf->SetLeftMargin(30);
                        $pdf->Cell(85,10,ttReportAssesmentStdDeviation,0,0);
                        $pdf->SetFont("Helvetica","",13);
                        $pdf->Cell(85,10,$db->qShowAssesmentStdDeviation($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']),0,1);
                    }
                    //now load all the topics relative to selected student
                    $usertopics=$db->qLoadTopicUser($exam,$_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']);
                    //print all statistics relative to each topics loaded before
                    foreach($usertopics as $topic){
                        if ((isset($_POST['topicAverageScore'])) or (isset($_POST['topicMinimumScore'])) or (isset($_POST['topicMaximumScore'])) or (isset($_POST['topicStdDeviation']))){
                            $pdf->SetLeftMargin(10);
                            if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))){
                                $pdf->AddPage();
                            }
                            else{
                                if ($i>0){
                                    $pdf->AddPage(); // add a page only from second assesment analysis
                                }
                            }
                            $pdf->SetFont("Helvetica","B",16);
                            $pdf->Cell(0,10,ttReportTopicInformation,1,1,'L',false);
                            $pdf->Cell(0,5,"",0,1);
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicName,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$topic,0,1);
                        }
                        //print topic medium score
                        if (isset($_POST['topicAverageScore'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicAverageScore,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicMedScore($topic,$_SESSION['userparam']),0,1);
                        }
                        //print topic min score
                        if (isset($_POST['topicMinimumScore'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicMinimumScore,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicMinScore($topic,$_SESSION['userparam']),0,1);
                        }
                        //print topic max score
                        if (isset($_POST['topicMaximumScore'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicMaximumScore,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicMaxScore($topic,$_SESSION['userparam']),0,1);
                        }
                        //print topic std deviation
                        if (isset($_POST['topicStdDeviation'])){
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportTopicStandardDeviation,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$db->qShowTopicStdDeviation($topic,$_SESSION['userparam']),0,1);
                        }
                        $pdf->SetLeftMargin(10);
                    }
                    if ((isset($_POST['graphicHistogram']))or(isset($_POST['graphicTopicScore']))){
                        $pdf->SetFont("Helvetica","B",16);
                        $pdf->Cell(0,5,"",0,1);
                        $pdf->Cell(0,10,ttReportGraphicalDsiplays,1,1,'L',false);
                        $pdf->Cell(0,1,"",0,1);
                    }
                    //draw assesments Histograms if selected
                    if (isset($_POST['graphicHistogram'])) {
                        $graphdata=$db->qLoadAssesmentScores($exam, $_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']);
                        ${'graph'.$i} = new PHPGraphLib(700,350, "../views/Report/generated_graphs/assesmentsgraph".$i.".png");
                        ${'graph'.$i}->addData($graphdata);
                        ${'graph'.$i}->setTitle("Assesments Scores");
                        ${'graph'.$i}->setTextColor("black");
                        ${'graph'.$i}->setXValuesHorizontal(true);
                        ${'graph'.$i}->setBarColor("#6da2ff");
                        ${'graph'.$i}->setDataValues(true);
                        ${'graph'.$i}->setDataValueColor("red");
                        ${'graph'.$i}->createGraph();
                        $pdf->Image("../views/Report/generated_graphs/assesmentsgraph".$i.".png");
                    }
                    //draw topics Histograms if selected
                    if (isset($_POST['graphicTopicScore'])) {
                        $graphdatatopic=$db->qLoadTopicScores($usertopics, $exam, $_SESSION['userparam'],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']);
                        ${'topics'.$i} = new PHPGraphLib(700,350, "../views/Report/generated_graphs/topicsgraph".$i.".png");
                        ${'topics'.$i}->addData($graphdatatopic);
                        ${'topics'.$i}->setTitle("Topics Scores");
                        ${'topics'.$i}->setTextColor("black");
                        ${'topics'.$i}->setXValuesHorizontal(true);
                        ${'topics'.$i}->setBarColor("green");
                        ${'topics'.$i}->setDataValues(true);
                        ${'topics'.$i}->setDataValueColor("red");
                        ${'topics'.$i}->createGraph();
                        $pdf->Image("../views/Report/generated_graphs/topicsgraph".$i.".png");
                    }
                    $i++;//counter for assesments
                }
            }
        }
        //report for all participants
        if (($_SESSION['userparam']=="") && ($_SESSION['groupsparam'][0]=="") && ($_SESSION['groupsparam'][0]==null)) {
            //case selected exams
            if (($_SESSION['examsparam'][0] != "") or ($_SESSION['examsparam'][0] != null)) {
                    $i = 0;
                    while (($_SESSION['examsparam'][$i] != "") or ($_SESSION['examsparam'][$i] != null)) {
                        $students = $db->qLoadAllStudent($_SESSION['examsparam'][$i],$_SESSION['minscoreparam'],$_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']);
                        $d=0;
                        foreach ($students as $student) {
                        if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))) {
                            if ($i > 0) {
                                $pdf->AddPage();
                            }
                            $pdf->SetFont('Helvetica', 'B', 16);
                            $pdf->Cell(0, 10, ttReportAssessmentInformation, 1, 1, 'L', false);
                            $pdf->Cell(0, 10, "", 0, 1);
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->Cell(85, 10, $db->qLoadStudent($student), 0, 0);
                            $pdf->Cell(70,10,"ID: User_".$student,0,1);
                            $pdf->Cell(0,0.5,"",1,1);
                            $pdf->Cell(0,5,"",0,1);
                            $pdf->SetFont("Helvetica", "B", 16);
                        }
                        //print assesment name
                        if (isset($_POST['assesmentName'])) {
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportAssesmentName,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$_SESSION['examsparam'][$i],0,1);
                        }
                        //print assesment ID
                        if (isset($_POST['assesmentID'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentID, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentID($_SESSION['examsparam'][$i]), 0, 1);
                        }
                        //print assesment author
                        if (isset($_POST['assesmentAuthor'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentAuthor, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentAuthor($_SESSION['examsparam'][$i]), 0, 1);
                        }
                        //print assesment DATA/TIME FIRST TAKEN
                        if (isset($_POST['assesmentDateTimeFirst'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentDateTimeFirst, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentDateTimeFirstTaken($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'],$_SESSION['datein'],$_SESSION['datefn']), 0, 1);
                        }
                        //print assesment DATA/TIME LAST TAKEN
                        if (isset($_POST['assesmentDateTimeLast'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentDateTimeLast, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentDateTimeLastTaken($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment number of times started
                        if (isset($_POST['assesmentNumberStarted'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentNumberStarted, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentNumberStarted($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print exam number of times not finished
                        if (isset($_POST['assesmentNumberNotFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentNumberNotFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentNumberNotFinished($_SESSION['examsparam'][$i],$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment number of times finished
                        if (isset($_POST['assesmentNumberFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentNumberFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentNumberFinished($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment min score finished
                        if (isset($_POST['assesmentMinscoreFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMinscoreFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMinScoreFinished($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment max score finished
                        if (isset($_POST['assesmentMaxscoreFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMaxcoreFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMaxScoreFinished($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment medium score finished
                        if (isset($_POST['assesmentMediumFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMediumFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMedScoreFinished($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment least time finished
                        if (isset($_POST['assesmentLeastTimeFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentLeastTimeFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentLeastTimeFinished($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment most time finished
                        if (isset($_POST['assesmentMostTimeFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMostTimeFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMostTimeFinished($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment medium time finished
                        if (isset($_POST['assesmentMediumTimeFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMediumTimeFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(15, 10, $db->qShowAssesmentMediumTimeFinished($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 0);
                            $pdf->Cell(20,10,ttMinutes,0,1);
                        }
                        //print assesment std deviation
                        if (isset($_POST['assesmentStdDeviation'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentStdDeviation, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentStdDeviation($_SESSION['examsparam'][$i], $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        
                        //now load all the topics relative to selected student
                        $topics = $db->qLoadTopicUser($_SESSION['examsparam'][$i],$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']);
                        //print all statistics relative to each topics loaded before
                        
                        foreach ($topics as $topic) {
                            if ((isset($_POST['topicAverageScore'])) or (isset($_POST['topicMinimumScore'])) or (isset($_POST['topicMaximumScore'])) or (isset($_POST['topicsStdDeviation']))) {
                                $pdf->SetLeftMargin(10);
                                if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))) {
                                    $pdf->AddPage();
                                } else {
                                    if ($i > 0) {
                                        $pdf->AddPage(); // add a page only from second assesment analysis
                                    }
                                }
                                $pdf->SetFont("Helvetica", "B", 16);
                                $pdf->Cell(0, 10, ttReportTopicInformation.":      $topic", 1, 1, 'L', false);
                                $pdf->Cell(0, 5, "", 0, 1);
                            }
                            //print topic medium score
                            if (isset($_POST['topicAverageScore'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicAverageScore, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicMedScore($topic, $student), 0, 1);
                            }
                            //print topic min score
                            if (isset($_POST['topicMinimumScore'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicMinimumScore, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicMinScore($topic, $student), 0, 1);
                            }
                            //print topic max score
                            if (isset($_POST['topicMaximumScore'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicMaximumScore, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicMaxScore($topic, $student), 0, 1);
                            }
                            //print topic std deviation
                            if (isset($_POST['topicStdDeviation'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicStandardDeviation, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicStdDeviation($topic,$student), 0, 1);
                            }
                            $pdf->SetLeftMargin(10);
                        }
                        if ((isset($_POST['graphicHistogram'])) or (isset($_POST['graphicTopicScore']))) {
                            $pdf->SetFont("Helvetica", "B", 16);
                            $pdf->Cell(0, 5, "", 0, 1);
                            $pdf->Cell(0, 10, ttReportGraphicalDsiplays, 1, 1, 'L', false);
                            $pdf->Cell(0, 1, "", 0, 1);
                        }
                        //draw assesments Histograms if selected
                        if (isset($_POST['graphicHistogram'])) {
                            $graphdata = $db->qLoadAssesmentScores($_SESSION['examsparam'][$i],$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']);
                            ${'graph' . $i} = new PHPGraphLib(700, 350, "../views/Report/generated_graphs/assesmentsgraph".$i."_".$d.".png");
                            ${'graph' . $i}->addData($graphdata);
                            ${'graph' . $i}->setTitle("Assesments Scores");
                            ${'graph' . $i}->setTextColor("black");
                            ${'graph' . $i}->setXValuesHorizontal(true);
                            ${'graph' . $i}->setBarColor("#6da2ff");
                            ${'graph'.$i}->setDataValues(true);
                            ${'graph'.$i}->setDataValueColor("red");
                            ${'graph' . $i}->createGraph();
                            $pdf->Image("../views/Report/generated_graphs/assesmentsgraph".$i."_".$d.".png");
                        }
                        //draw topics Histograms if selected
                        if (isset($_POST['graphicTopicScore'])) {
                            $graphdatatopic = $db->qLoadTopicScores($topics, $_SESSION['examsparam'][$i],$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']);
                            ${'topics' . $i} = new PHPGraphLib(700, 350, "../views/Report/generated_graphs/topicsgraph".$i."_".$d.".png");
                            ${'topics' . $i}->addData($graphdatatopic);
                            ${'topics' . $i}->setTitle("Topics Scores");
                            ${'topics' . $i}->setTextColor("black");
                            ${'topics' . $i}->setXValuesHorizontal(true);
                            ${'topics' . $i}->setBarColor("green");
                            ${'topics'.$i}->setDataValues(true);
                            ${'topics'.$i}->setDataValueColor("red");
                            ${'topics' . $i}->createGraph();
                            $pdf->Image("../views/Report/generated_graphs/topicsgraph".$i."_".$d.".png");
                        }
                        $d++;
                    }
                    $i++; //counter of assesments
                    }
            }
            else {
                //case exams are not selected
                $allexams = $db->qLoadExams();
                $i = 0;
                foreach ($allexams as $exam) {
                    $students = $db->qLoadAllStudent($exam, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']);
                    $d=0;
                    foreach ($students as $student) {
                        if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))) {
                            if ($i > 0) {
                                $pdf->AddPage();
                            }
                            $pdf->SetFont('Helvetica', 'B', 16);
                            $pdf->Cell(0, 10, ttReportAssessmentInformation, 1, 1, 'L', false);
                            $pdf->Cell(0, 10, "", 0, 1);
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->Cell(85, 10, $db->qLoadStudent($student), 0, 0);
                            $pdf->Cell(70,10,"ID: User_".$student,0,1);
                            $pdf->Cell(0,0.5,"",1,1);
                            $pdf->Cell(0,5,"",0,1);
                            $pdf->SetFont("Helvetica", "B", 16);
                        }
                        //print assesment name
                        if (isset($_POST['assesmentName'])) {
                            $pdf->SetFont("Helvetica","B",13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85,10,ttReportAssesmentName,0,0);
                            $pdf->SetFont("Helvetica","",13);
                            $pdf->Cell(85,10,$exam,0,1);
                        }
                        //print assesment ID
                        if (isset($_POST['assesmentID'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentID, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentID($exam), 0, 1);
                        }
                        //print assesment author
                        if (isset($_POST['assesmentAuthor'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentAuthor, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentAuthor($exam), 0, 1);
                        }
                        //print assesment DATA/TIME FIRST TAKEN
                        if (isset($_POST['assesmentDateTimeFirst'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentDateTimeFirst, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentDateTimeFirstTaken($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment DATA/TIME LAST TAKEN
                        if (isset($_POST['assesmentDateTimeLast'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentDateTimeLast, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentDateTimeLastTaken($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment number of times started
                        if (isset($_POST['assesmentNumberStarted'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentNumberStarted, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentNumberStarted($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print exam number of times not finished
                        if (isset($_POST['assesmentNumberNotFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentNumberNotFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentNumberNotFinished($exam,$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment number of times finished
                        if (isset($_POST['assesmentNumberFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentNumberFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentNumberFinished($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment min score finished
                        if (isset($_POST['assesmentMinscoreFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMinscoreFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMinScoreFinished($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment max score finished
                        if (isset($_POST['assesmentMaxscoreFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMaxcoreFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMaxScoreFinished($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment medium score finished
                        if (isset($_POST['assesmentMediumFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMediumFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMedScoreFinished($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment least time finished
                        if (isset($_POST['assesmentLeastTimeFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentLeastTimeFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentLeastTimeFinished($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment most time finished
                        if (isset($_POST['assesmentMostTimeFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMostTimeFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentMostTimeFinished($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //print assesment medium time finished
                        if (isset($_POST['assesmentMediumTimeFinished'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentMediumTimeFinished, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(15, 10, $db->qShowAssesmentMediumTimeFinished($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 0);
                            $pdf->Cell(20,10,ttMinutes,0,1);
                        }
                        //print assesment std deviation
                        if (isset($_POST['assesmentStdDeviation'])) {
                            $pdf->SetFont("Helvetica", "B", 13);
                            $pdf->SetLeftMargin(30);
                            $pdf->Cell(85, 10, ttReportAssesmentStdDeviation, 0, 0);
                            $pdf->SetFont("Helvetica", "", 13);
                            $pdf->Cell(85, 10, $db->qShowAssesmentStdDeviation($exam, $student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']), 0, 1);
                        }
                        //now load all the topics relative to selected student
                        $topics = $db->qLoadTopicUser($exam,$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']);
                        //print all statistics relative to each topics loaded before
                        foreach ($topics as $topic) {
                            if ((isset($_POST['topicAverageScore'])) or (isset($_POST['topicMinimumScore'])) or (isset($_POST['topicMaximumScore'])) or (isset($_POST['topicsStdDeviation']))) {
                                $pdf->SetLeftMargin(10);
                                if ((isset($_POST['assesmentName'])) or (isset($_POST['assesmentID'])) or (isset($_POST['assesmentAuthor'])) or (isset($_POST['assesmentDateTimeFirst'])) or (isset($_POST['assesmentDateTimeLast'])) or (isset($_POST['assesmentNumberStarted'])) or (isset($_POST['assesmentNumberNotFinished'])) or (isset($_POST['assesmentNumberFinished'])) or (isset($_POST['assesmentMinscoreFinished'])) or (isset($_POST['assesmentMaxscoreFinished'])) or (isset($_POST['assesmentMediumFinished'])) or (isset($_POST['assesmentLeastTimeFinished'])) or (isset($_POST['assesmentMostTimeFinished'])) or (isset($_POST['assesmentMediumTimeFinished'])) or (isset($_POST['assesmentStdDeviation']))) {
                                    $pdf->AddPage();
                                } else {
                                    if ($i > 0) {
                                        $pdf->AddPage(); // add a page only from second assesment analysis
                                    }
                                }
                                $pdf->SetFont("Helvetica", "B", 16);
                                $pdf->Cell(0, 10, ttReportTopicInformation, 1, 1, 'L', false);
                                $pdf->Cell(0, 5, "", 0, 1);
                            }
                            //print topic medium score
                            if (isset($_POST['topicAverageScore'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicAverageScore, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicMedScore($topic, $student), 0, 1);
                            }
                            //print topic min score
                            if (isset($_POST['topicMinimumScore'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicMinimumScore, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicMinScore($topic, $student), 0, 1);
                            }
                            //print topic max score
                            if (isset($_POST['topicMaximumScore'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicMaximumScore, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicMaxScore($topic, $student), 0, 1);
                            }
                            //print topic std deviation
                            if (isset($_POST['topicStdDeviation'])) {
                                $pdf->SetFont("Helvetica", "B", 13);
                                $pdf->SetLeftMargin(30);
                                $pdf->Cell(85, 10, ttReportTopicStandardDeviation, 0, 0);
                                $pdf->SetFont("Helvetica", "", 13);
                                $pdf->Cell(85, 10, $db->qShowTopicStdDeviation($topic,$student), 0, 1);
                            }
                            $pdf->SetLeftMargin(10);
                        }
                        if ((isset($_POST['graphicHistogram'])) or (isset($_POST['graphicTopicScore']))) {
                            $pdf->SetFont("Helvetica", "B", 16);
                            $pdf->Cell(0, 5, "", 0, 1);
                            $pdf->Cell(0, 10, ttReportGraphicalDsiplays, 1, 1, 'L', false);
                            $pdf->Cell(0, 1, "", 0, 1);
                        }
                        //draw assesments Histograms if selected
                        if (isset($_POST['graphicHistogram'])) {
                            $graphdata = $db->qLoadAssesmentScores($exam,$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']);
                            ${'graph' . $i} = new PHPGraphLib(700, 350, "../views/Report/generated_graphs/assesmentsgraph".$i."_".$d.".png");
                            ${'graph' . $i}->addData($graphdata);
                            ${'graph' . $i}->setTitle("Assesments Scores");
                            ${'graph' . $i}->setTextColor("black");
                            ${'graph' . $i}->setXValuesHorizontal(true);
                            ${'graph' . $i}->setBarColor("#6da2ff");
                            ${'graph'.$i}->setDataValues(true);
                            ${'graph'.$i}->setDataValueColor("red");
                            ${'graph' . $i}->createGraph();
                            $pdf->Image("../views/Report/generated_graphs/assesmentsgraph".$i."_".$d.".png");
                        }
                        //draw topics Histograms if selected
                        if (isset($_POST['graphicTopicScore'])) {
                            $graphdatatopic = $db->qLoadTopicScores($topics,$exam,$student, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'], $_SESSION['datein'], $_SESSION['datefn']);
                            ${'topics' . $i} = new PHPGraphLib(700, 350, "../views/Report/generated_graphs/topicsgraph".$i."_".$d.".png");
                            ${'topics' . $i}->addData($graphdatatopic);
                            ${'topics' . $i}->setTitle("Topics Scores");
                            ${'topics' . $i}->setTextColor("black");
                            ${'topics' . $i}->setXValuesHorizontal(true);
                            ${'topics' . $i}->setBarColor("green");
                            ${'topics'.$i}->setDataValues(true);
                            ${'topics'.$i}->setDataValueColor("red");
                            ${'topics' . $i}->createGraph();
                            $pdf->Image("../views/Report/generated_graphs/topicsgraph".$i."_".$d.".png");
                        }
                        $d++;//counter for students
                    }
                    $i++; //counter of assesments
                }
            }
        }
        $pdf->Output();
        $t=time();
        //creo la cartella Creport se non esiste
        $dir = $config['systemViewsDir']."Report/generated_report/AOreport";
        if (file_exists($dir)==false){
            mkdir($config['systemViewsDir']."Report/generated_report/AOreport");
        }
        //creo la cartella dell'examiner se non esiste
        $dir = $config['systemViewsDir']."Report/generated_report/AOreport/".$user->surname."_".$user->name;
        if (file_exists($dir)==false){
            mkdir($config['systemViewsDir']."Report/generated_report/AOreport/".$user->surname."_".$user->name);
        }
        $pdf->Output($config['systemViewsDir']."Report/generated_report/AOreport/".$user->surname."_".$user->name."/AOreport_".$user->surname."_".$user->name."_".date("d-m-Y_H:i:s",$t).".pdf","F");
    }

    /**
     *  @name   actionSavetemplate
     *  @descr  Save a Report Template
     */
    private function actionSavetemplate(){
        if (isset($_POST['assesmentName'])){
            $assesmentName=1;
        }else{$assesmentName=0;}
        if (isset($_POST['assesmentID'])){
            $assesmentID=1;
        }else{$assesmentID=0;}
        if (isset($_POST['assesmentAuthor'])){
            $assesmentAuthor=1;
        }else{$assesmentAuthor=0;}
        if (isset($_POST['assesmentDateTimeFirst'])){
            $assesmentDateTimeFirst=1;
        }else{$assesmentDateTimeFirst=0;}
        if (isset($_POST['assesmentDateTimeLast'])){
            $assesmentDateTimeLast=1;
        }else{$assesmentDateTimeLast=0;}
        if (isset($_POST['assesmentLeastTimeFinished'])){
            $assesmentLeastTimeFinished=1;
        }else{$assesmentLeastTimeFinished=0;}
        if (isset($_POST['assesmentNumberStarted'])){
            $assesmentNumberStarted=1;
        }else{$assesmentNumberStarted=0;}
        if (isset($_POST['assesmentNumberNotFinished'])){
            $assesmentNumberNotFinished=1;
        }else{$assesmentNumberNotFinished=0;}
        if (isset($_POST['assesmentNumberFinished'])){
            $assesmentNumberFinished=1;
        }else{$assesmentNumberFinished=0;}
        if (isset($_POST['assesmentMinscoreFinished'])){
            $assesmentMinscoreFinished=1;
        }else{$assesmentMinscoreFinished=0;}
        if (isset($_POST['assesmentMaxscoreFinished'])){
            $assesmentMaxscoreFinished=1;
        }else{$assesmentMaxscoreFinished=0;}
        if (isset($_POST['assesmentMediumFinished'])){
            $assesmentMediumFinished=1;
        }else{$assesmentMediumFinished=0;}
        if (isset($_POST['assesmentMostTimeFinished'])){
            $assesmentMostTimeFinished=1;
        }else{$assesmentMostTimeFinished=0;}
        if (isset($_POST['assesmentMediumTimeFinished'])){
            $assesmentMediumTimeFinished=1;
        }else{$assesmentMediumTimeFinished=0;}
        if (isset($_POST['assesmentStdDeviation'])){
            $assesmentStdDeviation=1;
        }else{$assesmentStdDeviation=0;}
        if (isset($_POST['topicAverageScore'])){
            $topicAverageScore=1;
        }else{$topicAverageScore=0;}
        if (isset($_POST['topicMinimumScore'])){
            $topicMinimumScore=1;
        }else{$topicMinimumScore=0;}
        if (isset($_POST['topicMaximumScore'])){
            $topicMaximumScore=1;
        }else{$topicMaximumScore=0;}
        if (isset($_POST['topicStdDeviation'])){
            $topicStdDeviation=1;
        }else{$topicStdDeviation=0;}
        if (isset($_POST['graphicHistogram'])){
            $graphicHistogram=1;
        }else{$graphicHistogram=0;}
        if (isset($_POST['graphicTopicScore'])){
            $graphicTopicScore=1;
        }else{$graphicTopicScore=0;}
        $db=new sqlDB();
        if($db->qInsertTemplate($_POST['templateName'],$assesmentName,$assesmentID,$assesmentAuthor,$assesmentDateTimeFirst,$assesmentDateTimeLast,$assesmentNumberStarted,$assesmentNumberNotFinished,$assesmentNumberFinished,$assesmentMinscoreFinished,$assesmentMaxscoreFinished,$assesmentMediumFinished,$assesmentLeastTimeFinished,$assesmentMostTimeFinished,$assesmentMediumTimeFinished,$assesmentStdDeviation,$topicAverageScore,$topicMinimumScore,$topicMaximumScore,$topicStdDeviation,$graphicHistogram,$graphicTopicScore)){
            echo "true";
        }
    }
    private function actionLoadtemplate(){
        global $log;
        $db=new sqlDB();
        $checkbox=json_encode($db->qLoadCheckboxTemplate($_POST['templateName']));
        echo $checkbox;
    }
private function actionDeletetemplate(){
        global $log;
        $db=new sqlDB();
        if (!($db->qDeleteReportTemplate($_POST['templateName']))){echo "error";}
    }



    //Creport

    /**
     *  @name   actionCreport
     *  @descr  Shows Creport home page
     */
    private function actionCreport(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionShowstudentcreport
     *  @descr  Shows report index page
     */
    private function actionShowstudentcreport(){

        $db=new sqlDB();

        if(!($db->qShowStudentCreport($_POST['exam'],$_POST['minscore'],$_POST['maxscore'],$_POST['datein'],$_POST['datefn']))){
            echo "query error check the log file";
        }
    }


    /**
     *  @name   actionCreportparameters
     *  @descr  Set parameters for AOreport
     */
    private function actionCreportparameters(){

        $_SESSION['CRuser']=$_POST['CRiduser'];
        $_SESSION['CRexam']=$_POST['CRexam'];
        $_SESSION['CRminscore']=$_POST['CRminscore'];
        $_SESSION['CRmaxscore']=$_POST['CRmaxscore'];
        $_SESSION['CRdatein']=$_POST['CRdatein'];
        $_SESSION['CRdatefn']=$_POST['CRdatefn'];

    }

    /**
     *  @name   actionCreportlist
     *  @descr  Shows list of all the test done
     */
    private function actionCreportlist(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionShowtestscreport
     *  @descr  Shows all the test for creport
     */
    private function actionShowtestscreport(){
        $db=new sqlDB();

        if(!($db->qShowTestsCreport($_SESSION['CRuser'],$_SESSION['CRexam'],$_SESSION['CRminscore'],$_SESSION['CRmaxscore'],$_SESSION['CRdatein'],$_SESSION['CRdatefn']))){
            echo "errore query caricamento test";
        }

    }

    /**
     *  @name   actionLoadcreportresult
     *  @descr  Load parameters for specific Test
     */
    private function actionLoadcreportresult(){
        global $engine;
        $_SESSION['CRdateTaken']=$_POST['dateTaken'];
        $_SESSION['CRscoreFinal']=$_POST['scoreFinal'];
        $_SESSION['CRstatus']=$_POST['status'];
        $_SESSION['CRidTest']=$_POST['idTest'];
    }

    /**
     *  @name   actionCreportpdf
     *  @descr  Shows the report
     */
    private function actionCreportpdf(){
        global $config,$user;
        include($config['systemPhpGraphLibDir'].'phpgraphlib.php');
        include($config['systemFpdfDir'].'fpdf.php');
        $db=new sqlDB();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica','B',22);
        $pdf->Image("themes/default/images/eol.png");
        $pdf->Cell(0,15,ttReportCoaching,1,1,'C',false);
        $pdf->Cell(0,5,"",0,1);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(30,10,ttReportPartecipant,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(70,10,$db->qLoadStudent($_SESSION['CRuser']),"B",0);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(40,10,ttStudentDetailCreport,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(45,10,"User_".$_SESSION['CRuser'],"B",1);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(30,10,ttGroup,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(70,10,$db->qLoadGroup($_SESSION['CRuser']),"B",0);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(40,10,ttStatus,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(45,10,$_SESSION['CRstatus'],"B",1);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(40,10,ttReportAssesmentName,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(60,10,$_SESSION['CRexam'],"B",0);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(40,10,ttScoreFinal,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(45,10,$_SESSION['CRscoreFinal'],"B",1);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(40,10,ttTimeUsed,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(60,10,$db->qLoadTimeUsed($_SESSION['CRidTest']),"B",0);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(40,10,ttTimeLimit,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(45,10,$db->qLoadTestTimeLimit($_SESSION['CRidTest']),"B",1);
        $pdf->SetFont('Helvetica','B',12);
        $pdf->Cell(40,10,ttReportDateTaken,"B",0);
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(45,10,$_SESSION['CRdateTaken'],"B",0);
        $pdf->Cell(100,10,"","B",1);
        $pdf->Cell(0,5,"",0,1);
        $pdf->SetFont('Helvetica','B',16);
        $num=$db->qLoadTestNumQuestions($_SESSION['CRidTest']);
        $pdf->Cell(0,10,ttQuestions." - ".ttQuestionsPresented.": ".$num['qpresented'].", ".ttQuestionsAnswered.": ".$num['qanswered'],0,1);
        $pdf->Cell(0,3,"",0,1);
        $questions=$db->qLoadTestQuestions($_SESSION['CRidTest']);
        $i=1;
        //select lang to load for question & answer
        $langs=get_required_files();
        foreach($langs as $lang){
            if(strpos($lang,"it/lang.php")){
                $idLang=2;
            }
            if(strpos($lang,"en/lang.php")){
                $idLang=1;
            }
        }
        $d=0;
        foreach($questions as $question){
            $details=$db->qShowQuestionsDetails($_SESSION['CRidTest'],$idLang,$question);
            //print_r($details);
            // if ($i==2){
            //     $pdf->Addpage();
            //     $d=0;
            // }else{
            //     if(($d % 3==0) && ($d!=0)){
            //         $pdf->AddPage();
            //         $d=0;
            //     }
            // }
            //var_dump($details);
            $pdf->SetFont('Helvetica','B',20);
            $pdf->Cell(10,10,$i,1,0,"C");
            $pdf->SetFont('Helvetica','B',12);
            $pdf->SetTextColor(255,0,0);
            //qui
            $pdf->MultiCell(160,10,$details['questionText'],1,'L',0);
            if ($details['score']>0){
                if ($details['maxScore']==$details['score']){
                    $pdf->Image($config['themeImagesDir'].'done.png',185,null,10);
                }
                else{
                    $pdf->Image($config['themeImagesDir'].'Inactive.png',185,null,10);
                }
            }else{
                $pdf->Image($config['themeImagesDir'].'False.png',185,null,10);

            }
            $pdf->Cell(0,3,"",0,1);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,ttReportQuestionType,0,0,"");
            $pdf->SetFont('Helvetica','',12);
            switch ($details['qtype']) {
                case "MC":
                    $pdf->Cell(50,10,ttQTMC,0,1,"");
                    break;
                case "MR":
                    $pdf->Cell(50,10,ttQTMR,0,1,"");
                    break;
                case "YN":
                    $pdf->Cell(50,10,ttQTYN,0,1,"");
                    break;
                case "TF":
                    $pdf->Cell(50,10,ttQTTF,0,1,"");
                    break;
                case "ES":
                    $pdf->Cell(50,10,ttQTES,0,1,"");
                    break;
                case "NM":
                    $pdf->Cell(50,10,ttQTNM,0,1,"");
                case "TM":
                    $pdf->Cell(50,10,ttQTTM,0,1,"");
                    break;
                case "PL":
                    $pdf->Cell(50,10,ttQTPL,0,1,"");
                    break;
                case "FB":
                    $pdf->Cell(50,10,"Fill in Blanks",0,1,"");
                    break;
                case "HS":
                    $pdf->Cell(50,10,"Hotspot",0,1,"");
                    break;
            }
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,ttTopic,0,0,"");
            $pdf->SetFont('Helvetica','',12);
            $pdf->Cell(50,10,$details['qtopic'],0,1,"");
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,"ID",0,0,"");
            $pdf->SetFont('Helvetica','',12);
            $pdf->Cell(50,10,$details['idQuestion'],0,1,"");
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,ttDifficulty,0,0,"");
            $pdf->SetFont('Helvetica','',12);
            $pdf->Cell(50,10,$details['difficulty'],0,1,"");
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,ttScore,0,0,"");
            $pdf->SetFont('Helvetica','',12);
            $pdf->Cell(50,10,$details['score'],0,1,"");
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,ttScoreMax,0,0,"");
            $pdf->SetFont('Helvetica','',12);
            $pdf->Cell(50,10,$details['maxScore'],0,1,"");
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,ttAnswerNum,0,0,"");
            $pdf->SetFont('Helvetica','',12);
            $pdf->MultiCell(90,10,$details['answerNum'],0,1,"");
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(80,10,ttAnswer,0,0,"");
            $pdf->SetFont('Helvetica','',12);
            $pdf->MultiCell(90,10,$details['answerText'],0,1,"");

            $i++;//questions counter
            $d++;
        }
        $t=time();
        //creo la cartella Creport se non esiste
        $dir = $config['systemViewsDir']."Report/generated_report/Creport";
        if (file_exists($dir)==false){
            mkdir($config['systemViewsDir']."Report/generated_report/Creport");
        }
        //creo la cartella dell'examiner se non esiste
        $dir = $config['systemViewsDir']."Report/generated_report/Creport/".$user->surname."_".$user->name;
        if (file_exists($dir)==false){
            mkdir($config['systemViewsDir']."Report/generated_report/Creport/".$user->surname."_".$user->name);
        }
       // $pdf->Output($config['systemViewsDir']."Report/generated_report/Creport/".$user->surname."_".$user->name."/Creport_".$user->surname."_".$user->name."_".date("d-m-Y_H:i:s",$t).".pdf","F");
        $pdf->Output();


    }


    private function replaceCharacter($word) {
        $word = str_replace("@","%40",$word);
        $word = str_replace("`","%60",$word);
        $word = str_replace("Â¢","%A2",$word);
        $word = str_replace("Â£","%A3",$word);
        $word = str_replace("Â¥","%A5",$word);
        $word = str_replace("|","%A6",$word);
        $word = str_replace("Â«","%AB",$word);
        $word = str_replace("Â¬","%AC",$word);
        $word = str_replace("Â¯","%AD",$word);
        $word = str_replace("Âº","%B0",$word);
        $word = str_replace("Â±","%B1",$word);
        $word = str_replace("Âª","%B2",$word);
        $word = str_replace("Âµ","%B5",$word);
        $word = str_replace("Â»","%BB",$word);
        $word = str_replace("Â¼","%BC",$word);
        $word = str_replace("Â½","%BD",$word);
        $word = str_replace("Â¿","%BF",$word);
        $word = str_replace("Ã€","%C0",$word);
        $word = str_replace("Ã","%C1",$word);
        $word = str_replace("Ã‚","%C2",$word);
        $word = str_replace("Ãƒ","%C3",$word);
        $word = str_replace("Ã„","%C4",$word);
        $word = str_replace("Ã…","%C5",$word);
        $word = str_replace("Ã†","%C6",$word);
        $word = str_replace("Ã‡","%C7",$word);
        $word = str_replace("Ãˆ","%C8",$word);
        $word = str_replace("Ã‰","%C9",$word);
        $word = str_replace("ÃŠ","%CA",$word);
        $word = str_replace("Ã‹","%CB",$word);
        $word = str_replace("ÃŒ","%CC",$word);
        $word = str_replace("Ã","%CD",$word);
        $word = str_replace("ÃŽ","%CE",$word);
        $word = str_replace("Ã","%CF",$word);
        $word = str_replace("Ã","%D0",$word);
        $word = str_replace("Ã‘","%D1",$word);
        $word = str_replace("Ã’","%D2",$word);
        $word = str_replace("Ã“","%D3",$word);
        $word = str_replace("Ã”","%D4",$word);
        $word = str_replace("Ã•","%D5",$word);
        $word = str_replace("Ã–","%D6",$word);
        $word = str_replace("Ã˜","%D8",$word);
        $word = str_replace("Ã™","%D9",$word);
        $word = str_replace("Ãš","%DA",$word);
        $word = str_replace("Ã›","%DB",$word);
        $word = str_replace("Ãœ","%DC",$word);
        $word = str_replace("Ã","%DD",$word);
        $word = str_replace("Ãž","%DE",$word);
        $word = str_replace("ÃŸ","%DF",$word);
        $word = str_replace("Ã ","%E0",$word);
        $word = str_replace("Ã¡","%E1",$word);
        $word = str_replace("Ã¢","%E2",$word);
        $word = str_replace("Ã£","%E3",$word);
        $word = str_replace("Ã¤","%E4",$word);
        $word = str_replace("Ã¥","%E5",$word);
        $word = str_replace("Ã¦","%E6",$word);
        $word = str_replace("Ã§","%E7",$word);
        $word = str_replace("Ã¨","%E8",$word);
        $word = str_replace("Ã©","%E9",$word);
        $word = str_replace("Ãª","%EA",$word);
        $word = str_replace("Ã«","%EB",$word);
        $word = str_replace("Ã¬","%EC",$word);
        $word = str_replace("Ã­","%ED",$word);
        $word = str_replace("Ã®","%EE",$word);
        $word = str_replace("Ã¯","%EF",$word);
        $word = str_replace("Ã°","%F0",$word);
        $word = str_replace("Ã±","%F1",$word);
        $word = str_replace("Ã²","%F2",$word);
        $word = str_replace("Ã³","%F3",$word);
        $word = str_replace("Ã´","%F4",$word);
        $word = str_replace("Ãµ","%F5",$word);
        $word = str_replace("Ã¶","%F6",$word);
        $word = str_replace("Ã·","%F7",$word);
        $word = str_replace("Ã¸","%F8",$word);
        $word = str_replace("Ã¹","%F9",$word);
        $word = str_replace("Ãº","%FA",$word);
        $word = str_replace("Ã»","%FB",$word);
        $word = str_replace("Ã¼","%FC",$word);
        $word = str_replace("Ã½","%FD",$word);
        $word = str_replace("Ã¾","%FE",$word);
        $word = str_replace("Ã¿","%FF",$word);        
        $word = str_replace("Å‘","o",$word);    
        $word = str_replace("Å»","Z",$word);    
        $word = str_replace("Å‚","l",$word);    
        $word = str_replace("Ä…","a",$word);
        $word = str_replace("â€™","'",$word);
        $word = str_replace("Ä™","e",$word);        
        $word = str_replace("Åš","S",$word);
        $cyr = [
            'Ð°','Ð±','Ð²','Ð³','Ð´','Ðµ','Ñ‘','Ð¶','Ð·','Ð¸','Ð¹','Ðº','Ð»','Ð¼','Ð½','Ð¾','Ð¿',
            'Ñ€','Ñ','Ñ‚','Ñƒ','Ñ„','Ñ…','Ñ†','Ñ‡','Ñˆ','Ñ‰','ÑŠ','Ñ‹','ÑŒ','Ñ','ÑŽ','Ñ',
            'Ð','Ð‘','Ð’','Ð“','Ð”','Ð•','Ð','Ð–','Ð—','Ð˜','Ð™','Ðš','Ð›','Ðœ','Ð','Ðž','ÐŸ',
            'Ð ','Ð¡','Ð¢','Ð£','Ð¤','Ð¥','Ð¦','Ð§','Ð¨','Ð©','Ðª','Ð«','Ð¬','Ð­','Ð®','Ð¯'
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


private function actionResultsexams(){
        global $config,$user;
        $nameReport="report".$user->iduser;
        $path = "temp/".$nameReport.".csv";
        $path=stripslashes($path);
        $file = fopen($path,"w");
        chmod($path,0777);
        $db=new sqlDB();
        $a = ttName."#".ttDay."#".ttSubject."#".ttSubgroup."#".ttStudent;
        $title = array($a);
        fputcsv($file,explode("#", $title[0]));
        if($db->qExams()){
            while($exam = $db->nextRowAssoc()){
                $name = $exam['exam'];
                $day = date('d/m/Y', strtotime($exam['datetime']));
                $subject = $exam['subject'];

                $db2 = new sqlDB();
                $subGroupDescription="";
                if($exam['EsubG']!=null){
                    if($db2->qGetSubGroupName($exam['EsubG'])){
                        $subGroupDescription=$db2->nextRowAssoc()["NameSubGroup"];
                    }
                }

                $numberOfStudents=0;
                if($exam['idExam']!=null){
                    if($db2->qCountStudentForExam($exam['idExam'])){
                        $numberOfStudents=$db2->nextRowAssoc()["numberOfStudents"];
                        if($numberOfStudents=="")
                            $numberOfStudents=0;
                    }
                }
                
                $rigaCSV = $name."#".$day."#".$subject."#".$subGroupDescription."#".$numberOfStudents;
                $rigaCSV = array($rigaCSV);
                fputcsv($file,explode("#", $rigaCSV[0]));
            }
            echo json_encode(array("success",$path));
        }else{
            echo json_encode(array("error","error"));
        }
        fclose($file);
    }



    private function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('Index', 'Aoreport','Showassesments','Showpartecipant',
                    'Showstudent','Addstudent','Aoreporttemplate','Showparticipantdetails',
                    'Printparticipantdetails','Aoreportparameters','Showgroups','Aoreportresult',
                    'Savetemplate','Loadtemplate','Deletetemplate',
                    'Creport','Showstudentcreport','Creportparameters','Creportlist',
                    'Showtestscreport','Loadcreportresult','Creportpdf','Resultstudent','Resultsexams'),
                'roles'   => array('a','e','t','at'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }
}
