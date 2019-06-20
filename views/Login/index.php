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


<div id="loginDiv" style="padding:16px">

    <form style="top:84px; width: 386px" name="login" class="login" onsubmit="return false;" id="loginForm">

        <label for="email"><?= ttEmail; ?></label>
        <input type="text" class="text" name="email" value="" id="email" /><br />
        <label for="password"><?= ttPassword; ?></label>
        <input type="password" class="text" name="password" value="" id="password" />    
        <div class="checkbox" style="padding-top: 5px">
            <label>
                <input id="checkbox" type="checkbox" value="" name="privacyPolicy" required>
                    <?= ttPrivacy1; ?> 
                    <a href="http://ectn.eu/privacy-policy/">
                        <?= ttPrivacy2; ?>
                    </a>
            </label>
        </div>
        <p id="result">&nbsp;</p>
        <br>
        <a class="little delete" style="width:85px;padding:7px" href="index.php?page=admin/lostpassword"><?= ttPasswordLost ?></a>        
        <a class="normal button" style="width:140px;padding:7px" id="login" onclick="logIn();"><?= ttLogin ?></a>
        <a class="little ok" style="width:85px;padding:7px" href="index.php?page=admin/newstudent"><?= ttJoin ?></a>
        
        <div class="clearer"></div>
    </form>

</div>
