<?php
/**
 * File: showsettingsinfo.php
 * User: Masterplan
 * Date: 4/23/13
 * Time: 5:54 PM
 * Desc: Shows test setting's info or shows new empty panel to add new test setting
 */

global $user, $tt, $log;

$YNlabel[0] = 'No';
$YNlabel[1] = 'Yes';

$editClass = 'writable';
$name = ttNewTestSettings;
$scoreType = '10';
$scoreMin = '0';
$bonus = '0';
$negative = '0';
$editable = '0';
$certificate = '0';
$duration = '90';
$questions = '15';
$easy = '5';
$medium = '5';
$hard = '5';
$desc = '';
$summaryClass = '';
$write = '';

$idUsr = -1;
$sbgrp = -1;
$grp = -1;
$topicsForSettings = array();

$db = new sqlDB();
$db2 = new sqlDB();
if($_POST['action'] == 'show'){
    if(($db->qSelect('TestSettings', 'idTestSetting', $_POST['idTestSetting'])) && ($testSettings = $db->nextRowAssoc())){

        $editClass = 'readonly';
        $name = $testSettings['name'];
        $scoreType = $testSettings['scoreType'];
        $scoreMin = $testSettings['scoreMin'];
        $bonus = $testSettings['bonus'];
        $negative = $testSettings['negative'];
        $editable = $testSettings['editable'];
        $duration = $testSettings['duration'];
        $questions = $testSettings['questions'];
        $easy = $testSettings['numEasy'];
        $medium = $testSettings['numMedium'];
        $hard = $testSettings['numHard'];
        $desc = $testSettings['description'];
        $certificate = $testSettings['certificate'];
        $idUsr = $testSettings['idUser'];
        $grp = $testSettings['group'];
        $sbgrp = $testSettings['subgroup'];
        $summaryClass = 'hidden';
        $write = 'disabled';

        if($db->qShowTopicsForSetting($_POST['idTestSetting'])){
            $topicsForSettings = $db->getResultAssoc('idTopic');
        }else{
            die($db->getError());
        }
    }else{
        die($db->getError());
    }
}
?>

<form class="infoEdit" onsubmit="return false;">

    <div class="columnLeft">
        <h2 class="center"><?= ttGeneralInformations ?></h2>

        <label><?= ttName ?> : </label>
        <input class="<?= $editClass ?>" type="text" id="settingsName" size="50" value="<?= $name ?>">
        <a id="settingsNameChars" class="charsCounter hidden"></a>

        <label class="tSpace"><?= ttScoreType ?> : </label>
        <dl class="dropdownInfo tSpace" id="settingsScoreType">
            <dt class="<?= $editClass ?>">
                <span><?= constant('ttST'.$scoreType) ?><span class="value"><?= $scoreType ?></span></span>
            </dt>
            <dd>
                <ol>
                    <?php
                    $scoreTypes = getScoreTypes();
                    $index = 0;
                    while($index < count($scoreTypes)){
                        $type = 'ttST'.$scoreTypes[$index];
                        echo '<li>'.constant($type).'<span class="value" value="">'.$scoreTypes[$index].'</span></li>';
                        $index++;
                    }
                    ?>
                </ol>
            </dd>
        </dl>
        <div class="clearer"></div>

        <label class="tSpace"><?= ttScoreMin ?> : </label>
        <input class="left <?= $editClass ?> tSpace numeric" type="number" min="0" id="settingsScoreMin" value="<?= $scoreMin ?>">

        <label class="tSpace"><?= ttBonus ?> : </label>
        <input class="left <?= $editClass ?> tSpace numeric" type="number" min="0" id="settingsBonus" value="<?= $bonus ?>">

        <div class="clearer"></div>

        <label class="tSpace"><?= ttNegativeScores ?> : </label>
        <dl class="dropdownInfo tSpace" id="settingsNegative">
            <dt class="<?= $editClass ?>">
                <span><?= constant('tt'.$YNlabel[$negative]) ?><span class="value"><?= $negative ?></span></span>
            </dt>
            <dd>
                <ol>
                    <li><?= ttNo ?><span class="value">0</span></li>
                    <li><?= ttYes ?><span class="value">1</span></li>
                </ol>
            </dd>
        </dl>

        <label id="settingsEditableLabel" class="tSpace"><?= ttEditableScore ?> : </label>
        <dl class="dropdownInfo tSpace" id="settingsEditable">
            <dt class="<?= $editClass ?>">
                <span><?= constant('tt'.$YNlabel[$editable]) ?><span class="value"><?= $editable ?></span></span>
            </dt>
            <dd>
                <ol>
                    <li><?= ttNo ?><span class="value">0</span></li>
                    <li><?= ttYes ?><span class="value">1</span></li>
                </ol>
            </dd>
        </dl>

        <div class="clearer"></div>

        <label class="tSpace"><?= ttDuration ?> : </label>
        <div id="settingsDuration" class="tSpace">
            <dl class="dropdownInfo" id="settingsDurationH">
                <dt class="<?= $editClass ?>">
                    <span><?= intval($duration/60) ?>
                        <span class="value"><?= intval($duration/60) ?></span>
                    </span>
                </dt>
                <dd>
                    <ol>
                        <?php
                        for($hour = 0; $hour <= 10; $hour++)
                            echo '<li>'.$hour.'<span class="value">'.$hour.'</span></li>';
                        ?>
                    </ol>
                </dd>
            </dl>
            <label><?= ttHours ?></label>
            <dl class="dropdownInfo" id="settingsDurationM">
                <dt class="<?= $editClass ?>">
                    <span><?= sprintf("%02u", intval($duration%60)) ?>
                        <span class="value"><?= intval($duration%60) ?></span>
                    </span>
                </dt>
                <dd>
                    <ol>
                        <?php
                        for($minutes = 0; $minutes <= 55; $minutes = $minutes+5){
                            echo sprintf("<li>%02u<span class=\"value\">%u</span></li>", $minutes, $minutes);
                        }
                        ?>
                    </ol>
                </dd>
            </dl>
            <label><?= ttMinutes ?></label>
            <div class="clearer"></div>
        </div>
        <div class="clearer"></div>

        <label class="tSpace"><?= ttQuestions ?> : </label>
        <input class="<?= $editClass ?> numeric tSpace setDifficulty" type="text" id="settingsQuestions" value="<?= $questions ?>" required>
        <div class="clearer"></div>
        <div class="clearer"></div>
        <table>
            <td><label id="tablelabel" class="tSpace"><?= ttD1s ?> : </label></td>
            <td><input class="<?= $editClass ?> numeric tSpace setDifficulty" id="setEasy" type="text" max="<?= $questions?>" value="<?= $easy?>" required></td>
            <td><label class="tSpace l2Space"><?= ttD2s ?> : </label></td>
            <td><input class="<?= $editClass ?> numeric tSpace setDifficulty" id="setMedium" type="text" max="<?= $questions?>" value="<?= $medium?>" required></td>
            <td><label class="tSpace l2Space"><?= ttD3s ?> : </label></td>
            <td><input class="<?= $editClass ?> numeric tSpace setDifficulty" id="setHard" type="text" max="<?= $questions?>" value="<?= $hard?>" required></td>

        </table>

        <br>
        <label class="tSpace"><?= ttDescription ?> : </label>
        <textarea class="<?= $editClass ?> tSpace" id="settingsDesc"><?= $desc ?></textarea>
        <a id="settingsDescChars" class="charsCounter hidden"></a>

        <label id="settingsCertificate" class="tSpace"><?= ttCertificate ?> : </label>
        <dl class="dropdownInfo tSpace" id="settingsCertificate">
            <dt class="<?= $editClass ?>">
                <span><?= constant('tt'.$YNlabel[$certificate]) ?><span class="value"><?= $certificate ?></span></span>
            </dt>
            <dd>
                <ol>
                    <li><?= ttNo ?><span class="value">0</span></li>
                    <li><?= ttYes ?><span class="value">1</span></li>
                </ol>
            </dd>
        </dl>
    </div>

    <?php
    $questionsTopic = array();
    $questionsTopicEasy = array();
    $questionsTopicMedium = array();
    $questionsTopicHard = array();

    $script = 'var oldSelectedQuestion = new Array(';

    if($_POST['action'] == 'show'){
        if(($db->qShowQuestionsForSetting($_POST['idTestSetting'])) && ($db->numResultRows()) > 0){
            $question = $db->nextRowAssoc();

            if (isset($questionsTopicEasy[$question['fkTopic']])){
                if ($question['difficulty'] == 1)
                    $questionsTopicEasy[$question['fkTopic']]++;
            }else{
                if ($question['difficulty'] == 1)
                    $questionsTopicEasy[$question['fkTopic']] = 1;
            }
            if (isset($questionsTopicMedium[$question['fkTopic']])){
                if ($question['difficulty'] == 2)
                    $questionsTopicMedium[$question['fkTopic']]++;
            }else{
                if ($question['difficulty'] == 2)
                    $questionsTopicMedium[$question['fkTopic']] = 1;
            }
            if (isset($questionsTopicHard[$question['fkTopic']])){
                if ($question['difficulty'] == 3)
                    $questionsTopicHard[$question['fkTopic']]++;
            }else{
                if ($question['difficulty'] == 3)
                    $questionsTopicHard[$question['fkTopic']] = 1;
            }
            $questionsTopic[$question['fkTopic']] = isset($questionsTopic[$question['fkTopic']])? $questionsTopic[$question['fkTopic']]+1 : 1;
            $script .= "'".$question['idQuestion']."'";

            while($question = $db->nextRowAssoc()){

                if (isset($questionsTopicEasy[$question['fkTopic']])){
                    if ($question['difficulty'] == 1)
                        $questionsTopicEasy[$question['fkTopic']]++;
                }else{
                    if ($question['difficulty'] == 1)
                        $questionsTopicEasy[$question['fkTopic']] = 1;
                }
                if (isset($questionsTopicMedium[$question['fkTopic']])){
                    if ($question['difficulty'] == 2)
                        $questionsTopicMedium[$question['fkTopic']]++;
                }else{
                    if ($question['difficulty'] == 2)
                        $questionsTopicMedium[$question['fkTopic']] = 1;
                }
                if (isset($questionsTopicHard[$question['fkTopic']])){
                    if ($question['difficulty'] == 3)
                        $questionsTopicHard[$question['fkTopic']]++;
                }else{
                    if ($question['difficulty'] == 3)
                        $questionsTopicHard[$question['fkTopic']] = 1;
                }
                $questionsTopic[$question['fkTopic']] = isset($questionsTopic[$question['fkTopic']])? $questionsTopic[$question['fkTopic']]+1 : 1;
                $script .= ", '".$question['idQuestion']."'";
            }
        }
    }
    $script .= ');';
    ?>

    <div class="columnCenter">
        <h2 class="center"><?= ttTopicsQuestions ?></h2>
            <table id="topicsTable">
                <thead>
                <tr style="font-weight: bold">
                    <th class="settingsTopicName"><?= ttName ?></th>
                    <th><?= ttQuestions ?></th>
                    <th><?= ttMandatory ?></th>
                    <th><?= ttD1s ?></th>
                    <th><?= ttD2s ?></th>
                    <th><?= ttD3s ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $totals = array('0','0','0','0','0');
                if($db->qSelect('Topics', 'fkSubject', $_SESSION['idSubject'])){
                    while($topic = $db->nextRowAssoc()){
                        //Calculates the number of total questions and mandatory questions of the topic
                        $class = 'hidden';
                        $numQuestionMandatory = 0;
                        $numQuestionMandatoryEasy = 0;
                        $numQuestionMandatoryMedium = 0;
                        $numQuestionMandatoryHard = 0;
                        $numQuestions = 0;
                        $numMaxEasy = 0;
                        $numMaxMedium = 0;
                        $numMaxHard = 0;

                        if(isset($topicsForSettings[$topic['idTopic']])){
                            $class = '';
                            if(isset($questionsTopicEasy[$topic['idTopic']]))
                                $numQuestionMandatoryEasy = $questionsTopicEasy[$topic['idTopic']];
                            if(isset($questionsTopicMedium[$topic['idTopic']]))
                                $numQuestionMandatoryMedium = $questionsTopicMedium[$topic['idTopic']];
                            if(isset($questionsTopicHard[$topic['idTopic']]))
                                $numQuestionMandatoryHard= $questionsTopicHard[$topic['idTopic']];

                            $numQuestionMandatory = $numQuestionMandatoryHard + $numQuestionMandatoryMedium + $numQuestionMandatoryEasy;
                            $numQuestions = (int) $topicsForSettings[$topic['idTopic']]['numQuestions'];
                        }elseif($_POST['action'] == 'new'){
                            $class = '';
                        }
                        //Calculatas the maximum number of available questions divided by difficulty
                        $db2->qSelect('Subjects','idSubject', $_SESSION['idSubject']);
                        $subject= $db2->nextRowAssoc();
                        $language = $subject['fkLanguage'];
                        $db2->qCountQuestionPerTopic($topic['idTopic'],1,$language);
                        $numMaxEasy = $db2->nextRowAssoc();
                        $numMaxEasy = $numMaxEasy['maxQuestions'];
                        $db2->qCountQuestionPerTopic($topic['idTopic'],2,$language);
                        $numMaxMedium = $db2->nextRowAssoc();
                        $numMaxMedium = $numMaxMedium['maxQuestions'];
                        $db2->qCountQuestionPerTopic($topic['idTopic'],3,$language);
                        $numMaxHard = $db2->nextRowAssoc();
                        $numMaxHard = $numMaxHard['maxQuestions'];

                        $totals[0] += $numQuestions + $numQuestionMandatory;
                        $totals[1] += $numQuestionMandatory;
                        $totals[2] += $numMaxEasy;
                        $totals[3] += $numMaxMedium;
                        $totals[4] += $numMaxHard;
                        ?>
                        <tr class="settingsTopic <?= $class ?>" value="<?= $topic['idTopic'] ?>">
                            <td class="settingsTopicName"><?= $topic['name'] ?> : </td>
                            <td class="settingsTopicQuestions">
                                <input id="numQuestions<?= $topic['idTopic'] ?>" name="<?= $topic['name'] ?>" class="<?= $editClass ?> numeric numQuestionsPerTopic"
                                       type="number" min="<?= $numQuestionMandatory ?>" max="<?= $numMaxEasy+ $numMaxMedium+ $numMaxHard ?>" value="<?= $numQuestions + $numQuestionMandatory ?>" required>
                            </td>
                            <td class="settingsTopicQuestionsMandatory">
                                <span id="topicQuestionsMandatory<?= $topic['idTopic'] ?>"><?= $numQuestionMandatory ?></span>
                            </td>
                            <td>
                                <span id="numMaxEasy<?= $topic['idTopic'] ?>" class="numMaxEasy">(<?= $numMaxEasy ?>)</span>
                            </td>
                            <td>
                                <span id="numMaxMedium<?= $topic['idTopic'] ?>" class="numMaxMedium">(<?= $numMaxMedium ?>)</span>
                            </td>
                            <td>
                                <span id="numMaxHard<?= $topic['idTopic'] ?>" class="numMaxHard">(<?= $numMaxHard ?>)</span>
                            </td>

                        </tr>
                        <?php
                    }
                }
                ?>
                    <tr><td>&nbsp;</td></tr>
                    <tr class="settingsTopic <?= $class ?>" value="totals" id="rigaTotale">
                        <td class="settingsTopicName"><?= tttotals ?> : </td>
                        <td class="settingsTopicQuestions">
                            <span id="topicQuestionsTotals"><?= $totals[0]; ?></span>
                        </td>
                        <td class="settingsTopicQuestionsMandatory">
                            <span id="topicQuestionsMandatoryTotals"><?= $totals[1]; ?></span>
                        </td>
                        <td>
                            <span id="numMaxEasyTotals">(<?= $totals[2]; ?>)</span>
                        </td>
                        <td>
                            <span id="numMaxMediumTotals">(<?= $totals[3]; ?>)</span>
                        </td>
                        <td>
                            <span id="numMaxHardTotals">(<?= $totals[4]; ?>)</span>
                        </td>

                    </tr>
                </tbody>
            </table>
   
        <script>
            <?= $script ?>
        </script>
    </div>

</form>
<?php 
    if (
        ($user->group ==$grp && $user->subgroup==$sbgrp) || ($grp==null && $subgroup==null)
        )
    {
//	echo "<div>".$user->group." - ".$testSettings['group']."</div>";
//        echo "<div>".$user->subgroup." - ".$sbgrp."</div>";
?>
    <div id="viewPanel">
        <a class="normal button right rSpace" id="editSettingsInfo" onclick="editSettingsInfo()"><?= ttEdit ?></a>
    </div>
<?php
    }
?>
