<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/21/13
 * Time: 8:44 PM
 * Desc: Admin's Homepage
 */

global $config, $user;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="adminHomepage">
        <?php
        openBox(ttAdministration, 'normal', 'admin');
        echo ttAdminWelcome;

        $db = new sqlDB();
        $teachers = $eteachers = $students = $subjects = $questions = $exams = $tests = 0;
        if($db->qSelect('Users', 'role', 't')){
            $teachers += $db->numResultRows();
        }
        if($db->qSelect('Users', 'role', 'at')){
            $teachers += $db->numResultRows();
        }
        if($db->qSelect('Users', 'role', 'e')){
            $eteachers += $db->numResultRows();
        }
        if($db->qSelect('Users', 'role', 's')){
            $students = $db->numResultRows();
        }
        if($db->qSelect('Subjects')){
            $subjects = $db->numResultRows();
        }
        if($db->qSelect('Questions')){
            $questions = $db->numResultRows();
        }
        if($db->qSelect('Exams')){
            $exams = $db->numResultRows();
        }
        if($db->qSelect('Tests')){
            $tests = $db->numResultRows();
        }
        ?>

        <table id="adminTable">
            <tr><td><?= $teachers ?></td><td><?= ttTeachers ?></td></tr>
            <tr><td><?= $eteachers ?></td><td><?= ttETeachers ?></td></tr>
            <tr><td><?= $students ?></td><td><?= ttStudents ?></td></tr>
            <tr><td><?= $subjects ?></td><td><?= ttSubjects ?></td></tr>
            <tr><td><?= $questions ?></td><td><?= ttQuestions ?></td></tr>
            <tr><td><?= $exams ?></td><td><?= ttExams ?></td></tr>
            <tr><td><?= $tests ?></td><td><?= ttTests ?></td></tr>
        </table>

        <?php
        closeBox();
        ?>
        <div class="clearer"></div>
    </div>
</div>