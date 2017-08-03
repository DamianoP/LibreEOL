<?php
/**
 * File: setpassword.php
 * User: Masterplan
 * Date: 5/30/13
 * Time: 12:08 AM
 * Desc: Shows page to insert the first password and activate user's account or sets a new password after reset operation
 */

global $config;

if(isset($_GET['t'])){
    $db = new sqlDB();
    if($db->qSelect('Tokens', 'value', $_GET['t'])){
        if($row = $db->nextRowAssoc())
            $token = $row['value'];
        else
            die(ttEActivationFails);
    }else
        die(ttEDatabase);
}

?>
<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">

    <?php openBox(ttSetPassword, 'small', 'register') ?>
    <form class="infoEdit" onsubmit="return false;" id="<?= $token ?>">
            <label class="b2Space" for="infoPassword"><?= ttPassword ?> : </label>
            <input class="b2Space writable" type="password" id="infoPassword" size="75%" value="">

            <label class="b2Space" for="infoPassword2"><?= ttConfirmPassword ?> : </label>
            <input class="b2Space writable" type="password" id="infoPassword2" size="75%" value="">

        <div>
            <a class="normal button" id="setPassword" onclick="setPassword();"><?= ttSet ?></a>
        </div>
    </form>
    <?php closeBox() ?>
</div>






