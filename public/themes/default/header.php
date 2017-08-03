<?php
/**
 * File: header.php
 * User: Masterplan
 * Date: 3/16/13
 * Time: 11:38 AM
 * Desc: Header of all pages
 */

global $user, $config;
?>

<div id="header">
    <div id="eolLogo"></div>

    <!--<div id="logoContainer">
        <img src="">
        <div id="systemTitle">EOL - Esami On Line</div>
    </div>
    -->

    <div id="welcome">
        <div>
            <a class="w"><?= ttWelcome ?></a><br/>
            <a class="u"><?= "$user->name $user->surname"; ?></a><br/>
            IP: <a class="i"><?= ($_SERVER['REMOTE_ADDR'] == "::1") ? "127.0.0.1" : $_SERVER['REMOTE_ADDR']; ?></a><br/>
            <a><?php echo ttRole;?>: <?php 
            if($user->role=="a") echo ttAdministrator;
            elseif ($user->role=="e") echo tteTeacher;
            elseif ($user->role=="t") echo ttTeacher;
            elseif ($user->role=="s") echo ttStudent;
            elseif ($user->role=="at") echo ttAdministrator."/".ttTeacher;
            elseif ($user->role=="er") echo ttTeacher;
            else echo "?" ;
            ?></a> <br/>
            <a class="l" href="index.php?page=login/logout"><?= ttLogout ?></a>
        </div>
    </div>
    <div class="clearer"></div>
</div>