<?php
global $config, $user;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="report-homepage">
        <?php
        openBox(ttCoachingReport, 'normal', 'report');
        $db = new sqlDB();
        ?>
        <div>
            <?php
            if ($db->qGetParticipantData($_SESSION['idTest'])) {
                $participantData = $db->nextRowAssoc();
                ?>

                <div class="report-button-right">
                    <a class="report-button" id="back" href="index.php?page=report/coachinglist"><?= ttBack ?></a>
                    <a class="report-button" id="buttonStampa" onclick="printReport()"><?= ttPrintReport ?></a>
                </div>
                <div id="print-section">
                    <div class="report-coaching-info">
                        <b> <?= ttParticipant ?> : </b>
                        <span><?= $participantData['name'] . " " . $participantData['surname'] ?></span> <br>
                        <b> <?= ttEmail ?> : </b>
                        <span><?= $participantData['email'] ?></span> <br>
                        <b> <?= ttGroup ?> : </b>
                        <span><?= $participantData['nameSubGroup'] ?></span> <br>
                        <b> <?= ttSubject ?> : </b>
                        <span><?= $_SESSION['subject'] ?></span> <br>
                    </div>
                    <div class="report-coaching-details">
                        <br><b> <?= ttScoreFinal ?> : </b>
                        <span><?= $participantData['scoreFinal'] . "/" . $participantData['scoreType'] ?></span>
                        <br>
                        <b> <?= ttTimeUsed2 ?> : </b>
                        <span><?= $participantData['time_used'] ?></span> <br>
                        <b> <?= ttTimeLimit ?> : </b>
                        <span><?= $participantData['duration'] . " " . ttMinutes ?></span> <br>
                        <b> <?= ttNDistractions ?> : </b>
                        <span><?= $participantData['nDistractions'] ?></span> <br>
                        <b> <?= ttAudioDistractions ?> : </b>
                        <span><?= $participantData['audioDistractions'] ?></span> <br>
                        <b> <?= ttMouseDistractions ?> : </b>
                        <span><?= $participantData['mouseDistractions'] ?></span> <br>
                        <b> <?= ttOpeningCounter ?> : </b>
                        <span><?= $participantData['openingCounter'] ?></span> <br>
                        <b> <?= ttCloseBySystem ?> : </b>
                        <?php
                        if ($participantData['closedbysystem'] == 0) {
                            echo '<span>' . ttCloseBySystemOK . '</span> <br>';
                        } else if ($participantData['closedBySystem'] == 1) {
                            echo '<span>' . ttBannedBySystem . '</span> <br>';
                        } else {
                            echo '<span>' . ttBannedByTeacher . '</span> <br>';
                        }
                        ?>
                        <b> <?= ttDate ?> : </b>
                        <span><?= $participantData['date'] ?></span> <br>
                    </div>
                </div>
                <?php
                if (($db->qTestDetails(null, $_SESSION['idTest'])) && ($testInfo = $db->nextRowAssoc())) {
                    $studentName = $testInfo['name'] . ' ' . $testInfo['surname'];
                    $idSubject = $testInfo['fkSubject'];
                    $numQuestions = $testInfo['questions'];
                    $scoreTest = $testInfo['scoreTest'];
                    $scoreType = $testInfo['scoreType'];
                    $bonus = $testInfo['testBonus'];
                    $scoreFinal = ($testInfo['scoreFinal'] > $testInfo['scoreType']) ? $testInfo['scoreType'] . ' ' . ttCumLaudae : $testInfo['scoreFinal'];
                    $scale = $testInfo['scale'];
                    $isEditable = ($testInfo['editable'] == 0) ? false : true;
                    ?>
                    <span class="report-coaching-number"> <?= ttNumberOfQuestion . ": " . $numQuestions ?> </span>
                    <?php
                    if (($db->qViewArchivedTest($_SESSION['idTest'], null, $idSubject)) && ($questions = $db->getResultAssoc('idQuestion'))) {
                        $lastQuestion = '';
                        foreach ($questions as $idQuestion => $questionInfo) {
                            $questionAnswers = '';
                            $questionScore = 0;
                            $lastQuestion = (--$numQuestions == 0) ? 'last' : '';
                            $answered = json_decode(stripslashes($questionInfo['answer']), true);
                            if ($answered == '')
                                $answered = array('');
                            $question = Question::newQuestion($questionInfo['type'], $questionInfo);
                            $question->printQuestionInView($idSubject, $answered, $scale, $lastQuestion);
                        }
                    } else {
                        die(ttEDatabase);
                    }
                } else {
                    die(ttEDatabase . ' / ' . ttETestNotFound);
                }
            }
            ?>
        </div>
        <?php closeBox(); ?>
        <div class="clearer"></div>
    </div>
</div>