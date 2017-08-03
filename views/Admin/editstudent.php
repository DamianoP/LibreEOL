<?php
/**
 * File: editstudent.php
 * User: tomma
 * Date: 23/10/16
 * Desc: Shows form for edit user user
 */

global $user, $tt;

?>
<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">    
    <div id="loader" class="loader"></div>
    <?php
        openBox(ttSelectStudent, 'left', 'studentsList');
        echo "<div id='contenitoreStudenti' style='height:350px;text-align:center; visibility:hidden'>";
        $db = new sqlDB();
        if ($db->qStudents()){
            echo '<div class="list"><ul>';
            while($student = $db->nextRowAssoc()){
                echo '<li>
                    <a 
                        class="showStudentInfo" 
                        value="'.$student['idUser'].'" 
                        onclick="showStudentInfo(this);">'.$student['surname']." ".$student['name'].'
                    </a>
                </li>';
            }
            echo '</ul></div>';
        }else{
            echo ttEDatabase;
        }
        echo "</div>";
        closeBox();
        openBox(ttInfo, 'right', 'studentsInfo'); ?>

        <form class="infoEdit" onsubmit="return false;"></form>

        <div id="editPanel" class="hidden">
            <a class="ok button right lSpace" id="saveEdit" onclick="saveEdit();"><?= ttSave ?></a>
            <a class="normal button right" id="cancelEdit" onclick="cancelEdit(true);"><?= ttCancel ?></a>
            <a class="delete button left lSpace" id="deleteStudent" onclick="deleteStudent();"><?= ttDelete ?></a>
        </div>
        <?php closeBox(); ?>

        <div class="clearer"></div>
</div>
<script>
$(document).ready(function() {
    $('#contenitoreStudenti').css("visibility","visible");
    $('#loader').hide();
});
</script>