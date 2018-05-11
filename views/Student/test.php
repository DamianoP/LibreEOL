<?php
/**
 * File: test.php
 * User: Masterplan
 * Date: 5/3/13
 * Time: 12:30 PM
 * Desc: Shows test page
 */

global $user, $log, $config;
session_start();
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
                       if($used > $duration){      // Time exipred, exit
                            //die(ttETimeExpired);
                            $remaining=0;
                       }
                       else                       // There is a remaining time, load questions
                           $remaining = $duration - $used;
        }
        if(($db->qQuestionSet($idSet, $lang, $subject)) && ($questions = $db->getResultAssoc())){
            if(count($questions) != $questionsNum){
                die(ttEQuestionNotFound);
            }
            echo '<div id="contenitoreDelTest">';
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
<!---

            <a class="ok button right" id="submitTest" onclick="submitTest(new Array(true));"><?= ttSubmit ?></a>
-->         
            <div class="contenitoreBottoneTest">
            <a class="bottoneTest" id="submitTest" onclick="submitTest(new Array(true));"><?= ttSubmit ?></a>
            </div>
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

<script> 
//questa funzione evita che amazon blocchi la sessione dell'utente
//var idSetAttuale=;

setInterval(function keepAlive_EOL() {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "index.php", true);
  	xhttp.send();
    }, 300000);

/*
$('.questionTest').click(function(index, div) {
   submitAnswer(index,div);
});
*/
// questa funzione controlla che l'insegnante non abbia chiuso l'esame dello studente
/* 
setInterval(function CheckExamAvailabilityJs() {
    $.ajax({
        url     : "index.php?page=student/checkexamavailability",
        type    : "post",
        data    : {
            //idSet   :  idSetAttuale
        },
        success : function (data) {
            if(data.trim() == "ACK" || data.trim() == ""){
                console.log("check ok");
            }else if(data.trim() == "SUBMITTED"){                
                showSuccessMessage(ttMTestSubmitted);
                $.ajax({
                    url     : "index.php?page=student/closetest",
                    type    : "post",
                    data    : {
                    },
                    success : function (data) {
                        setTimeout(function(){location.href = "index.php?page=student/index"}, 1500);
                    },
                    error : function (request, status, error) {
                        console.log("jQuery AJAX request error:".error);
                        setTimeout(function(){location.href = "index.php?page=student/index"}, 1500);
                    }
                });
            }else{                
                console.log("check failed: "+data);
            }
        },
        error : function (request, status, error) {
            console.log("jQuery AJAX request error:".error);
        }
    });
}, 60000);
*/

//questa funzione salva lo stato dell'esame sul server ogni X secondi
setInterval(function(){ 
    controllaCambiamenti();
    //controllaCambiamenti(idSetAttuale); 
}, 10000);
</script>

