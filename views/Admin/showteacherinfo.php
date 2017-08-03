<?php

$db = new sqlDB();
if($_POST['action'] == 'show'){
    if(($db->qSelect('Users', 'idUser', $_POST['idTeacher'])) && ($teacher = $db->nextRowAssoc())){
        $idteacher = $teacher['idTeacher'];
        $teacherName = $teacher['name'];
        $teacherSurname = $teacher['surname'];
        $teacherEmail = $teacher['email'];
        $teacherGroup = $teacher['group'];
        $teacherSubGroup = $teacher['subgroup'];
        $teacherRole = $teacher['role'];
        $class = 'readonly';
        $dropdownClass = 'notChange';
        $dropdownDTClass = '';
    }else{
        $log->append(__FUNCTION__." : ".$db->getError());
        die("NACK");
    }
}

?>

<label class="b2Space" for="teacherName"><?= ttName ?> : </label>
<input class="writable" type="text" id="teacherName" name="teacherName" value="<?= $teacherName ?>">
<div class="clearer"></div>
<label class="b2Space" for="teacherSurname"><?= ttSurname ?> : </label>
<input class="writable" type="text" id="teacherSurname" name="teacherSurname"  value="<?= $teacherSurname ?>">
<div class="clearer"></div>
<label for="teacherEmail"><?= ttEmail ?> : </label>
<textarea class="writable b2Space left" id="teacherEmail" name="teacherEmail"><?= $teacherEmail ?></textarea>
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
            if($idGroup == $teacherGroup && $idSubGroup == $teacherSubGroup){
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
    <input type="radio" name="teacherRole" value="a" <?php if ($teacherRole == 'a') echo "checked"; ?>> <?= ttAdministrator ?> <br/>

    <input type="radio" name="teacherRole" value="t" <?php if ($teacherRole == 't') echo "checked"; ?>> <?= ttTeacher ?>
    <input type="radio" name="teacherRole" value="at" <?php if ($teacherRole == 'at') echo "checked"; ?>> <?=ttTeacher?>/<?= ttAdministrator ?> <br/>

    <input type="radio" name="teacherRole" value="e" <?php if ($teacherRole == 'e') echo "checked"; ?>> <?= tteTeacher ?>
    <input type="radio" name="teacherRole" value="er"  <?php if ($teacherRole == 'er') echo "checked"; ?>> <?= tteTeacher?>/<?= ttAdministrator ?>
    </br>
</span>

<div class="clearer b2Space"></div>
