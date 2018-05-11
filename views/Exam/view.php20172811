<?php
/**
 * File: view.php
 * User: Masterplan
 * Date: 5/6/13
 * Time: 3:42 PM
 * Desc: View archived test
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
        $studentName = $testInfo['name'].' '.$testInfo['surname'];
        $idSubject = $testInfo['fkSubject'];
        $numQuestions = $testInfo['questions'];
        $scoreTest = $testInfo['scoreTest'];
        $bonus = $testInfo['testBonus'];
        $scoreFinal = ($testInfo['scoreFinal'] > $testInfo['scoreType'])? $testInfo['scoreType'].' '.ttCumLaudae : $testInfo['scoreFinal'];
        $scale = $testInfo['scale'];

        if(($db->qViewArchivedTest($_POST['idTest'], null, $idSubject)) && ($questions = $db->getResultAssoc('idQuestion'))){
            if(count($questions) != $numQuestions){
                die(ttEQuestionNotFound);
            }
            openBox(ttTest.': '.$studentName.' ('.$scoreFinal.')', 'normal', 'correct', array('showHide'));
            $lastQuestion = '';
            foreach($questions as $idQuestion => $questionInfo){
                $questionAnswers = '';
                $questionScore = 0;

                $lastQuestion = (--$numQuestions == 0) ? 'last' : '';
                $answered = json_decode(stripslashes($questionInfo['answer']), true);
                if($answered == '')
                    $answered = array('');

                $question = Question::newQuestion($questionInfo['type'], $questionInfo);
                $question->printQuestionInView($idSubject, $answered, $scale, $lastQuestion);

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
                            <td class="sScore"><label id="scoreBonus"><?= $bonus ?></label></td>
                            <td>=</td>
                        </tr>
                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttFinalScore ?></td>
                            <td colspan="2" class="sScore"><label id="scorePost"><?= $scoreFinal ?></label></td>
                        </tr>
                    </table>
                    <input type="hidden" id="idTest" value="<?= $testInfo['idTest'] ?>">

                </div>
                <a class="button ok" onclick="window.close();"><?= ttClose ?></a>
            </div>

            <div class="clearer"></div>

            <?php closeBox(); ?>
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