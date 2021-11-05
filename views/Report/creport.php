<?php
global $config, $user;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="report-homepage">
        <?php openBox(ttCoachingReport, 'normal', 'report'); ?>
        <div>
            <div id="report-container">
                <form id="creport" name="creport" method="post">
                    <h2><?= ttCoachingReportWelcome ?></h2>
                    <div class="report-select-container" id="subjects">
                        <h3><?= ttSelectSubject ?><br></h3>
                        <?php
                        $db = new sqlDB();
                        if ($db->qSubjects($user->id, $user->role)) {
                            echo '<select size="5" id="crsubject" class="report-select"
                                    onchange=optionSelected("years");getExamYears();showExams()>';
                            while ($subject = $db->nextRowAssoc()) {
                                echo "<option value='$subject[idSubject]'>" . $subject['name'] . "</option>";
                            }
                            echo '</select>';
                        }
                        ?>
                    </div>
                    <div class="report-select-container" id="years" hidden>
                        <h3><?= ttSelectYear ?><br></h3>
                        <select size="5" id="crsearchedyear" class="report-select-year"
                                onchange='optionSelected("exam"); showExams()'></select>
                    </div>
                    <div class="report-select-container" id="exam" hidden>
                        <h3><?= ttSelectExam ?><br></h3>
                        <select size="5" id="crexam" class="report-select-exam"></select>
                    </div>
                    <br/>
                    <hr class="report-divider"/>
                    <h3><?= ttFilterByScore ?></h3>
                    <?= ttActivate ?><input type="checkbox" id="assessmentScore"
                                            onclick="unlockFilter(this,assessmentMinScore,assessmentMaxScore)">
                    <br/><br/>
                    <?= ttMinimumScore ?>
                    <input class="report-input" type="number" min="0" max="30" value="0" disabled
                           id="assessmentMinScore">
                    <br>
                    <br>
                    <?= ttMaximumScore ?>
                    <input class="report-input" type="number" min="0" max="30" value="30" disabled
                           id="assessmentMaxScore">
                    <hr/>
                    <div id="tabsbutton">
                        <a class="report-button" id="next" onclick="transferData()"><?= ttNext ?></a>
                    </div>
                </form>
            </div>
        </div>
        <?php closeBox(); ?>
    </div>
    <div class="clearer"></div>
</div>