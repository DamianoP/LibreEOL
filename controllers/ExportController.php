<?php

class ExportController extends Controller
{
    public $defaultAction = 'Index';

    public function executeAction(string $action)
    {
        global $user;

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

    private function actionExportsubjects()
    {
        global $user, $log;
        $sql = new sqlDB();
        if ($sql->qExportRequests()) {
            $rows = $sql->getResultAssoc();
            //invio della mail per ogni record della query
            for ($i = 0; $i < count($rows); $i++) {
                try {
                    $row = $rows[$i];

                    //creazione dello zip della materia
                    $zipname = "subject_" . $row['subject'] . "_" . date("YmdHms") . ".zip";
                    $currentSubject = new ZipArchive();
                    if($currentSubject->open($zipname, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true){
                        throw new Exception("error in zip file creation");
                    }

                    if ($row['type'] === 'moodle') {
                        include(dirname(__FILE__) . "/../includes/MoodleXMLDocument.php");
                        $currentSubject->addFromString($row['subject'].'.xml', $this->createSubjectXMLMoodle($row['subject']));

                    } else {
                        include(dirname(__FILE__) . "/../includes/QTIXMLDocument.php");
                        $qti = $this->createSubjectXMLQTI($row['subject']);
                        $currentSubject->addFromString($row['subject'].'.xml', $qti['doc']);
                        foreach ($qti['res'] as $res){
                            echo $_SERVER['DOCUMENT_ROOT'].$res."<br>";
                            if(file_exists($_SERVER['DOCUMENT_ROOT'].$res)) {
                                $pathExpl = explode('/', $res);
                                $currentSubject->addFile($_SERVER['DOCUMENT_ROOT'] . $res, end($pathExpl));
                            }
                        }
                    }
                    echo $currentSubject->getStatusString();
                    if($currentSubject->close() !== true){
                        throw new Exception($currentSubject->getStatusString(). "error in zip file creation");
                    };

                    //setting dell'header della mail
                    $headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
                    $headers .= "From: info@libreeol.org\r\n";  //$user->email /paolobitini.tesista@libreeol.org
                    $headers .= "Reply-To: $user->email";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    $headers .= "Content-Type: multipart/mixed;";
                    $headers .= "boundary=\"" . "PHP-mixed-" . md5(time()) . "\"";

                    //invio della mail
                    if (!mail($row['email'], 'No-reply. Subject exporting',
                        $this->createMessage("your file requested", $zipname, $row['subject']), $headers)) {
                        $log->append(__FUNCTION__." email not sended: " . error_get_last()['message'] . "\n"); // in caso di errore mostra il messaggio
                    } else {
                        //aggiornamento database
                        if (!$sql->qUpdateExportRequest($row['subject'])) {
                            $log->append(__FUNCTION__. " update request error: " . $sql->getError());
                        }
                    }
                    unlink($zipname);
                } catch (Exception $err) {
                    echo __FUNCTION__. " exception: " . $err->getMessage() . "\nline: " . $err->getLine() . "\ncode: " . $err->getCode() . "\ntrace: " . $err->getTrace();
                    $log->append(__FUNCTION__. " exception: " . $err->getMessage() . "\nline: " . $err->getLine() . "\ncode: " . $err->getCode() . "\ntrace: " . $err->getTrace());
                }
            }
        } else {
            $log->append(__FUNCTION__." query error: ".$sql->getError());
        }
    }

    private function actionExportrequest()
    {
        $idSubject = $_POST['idSubject'];
        $type = $_POST['type'];

        $controlSubject = $this->controlSubject($idSubject);

        if ($controlSubject === 'OK') {
            $sql = new sqlDB();
            if ($sql->qInsertExportRequest($idSubject, $type)) {
                echo 'ACK';
            } else {
                echo $sql->getError();
            }
        } else {
            echo $controlSubject;
        }

    }

    private function createSubjectXMLMoodle($idSubject)
    {
        global $log;
        $sql = new sqlDB();

        if ($sql->qSubjectQuestionsAndAnswers($idSubject)) {

            $xml = new MoodleXMLDocument();

            $currentTopic = null;
            $currentQuestion = null;
            $rows = $sql->getResultAssoc();

            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];

                if ($currentTopic != $row['idTopic']) {
                    $currentTopic = $row['idTopic'];
                    if (!$xml->createCategory($row["topicName"], " ", $row['idTopic'])) {
                        $log->append($xml->getError());
                        die($xml->getError());
                    }
                }

                if ($currentQuestion != $row['idQuestion']) {
                    $currentQuestion = $row['idQuestion'];
                    if(!$xml->createQuestion($row['questionType'], $row['idQuestion'], $row['questionName'], $row['questionText'])){
                        $log ->append($xml->getError());
                        die($xml->getError());
                    }
                }

                if(!$xml->createAnswer($row['idAnswer'], $row['answerText'], $row['answerScore'], "")){
                    $log ->append($xml->getError());
                    die($xml->getError());
                }
            }
            return $xml->getDoc();

        } else {
            return $sql->getError();
        }
    }

    private function createSubjectXMLQTI($idSubject)
    {
        global $log;
        $sql = new sqlDB();

        if ($sql->qSubjectQuestionsAndAnswers($idSubject)) {

            $xml = new QTIXMLDocument();
            $rows = $sql->getResultAssoc();

            $i = 0;
            while ($i < count($rows)) {

                $row = $rows[$i];
                $topic = $row['topicName'];
                $questionName = $row['questionName'];
                $questionText = $row['questionText'];
                $questionType = $row['questionType'];
                $currentQuestion = $row['idQuestion'];
                $answers = [];
                $index = 0;

                // converting the answers in the right format (like the one in the next comment)
                while ($currentQuestion == $rows[$i]['idQuestion']) {
                    $row = $rows[$i];
                    $answer = $this->convertAnswer($index, $row['answerText'], $row['answerScore'], null, null, $questionType);
                    array_push($answers, $answer);
                    $i++;
                    $index++;
                }

                // addItemNode wants an associative array for the answers:   answers = [['id'=>id, 'text'=>text, 'score'=>score, 'feedback'=>feedback, 'feedbackId'=>feedbackId],['id'=>id2,..]..]
                if(!$xml->addItemNode($topic, $currentQuestion, $questionName, $questionText, $questionType, $answers)){
                    $log ->append($xml->getError());
                    die($xml->getError());
                }

            }
            return ['doc'=> $xml->getDoc(), 'res' => $xml->getResources()];
        } else {
            return $sql->getError();
        }
    }

    private function createMessage($mailMessage, $zipName, $idSubject): string
    {
        $attachment = chunk_split(base64_encode(file_get_contents($zipName)));

        $boundary = "PHP-mixed-" . md5(time());
        $boundWithPre = "\n--" . $boundary;

        $message = $boundWithPre;
        $message .= "\n Content-Type: text/plain; charset=UTF-8\n";
        $message .= "\n $mailMessage";

        $message .= $boundWithPre . "\r\n";
        $message .= "Content-Type: application/zip; name= " . $zipName . "\r\n";
        $message .= "Content-Disposition: attachment; filename = " . $zipName . "\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-length: " . filesize($zipName)."\r\n";
        $message .= "Pragma: no-cache\r\n";
        $message .= "Expires: 0\r\n";
        $message .= "X-Attachment-Id: ".$idSubject."\r\n\r\n";
        $message .= $attachment;

        return $message;
    }

    //this function check if the subject is well-formed, for example i consider error the fact that there is a non-essay question without answers
    private function controlSubject($idSubject): string
    {
        $currentQuestion = -1;
        $sql = new sqlDB();
        if ($sql->qSubjectQuestionsAndAnswers($idSubject)) { // se non sono presenti domande o topic viene considerata vuota
            $rows = $sql->getResultAssoc();
            if (empty($rows)) {
                return 'EMPTYSUB';
            }
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                if ($currentQuestion === $row['idQuestion']) {
                    continue;
                } else {
                    $currentQuestion = $row['idQuestion'];
                }

                if ($row['questionType'] !== 'ES' && $row['idAnswer'] === null) {   //domanda non aperta senza risposte
                    return 'NOANSWER';
                }

                if ($row['questionType'] === 'MC' || $row['questionType'] === 'MR') {  //domande di tipo MR e MC con una sola risposta
                    if ($i + 1 < count($rows)) {
                        $nextRow = $rows[$i + 1];
                        if ($currentQuestion !== $nextRow['idQuestion']) {
                            return "NOEANSWERS";
                        }

                    } else {
                        return "NOEANSWERS";
                    }
                }
            }
            return 'OK';
        } else {
            return $sql->getError();
        }
    }

    //this function convert the answer from the database in a better format for the qti document
    private function convertAnswer($index, $text, $score, $feedback, $feedbackId, $type): array
    {
        $answerId = null;
        $answerText = null;
        $answerScore = $this->convertScoreYNTF($score);

        switch ($type) {
            case 'MC':
            case 'MR':
                $answerId = chr(65 + $index); //chr convert a number in the relative char from the ASCII code (for example 65 is 'A')
                $answerText = $text;                   // in the response_label node (of response_lid type questions) is needed an id for the label and letters are commonly used
                break;
            case 'YN':
            case 'TF':
                $answerId = chr(65 + $index);
                $answerText = $this->getAnswerFromScoreYNTF($score);
                break;
            case 'NM':
            case 'TM':
                $answerId = $text;
                $answerText = $text;
                break;
            default:
                $answerId = 'essay';
                $answerText = 'essay';
        }

        return array(
            'id' => $answerId,
            'text' => $answerText,
            'score' => $answerScore,
            'feedback' => $feedback,
            'feedbackId' => $feedbackId
        );
    }

    //this function convert the score of the yes/no true/false questions, in the qti format the score must be a number
    private function convertScoreYNTF($score)
    {
        switch ($score) {
            case 'N*1':
            case 'Y*1':
            case 'T*1':
            case 'F*1':
                return 10;
            case 'N*0':
            case 'Y*0':
            case 'T*0':
            case 'F*0':
                return 0;
            default:
                return $score * 10;
        }
    }

    //this function get the answer text from yes/no true/false score, since in the database there are no answer texts for those categories
    private function getAnswerFromScoreYNTF($score): string
    {
        switch ($score) {
            case 'T*1':
            case 'T*0':
                return 'True';
            case 'F*0':
            case 'F*1':
                return 'False';
            case 'Y*1':
            case 'Y*0':
                return 'Yes';
            case 'N*1':
            case 'N*0':
                return 'No';
            default:
                return '';
        }
    }

    private function accessRules(): array
    {

        return array(
            array(
                'allow',
                'actions' => array('Exportrequest'),
                'roles' => array('t'),
            ),
            array(
                'allow',
                'actions' => array('Exportrequest'),
                'roles' => array('e'),
            ),
            array(
                'allow',
                'actions' => array('Exportsubjects'),
                'roles' => array('a'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles' => array('*'),
            ),
        );
    }
}
