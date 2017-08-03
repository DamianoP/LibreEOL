<?php
/**
 * File: settings.php
 * User: Masterplan
 * Date: 4/19/13
 * Time: 10:58 AM
 * Desc: Shows test settings for requested subject
 */

global $user, $tt, $config;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <?php
    $subjectInfo = null;
    $db = new sqlDB();
    if(($db->qSelect('Languages')) && ($allLangs = $db->getResultAssoc('idLanguage')) &&
       ($db->qSelect('Subjects', 'idSubject', $_SESSION['idSubject'])) && ($subjectInfo = $db->nextRowAssoc())){
        openBox(ttSettings, 'left-18%', 'settingsList', array('new-newSettings'));
        if($db->qSelect('TestSettings', 'fkSubject', $_SESSION['idSubject'], 'name')){
            echo '<div class="list">
                    <ul>
                        <li><a class="showSettingsInfo">'.$subjectInfo['name'].'</a></li>';
            while($setting = $db->nextRowAssoc()){
                echo '<li class="lPad"><a class="showSettingsInfo" value="'.$setting['idTestSetting'].'"
                                          onclick="showSettingsInfo(new Array(this, settingsEditing));"
                                          >'.$setting['name'].'</a></li>';
            }
            echo '</ul></div>';
        }else{
            echo ttEDatabase;
        }
        closeBox();
    }else{
        echo ttEDatabase;
    }

    $mainLang = $subjectInfo['fkLanguage'];
    $topics = array();
    $questionsS = array();

    openBox(ttInfo, 'left-82%', 'settingsInfo');
    ?>
    <form id="testSettingsInfoEditForm" onsubmit="return false;">
        <div id="testSettingsInfo"></div>

        <div id="testSettingsQuestion" class="bSpace">
            <?php
            if(($db->qSelect('Topics', 'fkSubject', $_SESSION['idSubject'], 'name')) && ($topics = $db->getResultAssoc('idTopic'))){

                if(count($topics) > 0){ ?>
                    <table id="questionsTable" class="stripe hover order-column">
                        <thead>
                            <tr>
                                <th class="qCheckbox"></th>
                                <th class="qText"><?= ttText ?></th>
                                <th class="qLanguages"><?= ttLanguages ?></th>
                                <th class="qTopic"><?= ttTopic ?></th>
                                <th class="qType"><?= ttType ?></th>
                                <th class="qDifficulty"><?= ttDifficulty ?></th>
                                <th class="qQuestionID">questionID</th>
                                <th class="qTopicID">topicID</th>
                                <th class="qTypeID">typeID</th>
                                <th class="qDifficultyID">difficultyID</th>
                                <th class="qSelected">selected</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                        $statuses = array('a' => 'Active',
                                          'i' => 'Inactive',
                                          'e' => 'Error');
                        if($db->qQuestions($_SESSION['idSubject'], '-1')){
                            $idQuestion = "first";
                            $checkbox = '';
                            $text = '';
                            $languages = '';
                            $topic = '';
                            $idTopic = '';
                            $idType = '';
                            $type = '';
                            $difficulty = '';
                            $difficultyID = '';
                            $languageID = $subjectInfo['fkLanguage'];
                            while($question = $db->nextRowAssoc()){
                                if($question['status'] == 'a'){
                                    if($idQuestion == $question['idQuestion']){
                                        if($question['fkLanguage'] == $subjectInfo['fkLanguage'])
                                            $text = $question['shortText'];

                                        $languages .= '<img title="'.$allLangs[$question['fkLanguage']]['description'].'"
                                                            class="flag" alt="'.$allLangs[$question['fkLanguage']]['alias'].'"
                                                            src="'.$config['themeFlagsDir'].$allLangs[$question['fkLanguage']]['alias'].'.gif">';
                                    }else{
                                        if($idQuestion == 'first'){
                                            $idQuestion = $question['idQuestion'];
                                            $topic = $question['name'];
                                            $idTopic = $question['idTopic'];
                                            $idType = $question['type'];
                                            $type = constant('ttQT'.$question['type']);
                                            $difficulty = constant('ttD'.$question['difficulty']);
                                            $difficultyID = 'settingsD'.$question['difficulty'];
                                            $checkbox = '<input type="checkbox" id="check'.$idQuestion.'" value="'.$idQuestion.'" name="question" onchange="selectQuestion(this);"/>';
                                            if($question['fkLanguage'] == $subjectInfo['fkLanguage'])
                                                $text = $question['shortText'];
                                            $languages = '<img title="'.$allLangs[$question['fkLanguage']]['description'].'"
                                                               class="flag" alt="'.$allLangs[$question['fkLanguage']]['alias'].'"
                                                               src="'.$config['themeFlagsDir'].$allLangs[$question['fkLanguage']]['alias'].'.gif">';
                                        }else{
                                            echo '<tr>
                                                      <td>'.$checkbox.'</td>
                                                      <td>'.$text.'</td>
                                                      <td>'.$languages.'</td>
                                                      <td>'.$topic.'</td>
                                                      <td>'.$type.'</td>
                                                      <td>'.$difficulty.'</td>
                                                      <td>'.$idQuestion.'</td>
                                                      <td>'.$idTopic.'</td>
                                                      <td>'.$idType.'</td>
                                                      <td>'.$difficultyID.'</td>
                                                      <td></td>
                                                  </tr>';
                                            $idQuestion = $question['idQuestion'];
                                            $topic = $question['name'];
                                            $idTopic = $question['idTopic'];
                                            $idType = $question['type'];
                                            $type = constant('ttQT'.$question['type']);
                                            $difficulty = constant('ttD'.$question['difficulty']);
                                            $difficultyID = 'settingsD'.$question['difficulty'];
                                            $checkbox = '<input type="checkbox" id="check'.$idQuestion.'"  value="'.$idQuestion.'" name="question" onchange="selectQuestion(this);"/>';
                                            if($question['fkLanguage'] == $subjectInfo['fkLanguage'])
                                                $text = $question['shortText'];
                                            $languages = '<img title="'.$allLangs[$question['fkLanguage']]['description'].'"
                                                               class="flag" alt="'.$allLangs[$question['fkLanguage']]['alias'].'"
                                                               src="'.$config['themeFlagsDir'].$allLangs[$question['fkLanguage']]['alias'].'.gif">';
                                        }
                                    }
                                }
                            }
                            echo '<tr>
                                      <td>'.$checkbox.'</td>
                                      <td>'.$text.'</td>
                                      <td>'.$languages.'</td>
                                      <td>'.$topic.'</td>
                                      <td>'.$type.'</td>
                                      <td>'.$difficulty.'</td>
                                      <td>'.$idQuestion.'</td>
                                      <td>'.$idTopic.'</td>
                                      <td>'.$idType.'</td>
                                      <td>'.$difficultyID.'</td>
                                      <td></td>
                                  </tr>';
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>
                                    <select id="questionTopicSelect" style="width:100px;">
                                        <option value="-1"> ------- </option>
                                        <?php
                                        foreach($topics as $key => $topic){
                                            echo '<option value="'.$topic['idTopic'].'">'.$topic['name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </th>
                                <th>
                                    <select id="questionTypeSelect" style="width:100px;">
                                        <option value="-1"> ------- </option>
                                        <?php
                                        foreach(getQuestionTypes() as $type){
                                            echo '<option value="'.$type.'">'.constant('ttQT'.$type).'</option>';
                                        }
                                        ?>
                                    </select>
                                </th>
                                <th>
                                    <select id="questionDifficultySelect" style="width:80px;">
                                        <option value="-1"> ------- </option>
                                        <?php
                                        $index = 1;
                                        while($index <= getMaxQuestionDifficulty()){
                                            echo '<option value="settingsD'.$index.'">'.constant('ttD'.$index).'</option>';
                                            $index++;
                                        }
                                        ?>
                                    </select>
                                </th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
            <?php
                }
            }else{
                die(ttEDatabase);
            }
            ?>
        </div>
        <div class="clearer"></div>
        <div id="viewPanel">
            <a class="normal button right rSpace" id="editSettingsInfo" onclick="editSettingsInfo()"><?= ttEdit ?></a>
        </div>
        <div id="editPanel" class="hidden">
            <a class="ok button right bSpace tSpace rSpace" id="saveSettingsInfo" onclick="saveSettingsInfo(new Array(true));"><?= ttSave ?></a>
            <a class="red button right bSpace tSpace rSpace" id="deleteSettings" onclick="deleteSettings(new Array(true));"><?= ttDelete ?></a>
            <a class="normal button left bSpace tSpace rSpace" id="cancel" onclick="cancelEdit(new Array(settingsEditing));"><?= ttCancel ?></a>
        </div>
        <div id="newPanel" class="hidden">
            <a class="ok button right bSpace tSpace rSpace" id="createNewSettings" onclick="createNewSettings();"><?= ttCreate ?></a>
            <a class="normal button left bSpace tSpace rSpace" id="cancelNew" onclick="cancelNew(new Array(true));"><?= ttCancel ?></a>
        </div>
        <div class="clearer"></div>
    </form>

<?php closeBox(); ?>

    <div class="clearer"></div>
</div>

<script>
    var mainLang = <?= $mainLang ?>;
</script>
