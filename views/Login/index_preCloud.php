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
        <div class="clearer"></div>
    </form>

</div>