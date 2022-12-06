<?php
/**
 * File: QT_OC.php
 * User: Anis
 * Date: 02/03/2021
 * Desc: Class for OnClick questions
 */

class QT_OC extends Question {

    public function createNewQuestion(){
        $idQuestion = parent::createNewQuestion();
        return $idQuestion;
    }

    public function printQuestionEditForm($action, $readonly){
        global $config, $user;

        $db = new sqlDB();
        if(!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))){
            die($db->getError());
        }
        ?>
        <ul xmlns="http://www.w3.org/1999/html">
            <li><a id="questionTab" href="#question-tab"><?= ttQuestion ?></a></li>
            <?php if($action == 'show') echo '<li><a id="answersTab" href="#answers">'.ttAnswers.'</a></li>'; ?>
        </ul>

        <div id="question-tab">

            <!-- Print question language tabs and textareas user by ckeditor -->
            <?php $this->printQuestionTabsAndTextareas($action); ?>

            <!-- Print question's extras list -->
            <?php $this->printQuestionExtraForm($action); ?>

            <div class="clearer bSpace"></div>

            <!-- Print hidden field for question's type (OC) -->
            <input type="hidden" id="questionType" value="OC">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly); ?>

            <div class="clearer"></div>

        </div>

        <?php if($action == 'show'){ ?>
            <div id="answers">
            <?php
                $db = new sqlDB();
                $db1 = new sqlDB();
                if (!($db->qSelect("Answers", "fkQuestion", $this->get('idQuestion'))) || !($answersIdItem = $db->getResultAssoc())) {
                    if ($db->getError() != null && $db->getError() != "")
                        die($db->getError());
                }
                else {
                    if (count($answersIdItem) > 0) {
                        foreach ($answersIdItem as $item) {
                            if (!($db1->qSelect("TranslationAnswers", "fkAnswer", $item['idAnswer'])) || !($answersTranslationItem = $db1->getResultAssoc())) {
                                if ($db1->getError() != null && $db1->getError() != "")
                                    die($db1->getError());
                            }
                            else {
                                foreach ($answersTranslationItem as $element) {
                                    $data = $element['translation'];
                                }
                                unset($element, $data);
                            }
                        }
                        unset($item);
                    }
                }
                if (!($db->qSelect("TranslationQuestions", "fkQuestion", $this->get('idQuestion'))) || !($translations = $db->getResultAssoc())) {
                    die($db->getError());
                }
                else {
                    $text = $this->get('translation');
                    $pattern = "/<\s* input [^>]+ >/xi";
                    $counter = 1;
                    $text = preg_replace_callback($pattern, function($m) use (&$counter) {
                        return "<span class=\"droptarget\" style=\"pointer-events: none\"></span>" . "<sub><font color='0f4c81'>" . $counter++ . "</font></sub>";
                        }, $text);
                    echo "<div class='questionTextOC'>
                            <h3>".ttQuestion."</h3>
                            <hr>
                            <p>".$text."</p>
                          </div>";
                }
                ?>
                <div class="bSpace" id="answersTableContainer">
                    <tr>
                        <td><?ttQuestion?></td>
                    </tr>
                </div>

                <div class="bSpace" id="answersTableContainer">
                    <div class="smallButtons">
                        <div id="newAnswer_OC">
                            <img class="icon" src="<?= $config['themeImagesDir'].'new.png' ?>"/><br/>
                            <?= ttNew ?>
                        </div>
                    </div>

                    <?php $this->printAnswersTable($this->get('idQuestion'), $_SESSION['idSubject']) ?>

                </div>

                <div class="clearer"></div>
                <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(true);"><?= ttExit ?></a>
                <div class="clearer"></div>
            </div>

            <?php
        }
        $this->printQuestionTypeLibrary();
        echo '<script> initialize_OC(); </script>';
    }

    public function printAnswersTable($idQuestion, $idSubject){
        ?>

        <table id="answersTable" class="stripe hover">
            <thead>
            <tr>
                <th class="aScore"><?= ttPosition ?></th>
                <th class="aText"><?= ttText ?></th>
                <th class="aAnswerID">answerID</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = new sqlDB();
            if($db->qAnswerSet($idQuestion, null, $idSubject)){
                while($answer = $db->nextRowAssoc()){
                    $position = explode("*", $answer['score'])[0];
                    echo '<tr>
                              <td>'.$position.'</td>
                              <td>'.strip_tags($answer['translation']).'</td>
                              <td>'.$answer['idAnswer'].'</td>
                          </tr>';
                }
            }
            ?>
            </tbody>
        </table>
        <?php
    }

    public function printQuestionPreview(){
        global $config;

        $positionCheck = 0;
        $checkingArray = [];
        $db = new sqlDB();
        $numAnswers = 0;
        $totScore = 0;
        $error = 0;
        $text = $this->get('translation');
        $numQuestions = substr_count($text, "<input");
        $problemMessage='<div class="questionOCProblems">'.ttError.'<br>';
        if (($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $_SESSION['idSubject']))&&($answerSet = $db->getResultAssoc())) {
            for ($i = 0; $i < $numQuestions; $i++) {
                $checkingArray[$i] = $i + 1;
            }
            //checks if there is at least one empty space
            if ($numQuestions <= 0) {
                $error = 1;
                $problemMessage .= ttOCE1 . '<br>';
            }
            foreach ($answerSet as $item) {
                if ($item['score'] != 0) {
                    $totScore += explode("*", $item['score'])[1];
                    $position = explode("*", $item['score'])[0];
                    if ($position == $checkingArray[$position - 1]) {
                        $checkingArray[$position - 1] = 0;
                    }
                }
                $numAnswers++;
            }

            //checks if there are at least 2 labels
            if ($numAnswers < 2) {
                $problemMessage .= ttOCE2 . '<br>';
                $error = 1;
            }
            //checks if there are enough labels for the empty spaces
            if ($numQuestions > $numAnswers) {
                $problemMessage .= ttOCE3 . '<br>';
                $error = 1;
            }

            for ($i = 0; $i < count($checkingArray); $i++) {
                if ($checkingArray[$i] != 0) {
                    $positionCheck = 1;
                }
            }
            //checks if at least 1 space hasn't got an assigned label
            if ($positionCheck != 0) {
                $problemMessage .= ttOCE4 . '<br>';
                $error = 1;
            }

            $questionAnswers = '';
            shuffle($answerSet);
            foreach($answerSet as $answer){
                $class = '';
                $idQ = $this->get('idQuestion');
                if($answer['fkLanguage'] != $this->get('fkLanguage'))
                    $class = 'mainLang';
                    $questionAnswers .= '<span idQ = "'.$idQ.'" value="'.$answer['idAnswer'].'" class = "answerOC">
                    '.$answer['translation'].'
                    </span>';
                }
        }
                if ($numAnswers == 0) {
                    $problemMessage .= ttOCE3 . '<br>';
                    $error = 1;
                }
                // -------  Add extra buttons  ------- //
                $extra = '';
                if(strpos($this->get('extra'), 'c') !== false)
                    $extra .= '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
                if(strpos($this->get('extra'), 'p') !== false)
                    $extra .= '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';
                ?>

                <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="OC">
                    <?php
                    $text = $this->get('translation');
                    $pattern = "/<\s* input [^>]+ >/xi";
                    $dropId = $this->get('idQuestion');
                    $regex = preg_replace($pattern, "<span class=\"droptarget\" value=$dropId idAns=\"\"></span>", $text);
                    ?>

                    <div class="questionText"><div class="questionSubTextOC"><?= $regex ?></div><div class="infoButtonOCContainer"><img class="infoButtonOC" src="themes/default/images/help.png"></div></div>
                    <div class="questionAnswers"><?= $questionAnswers ?></div>
                    <?= $error==1?$problemMessage."</div>":"" ?>
                </div>

                <?php
        $this->printQuestionTypeLibrary();
        echo '<script> main_OC(); </script>';
}

    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;
        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $idSubject)) && ($answerSet = $db->getResultAssoc())){

            $questionAnswers = '';
            shuffle($answerSet);
            $idQ = $this->get('idQuestion');
            foreach($answerSet as $answer){
                $questionAnswers .= '<span idQ = "'.$idQ.'" value="'.$answer['idAnswer'].'" class = "answerOC">
                                         '.$answer['translation'].'
                                     </span>';
            }

            // -------  Add extra buttons  ------- //
            $extra = '';
            if(strpos($this->get('extra'), 'c') !== false){
                $extras['calculator'] = true;
                $extra .= '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
            }
            if(strpos($this->get('extra'), 'p') !== false){
                $extras['periodicTable'] = true;
                $extra .= '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';
            }
            ?>

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="OC">
                <?php
                $text = $this->get('translation');
                $pattern = "/<\s* input [^>]+ >/xi";
                $regex = preg_replace($pattern, "<span class=\"droptarget\" value=$idQ idAns=\"\"></span>", $text)
                ?>
                <div class="questionText"><div class="questionSubTextOC"><?= $regex ?></div><div class="infoButtonOCContainer"><img class="infoButtonOC" src="themes/default/images/help.png"></div></div>
                <div class="questionAnswers"><?= $questionAnswers ?></div>
            </div>

            <?php
            $this->printQuestionTypeLibrary();
            echo '<script> main_OC(); </script>';
            return $extras;
        }else{
            die($db->getError());
        }

    }

    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion){
        global $config;

        $questionAnswers = '';
        $questionScore = 0;
        $correctCounter = 0;
        $questionClass = 'emptyQuestion';
        $correctAnswer = array();
        $answeredText = array();

        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), null, $idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))){
            foreach($answerSet as $idAnswer => $answer){
                $answerdClass = "";
                $scoreNoPosition = explode("*", $answer['score'])[1];
                $position = explode("*", $answer['score'])[0];
                $right_wrongClass = ($scoreNoPosition > 0) ? 'rightAnswer' : 'wrongAnswer';

                if(in_array($idAnswer, $answered))
                {
                    $answeredPos = array_search($idAnswer, $answered);
                    $answeredText[$answeredPos]=$answer['translation'];
                    $answerdClass = 'answered';
                }
                if($position>0) {
                    $correctAnswer[$position - 1] = $idAnswer;
                    if ($correctAnswer[$position - 1] == $answered[$position - 1]) {
                        $questionScore += round(($scoreNoPosition * $scale), 2);
                        $correctCounter++;
                    }
                }
                $questionAnswers .= '<div class="'.$answerdClass.'">
                                         <span value="'.$idAnswer.'" class="responseOC '.$right_wrongClass.'"></span>
                                         <label>'.$answer['translation'].'</label>
                                         <label class="score">'.number_format(round($scoreNoPosition * $scale, 2), 2).'</label>
                                     </div>';
            }
            if(count($answered)==$correctCounter){
                $questionScore = 1*$scale;
            }
            $questionAnswers .= '<label class="questionScore">'.number_format($questionScore, 2).'</label>
                                 <div class="clearer"></div>';

            $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';
            $counter = 1;
            $pattern = "/<\s* input [^>]+ >/xi";
            $regex = preg_replace_callback($pattern, function($m) use (&$counter, $answeredText, $correctAnswer, $answered) {
                $pos = $counter-1;
                $counter++;
                if($correctAnswer[$pos] == $answered[$pos]) {
                    return "<span class=\"droptarget\" style=\"pointer-events: none\">$answeredText[$pos]</span>";
                }
                else{
                    if($answeredText[$pos]==""){
                        return "<span class=\"droptarget\" style=\"pointer-events: none; background-color: #ffffd1\">$answeredText[$pos]</span>";
                    }
                    else {
                        return "<span class=\"droptarget\" style=\"pointer-events: none; background-color: #ff5047\">$answeredText[$pos]</span>";
                    }
                }
            }, $this->get('translation'));
            $responseScore = ($questionScore > 0) ? $questionScore : 0;

            ?>

            <div class="questionTest <?= $questionClass.' '.$lastQuestion ?>" value="<?= $this->get('idQuestion') ?>" type="OC">
                <div class="questionText" onclick="showHide(this);">
                    <span class="responseQuestion"></span>
                    <?=
                        $regex
                    ?>
                    <span class="responseScore"><?= number_format($responseScore, 2); ?></span>
                </div>
                <div class="questionAnswers hidden"><?= $questionAnswers ?></div>
            </div>

            <?php
        }else{
            die(ttEAnswers);
        }
    }

    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion){
        $this->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);
    }

    public function getScoreFromGivenAnswer(){
        $score = 0;

        $answer = json_decode(stripslashes($this->get('answer')), true);
        $idQuestion = $this->get('idQuestion');

        $db = new sqlDB();

        $totalCorrectCheck = 0;
        if(($db->qSelect("Answers", "fkQuestion", $idQuestion)) && ($answerSet = $db->getResultAssoc())){
            foreach ($answerSet as $item) {
                if ($item['score'] != 0) {
                    $ansScore = explode("*", $item[score])[1];
                    $position = explode("*", $item[score])[0];
                    $answerChecker = $answer[($position - 1)];
                    if ($answerChecker == $item['idAnswer']) {
                        $score += $ansScore;
                        $totalCorrectCheck+=1;
                    }
                }
            }
            unset($item, $idItemArray, $element);
        } else {
            $error = $db->getError();
            die($error);
        }
        if($totalCorrectCheck == count($answerSet)) {
            $score = 1;
        }
        return $score;
    }

    public function checkConsistency($writer=1){
        global $config;
        $ack = false;
        $positionCheck = 0;
        $checkingArray = [];
        $db = new sqlDB();
        $numAnswers = 0;
        $totScore = 0;
        $checkingCount = 0;
        $stringError = "";
        if (($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $_SESSION['idSubject']))&&($answerSet = $db->getResultAssoc())){
            $text = $this->get('translation');
            $numQuestions = substr_count($text, "<input");
        }
        for($i=0; $i<$numQuestions; $i++){
            $checkingArray[$i]=$i+1;
        }
        //checks if there is at least one empty space
        if($numQuestions>0){
            $checkingCount +=1;
        }
        else{
            $stringError .= ttOCE1.'<br>';
        }

        foreach ($answerSet as $item) {
            if ($item['score'] != 0) {
                $totScore += explode("*", $item['score'])[1];
                $position = explode("*", $item['score'])[0];
                if ($position == $checkingArray[$position-1]) {
                    $checkingArray[$position-1] = 0;
                }
            }
            $numAnswers++;
        }

        //checks if there are at least 2 labels
        if($numAnswers>1){
            $checkingCount+=1;
        }
        else{
            $stringError .= ttOCE2.'<br>';
        }
        //checks if there are enough labels for the empty spaces
        if($numQuestions <= $numAnswers){
            $checkingCount+=1;
        }
        else{
            $stringError .= ttOCE3.'<br>';
        }

        for($i=0; $i<count($checkingArray); $i++){
            if($checkingArray[$i]!=0){
                $positionCheck=1;
            }
        }
        //checks if at least 1 space hasn't got an assigned label
        if($positionCheck==0){
            $checkingCount+=1;
        }
        else{
            $stringError .= ttOCE4.'<br>';
        }
        //checks if the total score is = 1
        if(round($totScore) == 1){
            $checkingCount+=1;
        }
        else{
            $stringError .= ttOCE5.'<br>';
        }
        if($checkingCount==5){$ack=true;}
        return $ack;
    }
}
