<?php
/**
 * File: rooms.php
 * User: Masterplan
 * Date: 6/10/13
 * Time: 9:21 AM
 * Desc: Shows rooms edit page
 */
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div>
        <?php openBox(ttRooms, 'left', 'roomsList', array('new'));
        $db = new sqlDB();
        if($db->qSelect('Rooms')){
            echo '<div class="list"><ul>';
            while($room = $db->nextRowAssoc()){
                echo '<li><a class="showInfo" value="'.$room['idRoom'].'">'.$room['name'].'</a></li>';
            }
            echo '</ul></div>';
        }else{
            echo ttEDatabase;
        }
        closeBox();
        ?>
        <?php openBox(ttInfo, 'right', 'roomInfo'); ?>
        <form class="infoEdit" onsubmit="return false;">
            <label class="b2Space" for="infoName"><?= ttName ?> : </label>
            <input class="readonly" type="text" id="infoName" size="75%" value="">
            <a id="infoNameChars" class="charsCounter hidden"></a>
            <div class="clearer"></div>

            <label class="b2Space" for="infoDesc"><?= ttDescription ?> : </label>
            <textarea class="b2Space rSpace readonly left" id="infoDesc"></textarea>
            <a id="infoDescChars" class="charsCounter hidden"></a>
            <div class="clearer"></div>

            <label class="b2Space" for="infoIPStart"><?= ttIPStart ?> : </label>
            <input class="readonly" style="width:13%" type="text" id="infoIPStart0" size="3" value="">.
            <input class="readonly" style="width:13%"  type="text" id="infoIPStart1" size="3" value="">.
            <input class="readonly" style="width:13%"  type="text" id="infoIPStart2" size="3" value="">.
            <input class="readonly" style="width:13%"  type="text" id="infoIPStart3" size="3" value="">
            <div class="clearer"></div>

            <label class="b2Space" for="infoIPEnd"><?= ttIPEnd ?> : </label>
            <input class="readonly" style="width:13%"  type="text" id="infoIPEnd0" size="3" value="">.
            <input class="readonly" style="width:13%"  type="text" id="infoIPEnd1" size="3" value="">.
            <input class="readonly" style="width:13%"  type="text" id="infoIPEnd2" size="3" value="">.
            <input class="readonly" style="width:13%"  type="text" id="infoIPEnd3" size="3" value="">
            <div class="clearer"></div>

        </form>

        <a class="normal button right" id="editInfo" onclick="editInfo();"><?= ttEdit ?></a>
        <div class="clearer"></div>

        <?php closeBox(); ?>

        <div class="clearer"></div>
    </div>
</div>