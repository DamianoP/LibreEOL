<?php
/**
 * File: showanswerinfo.php
 * User: Masterplan
 * Date: 4/10/13
 * Time: 8:59 PM
 * Desc: Show answer's info panel or empty panel for new answer
 */

global $config;

$answerInfo = array();
$answerTranslations = array();

$db = new sqlDB();
if(($_POST['action'] == 'show') && ($db->qAnswerInfo($_POST['idAnswer'])) && ($answerTranslations = $db->getResultAssoc('idLanguage'))){

    $answerInfo = $answerTranslations[$_POST['mainLang']];

}elseif(($_POST['action'] == 'show') && (in_array($_POST['type'], array('YN', 'TF'))) &&
        ($db->qSelect('Answers', 'idAnswer', $_POST['idAnswer'])) && ($answers = $db->getResultAssoc())){

    $answerInfo = $answers[0];

}elseif($_POST['action'] == 'new'){
    $answerInfo = array('score' => 0,
                        'type'  => $_POST['type'],
                        'fkLanguage' => $_POST['mainLang'],
                        'translation' => '');
}else{
    die($db->getError());
}

openBox(ttAnswer, 'normal-683px', 'answerInfo'); ?>

    <form id="answerInfoForm" class="ui-widget">

        <?php

        $answer = Answer::newAnswer($_POST['type'], $answerInfo);
        $answer->set('ATranslations', $answerTranslations);
        $answer->printAnswerEditForm($_POST['action']);

        ?>

    </form>
    <div class="clearer"></div>

<?php closeBox(); ?>