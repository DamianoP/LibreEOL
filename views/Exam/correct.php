<?php
/**
 * File: correct.php
 * User: Masterplan
 * Date: 5/5/13
 * Time: 3:51 PM
 * Desc: Shows test correction page
 */

global $config;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">

    <?php
    $db = new sqlDB();
    if(($db->qTestDetails(null, $_POST['idTest'])) && ($testInfo = $db->nextRowAssoc())){
        global $log;
        $studentName = $testInfo['name'].' '.$testInfo['surname'];
        $idSet = $testInfo['fkSet'];
        $idSubject = $testInfo['fkSubject'];
        $numQuestions = $testInfo['questions'];
        $scoreTest = $testInfo['scoreTest'];
        $scoreType = $testInfo['scoreType'];
        $scale = $testInfo['scale'];
        $isEditable = ($testInfo['editable'] == 0)? false : true;

        if(($db->qQuestionSet($idSet, null, $idSubject)) && ($questions = $db->getResultAssoc('idQuestion'))){
            if(count($questions) != $numQuestions){
                die(ttEQuestionNotFound);
            }
            openBox(ttTest.': '.$studentName.' ('.$scoreTest.')', 'normal', 'correct', array('showHide'));
            $lastQuestion = '';
            foreach($questions as $idQuestion => $questionInfo){

                $lastQuestion = (--$numQuestions == 0) ? 'last' : '';
                $answered = json_decode(stripslashes($questionInfo['answer']), true);
                if($answered == '')
                    $answered = array('');

                $question = Question::newQuestion($questionInfo['type'], $questionInfo);
                $question->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);

            }
            ?>

            <div id="lastLine">
                <div id="finalScorePanel">
                    <table id="finalScore">
                        <tr>
                            <td class="sLabel"><?= ttScoreTest ?></td>
                            <td class="sScore"><label id="scorePre"><?= $scoreTest ?></label></td>
                            <td>+</td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttBonus ?></td>
                            <?php if($testInfo['bonus'] == 0){ ?>
                            <td class="sScore"><label id="scoreBonus">0</label></td>
                            <?php }else{ ?>
                            <td>
                                <dl class="dropdownBonus">
                                    <dt><span><?= $testInfo['bonus'] ?><span class="value"><?= $testInfo['bonus'] ?></span></span></dt>
                                    <dd>
                                        <ol>
                                            <?php
                                            $index = 0;
                                            while($index <= $testInfo['bonus']){
                                                echo '<li>'.$index.'<span class="value">'.$index.'</span></li>';
                                                $index += 0.5;
                                            }
                                            ?>
                                        </ol>
                                    </dd>
                                </dl>
                                <label id="scoreBonus" class="hidden"><?= $testInfo['bonus'] ?></label>
                            </td>
                            <?php } ?>
                            <td>=</td>
                        </tr>
                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttFinalScore ?></td>
                            <?php
                            $finalScore = $scoreTest + $testInfo['bonus'];
                            if($finalScore > $scoreType)
                                $finalScore = $scoreType;
                            if($isEditable){ ?>
                                <!-- Print dropdown for editable final score -->
                                <td>
                                    <dl class="dropdownFinalScore">
                                        <dt><span><?= number_format(round($finalScore, 0), 0); ?><span class="value"><?= number_format(round($finalScore, 0), 0); ?></span></span></dt>
                                        <dd>
                                            <ol>
                                                <?php
                                                $index = 0;
                                                while($index <= $testInfo['scoreType']){
                                                    echo '<li>'.$index.'<span class="value">'.$index.'</span></li>';
                                                    $index += 1;
                                                }
                                                ?>
                                            </ol>
                                        </dd>
                                    </dl>
                                    <label id="scorePost" class="hidden"><?= number_format(round($finalScore, 0), 0); ?></label>
                                </td>
                            <?php }else{ ?>
                                <!-- Print label for non-editable final score -->
                                <td class="sScore"><label id="scorePost"><?= number_format(round($finalScore, 0), 0); ?>/<?= $scoreType?></label></td>
                            <?php } ?>
                            <td>
                                <?php if($finalScore == $scoreType){ ?>
                                    <span id="laudae"><input type="checkbox" id="scoreLaudae"><label><?= ttCumLaudae ?></label></span>
                                <?php }else{ ?>
                                    <span id="laudae" class="hidden"><input type="checkbox" id="scoreLaudae"<label>Lode</label></span>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" id="maxScore" value="<?= $scoreType ?>">
                    <input type="hidden" id="idTest" value="<?= $testInfo['idTest'] ?>">

                    <form action="index.php?page=exam/exams" method="post" id="idExamForm">
                        <input type="hidden" id="idExam" name="idExam" value="<?= $testInfo['fkExam'] ?>">
                    </form>
                </div>
                <a class="ok button" onclick="confirmTest(new Array(true));"><?= ttConfirm ?></a>
            </div>

            <div class="clearer"></div>

            <?php closeBox(); ?>
            <div class="clearer"></div>

        <?php
        }else{
           // die(ttEDatabase);
	    echo '
            <form id="myForm" action="index.php?page=exam/view" method="post">
                <input type="hidden" name="idTest" value="'.$_POST['idTest'].'">
            </form>
            <script type="text/javascript">
                document.getElementById("myForm").submit();
            </script>
            ';
        }
    }else{
        die(ttEDatabase.' / '.ttETestNotFound);
    }

    ?>

</div>
