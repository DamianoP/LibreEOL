<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/19/13
 * Time: 6:06 PM
 * Desc: Login index page
 */

global $config;

?>

<div id="loginDiv">

    <form name="login" class="login" onsubmit="return false;" id="loginForm">

        <label for="email"><?= ttEmail; ?></label>
        <input type="text" class="text" name="email" value="" id="email" /><br />
        <label for="password"><?= ttPassword; ?></label>
        <input type="password" class="text" name="password" value="" id="password" />
        <p id="result">&nbsp;</p>
        <a class="normal button" style="width:172px;padding:7px" id="login" onclick="logIn();"><?= ttLogin ?></a><br/>
        <a class="little delete" style="width:85px;padding:2px" href="index.php?page=admin/lostpassword"><?= ttPasswordLost ?></a>
        <a class="little ok" style="width:85px;padding:2px" href="index.php?page=admin/newstudent"><?= ttJoin ?></a>
        
        <div class="clearer"></div>
    </form>

</div>