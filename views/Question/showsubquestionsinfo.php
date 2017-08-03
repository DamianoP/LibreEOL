<?php
/**
 * File: showsubquestionsinfo.php
 * User: Gmarsi
 * Date: 4/10/13
 * Time: 8:59 PM
 * Desc: Show subquestion's info panel or empty panel for new answer
 */

global $config;

$subInfo = array();
$subTranslations = array();

$db = new sqlDB();
if(($_POST['action'] == 'show') && ($db->qSubquestionsInfo($_POST['sub_questions'])) && ($subTranslations = $db->getResultAssoc('idLanguage'))){
    $subInfo = $subTranslations[$_POST['mainLang']];

}elseif(($_POST['action'] == 'show') && (in_array($_POST['type'], array('YN', 'TF'))) &&
    ($db->qSelect('Sub_questions', 'sub_questions', $_POST['sub_questions'])) && ($sub_questions = $db->getResultAssoc())){

    $subInfo = $sub_questions[0];

}elseif($_POST['action'] == 'new'){
    $subInfo = array('score' => 0,
        'type'  => $_POST['type'],
        'fkLanguage' => $_POST['mainLang'],
        'translation' => '');
}else{
    die($db->getError());
}

openBox(ttQuestionsubPL, 'normal-683px', 'answerInfo'); ?>

    <form id="answerInfoForm" class="ui-widget">

        <?php
        $sub_questions = Answer::newAnswer($_POST['type'], $subInfo);
        $sub_questions->set('text', $subTranslations);
        $sub_questions->printSubEditForm($_POST['action']);

        ?>

    </form>
    <div class="clearer"></div>

<?php closeBox(); ?>