<?php
/**
 * File: showquestionpreview.php
 * User: Masterplan
 * Date: 17/05/14
 * Time: 16:30
 * Desc: Shows a preview of requested question in a specific translation
 */

global $config;

$idQuestion = $_POST['idQuestion'];
$idLanguage = $_POST['idLanguage'];
$type = $_POST['type'];

$db = new sqlDB();
if(($db->qQuestionInfo($idQuestion, $idLanguage)) && ($questionInfo = $db->nextRowAssoc())){

    $question = Question::newQuestion($type, $questionInfo);

    $question->printQuestionPreview($preview = true, $_SESSION['idSubject']);

}