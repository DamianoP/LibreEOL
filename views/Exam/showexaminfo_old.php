<?php
/**
 * File: showexaminfo.php
 * User: Masterplan
 * Date: 4/23/13
 * Time: 5:54 PM
 * Desc: Shows exam's info or shows new empty panel to add new exam
 */

global $user, $config, $log, $tt;

$new = true;
$editClass = 'writable';
$infoExam = array(
    'name' => ttExam,
    'fkSubject' => 0,
    'fkTestSetting' => 0,
    'datetime' => date('Y-m-d').' '.date('H:i'),
    'description' => '',
    'regStart' => '',
    'regEnd' => '',
    'status' => 'w'
);

$info = '';

$db = new sqlDB();
if($_POST['action'] == 'show'){
    $new = false;
    if($db->qSelect('Exams', 'idExam', $_POST['idExam'])){
        if($db->numResultRows() > 0){

            $editClass = 'readonly';
            $infoExam = $db->nextRowAssoc();

        }else die(ttEExamNotFound);
    }else die($db->getError());
}

openBox(ttExam.' '.ttInfo, 'normal-70.1%', 'examInfo');
?>
<form class="infoEdit">
    <div class="columnLeft">
        <h2 class="center"><?= ttGeneralInformations ?></h2>

        <label class="tSpace"><?= ttName ?> : </label>
        <input class="tSpace <?= $editClass ?>" type="text" id="examName" size="50" value="<?= $infoExam['name'] ?>">
        <a id="examNameChars" class="charsCounter hidden"></a>

        <label class="tSpace"><?= ttSubject ?> : </label>
        <dl class="tSpace dropdownInfo" id="examSubject">
            <?php
            if(!$new){
                if($db->qSelect('Subjects', 'idSubject', $infoExam['fkSubject'])){
                    if($subject = $db->nextRowAssoc()){
                        echo '<dt class="'.$editClass.'"><span>'.$subject['name'].'<span class="value">'.$infoExam['fkSubject'].'</span></span></dt>';
                    }
                }
            }else{
                echo '<dt class="'.$editClass.'"><span> -------- <span class="value">-1</span></span></dt>';
                if($db->qSubjects($user->id, $user->role)){
                    echo '<dd><ol><li> -------- <span class="value">-1</span></li>';
                    while($subject = $db->nextRowAssoc()){
                        echo '<li>'.$subject['name'].'<span class="value">'.$subject['idSubject'].'</li>';
                    }
                    echo '</ol></dd>';
                }
            }
            ?>
        </dl>
        <div class="clearer"></div>

        <label class="tSpace"><?= ttSettings ?> : </label>
        <dl class="tSpace dropdownInfo" id="examSettings">
            <?php
            if(!$new){
                if($db->qSelect('TestSettings', 'idTestSetting', $infoExam['fkTestSetting'])){
                    if($testsetting = $db->nextRowAssoc()){
                        echo '<dt class="'.$editClass.'"><span>'.$testsetting['name'].'<span class="value">'.$infoExam['fkSubject'].'</span></span></dt>';
                    }
                }
            }else{
                echo ttSelectSubjectBefore;
            }
            ?>
        </dl>
        <div class="clearer"></div>

        <?php
        $datetime = strtotime($infoExam['datetime']);
        $day = date('Y-m-d', $datetime);
        $time = date('H:i', $datetime);
        ?>
        <label class="tSpace"><?= ttDay ?> : </label>
        <input type="text" class="datepicker left tSpace <?= $editClass ?>" id="examDay" value="<?= $day ?>">
        <label style="min-width: 0" class="tSpace lSpace"><?= ttTime ?> : </label>
        <input type="text" class="timepicker tSpace <?= $editClass ?>" id="examTime" value="<?= $time ?>">
        <div class="clearer"></div>

        <label class="tSpace"><?= ttDescription ?> : </label>
        <textarea class="tSpace <?= $editClass ?>" id="examDesc"><?= $infoExam['description'] ?></textarea>
        <a id="examDescChars" class="charsCounter hidden"></a>
        <div class="clearer"></div>
    </div>
    <div class="columnCenter">
        <h2 class="center"><?= ttRegistrations ?></h2>

        <?php
        $manualChecked = 'checked';
        $autoChecked = '';
        $hidden = 'hidden';
        if($infoExam['regStart'] == ''){
            $infoExam['regStart'] = date('Y-m-d').' '.date('H:i');
            $infoExam['regEnd'] = date('Y-m-d', strtotime($infoExam['regStart'] . "+7 days")).' '.date('H:i');
        }else{
            $manualChecked = '';
            $autoChecked = 'checked';
            $hidden = '';
        }
        $datetime = strtotime($infoExam['regStart']);
        $regStartDay = date('Y-m-d', $datetime);
        $regStartTime = date('H:i', $datetime);
        $datetime = strtotime($infoExam['regEnd']);
        $regEndDay = date('Y-m-d', $datetime);
        $regEndTime = date('H:i', $datetime);

        ?>
        <input type="radio" class="<?= $editClass ?>" name="examReg" value="manual" <?= $manualChecked ?>><?= ttManual ?><br/>
        <input type="radio" class="<?= $editClass ?>" name="examReg" value="auto" <?= $autoChecked ?> /><?= ttAutomatic ?>

        <div id="examRegiDiv" class="registration <?= $hidden ?>">

            <label class="center"><?= ttStart ?> : </label>
            <input type="text" class="datepicker left <?= $editClass ?>" id="examRegStartDay" value="<?= $regStartDay ?>">
            <input type="text" class="timepicker left lSpace <?= $editClass ?>" id="examRegStartTime" value="<?= $regStartTime ?>">
            <div class="clearer"></div>

            <label class="tSpace center"><?= ttEnd ?> : </label>
            <input type="text" class="datepicker left tSpace <?= $editClass ?>" id="examRegEndDay" value="<?= $regEndDay ?>">
            <input type="text" class="timepicker left tSpace lSpace <?= $editClass ?>" id="examRegEndTime" value="<?= $regEndTime ?>">
            <div class="clearer"></div>
        </div>
    </div>
    <div class="columnRight">
        <h2 class="center"><?= ttRooms ?></h2>

        <div class="rooms">
            <?php
            $checked = '';
            $examRooms = array();
            if(!$new){
                if($db->qSelect('Exams_Rooms', 'fkExam', $_POST['idExam'])){
                    $examRooms = $db->getResultAssoc('fkRoom');
                    if(count($examRooms) == 0){
                        $checked = 'checked';
                    }
                }
            }
            echo '<input id="allRooms" type="checkbox" class="'.$editClass.'" value="-1" '.$checked.'><a class="italic">'.ttEverywhere.'</a><br/>';
            if($db->qSelect('Rooms', '', '', 'name')){
                while($room = $db->nextRowAssoc()){
                    $checked = '';
                    if(isset($examRooms[$room['idRoom']]))
                        $checked = 'checked';
                    echo '<input type="checkbox" name="rooms" class="'.$editClass.'" value="'.$room['idRoom'].'" '.$checked.'>'.$room['name'].'<br/>';
                }
            }
            ?>
        </div>
    </div>
    <div class="clearer b2Space"></div>

    <?php

    $editPanelClass = 'hidden';
    if($new){
        $newPanelClass = '';
        $viewPanelClass = 'hidden';
    }else{
        $newPanelClass = 'hidden';
        $viewPanelClass = '';
    }

    ?>

    <div id="viewPanel" class="<?= $viewPanelClass ?>">
        <a class="normal button left lSpace" onclick="cancelEdit(new Array(examEditing));"><?= ttExit ?></a>
        <?php if($infoExam['status'] != 'a') { ?>
            <a class="normal button right rSpace" onclick="editExamInfo()"><?= ttEdit ?></a>
            <a class="ok button right rSpace" onclick="renewPassword(new Array(true));"><?= ttRenewPassword ?></a>
        <?php } ?>
    </div>
    <div id="editPanel" class="<?= $editPanelClass ?>">
        <a class="ok button right bSpace rSpace" onclick="saveExamInfo(new Array(true));"><?= ttSave ?></a>
        <a class="normal button left lSpace bSpace" onclick="cancelEdit(new Array(examEditing));"><?= ttCancel ?></a>
    </div>
    <div id="newPanel" class="<?= $newPanelClass ?>">
        <a class="normal button left lSpace" onclick="cancelNew(new Array(true));"><?= ttCancel ?></a>
        <a class="ok button right bSpace rSpace" onclick="createNewExam();"><?= ttCreate ?></a>
    </div>

    <div class="clearer"></div>
</form>

<?php
closeBox();
?>