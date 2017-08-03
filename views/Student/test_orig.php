<?php
/**
 * File: test.php
 * User: Masterplan
 * Date: 5/3/13
 * Time: 12:30 PM
 * Desc: Shows test page
 */

global $user, $log, $config;

$questionTypesLibDir = opendir($config['systemQuestionTypesLibDir']);
while (($script = readdir($questionTypesLibDir)) !== false)
    if(substr($script, 0, 2) == 'QT')
        echo '<script type="text/javascript" src="'.$config['systemQuestionTypesLibDir'].$script.'"></script>';
closedir($questionTypesLibDir);

$extras = array(
    'calculator' => false,
    'periodicTable' => false
);

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="countdown"></div>
<div id="main">

    <?php
    $idSet = $_SESSION['idSet'];
    $db = new sqlDB();
    if(($db->qTestDetails($idSet)) && ($testInfo = $db->nextRowAssoc())){
        $lang = $testInfo['fkLanguage'];
        $subject = $testInfo['fkSubject'];
        $questionsNum = $testInfo['questions'];
        $timeStart = $testInfo['timeStart'];
        $timeEnd = $testInfo['timeEnd'];
        $now = date("Y-m-d H:i:s");
        $remaining = $duration = $testInfo['duration'] * 60;  // Minutes to seconds conversion

        switch($testInfo['status']){
            case 'e' :
            case 'a' : // This test has been already submitted, so don't load questions and exit
                       die(ttETestAlreadySubmitted);
            case 'b' : // This test has been blocked, so don't load questions and exit
                       die(ttBlocked); break;
            case 'w' : // Opening test for the first time, so set timeStart, status and load questions
                       if(!($db->qStartTest($testInfo['idTest'], $now))){ die(ttEDatabase); } break;
            case 's' : // This test was already opened (status = s), check remaining time
                       $timeStart = strtotime($testInfo['timeStart']);
                       $now = strtotime($now);
                       $used = $now - $timeStart;
                       if($used > $duration)      // Time exipred, exit
                           die(ttETimeExpired);
                       else                       // There is a remaining time, load questions
                           $remaining = $duration - $used;
        }

        if(($db->qQuestionSet($idSet, $lang, $subject)) && ($questions = $db->getResultAssoc())){
            if(count($questions) != $questionsNum){
                die(ttEQuestionNotFound);
            }
            shuffle($questions);
            openBox(ttTest, 'normal', 'test');
            foreach($questions as $questionInfo){
                $idQuestion = $questionInfo['idQuestion'];
                $answered = json_decode(stripslashes($questionInfo['answer']), true);
                if($answered == '')
                    $answered = array('');

                $question = Question::newQuestion($questionInfo['type'], $questionInfo);
                $extras = $question->printQuestionInTest($subject, $answered, $extras);

            }
            ?>

            <a class="ok button right" id="submitTest" onclick="submitTest(new Array(true));"><?= ttSubmit ?></a>
            <div class="clearer"></div>
            <?php
            closeBox();
            if($extras['calculator']){
            ?>
                <div class="extra" id="calculator" style="display:none">
                    <span class="extraTitle"><?= ttQEc ?></span>
                    <span class="extraClose" title="<?= ttClose ?>"></span>
                    <object width="100%" height="100%" data="<?= $config['systemExtraDir']?>EChem_calc.swf"></object>
                </div>
            <?php
            }
            if($extras['periodicTable']){
            ?>
                <div class="extra" id="periodicTable" style="display:none">
                    <span class="extraTitle"><?= ttQEp ?></span>
                    <span class="extraClose" title="<?= ttClose ?>"></span>
                    <img style="width:100%; height:100%;" src="<?= $config['systemExtraDir']?>T_PERIOD_it_COL.gif"/>
                </div>
            <?php
            }
            ?>
                <div class="clearer"></div>
        <?php
        }else{
            die(ttEDatabase);
        }
    }else{
        die(ttEDatabase.' / '.ttETestNotFound);
    }


    ?>

</div>

<script type="application/javascript">
    var countdown = new Countdown({
        time	    : <?= $remaining ?>,
        width		: 210,
        height		: 50,
        inline		: true,
        target		: "countdown",
        style 		: "flip",
        rangeHi		: "hour",
        rangeLo		: "second",
        padding 	: 0.4,
        onComplete	: countdownComplete,
        labels		: 	{
            font 	: "Arial",
            color	: "#ffffff",
            weight	: "normal"
        }
    });
</script>