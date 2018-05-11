<?php

global $log;
$db = new sqlDB();

if(($_POST['action'] == 'show')&&($_POST['type'] == 'g')){
    if(($db->qSelect('groupntc', 'idGroup', $_POST['idGroup'])) && ($group = $db->nextRowAssoc())){
        $idGroup = $group['idGroup'];
        $groupName = $group['NameGroup'];
    }else{
        $log->append(__FUNCTION__." : ".$db->getError());
        die("NACK");
    }
    ?>
    <label class="b2Space" for="groupName"><?= ttGroup?> : </label>
    <?php
    echo '<input class="writable" type="text" id="groupName" name="groupName" value="'.$groupName.'">';
    ?>
    <div class="clearer"></div>
    <br>
    <div id="editPanel" class="hidden">
        <a class="ok button right lSpace" id="saveEdit" onclick="saveGroupEdit();"><?= ttSave ?></a>
        <a class="normal button left lSpace" id="cancelEdit" onclick="cancelEdit(true);"><?= ttCancel ?></a>
    </div>
    <?php
}

if(($_POST['action'] == 'show')&&($_POST['type'] == 's')){
    if(($db->qSelect('SubGroup', 'idSubGroup', $_POST['idSubGroup'])) && ($subgroup = $db->nextRowAssoc())){

        $idSubGroup = $subgroup['idSubGroup'];
        $subgroupName = $subgroup['NameSubGroup'];
        $fkGroup = $subgroup['fkGroup'];

    }else{
        $log->append(__FUNCTION__." : ".$db->getError());
        die("NACK");
    }

    ?>
    <label class="b2Space"><?= ttGroup?> : </label>
    <?php
    echo' <select  id="group">';

    $db2 = new sqlDB();
    $db2->qListOnlyGroup();
    $current = null;
    while($a = $db2->nextRowAssoc()){
        $current = $a["NameGroup"];
        $idGroup = $a["idGroup"];
        if ($idGroup == $fkGroup){
            echo "<option value='$idGroup' selected>$current</option>";
        }else{
            echo "<option value='$idGroup'>$current</option>";
        }

    }
    echo "</optgroup>";

    echo '</select>';
    echo '<div class="clearer"></div>';

    ?>
    <label class="" for="subgroupName"><?= ttSubgroup?> : </label>
    <?php
    echo '<input class="writable" type="text" id="subgroupName" name="subgroupName" value="'.$subgroupName.'">';
    ?>
<br><br>
    <label class="" for="subgroupName"><?= ttDescription?> : </label>

    <?php
    echo '<input class="writable" type="text" id="subgroupDescription" name="subgroupDescription" value="'.$subgroup['Description'].'">';
    ?>
    <div class="clearer"></div>
    <br>
    <div id="editPanel" class="hidden">
        <a class="ok button right lSpace" id="saveEdit" onclick="saveSubgroupEdit();"><?= ttSave ?></a>
        <a class="normal button left lSpace" id="cancelEdit" onclick="cancelEdit(true);"><?= ttCancel ?></a>
    </div>
    <?php
}
?>


