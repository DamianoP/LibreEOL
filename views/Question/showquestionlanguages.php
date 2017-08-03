<?php
/**
 * File: showquestionlanguage.php
 * User: Masterplan
 * Date: 17/05/14
 * Time: 16:30
 * Desc: Your description HERE
 */

global $config;

$allLangs = null;
$db = new sqlDB();
if(($db->qSelect('Languages') && ($allLangs = $db->getResultAssoc('idLanguage')))){
    if(($db->qSelect('Subjects', 'idSubject', $_SESSION['idSubject'])) && ($subjectInfo = $db->nextRowAssoc())){
        echo '<div class="list">
                 <ul>
                     <li><a class="showQuestionPreview selected" value="'.$subjectInfo['fkLanguage'].'"
                            onclick="showQuestionPreview('.$_POST['idQuestion'].', '.$subjectInfo['fkLanguage'].', this);"
                            on_dblclick="showQuestionInfo('.$_POST['idQuestion'].', '.$subjectInfo['fkLanguage'].', this)">
                            <img title="'.$allLangs[$subjectInfo['fkLanguage']]['description'].'" class="flag"
                                 src="'.$config['themeFlagsDir'].$allLangs[$subjectInfo['fkLanguage']]['alias'].'.gif">* '.$allLangs[$subjectInfo['fkLanguage']]['description'].' *</a></li>';
        if($db->qSelect('TranslationQuestions', 'fkQuestion', $_POST['idQuestion'])){
            while($translation = $db->nextRowAssoc()){
                if($subjectInfo['fkLanguage'] != $translation['fkLanguage']){
                    echo '<li>
                    <a class="showQuestionPreview" value="'.$translation['fkLanguage'].'"
                       onclick="showQuestionPreview('.$_POST['idQuestion'].', '.$translation['fkLanguage'].', this);"
                       on_dblclick="showQuestionInfo('.$_POST['idQuestion'].', '.$translation['fkLanguage'].', this);">
                       <img title="'.$allLangs[$translation['fkLanguage']]['description'].'" class="flag"
                            src="'.$config['themeFlagsDir'].$allLangs[$translation['fkLanguage']]['alias'].'.gif">'.$allLangs[$translation['fkLanguage']]['description'].'</a></li>';
                }
            }
        }
        echo '</ul></div>';

    }else{
        echo ttEDatabase;
    }
}else{
    echo ttEDatabase;
}