<?php
/**
 * File: lostpassword.php
 * User: Masterplan
 * Date: 6/9/13
 * Time: 2:12 PM
 * Desc: Shows form for reset account password
 */

global $config, $tt;

if(isset($_GET['t'])){
    $db = new sqlDB();
    if($db->qSelect('Users', 'password', $_GET['c'])){
        if($row = $db->nextRowAssoc()){
            $idUser = $row['idUser'];
            $name = $row['name'];
            $surname = $row['surname'];
        }else{
            die(ttEActivationFails);
        }
    }else{
        die(ttEDatabase);
    }
}else{
    echo '<div id="navbar">';
    printMenu();
    echo '</div>
          <div id="main">';

    openBox(ttResetPassword, 'small', 'register');
    ?>

    <form class="infoEdit" onsubmit="return false;">

        <label for="infoEmail"><?= ttEmail ?> : </label>
        <input class="writable" type="text" id="infoEmail" size="75%" value="">

        <div class="t2Space">
            <a class="normal button" id="reset" onclick="reset();"><?= ttReset ?></a>
        </div>
    </form>

    <?php
    closeBox();
    echo '</div>';
}
?>






