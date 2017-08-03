<?php
/**
 * File: newroom.php
 * User: Masterplan
 * Date: 6/10/13
 * Time: 1:56 PM
 * Desc: Creates a new room from added informations
 */

global $user;
global $config;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div>
        <?php

        openBox(ttRooms, 'left', 'roomsList');
        $db = new sqlDB();
        if($db->qSelect('Rooms')){
            echo '<div class="list"><ul>';
            echo '<li><a class="showInfo selected" value="-1">'.ttNewRoom.'</a></li>';
            if($db->numResultRows() > 1){                   // Exclude 'All' in list
                while($room = $db->nextRowAssoc()){
                    if($room['name'] != 'All'){             // Exclude 'All' in list
                        echo '<li><a class="showInfo" value="'.$room['idRoom'].'">'.$room['name'].'</a></li>';
                    }
                }
            }
        }else{
            echo ttEDatabase;
        }
        echo '</ul></div>';
        closeBox();
        ?>

        <?php openBox(ttInfo, 'right', 'roomInfo'); ?>

        <form class="infoEdit" onsubmit="return false;">

            <label class="b2Space" for="infoName"><?= ttName ?> : </label>
            <input class="writable" type="text" id="infoName" size="65%" value="<?= ttNewRoom ?>">
            <a id="infoNameChars" class="charsCounter"></a>
            <div class="clearer"></div>

            <label class="b2Space" for="infoDesc"><?= ttDescription ?> : </label>
            <textarea class="b2Space rSpace writable left" id="infoDesc"></textarea>
            <a id="infoDescChars" class="charsCounter"></a>
            <div class="clearer"></div>

            <label class="b2Space" for="infoIPStart"><?= ttIPStart ?> : </label>
            <input class="writable" type="text" id="infoIPStart0" size="3" value=""> .
            <input class="writable" type="text" id="infoIPStart1" size="3" value=""> .
            <input class="writable" type="text" id="infoIPStart2" size="3" value=""> .
            <input class="writable" type="text" id="infoIPStart3" size="3" value="">
            <div class="clearer"></div>

            <label class="b2Space" for="infoIPEnd"><?= ttIPEnd ?> : </label>
            <input class="writable" type="text" id="infoIPEnd0" size="3" value=""> .
            <input class="writable" type="text" id="infoIPEnd1" size="3" value=""> .
            <input class="writable" type="text" id="infoIPEnd2" size="3" value=""> .
            <input class="writable" type="text" id="infoIPEnd3" size="3" value="">
            <div class="clearer"></div>

            <a class="ok button right lSpace" id="createNew" onclick="createRoom();"><?= ttCreate ?></a>
            <a class="normal button right" id="cancelNew"><?= ttCancel ?></a>
            <div class="clearer"></div>

        </form>

        <?php closeBox(); ?>
        <div class="clearer"></div>
    </div>
</div>

