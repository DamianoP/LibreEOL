<?php
/**
 * File: profile.php
 * User: Masterplan
 * Date: 5/30/13
 * Time: 4:13 PM
 * Desc: Shows profile page of user's account
 */

global $user, $log;
$db = new sqlDB();

if(($db->qSelect('Users', 'idUser', $user->id)) && ($u = $db->nextRowAssoc())){
    $userGroup = $u['group'];
    $userSubGroup = $u['subgroup'];
}else{
    $log->append(__FUNCTION__." : ".$db->getError());
    die("NACK");
}

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div class="clearer"></div>
    <?php openBox(ttProfile, 'small', 'profile') ?>
    <form class="infoEdit" onsubmit="return false;">

        <label class="b2Space"><?= ttName ?> : </label>
        <input class="writable" type="text" id="userName" value="<?= $user->name ?>">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttSurname ?> : </label>
        <input class="writable" type="text" id="userSurname" value="<?= $user->surname ?>">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttGroup ?> : </label>
        <select id="userGroup">
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
                    if($idGroup == $userGroup && $idSubGroup == $userSubGroup){
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

        <div class="clearer"></div>

        <label class="b2Space"><?= ttEmail ?> : </label>
        <input class="readonly" type="text" id="userEmail" value="<?= $user->email ?>">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttOldPassword ?> : </label>
        <input class="writable" type="password" id="oldPassword" value="">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttNewPassword ?> : </label>
        <input class="writable" type="password" id="newPassword" value="">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttConfirmPassword ?> : </label>
        <input class="writable" type="password" id="newPassword2" value="">
        <div class="clearer"></div>

        <div>
            <a class="normal button" id="saveProfile" onclick="saveProfile();"><?= ttSave ?></a>
        </div>
    </form>
    <?php closeBox() ?>
</div>
