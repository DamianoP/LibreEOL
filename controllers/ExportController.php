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
        include(dirname(__FILE__) . "/../includes/MoodleXMLDocument.php");
        global $user, $log;
        $sql = new sqlDB();
        if ($sql->qExportRequests()) {
            $rows = $sql->getResultAssoc();
            //invio della mail per ogni record della query
            for ($i = 0; $i < count($rows); $i++) {
                try {
                    $row = $rows[$i];

                    //creazione del xml della materia
                    $currentSubject = $this->createSubjectXMLMoodle($row['subject']);

                    //setting dell'header della mail
                    $headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
                    $headers .= "From: info@libreeol.org\r\n";  //$user->email /paolobitini.tesista@libreeol.org
                    $headers .= "Reply-To: $user->email";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    $headers .= "Content-Type: multipart/mixed;";
                    $headers .= "boundary=\"" . "PHP-mixed-" . md5(time()) . "\"";

                    //invio della mail
                    if (!mail($row['email'], 'No-reply. Subject exporting',
                        $this->createMessage("your file requested", $currentSubject, $row['subject']), $headers)) {
                        $log->append(__FUNCTION__." email not sended: " . error_get_last()['message'] . "\n"); // in caso di errore mostra il messaggio
                    } else {
                        //aggiornamento database
                        if (!$sql->qUpdateExportRequest($row['subject'])) {
                            $log->append(__FUNCTION__. " update request error: " . $sql->getError());
                        }
                    }
                } catch (Exception $err) {
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

        $controlSubject = $this->controlSubject($idSubject);

        if ($controlSubject === 'OK') {
            $sql = new sqlDB();
            if ($sql->qInsertExportRequest($idSubject)) {
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
                    if(!$xml->createCategory($row["topicName"], " ", $row['idTopic'])){
                        $log ->append($xml->getError());
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

                while ($currentQuestion == $rows[$i]['idQuestion']) {
                    $row = $rows[$i];
                    $answer = $this->convertAnswer($index, $row['answerText'], $row['answerScore'], null, null, $questionType);
                    array_push($answers, $answer);
                    $i++;
                    $index++;
                }
                $xml->addItemNode($topic, $currentQuestion, $questionName, $questionText, $questionType, $answers);

            }
            return $xml->getDoc();

        } else {
            return $sql->getError();
        }
    }

    private
    function createMessage($mailMessage, $subject, $idSubject): string
    {
        $attchmentName = "subject_" . $idSubject . "_" . date("YmdHms") . ".xml";
        $attachment = chunk_split(base64_encode($subject));

        $boundary = "PHP-mixed-" . md5(time());
        $boundWithPre = "\n--" . $boundary;

        $message = $boundWithPre;
        $message .= "\n Content-Type: text/plain; charset=UTF-8\n";
        $message .= "\n $mailMessage";

        $message .= $boundWithPre . "\r\n";
        $message .= "Content-Type: application/xml; name= " . $attchmentName . "\r\n";
        $message .= "Content-Disposition: attachment; filename = " . $attchmentName . "\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "X-Attachment-Id: ".$idSubject."\r\n\r\n";
        $message .= $attachment;

        return $message;
    }

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

    private function convertAnswer($index, $text, $score, $feedback, $feedbackId, $type): array
    {
        $answerId = null;
        $answerText = null;
        $answerScore = $this->convertScoreYNTF($score);

        switch ($type) {
            case 'MC':
            case 'MR':
                $answerId = chr(65 + $index);
                $answerText = $text;
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
                $answerId = 0;
                $answerText = 'undefined';
        }

        return array(
            'id' => $answerId,
            'text' => $answerText,
            'score' => $answerScore,
            'feedback' => $feedback,
            'feedbackId' => $feedbackId
        );
    }

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

    private
    function accessRules(): array
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
