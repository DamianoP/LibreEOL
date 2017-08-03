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
    openBox(ttSelectTeacher, 'left', 'teachersList');
    echo "<div id='contenitoreDocenti' style='height:350px;text-align:center; visibility:hidden'>";
    $db = new sqlDB();
    if ($db->qAdminsTeachers()){
        echo '<div class="list"><ul>';
        while($teacher = $db->nextRowAssoc()){
            echo '<li><a class="showTeacherInfo" value="'.$teacher['idUser'].'" onclick="showTeacherInfo(this);">'.$teacher['surname']." ".$teacher['name'].'</a></li>';
        }
        echo '</ul></div>';
    }else{
        echo ttEDatabase;
    }
    echo "</div>";
    closeBox();
    openBox(ttInfo, 'right', 'teachersInfo'); ?>

    <form class="infoEdit" onsubmit="return false;"></form>

    <div id="editPanel" class="hidden">
        <a class="ok button right lSpace" id="saveEdit" onclick="saveEdit();"><?= ttSave ?></a>
        <a class="normal button right" id="cancelEdit" onclick="cancelEdit(true);"><?= ttCancel ?></a>
        <a class="delete button left lSpace" id="deleteTeacher" onclick="deleteTeacher();"><?= ttDelete ?></a>
    </div>
    <?php closeBox(); ?>

    <div class="clearer"></div>
</div>
<script>
$(document).ready(function() {
    $('#contenitoreDocenti').css("visibility","visible");
    $('#loader').hide();
});
</script>