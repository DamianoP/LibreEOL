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
$duration = '90';
$questions = '0';
$desc = '';
$summaryClass = '';

$topicsForSettings = array();

$db = new sqlDB();
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
        $desc = $testSettings['description'];
        $summaryClass = 'hidden';

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
        <input class="readonly numeric tSpace" type="text" id="settingsQuestions" <?php if($_POST['action'] == 'new') echo 'style="background: rgb(12, 156, 12); color: white;" ';?>value="<?= $questions ?>">
        <div class="clearer"></div>

        <label class="tSpace"><?= ttDescription ?> : </label>
        <textarea class="<?= $editClass ?> tSpace" id="settingsDesc"><?= $desc ?></textarea>
        <a id="settingsDescChars" class="charsCounter hidden"></a>
    </div>

<?php
$questionsTopic = array();
$questionsDifficulty = array_fill(1, getMaxQuestionDifficulty(), 0);

$script = 'var oldSelectedQuestion = new Array(';
if($_POST['action'] == 'show'){
    if(($db->qShowQuestionsForSetting($_POST['idTestSetting'])) && ($db->numResultRows()) > 0){
        $question = $db->nextRowAssoc();

        $questionsTopic[$question['fkTopic']] = isset($questionsTopic[$question['fkTopic']])? $questionsTopic[$question['fkTopic']]+1 : 1;
        $questionsDifficulty[$question['difficulty']]++;

        $script .= "'".$question['idQuestion']."'";
        while($question = $db->nextRowAssoc()){

            $questionsTopic[$question['fkTopic']] = isset($questionsTopic[$question['fkTopic']])? $questionsTopic[$question['fkTopic']]+1 : 1;
            $questionsDifficulty[$question['difficulty']]++;

            $script .= ", '".$question['idQuestion']."'";
        }
    }
}
$script .= ');';
?>

    <div class="columnCenter">
        <h2 class="center"><?= ttTopicsQuestions ?></h2>
        <div class="tableScroll">
            <table id="topicsTable">
                <thead>
                    <tr>
                        <th class="settingsTopicName"><?= ttName ?></th>
                        <th class="settingsTopicQuestions"><?= ttRandom ?></th>
                        <th></th>
                        <th class="settingsTopicQuestionsMandatory"><?= ttMandatory ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $topicQuestionSummary = 0;
                if($db->qSelect('Topics', 'fkSubject', $_SESSION['idSubject'])){
                    while($topic = $db->nextRowAssoc()){
                        $class = 'hidden';
                        $numQuestionMandatory = 0;
                        $numQuestionsRandom = 0;
                        if(isset($topicsForSettings[$topic['idTopic']])){
                            $class = '';
                            if(isset($questionsTopic[$topic['idTopic']]))
                                $numQuestionMandatory = $questionsTopic[$topic['idTopic']];
                            $numQuestionsRandom = (int)$topicsForSettings[$topic['idTopic']]['numQuestions'] - $numQuestionMandatory;
                            $topicQuestionSummary += (int)$topicsForSettings[$topic['idTopic']]['numQuestions'];
                        }elseif($_POST['action'] == 'new'){
                            $class = '';
                        }?>
                        <tr class="settingsTopic <?= $class ?>" value="<?= $topic['idTopic'] ?>">
                            <td class="settingsTopicName"><?= $topic['name'] ?> : </td>
                            <td class="settingsTopicQuestions">
                                <input class="<?= $editClass ?> numeric" type="number" min="0"
                                       id="topicQuestions<?= $topic['idTopic'] ?>" value="<?= $numQuestionsRandom ?>"
                                       onchange="changeTopicQuestions(this);" onkeyup="changeTopicQuestions(this);">
                            </td>
                            <td>+</td>
                            <td class="settingsTopicQuestionsMandatory">
                                <span id="topicQuestionsMandatory<?= $topic['idTopic'] ?>"><?= $numQuestionMandatory ?></span>
                            </td>
                          </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="<?= $summaryClass ?> backSuccess" id="topicQuestionsSummary"><span><?= $topicQuestionSummary ?></span> <?= ttQuestions ?></div>
    </div>

    <div class="columnRight">
        <h2 class="center"><?= ttDifficultyQuestions ?></h2>
        <table id="difficultyTable">
            <thead>
                <tr>
                    <th class="settingsDifficultyName"><?= ttName ?></th>
                    <th class="settingsDifficultyQuestions"><?= ttRandom ?></th>
                    <th></th>
                    <th class="settingsDifficultyQuestionsMandatory"><?= ttMandatory ?></th>
                </tr>
            </thead>
            <tbody>

                <?php
                $difficultyQuestionSummary = 0;
                $difficulties = array(
                    1 => "Easy",
                    2 => "Medium",
                    3 => "Hard"
                );
                foreach(range(1, 3) as $indexDifficulty){

                    if($_POST['action'] == 'show'){
                        $numQuestionMandatory = $questionsDifficulty[$indexDifficulty];
                        $numQuestionsRandom = (int)$testSettings['num'.$difficulties[$indexDifficulty]] - $numQuestionMandatory;
                        $difficultyQuestionSummary += (int)$testSettings['num'.$difficulties[$indexDifficulty]];
                    }else{
                        $numQuestionMandatory = 0;
                        $numQuestionsRandom = 0;
                    }
                    ?>

                    <tr class="settingsDifficulty" value="<?= $indexDifficulty ?>">
                        <td class="settingsDifficultyLabel"><?= constant('ttD'.$indexDifficulty) ?> : </td>
                        <td class="settingsDifficultyQuestions">
                            <input class="<?= $editClass ?> numeric" id="settingsD<?= $indexDifficulty ?>" type="number" min="0"
                                   value="<?= $numQuestionsRandom ?>"
                                   onchange="changeDifficultyQuestions(this);" onkeyup="changeDifficultyQuestions(this);">
                        </td>
                        <td>+</td>
                        <td class="settingsDifficultyQuestionsMandatory">
                            <span id="settingsD<?= $indexDifficulty ?>Mandatory"><?= $questionsDifficulty[$indexDifficulty] ?></span>
                        </td>
                    </tr>

                <?php
                }
                ?>

            </tbody>
        </table>
        <div class="<?= $summaryClass ?> backSuccess" id="difficultyQuestionsSummary"><span><?= $difficultyQuestionSummary ?></span> <?= ttQuestions ?></div>
    </div>

    <div class="clearer"></div>

    <script>
        var topicQuestionSummary = <?= $topicQuestionSummary ?>;
        var difficultyQuestionSummary = <?= $difficultyQuestionSummary ?>;

    <?= $script ?>
    </script>

</form>
