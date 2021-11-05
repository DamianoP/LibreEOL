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
        ?>

        <div id="report-container">
            <form id="aoreport" name="aoreport" method="post">
                <h2><?= ttAssessmentSelect ?></h2>
                <div class="report-select-container" id="subjects">
                    <h3><?= ttSelectSubject ?><br></h3>
                    <?php
                    $db = new sqlDB();
                    if ($db->qSubjects($user->id, $user->role)) {
                        echo '<select name="subject" size="5" id="arsubject" class="report-select"
                                    onchange=optionSelected("years");getExamYears();showExams();removeAllExams()>';
                        while ($subject = $db->nextRowAssoc()) {
                            echo "<option value='$subject[idSubject]'>" . $subject['name'] . "</option>";
                        }
                        echo '</select>';
                    }
                    ?>
                </div>
                <div class="report-select-container" id="years" hidden>
                    <h3><?= ttSelectYear ?><br></h3>
                    <select name="year" size="5" id="arsearched_year" class="report-select-year"
                            onchange='optionSelected("exam"); optionSelected("addbutton"); showExams()'></select>
                </div>
                <div class="report-select-container" id="exam" hidden>
                    <h3><?= ttAssessmentSelectExam ?><br></h3>
                    <select size="5" id="arexam" class="report-select-exam"></select>
                </div>
                <div class="report-buttons" id="addbutton" hidden>
                    <a class="report-button" id="add"
                       onclick='addExam(); optionSelected("selected"); optionSelected("removebutton")'><?= ttAdd ?></a>
                    <br>
                    <a class="report-button" id="addall"
                       onclick='addAll(); optionSelected("selected"); optionSelected("removebutton")'><?= ttAddAll ?></a>
                </div>
                <div class="report-select-container" id="selected" hidden>
                    <h3><?= ttExamsSelected ?><br></h3>
                    <select size="5" id="selectedexams" class="report-select-selected"></select>
                </div>
                <div class="report-buttons" id="removebutton" hidden>
                    <a class="report-button" id="remove" onclick="removeExam()"><?= ttRemove ?></a>
                    <br>
                    <a class="report-button" id="removeall"
                       onclick="removeAllExams()"><?= ttRemoveAll ?></a>
                </div>
                <br/>
                <hr class="report-divider"/>

                <table id="filterscore" class="report-filter">
                    <tr>
                        <td class="bold"><?= ttFilterByScore ?></td>
                        <td><input type="checkbox" id="assessmentScore"
                                   onclick="unlockFilter(this,assessmentMinScore,assessmentMaxScore)"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bold"><?= ttMinimumScore ?></td>
                        <td><input class="report-input" type="number" min="0" value="0" disabled="disabled"
                                   id="assessmentMinScore"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="bold"><?= ttMaximumScore ?></td>
                        <td><input class="report-input" type="number" min="0" value="30" disabled="disabled"
                                   id="assessmentMaxScore"></td>
                        <td></td>
                    </tr>
                </table>
                <hr>
                <div id="report-next">
                    <a class="report-button" id="next"
                       onclick="transferData(assessmentMinScore.value,assessmentMaxScore.value)"><?= ttNext ?></a>
                </div>
        </div>
        <?php closeBox(); ?>
    </div>
    <div class="clearer"></div>
</div>