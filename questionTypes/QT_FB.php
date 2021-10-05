<?php
/**
 * File: QT_FB.php
 * User: Francesco Giancristofaro
 * Date: 31/05/21
 * Time: 17:05
 * Desc: Class for Fill In The Blanks question type
 */

class QT_FB extends Question
{
    //utility for get string between two strings
    private function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function createNewQuestion()
    {
        $idQuestion = parent::createNewQuestion();
        return $idQuestion;
    }

    public function printQuestionEditForm($action, $readonly)
    {

        $db = new sqlDB();
        if (!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))) {
            die($db->getError());
        }
        ?>
        <ul xmlns="http://www.w3.org/1999/html">
            <li><a id="questionTab" href="#question-tab"><?= ttQuestion ?></a></li>
            <?php if ($action == 'show') echo '<li><a id="answersTab" href="#answers">' . ttAnswers . '</a></li>'; ?>
        </ul>

        <div id="question-tab">

            <!-- Print question language tabs and textareas user by ckeditor -->
            <?php $this->printQuestionTabsAndTextareas($action); ?>

            <!-- Print question's extras list -->
            <?php $this->printQuestionExtraForm($action); ?>

            <div class="clearer bSpace"></div>

            <!-- Print hidden field for question's type (FB) -->
            <input type="hidden" id="questionType" value="FB">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly); ?>

            <div class="clearer"></div>

        </div>

        <?php if ($action == 'show') { ?>

        <div id="answers">

            <div class="bSpace" id="answersTablecountinginerFB">

                <?php $this->printAnswersTable($this->get('idQuestion'), $_SESSION['idSubject']) ?>

            </div>

            <div class="clearer"></div>
            <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(true);"><?= ttExit ?></a>
            <div class="clearer"></div>
        </div>

        <?php
    }
        $this->printQuestionTypeLibrary();
        echo '<script> initialize_FB(); </script>';
    }


    //PRINT INTERFACE OF ANSWERS
    public function printAnswersTable($idQuestion, $idSubject)
    {
        $this->printAnswersTabs_FB();
        ?>

        <!--HEADER TABLE WITH HELP GUIDE -->
        <div id="answerInfoFB">
            <section>
                <div id="headerTable" class="tbl-headerFB">
                    <div class="answerHeaderFB">
                        <div class="split2">
                            <div id="header1FB">
                                <h1 style="padding-left: 17px;font-size: 20px;"><?= ttElement ?></h1>
                            </div>

                            <div id="header3FB">
                                <h1 style="padding-left: 15px;font-size: 20px;"><?= ttAnswers ?>
                                    <span style="width: 50px;float: right">
                                        <svg class="help_FB" height="30" width="50"><text x="0" y="15"
                                                                                          fill="white">Help</text></svg>
                                    </span>
                                </h1>
                            </div>
                        </div>
                        <div class="wideFB"></div>
                    </div>
                </div>

                <?php

                $db = new sqlDB();
                $db1 = new sqlDB();

                //empty array to hold text of answers data
                $acceptedAnswer = array();

                //empty array to hold couple data-id of textbox/ idAnswer
                $answersIdArray = array();

                //fill previous arrays with data from database
                if (!($db->qSelect("Answers", "fkQuestion", "$idQuestion")) || !($answersIdItem = $db->getResultAssoc())) {
                    if ($db->getError() != null && $db->getError() != "")
                        die($db->getError());
                } else {
                    if (count($answersIdItem) > 0) {
                        foreach ($answersIdItem as $item) {
                            $data1 = explode("*", $item['score']);
                            $answersIdArray[$data1[0]] = $item['idAnswer'];
                            if (!($db1->qSelect("TranslationAnswers", "fkAnswer", $item['idAnswer'])) || !($answersTranslationItem = $db1->getResultAssoc())) {
                                if ($db1->getError() != null && $db1->getError() != "")
                                    die($db1->getError());
                            } else {
                                foreach ($answersTranslationItem as $element) {
                                    $data = $element['translation'];
                                    $acceptedAnswer[$element['fkLanguage']][$data1[0]] = explode("==@@=@@==", $data);
                                }
                                unset($element, $data);
                            }
                        }
                        unset($item);
                    }
                }

                //PRINT AN ANSWERS TABLE MENU FOR EVERY TRANSLATIONS EXISTING ON QUESTION
                if (!($db->qSelect("TranslationQuestions", "fkQuestion", "$idQuestion")) || !($translations = $db->getResultAssoc())) {
                    die($db->getError());
                } else {
                    foreach ($translations as $item) {
                        ?>
                        <div class="tbl-contentFB" id="tbodyAnswer<?= $item['fkLanguage'] ?>">
                            <table id="contentAnswerFB" >
                                <tbody>

                                <?php


                                // singleFB=array of strings represented the question splitted by input FB item used as a separator
                                // numberitemFB=count of items
                                $singleFB = preg_replace('~</?p[^>]*>~', '', $item['translation']);
                                $numberItemFB = preg_match_all("#<input data-id='@==@=@==@[0-99]@==@=@==@' name='fb_item' type='text' />#", $singleFB, $idItemArray);
                                $singleFB = preg_split("#<input data-id='@==@=@==@[0-99]@==@=@==@' name='fb_item' type='text' />#", " " . $singleFB);

                                // PRINT A ROW FOR EVERY ITEM FB
                                for ($i = 0; $i < $numberItemFB; $i++) {

                                    // get item id
                                    $idCurrent = $this->get_string_between($idItemArray[0][$i], "@==@=@==@", "@==@=@==@");

                                    //OPEN ROW
                                    echo("<tr class='rowAnswer'>");

                                    // help input for hold id answer relative to this item
                                    echo "<input class='idAnswer' type='hidden' data-id='" . $idCurrent . "' data-lang='" . $item['fkLanguage'] . "' value='" . $answersIdArray[$idCurrent] . "'>";

                                    if (array_key_exists($i, $singleFB) && is_string($singleFB[$i])) {

                                        //Part of question to show in one row of table
                                        $text = html_entity_decode(current($singleFB));

                                        // row field for display part of question
                                        echo "<td class='renderItemFB'>" . $text . "&nbsp;<input style='height: 11px;width: 31px; border-radius: 4px;border:1px black solid;pointer-events: none' name='fb_item' type='text' readonly/> ";

                                        //next pointer in singleFB array
                                        next($singleFB);

                                        //If is the the last fb item print the final part of question.
                                        if ($i==$numberItemFB-1)
                                            echo html_entity_decode(current($singleFB))."</td>";
                                        else
                                            echo "</td>";

                                    } else {
                                        echo "<td class='renderItemFB'><input name='fb_item' type='text' readonly/></td>";
                                    }

                                    //create answers menu. $answer may be an array of accepted answer or a single accepted answer (if empty = =@a=@b=empty=@a=@b=")
                                    $answer = (isset($acceptedAnswer[$item['fkLanguage']][$idCurrent])) ? $acceptedAnswer[$item['fkLanguage']][$idCurrent] : 'No';
                                    ?>

                                    <!--row field for answers menu -->
                                    <td class="answersMenuFB">
                                        <div>
                                            <select data-lang="<?= $item['fkLanguage'] ?>" data-id="<?= $idCurrent ?>"
                                                    class="listAnswerFB">
                                                <option value="hide" disabled
                                                <?php
                                                if ($answer != "No") {
                                                    foreach ($answer as $element) {
                                                        if ($element != "=@a=@b=empty=@a=@b=") {
                                                            $elementDisplay = html_entity_decode(html_entity_decode(html_entity_decode($element, ENT_QUOTES), ENT_QUOTES), 3);
                                                            $elementDisplay = (strlen($elementDisplay) > 29) ? substr($elementDisplay, 0, 26) . ".." : $elementDisplay;
                                                            $elementDisplay = (htmlspecialchars($elementDisplay, ENT_QUOTES));
                                                            echo ">Risposte Accettate
                                                </option><option value='" . $element . "' data-lang='" . $item['fkLanguage'] . "' data-id='" . $idCurrent . "' selected>" . $elementDisplay . "</option>";
                                                        } else
                                                            echo " selected>Risposte Accettate
                                                </option>";
                                                    }
                                                    unset($element);
                                                }

                                                ?>
                                            </select> &nbsp;&nbsp;&nbsp;

                                            <!--update answer button-->
                                            <input type="button" data-lang="<?= $item['fkLanguage'] ?>"
                                                   data-id="<?= $idCurrent ?>"
                                                   class="updateAnswerFB" value="Modifica" disabled>

                                            <!--new answer button-->
                                            <input type="button" value="Aggiungi" class="newAnswer_FB" data-lang="<?= $item['fkLanguage'] ?>"
                                                 data-id="<?= $idCurrent ?>"/>
                                        </div>
                                    </td>
                                    <?php

                                    //CLOSE ROW
                                    echo("</tr>");
                                }
                                ?>

                                </tbody>
                            </table>

                        </div>
                        <?php

                    }
                    unset($item);

                }
                ?>

            </section>
        </div>

        <?php
    }


    public function printQuestionPreview()
    {
        global $config;
        // -------  Add extra buttons  ------- //
        $extra = '';
        if (strpos($this->get('extra'), 'c') !== false)
            $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
        if (strpos($this->get('extra'), 'p') !== false)
            $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';
        ?>

        <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="FB">
            <div class="questionText"><?php echo ttQTFB_TEXT; ?><span style="float: right"><?= $extra ?></span>
            </div>

            <div class="questionAnswers">
                <?php
                $demand = $this->get('translation');
                $language = $this->get('fkLanguage');

                /* THIS INPUT TAG IS USED FOR SEND LANGUAGE OF TRANSLATION OF QUESTION USED BY printQuestionInCorrection() AND getScoreFromGivenAnswer()
                 TO KNOW WHICH TRANSLATIONS IT HAVE TO LOOK FOR WHEN CORRECT QUEST */
                echo "<input style='display:none' name='fb_item' type='text' value='" . $language . "' />";
                echo $demand;

                ?>
            </div>
        </div>

        <?php
    }


    //SAME AS PREVIEW
    public function printQuestionInTest($idSubject, $answered, $extras)
    {
        global $config,$log;
        $this->printQuestionPreview();

        // -------  Add extra buttons  ------- //
        $extra = '';
        if (strpos($this->get('extra'), 'c') !== false) {
            $extras['calculator'] = true;
            $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
        }
        if (strpos($this->get('extra'), 'p') !== false) {
            $extras['periodicTable'] = true;
            $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';
        }
        return $extras;
    }


    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion)
    {
        global $config,$log;

        // represent the total score of question in consideration of the scale used
        $questionScore = 0;
        $db = new sqlDB();
        $db2 = new sqlDB();

        //number of replies
        $countingAnswer = count($answered);

        for ($m = 1; $m < $countingAnswer; $m++)
            $studentAnswers[$answered[$m][id]] = preg_replace('!\s+!', ' ', strtolower(trim(html_entity_decode(html_entity_decode($answered[$m][value], ENT_QUOTES),ENT_QUOTES))));

        if (($db->qAnswerSet($this->get('idQuestion'), null, $idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))) {

            $inc = 1;
            //total score obtained by student
            $graphicScore = 0;

            //variable to store max score that student can get in this question in consideration of scale
            $answermaxscore = 0;

            //array for printing answers in hidden menu with id's as index
            $answerArrayForHiddenMenu=array();

            foreach ($answerSet as $idAnswer => $answer) {
                $language=$answer['fkLanguage'];
                $answermaxscore += round((explode("*", $answer['score'])[1] * $scale), 2);
                $idItem= explode("*",$answer['score'])[0];
                $studentResponse = preg_replace('~</?p[^>]*>~', '',strtolower(trim(html_entity_decode($studentAnswers[$idItem], ENT_QUOTES))));
                $find = "false";

                    $db2->qSelect('TranslationAnswers', 'fkAnswer', $idAnswer);
                    while (($translation = $db2->nextRowAssoc()) && $find == "false") {

                        $answerArrayForHiddenMenu[$idItem]="";

                        $correct_answer = $translation['translation'];

                        // if correct answer contains this separator i know that it is an array of accepted answers
                        if (strpos($correct_answer, "==@@=@@==")) {

                            //explode this array for get single accepted answer and compare with student response
                            $correct_answer = explode("==@@=@@==", $correct_answer);

                            foreach ($correct_answer as &$element) {
                                $element =strtolower( html_entity_decode(html_entity_decode($element, ENT_QUOTES), ENT_QUOTES));
                                if ($studentResponse == $element) {
                                    $find = "true";
                                    $answerArrayForHiddenMenu[$idItem] .= "<b>" . $element . "</b>@*@*?!?";
                                }else
                                    $answerArrayForHiddenMenu[$idItem] .= $element."@*@*?!?";
                            }
                            unset($element);

                        } // if there is a unique accepted answer
                        else {
                            $correct_answer=strtolower(html_entity_decode(html_entity_decode($correct_answer, ENT_QUOTES), ENT_QUOTES));
                            if ($studentResponse == $correct_answer) {
                                $find = "true";
                                $answerArrayForHiddenMenu[$idItem] .= "<b>" . $correct_answer . "</b>@*@*?!?";
                            }else
                                $answerArrayForHiddenMenu[$idItem] .= $correct_answer."@*@*?!?";
                        }
                    }

                //IF THE ANSWER IS CORRECT INCREMENT THE GRAPHIC SCORE AND SET THE VECTOR ARRAY TO 1 I.E. CORRECT ANSWER
                if ($find == "true") {
                    $vector[$inc] = 1;
                    $graphicScore += round((explode("*", $answer['score'])[1] * $scale), 2);
                } else
                    $vector[$inc] = 0;

                $inc++;
            }

            // INSERT THE QUESTION IN ONE OF THIS CLASS ACCORDING TO THEIR SCORE
            $questionClass = 'wrongQuestion';
            if ($graphicScore > 0 && $graphicScore < $answermaxscore)
                $questionClass = 'halfRightQuestion';
            elseif ($graphicScore >= $answermaxscore)
                $questionClass = 'rightQuestion';

            ?>

            <!--PRINT QUESTION-->
            <div class="questionTest <?= $questionClass . ' ' . $lastQuestion ?>"
                 value="<?= $this->get('idQuestion') ?>" type="FB">
                <div class="questionText" onclick="showHide(this);">
                    <span class="responseQuestion"></span>
                    <?php
                    $textboxCount = preg_match_all("#<input data-id=.@==@=@==@[0-99]@==@=@==@. name=.fb_item. type=.text. />#", $this->get('translation'), $idItemArray);
                    $contentDemand = preg_split("#<input data-id=.@==@=@==@[0-99]@==@=@==@. name=.fb_item. type=.text. />#", $this->get('translation'));

                    $i = 0;
                    foreach ($contentDemand as $element) {
                        echo $element;
                        $answerStudent = ($answered[$i + 1][value] != "") ? $answered[$i + 1][value] : "--nessuna risposta--";
                        if ($i < $textboxCount) {
                            echo "&nbsp;<sup><b>" . ($i + 1) . "</b></sup>&nbsp;<input style='font-weight:bold;text-align: center;";
                            //add background to input tag based on the response (right/wrong answer)
                            echo ($vector[$i + 1] == 1) ? "background-color:#8EEB90" : "background-color:#ED7F80";
                            echo "' name='fb_item' type='text' value='" . strtolower($answerStudent) . "' readonly/>&nbsp;";
                        }
                        $i++;
                    }
                    unset($element);

                    ?>

                    <!--PRINT STUDENT'S SCORE-->
                    <span style="float: right"><span><b><?= ttTotalScore ?>&#58;&nbsp;</b></span><span class="responseScore"><?= round(number_format($graphicScore, 2), 2); ?></span></span>
                </div>

                <!--PRINT ACCORDION MENU WITH ALL INFO -->
                <div class="questionAnswers hidden">

                    <?php
                    echo "<div><b>Risposte Accettate:</b></div><br>";
                    for ($j=1;$j<count($answered);$j++){
                        echo '<div ';
                        echo ($vector[$j] == "1") ? 'style="background:#cdf9d3;">' :'style="background:#ffcece;">';
                        echo "<b>".$j.")</b>  ".substr(str_replace("@*@*?!?"," , ",$answerArrayForHiddenMenu[$answered[$j][id]]),0,-2);
                        $questionScore += round((explode("*", $answer['score'])[1] * $scale), 2);
                        echo'<label class="score">';
                        echo ($vector[$j] == "1") ? round(explode("*", $answer['score'])[1] * $scale, 2) : 0;
                        echo '</label></div><br>';
                    }
                    ?>

                    <label class="questionScore">
                        <?php
                        if (in_array(0, $vector))
                            echo "1";
                        else
                            echo round(number_format($questionScore, 2), 1);
                        ?>
                    </label>
                    <div class="clearer"></div>
                </div>
            </div>
            <?php
        } else {
            die(ttEAnswers);
        }
    }


    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion)
    {
        $this->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);
    }


    public function getScoreFromGivenAnswer()
    {
        global $log;
        $score = 0;
        $answerr = json_decode(stripslashes($this->get('answer')), true);
        $languageQuest = $answerr[0][value];
        //number of replies
        $countingrisposte = count($answerr);

        for ($m = 1; $m < $countingrisposte; $m++)
            $rispostautente[$answerr[$m][id]] = preg_replace('!\s+!', ' ', strtolower(trim(html_entity_decode(html_entity_decode($answerr[$m][value], ENT_QUOTES),ENT_QUOTES))));

        //ID demand
        $idQuestion = $this->get('idQuestion');

        $db = new sqlDB();
        $db2 = new sqlDB();

        //variable for check if all answer are right and so i can retrieve score=1 even in case of total score<1
        // (example: 6 items have score 0,16 as rounded value. So all 6 answer right is equal to 0,96)
        $checkIfAllAnswerRight = true;

        //leggo le risposte di questa domanda
        if ($db->qSelect('Answers', 'fkQuestion', $idQuestion) && $result = $db->getResultAssoc()) {

                foreach ($result as $element) {

                    $data = explode("*", $element['score']);
                        if ($db2->qSelect('TranslationAnswers', 'fkAnswer', $element['idAnswer']) && $translation = $db2->getResultAssoc()) {

                            foreach ($translation as $item){
                                $data2 =$item['translation'];
                                if (strpos($data2, "==@@=@@==")) {
                                    $acceptedAnswer = explode("==@@=@@==", $data2);
                                    $acceptedAnswer = array_map(function ($value) {
                                        return strtolower(html_entity_decode(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES));
                                    }, $acceptedAnswer);

                                    if ($rispostautente[$data[0]]!= null && in_array($rispostautente[$data[0]], $acceptedAnswer)) {
                                        $score += $data[1];
                                        break;
                                    }else{
                                        $checkIfAllAnswerRight = false;
                                    }
                                } else {
                                    if (trim(strtolower(html_entity_decode(html_entity_decode($data2,ENT_QUOTES),ENT_QUOTES))) == $rispostautente[$data[0]]) {
                                        $score += $data[1];
                                        break;
                                    }else{
                                        $checkIfAllAnswerRight = false;
                                    }
                                }
                            }
                            unset($item);
                        } else {
                            $error = $db2->getError();
                            $log->append("Debug(crash):" . $error);
                            die($error);
                        }

                }

            unset($item, $element, $key, $value);
        } else {
            $error = $db->getError();
            $log->append("Debug(crash):" . $error);
            die($error);
        }

        $log->append("The score is:" . $score);
        if ($checkIfAllAnswerRight)
            return 1;
        else
            return $score;
    }
}

