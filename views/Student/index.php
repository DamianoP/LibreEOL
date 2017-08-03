<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 5/2/13
 * Time: 12:06 PM
 * Desc: Student's Homepage
 */

global $user;
//unset($_SESSION['idSet']);
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div>
        <?php openBox(ttSubjects, 'left', 'subjectList');
        $readedSubjects = array();
        $db = new sqlDB();
        $db->qGetSubGroup($user->id);
        $a = $db->nextRowAssoc();
        $subGroup = $a["subgroup"];
        if($db->qExamsInProgress($subGroup)){
            echo '<div class="list"><ul>';
            while($subject = $db->nextRowAssoc()){
                if(! in_array($subject['fkSubject'], $readedSubjects)){
                    echo '<li>
                    <a class="showSubjectInfoAndExams" 
                    value="'.$subject['fkSubject'].'" 
                    onclick="showSubjectInfoAndExams(this);">
                    '.$subject['subjectName'].'</a>
                    </li>';
                    array_push($readedSubjects, $subject['fkSubject']);
                }
            }
            echo '</ul></div>';
        }else{
            die($db->getError());
        }

        closeBox();

        openBox(ttInfo, 'right', 'subjectInfoAndExams'); ?>
        <form class="infoEdit" onsubmit="return false;"></form>

        <form method="post" id="idExamForm">
            <input type="hidden" id="idExam" name="idExam" value="">
        </form>

        <?php closeBox(); ?>

        <div class="clearer"></div>
    </div>
</div>