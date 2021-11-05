<?php

/**
 * File: ReportController.php
 * User: Masterplan
 * Date: 4/19/13
 * Time: 10:04 AM
 * Desc: Controller for all Report operations
 */
class ReportController extends Controller
{
    /**
     * @name   ReportController
     * @descr  Creates an instance of ReportController class
     */
    public function ReportController()
    {
    }

    /**
     * @name    executeAction
     * @param   $action         String      Name of requested action
     * @descr   Executes action (if exists and if user is allowed)
     */
    public function executeAction($action)
    {
        global $user, $log;
        // If have necessary privileges execute action
        if ($this->getAccess($user, $action, $this->accessRules())) {
            $action = 'action' . $action;
            $this->$action();
            // Else, if user is not logged bring him the to login page
        } elseif ($user->role == '?') {
            header('Location: index.php?page=login');
            // Otherwise: Access denied
        } else {
            Controller::error('AccessDenied');
        }
    }

    /**
     * @name   actionIndex
     * @descr  Shows report index page
     */
    private function actionIndex()
    {
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
     * @name   actionResultstudent
     * @descr  Shows the report
     */
    private function actionResultstudent()
    {
        global $config, $user, $log;
        $idExam = $_POST["idExam"];
        $subject = $_POST["subject"];
        $subject = str_replace(" ", "_", $subject);
        $subject = str_replace("/", "_", $subject);
        $date = $_POST["date"];
        $date = explode("/", $date);
        $date = $date[0] . "-" . $date[1] . "-" . $date[2];

        $dir = $config['systemViewsDir'] . "Report/generated_report/RatingExam";
        if (file_exists($dir) == false) {
            mkdir($config['systemViewsDir'] . "Report/generated_report/RatingExam");
        }
        $dir = $config['systemViewsDir'] . "Report/generated_report/RatingExam/" . $subject;
        if (file_exists($dir) == false) {
            mkdir($config['systemViewsDir'] . "Report/generated_report/RatingExam/" . $subject);
        }
        if (file_exists("temp") == false) {
            mkdir("temp");
        }
        $path = $dir . "/" . $date . ".csv";
        $path2 = "temp/" . $subject . "_" . $date . ".csv";
        $path2 = "temp/" . $date . ".csv";
        $file = fopen($path, "w");
        $file2 = fopen($path2, "w");
        chmod($path, 0777);
        chmod($path2, 0777);
        $db = new sqlDB();
        if ($db->qGetRatingExam($idExam)) {
            if ($config['dbName'] == 'EOL')
                $a = ttName . "#" . ttSurname . "#" . ttEmail . "#" . ttTimeStart . "#" . ttTimeEnd . "#" . ttTimeUsed . "#" . ttScoreTest . "#" . ttFinalScore;
            else
                $a = ttName . "#" . ttSurname . "#Certificate#" . ttEmail . "#" . ttTimeStart . "#" . ttTimeEnd . "#" . ttTimeUsed . "#" . ttScoreTest . "#" . ttFinalScore;
            $db2 = new sqlDB();
            $db2->getTopicByExam($idExam);
            $h = 0;
            $listaTopic = array();
            while ($topic = $db2->nextRowAssoc()) {
                $a .= "#" . $topic["Topics"];
                $listaTopic[$h] = $topic["Topics"];
                $h++;
            }
            $title = array($a);
            fputcsv($file, explode("#", $title[0]));
            fputcsv($file2, explode("#", $title[0]));
            while ($info = $db->nextRowAssoc()) {
                $start = strtotime($info['timeStart']);
                $end = strtotime($info['timeEnd']);
                $diff = $end - $start;
                $arr['days'] = floor($diff / (60 * 60 * 24));
                $diff = $diff - (60 * 60 * 24 * $arr['days']);
                $arr['hours'] = floor($diff / (60 * 60));
                $diff = $diff - (60 * 60 * $arr['hours']);
                $arr['minutes'] = floor($diff / 60);
                $diff = $diff - (60 * $arr['minutes']);
                $arr['seconds'] = $diff;

                //if (date('d',$diff)> 0) {
                if ($arr['days'] > 0) {
                    $time = '> 24 h';
                } else {
                    //$time = $diff->format("%H:%I:%S");
                    $time = date("H:i:s", mktime($arr['hours'], $arr['minutes'], $arr['seconds']));
                }
                $info["timeDiff"] = $time;
                $db3 = new sqlDB();
                $db3->getReportDetailed($info["idTest"]);
                $scoreArray = array();
                while ($scoreTemp = $db3->nextRowAssoc()) {
                    $scoreArray[$scoreTemp["Topics"]]["punteggio"] = $scoreTemp["punteggio"];
                    $scoreArray[$scoreTemp["Topics"]]["MaxScore"] = $scoreTemp["MaxScore"];
                }
                $count = 0;
                while ($count < $h) {
                    $valore = 0;
                    $risultato = "";
                    if (isset($scoreArray[$listaTopic[$count]]))
                        $valore = $scoreArray[$listaTopic[$count]]["punteggio"];
                    if ($scoreArray[$listaTopic[$count]]["MaxScore"] > 0) {
                        $percentage = $scoreArray[$listaTopic[$count]]["MaxScore"] / 100 * 1.1;
                        if ($valore >= ($scoreArray[$listaTopic[$count]]["MaxScore"] - $percentage))
                            $risultato = "100%";
                        else
                            $risultato = round((($valore / $scoreArray[$listaTopic[$count]]["MaxScore"]) * 100), 3) . "%";
                    }
                    $info[$count] = $risultato;
                    $count++;
                }
                $info["name"] = urldecode($this->replaceCharacter($info["name"]));
                $info["surname"] = urldecode($this->replaceCharacter($info["surname"]));
                if ($info["privacy"] == 1) {
                    $info["privacy"] = "Requested";
                } else {
                    $info["privacy"] = "Not requested";
                }
                if ($config['dbName'] == 'EOL')
                    unset($info["privacy"]);
                unset($info["idTest"]);
                fputcsv($file, $info);
                fputcsv($file2, $info);
            }
            echo json_encode(array("success", $path2));

            try {
                if ($config['dbName'] == 'echemtest') { // LEVA QUESTA COSA !!!

                    $dbGroup = new sqlDB();
                    $dbGroup->qGetGroupAndSubgroupName($user->group, $user->subgroup);
                    $result = $dbGroup->nextRowAssoc();
                    $emailGroup = $result['NameGroup'];
                    $emailSubGroup = $result['NameSubGroup'];

                    exec("curl --user " . $config['usernameCertificate'] . ":" . $config['passwordCertificate'] . " -F data=@" . $path2 . " " . $config['urlCertificate'] . " >/dev/null 2>/dev/null &");


                    try {
                        $filename = $subject . "_" . $date . ".csv";
                        $emailTo = "echemtest@master-up.it";
                        $emailSubject = "Automatic notification from echemTest";
                        $emailMessage = "File sent to the server: " . $filename .
                            " \r\n The certificate was created by the user: " . $user->email .
                            " \r\n Exam date: " . $date .
                            " \r\n Group: " . $emailGroup .
                            " \r\n SubGroup: " . $emailSubGroup;
                        $emailHeaders = 'from:' . $user->email . "\r\n" .
                            'Reply-To:' . $user->email . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                        if (!mail($emailTo, $emailSubject, $emailMessage, $emailHeaders)) {
                            $log->append("Error, the email has not been sent. Filename:" . $filename . " by " . $user->email . " for the date:" . $date);
                        }
                    } catch (Exception $ex) {
                    }


                }
            } catch (Exception $ex) {
                echo $ex;
            }

        } else {
            echo json_encode(array("success", "error"));
        }
        fclose($file);
        fclose($file2);
        //echo json_encode(array("success",$path2));
    }

    /**
     * @name   actionCreport
     * @descr  Shows Coaching Report homepage
     */
    private function actionCreport()
    {
        global $engine;
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     * @name   actionCoachinglist
     * @descr  Shows list of all the test done
     */
    private function actionCoachinglist()
    {
        global $engine;
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     * @name   actionCoachingresult
     * @descr  Shows report template for Assessment Report
     */
    private function actionCoachingresult()
    {
        global $engine;
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     * @name   actionAoreport
     * @descr  Shows Assessment Report home page
     */
    private function actionAoreport()
    {
        global $engine;
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     * @name   actionAssessmentresult
     * @descr  Shows report template for Assessment Report
     */
    private function actionAssessmentresult()
    {
        global $engine;
        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     * @name   actionGetexamyears
     * @descr  Shows the years in which the exams took place
     */
    private function actionGetexamyears()
    {
        $db = new sqlDB();
        if (!($db->qGetExamYears($_POST['subject']))) {
            echo "NACK";
        } else {
            while ($year = $db->nextRowAssoc()) {
                echo "<option value='$year[examyear]'>" . $year['examyear'] . "</option>";
            }
        }
    }

    /**
     * @name   actionFindexambyyear
     * @descr  Shows exams based on subject and year selected
     */
    private function actionFindexambyyear()
    {
        $db = new sqlDB();
        if (!($db->qGetExambyYear($_POST['subject'], $_POST['year']))) {
            echo "NACK";
        } else {
            while ($row = $db->nextRowAssoc()) {
                echo "<option value='$row[idExam]'>" . $row['date'] . "</option>";
            }
        }
    }

    /*******************************************************************
     *                              Coaching Report                    *
     *******************************************************************/


    /**
     * @name   actionCoachingparameters
     * @descr  Sets session parameters for Coaching report
     */
    private function actionCoachingparameters()
    {
        $_SESSION['exam'] = $_POST['exam'];
        $_SESSION['year'] = $_POST['year'];
        $_SESSION['examdate'] = $_POST['examdate'];
        $_SESSION['subject'] = $_POST['subject'];
        $_SESSION['minscoreparam'] = $_POST['minscore'];
        $_SESSION['maxscoreparam'] = $_POST['maxscore'];
    }

    /**
     * @name   actionCoachingtestparam
     * @descr  Sets idTest as session parameter for Coaching report
     */
    private function actionCoachingtestparam()
    {
        $_SESSION['idTest'] = $_POST['idTest'];
    }


    /*******************************************************************
     *                              Assessment Report                   *
     *******************************************************************/

    /**
     * @name   actionAssessmentparameters
     * @descr  Sets session parameters for Assessment report
     */
    private function actionAssessmentparameters()
    {
        $_SESSION['examsparam'] = $_POST['exams'];
        $_SESSION['minscoreparam'] = $_POST['minscore'];
        $_SESSION['maxscoreparam'] = $_POST['maxscore'];
        $_SESSION['year'] = $_POST['year'];
    }


    private function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('Index', 'Resultstudent', 'Aoreport',
                    'Assessmentresult', 'Assessmentparameters',
                    'Creport', 'Coachingparameters', 'Coachinglist',
                    'Coachingresult', 'Coachingtestparam',
                    'Getexamyears', 'Findexambyyear'),
                'roles' => array('a', 'e', 't', 'at'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles' => array('*'),
            ),
        );
    }
}
