<?php
/**
 * File: showquestioninfo.php
 * User: Masterplan
 * Date: 19/05/14
 * Time: 11:19
 * Desc: Show question and releated answers info panels or show empty infos for new question
 */


global $config, $log, $user, $qlog, $old_question_id, $new_question_id;

// Variables for new question
$subjectInfo = null;
$questionInfo = array();
$questionTranslations = array();

$readonly = false;
$questionMandatory = '';

$db = new sqlDB();
if(($db->qSelect('Subjects', 'idSubject', $_SESSION['idSubject'])) && ($subjectInfo = $db->nextRowAssoc())){
    if($_POST['action'] == 'show'){
        if($db->qQuestionInfo($_POST['idQuestion'])){
            $questionTranslations = $db->getResultAssoc('idLanguage');
            $questionInfo = $questionTranslations[$subjectInfo['fkLanguage']];
        }
        if(($db->qGetEditAndDeleteConstraints('edit', 'question1', array($_POST['idQuestion']))) && ($row = $db->nextRowAssoc())){
            $readonly = true;
            $questionMandatory = $row['name'];
        }
    }elseif($_POST['action'] == 'new'){
        $questionInfo = array ('type'        => $_POST['type'],               // Question's type
                               'fkLanguage'  => $subjectInfo['fkLanguage'],   // Question's main language
                               'translation' => '');                          // Question's main translation
    }
}

openBox(ttQuestion, 'normal-900px', 'questionInfo');

if($readonly){
    echo '<div class="mandatoryNotice">'.str_replace('_TESTSETTING_', $questionMandatory, ttEMandatoryQuestion).'</div>';
}

?>
<form id="questionInfoForm">
    <div id="questionInfoTabs">
        <script>
            var mainLang = "<?= $questionInfo['fkLanguage'] ?>";
            var userLang = "<?= $user->lang ?>";
        </script>

        <?php
        $question = Question::newQuestion($questionInfo['type'], $questionInfo);
        $question->set('QTranslations', $questionTranslations);
        $question->printQuestionEditForm($_POST['action'], $readonly);
        ?>

    </div>
</form>
<div class="clearer"></div>

<?php closeBox(); ?>
