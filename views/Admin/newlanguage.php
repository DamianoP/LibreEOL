<?php
/**
 * File: newlanguage.php
 * User: Masterplan
 * Date: 6/10/13
 * Time: 1:56 PM
 * Desc: Your description HERE
 */
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div>
        <?php openBox(ttNewLanguage, 'center', 'newLanguage');?>

        <form class="infoEdit" onsubmit="return false;">
            <label class="b2Space" for="infoDescription"><?= ttName ?> : </label>
            <input class="writable left" type="text" id="infoDescription" size="20" value="<?= ttNewLanguage ?>">

            <label class="b2Space" for="infoAlias"><a href="http://www.mathguide.de/info/tools/languagecode.html" target="_blank"><?= ttAlias ?></a> : </label>
            <input class="writable left" type="text" id="infoAlias" size="2" value="##">

            <div class="clearer"></div>
        </form>
        <div class="clearer"></div>

        <a class="ok button right lSpace tSpace" id="createNew"><?= ttCreate ?></a>
        <a class="normal button left tSpace" id="cancelNew"><?= ttCancel ?></a>
        <div class="clearer"></div>

        <?php closeBox(); ?>
    </div>
</div>

