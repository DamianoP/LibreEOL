<?php
/**
 * File: importpage.php
 * User: Elia
 * Date: 5/21/15
 * Time: 4:13 PM
 * Desc: Shows import page
 */


?>


<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div class="clearer"></div>
    <?php openBox(ttImport, 'small', 'profile') ?>
    <form class="infoEdit" onsubmit="return false;">
        <div id="ImportMsg"></div>
        <div>
            <a class="normal button" id="previewImport"><?= ttPrepareImportQM ?></a>
            <a class="normal button" id="importQuestions"><?= ttImport ?></a>
        </div>
    </form>
    <?php closeBox() ?>
</div>

