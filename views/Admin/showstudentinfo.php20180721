<?php

$db = new sqlDB();
if($_POST['action'] == 'show'){
    if(($db->qSelect('Users', 'idUser', $_POST['idStudent'])) && ($student = $db->nextRowAssoc())){
        $idStudent = $student['idStudent'];
        $studentName = $student['name'];
        $studentSurname = $student['surname'];
        $studentEmail = $student['email'];
        $studentGroup = $student['group'];
        $studentSubGroup = $student['subgroup'];
        $studentRole = $student['role'];
        $class = 'readonly';
        $dropdownClass = 'notChange';
        $dropdownDTClass = '';
    }else{
        $log->append(__FUNCTION__." : ".$db->getError());
        die("NACK");
    }
}

?>

<label class="b2Space" for="studentName"><?= ttName ?> : </label>
<input class="writable" type="text" id="studentName" name="studentName" value="<?= $studentName ?>">
<div class="clearer"></div>
<label class="b2Space" for="studentSurname"><?= ttSurname ?> : </label>
<input class="writable" type="text" id="studentSurname" name="studentSurname"  value="<?= $studentSurname ?>">
<div class="clearer"></div>
<label for="studentEmail"><?= ttEmail ?> : </label>
<textarea class="writable b2Space left" id="studentEmail" name="studentEmail"><?= $studentEmail ?></textarea>
<div class="clearer"></div>
<label class="b2Space"><?= ttGroup ?> : </label>
<select id="group">
    <?php
    $db = new sqlDB();
    $db->qListGroup();
    $current = null;
    $now = null;
    while($a = $db->nextRowAssoc()){
        $current = $a["NameGroup"];
        if($now == null){
            $now = $current;
            echo "<optgroup label='$now'>";
        }
        if($now != $current){
            echo "</optgroup>";
            echo "<optgroup label='$current'>";
            $now = $current;
        }
        if($now == $current){
            $value = $a["NameSubGroup"];
            $idGroup = $a["idGroup"];
            $idSubGroup = $a["idSubGroup"];
            if($idGroup == $studentGroup && $idSubGroup == $studentSubGroup){
                echo "<option value='$idGroup-$idSubGroup' selected>$value</option>";
            }else{
                echo "<option value='$idGroup-$idSubGroup' >$value</option>";
            }


        }
    }
    echo "</optgroup>";
    ?>
</select>

<div class="clearer"></div>

<label><?= ttRole ?> : </label>
<span class="left">
    <input type="radio" name="studentRole" value="a" <?php if ($studentRole == 'a') echo "checked"; ?>> <?= ttAdministrator ?> <br/>

    <input type="radio" name="studentRole" value="t" <?php if ($studentRole == 't') echo "checked"; ?>> <?= ttTeacher ?>
    <input type="radio" name="studentRole" value="at" <?php if ($studentRole == 'at') echo "checked"; ?>> <?=ttTeacher?>/<?= ttAdministrator ?> <br/>

    <input type="radio" name="studentRole" value="e" <?php if ($studentRole == 'e') echo "checked"; ?>> <?= tteTeacher ?>
    <input type="radio" name="studentRole" value="er"  <?php if ($studentRole == 'er') echo "checked"; ?>> <?= tteTeacher?>/<?= ttAdministrator ?> <br/>

    <input type="radio" name="studentRole" value="s"  <?php if ($studentRole == 's') echo "checked"; ?>> <?= ttStudent?>
    </br>
</span>

<div class="clearer b2Space"></div>


