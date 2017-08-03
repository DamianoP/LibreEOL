<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/19/13
 * Time: 6:06 PM
 * Desc: Login index page
 */

global $config;
session_start();

if (isset($_GET['unipg'])){
    unset($config['dbName']);
    $_SESSION['dbNameChanged']='EOL';
    $config['dbName']="EOL";
}if (isset($_GET['echemtest'])){
    $_SESSION['dbNameChanged']='echemtest';
    $config['dbName']="echemtest";
}
?>

<div id="loginDiv">
    <img src="<?= $config['themeImagesDir']; ?>logo.gif" alt="logo"/>

    <form name="login" class="login" onsubmit="return false;" id="loginForm">

        <label for="email"><?= ttEmail; ?></label>
        <input type="text" class="text" name="email" value="" id="email" /><br />
        <label for="password"><?= ttPassword; ?></label>
        <input type="password" class="text" name="password" value="" id="password" />
        <p id="result"></p>
            <a class="normal button" id="login" onclick="logIn();"><?= ttLogin ?></a><br/>
            <a class="little" href="index.php?page=admin/newstudent"><?= ttJoin ?></a>
            <a class="little" href="index.php?page=admin/lostpassword"><?= ttPasswordLost ?></a>
        <?php 
        if($config['dbName'] == 'echemtest' || $_SESSION['dbNameChanged'] =='echemtest'){?>
                <a class="little" href="index.php?unipg=1"><?= UniPG ?></a>
        <?php 
        }if($config['dbName'] == 'EOL' ||  $_SESSION['dbNameChanged'] =='EOL'){?>
                <a class="little" href="index.php?echemtest=1"><?= EchemTest ?></a>
                
        <?php 
        }
        ?>
        <div class="clearer"></div>
    </form>

</div>