<?php
/**
 * File: errorquestion.php
 * User: GMarsi
 * Date: 14/10/2015
 * Time: 4:13 PM
 * Desc: Shows error segnalation
 */

global $user, $log;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div class="clearer"></div>
    <?php openBox(ttQuesionid, 'small', 'profile') ?>
    <form class="infoEdit" onsubmit="return false;">

        <label class="b2Space"><?= ttQuesionid ?> : </label>
        <input class="writable" id="idquestion" >
        <div class="clearer"></div>

        <label class="b2Space"><?= ttQuestionNotes ?> : </label>
        <textarea  id="notes"> </textarea>
        <div class="clearer"></div>


<br>

        <div>
            <a class="normal button" id="saveProfile" onclick="errorEmail();"><?= ttQuestionsend?></a>
            <br>
            <br>
        </div>
        <label class="b3Space"><?= ttQuestionreporting ?>  </label>
    </form>
    <?php closeBox() ?>
</div>