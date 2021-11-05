<?php
global $config, $user;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="report-homepage">
        <?php
        openBox(ttAssessmentReport, 'normal', 'report');
        $db = new sqlDB();
        ?>
        <div class="report-button-right">
            <a class="report-button" id="back" href="index.php?page=report/aoreport"><?= ttBack ?></a>
            <a class="report-button" id="buttonStampa" onclick="printReport()"><?= ttPrintReport ?></a>

        </div>
        <div id="print-section">
            <b class="report-overview"> <?= ttTeacher ?> : </b>
            <span class="report-overview-text"><?= $user->name ?> <?= $user->surname ?></span> <br>
            <b class="report-overview"> <?= ttSubject ?> : </b>
            <?php
            if ($db->qGetSubjectName($_SESSION['examsparam'][0])) {
                $subjectname = $db->nextRowAssoc();
                ?>
                <span class="report-overview-text"> <?= $subjectname['name'] ?> </span>
                <?php
            }
            if ($_SESSION['minscoreparam'] != -1 && $_SESSION['maxscoreparam'] != -1) {
                ?>
                <br>
                <b class="report-overview"> <?= ttReportScoreRange ?> : </b>
                <span class="report-overview-text"> <?= $_SESSION['minscoreparam'] . " - " . $_SESSION['maxscoreparam'] ?> </span>
                <?php
            }
            ?>

            <?php
            $i = 1; //exam counter
            foreach ($_SESSION['examsparam'] as $exam) {
                if ($db->qGetAssessmentData($exam, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'])) {
                    $assessmentData = $db->nextRowAssoc();
                    ?>
                    <h1 class="report-exam"> <?= ttExam . " #" . $i . ") " . $assessmentData['date'] ?> </h1>
                    <div class="report-assessment">
                        <table class="report-fields">
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportFirstTestDate ?></td>
                                <td class=" report-field-value"><?= $assessmentData['first'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportLastTestDate ?></td>
                                <td class=" report-field-value"><?= $assessmentData['last'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportNumberStarted ?></td>
                                <td class=" report-field-value"><?= $assessmentData['numberstart'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportNumberFinished ?></td>
                                <td class=" report-field-value"><?= $assessmentData['finished'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportMinscore ?></td>
                                <td class=" report-field-value"><?= $assessmentData['minscore'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportMaxscore ?></td>
                                <td class=" report-field-value"><?= $assessmentData['maxscore'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportAverageScore ?></td>
                                <td class=" report-field-value"><?= $assessmentData['avgscore'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportLeastTime ?></td>
                                <td class=" report-field-value"><?= $assessmentData['mintime'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportMostTime ?></td>
                                <td class=" report-field-value"><?= $assessmentData['maxtime'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportAverageTime ?></td>
                                <td class=" report-field-value"><?= $assessmentData['avgtime'] ?></td>
                            </tr>
                            <tr>
                                <td class=" report-field"><?= ttAssessmentReportStdDeviation ?></td>
                                <td class=" report-field-value"><?= $assessmentData['stddeviation'] ?></td>
                            </tr>
                        </table>
                    </div>

                    <?php
                }
                if ($db->qGetParticipants($exam, $_SESSION['minscoreparam'], $_SESSION['maxscoreparam'])) {
                    ?>
                    <div class="report-participant">
                        <br>
                        <h2 class="report-participant-title"> <?= ttParticipants ?> </h2><br>
                        <div class="report-participant-grid">
                            <?php
                            while ($row = $db->nextRowAssoc()) {
                                ?>
                                <div class="report-participant-grid-item">
                                    <div class="report-assessment-name">
                                        <b> <?= $row['name'] . " " . $row['surname'] ?> </b> <br>
                                        <span> <?= $row['email'] ?> </span>
                                        <br>
                                    </div>
                                    <table id="report-assessment-user">
                                        <tr>
                                            <td class="bold"><?= ttDate ?></td>
                                            <td><?= $row['start'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bold"><?= ttScore ?></td>
                                            <td><?= $row['scoreFinal'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="bold"><?= ttTimeUsed2 ?></td>
                                            <td><?= $row['time'] ?></td>
                                        </tr>
                                    </table>
                                    <hr>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $i++;
                }
            }
            ?></div><?php
        closeBox();
        ?>
        <div class="clearer"></div>
    </div>
</div>