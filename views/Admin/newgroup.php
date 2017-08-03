<?php
/**
 * File: newstudent.php
 * User: Masterplan
 * Date: 6/7/13
 * Time: 12:06 PM
 * Desc: Shows form for add new user
 */

global $user, $tt;

?>
<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div class="clearer">
        <?php openBox(ttNewGroup, 'small', 'register') ?>
        <form class="infoEdit" onsubmit="return false;">

            <label class="b2Space"><?= ttGroup ?> : </label>
            <input class="writable" type="text" id="groupName" size="75%" value="">
            <div class="clearer"></div>

            <div>
                <a class="normal button" id="create" onclick="createGroup();"><?= ttCreate ?></a>
            </div>
        </form>
        <?php closeBox() ?>

        <div class="clearer"></div>
        <br>

        <?php openBox(ttNewSubgroup, 'small', 'register') ?>
        <form class="infoEdit" onsubmit="return false;">

            <label class="b2Space"><?= ttGroup ?> : </label>
            <select  id="group">
                <?php
                $db = new sqlDB();
                $db->qListOnlyGroup();
                $current = null;
                $now = null;
                while($a = $db->nextRowAssoc()){
                    $current = $a["NameGroup"];
                    $idGroup = $a["idGroup"];
                    echo "<option value='$idGroup'>$current</option>";
                }
                echo "</optgroup>";
                ?>
            </select>
            <div class="clearer"></div>
            <label class="b2Space"><?= ttSubgroup ?> : </label>
            <input class="writable" type="text" id="subgroupName" size="75%" value="">
            <div class="clearer"></div>

            <div>
                <a class="normal button" id="create" onclick="createSubgroup();"><?= ttCreate ?></a>
            </div>
        </form>
        <?php closeBox() ?>
    </div>
</div>


