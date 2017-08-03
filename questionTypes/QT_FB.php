
<?php
/**
* File: QT_FB.php
* User: Damiano Perri
* Date: 18/08/16
* Time: 17:05
* Desc: FB aggiornato per EOL 2016
*/
/**
* File: QT_FB.php
* User: Alberto
* Date: 18/09/14
* Time: 12:41
* Desc: Class for Fill In Blanks Question
*/

class QT_FB extends Question {

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

            <!-- Print hidden field for question's type (FB) -->
            <input type="hidden" id="questionType" value="FB">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly); ?>

            <div class="clearer"></div>

        </div>

        <?php if($action == 'show'){ ?>

            <div id="answers">

                <div class="bSpace" id="answersTablecountinginer">
                    <div class="smallButtons">
                        <div id="newAnswer_FB">
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
        echo '<script> initialize_FB(); </script>';
    }

    public function printAnswersTable($idQuestion, $idSubject){ ?>

        <table id="answersTable" class="stripe hover">
            <thead>
            <tr>
                <th class="aScore"><?= ttScore ?></th>
                <th class="aText"><?= ttText ?></th>
                <th class="aAnswerID">answerID</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = new sqlDB();
            if($db->qAnswerSet($idQuestion, null, $idSubject)){
                while($answer = $db->nextRowAssoc()){
                    echo '<tr>
                              <td>'.$answer['score'].'</td>
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

    public function printQuestionPreview()
    {
        global $config;


        $db = new sqlDB();
        if (($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $_SESSION['idSubject'])) && ($answerSet = $db->getResultAssoc())) {


            // -------  Add extra buttons  ------- //
            $extra = '';
            if (strpos($this->get('extra'), 'c') !== false)
                $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
            if (strpos($this->get('extra'), 'p') !== false)
                $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';




            ?>



            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="FB">
                <div class="questionText"></div>

                    <div class="questionAnswers"><?php
                echo $this->get('translation').$extra;
                ?>
        </div>

            </div>

            <?php
        }
    }

    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;
        $idQuestionTest=$this->get('idQuestion');
        ?>
        <div class="questionTest" value=<?php echo "'".$idQuestionTest."'" ?> type="FB">
            <div class="questionText"><?php echo ttQTFB_TEXT ?>
                <br>
            </div>
            <div class="questionAnswers"><?php
                $null = "";
                $demand = $this->get('translation');
                $length = strlen($demand);
                $textboxCount = 0;
                $counting = 1;
                for ($i = 0; $i < $length; $i++) {
                    if ($demand[$i] != "<") {
                        echo "$demand[$i]";
                    } else {
                        if (isset($demand[$i + 5])) {
                            $ok = true;
                            if (!($ok && (strcmp($demand[$i + 1], "i") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 2], "n") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 3], "p") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 4], "u") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 5], "t") == 0))) $ok = false;
                            if ($ok == true) {
                                $ok = 0;
                                $okT = 0;
                                $j = 0;
                                for ($j = $i; $j < $length && $ok < 11; $j++) {

                                    if ($demand[$j] == "t" && $ok == 0) $ok++;
                                    elseif ($demand[$j] == "y" && $ok == 1) $ok++;
                                    elseif ($demand[$j] == "p" && $ok == 2) $ok++;
                                    elseif ($demand[$j] == "e" && $ok == 3) $ok++;
                                    elseif ($demand[$j] == "=" && $ok == 4) $ok++;
                                    elseif ($ok == 5) $ok++;
                                    elseif ($demand[$j] == "t" && $ok == 6) $ok++;
                                    elseif ($demand[$j] == "e" && $ok == 7) $ok++;
                                    elseif ($demand[$j] == "x" && $ok == 8) $ok++;
                                    elseif ($demand[$j] == "t" && $ok == 9) $ok++;
                                    elseif ($ok == 10) $ok++;
                                    else {
                                        $ok = 0;
                                        if ($okT >= 5 || $demand[$j]==">") {
                                            $j = $length;
                                        }
                                    }
                                    $okT = $ok;
                                }
                                if ($ok == 11) {
                                    if(isset($answered[$textboxCount]))
                                        $textbox = '<input type="text" id="rispostas" value="'.$answered[$textboxCount].'">';
                                    else
                                        $textbox = '<input type="text" id="rispostas" value="">';
                                    echo $textbox;
                                    $textboxCount++;
                                    $counting++;
                                    while ($j < $length && $demand[$j] != ">") {
                                        $j++;
                                    }
                                    $i = $j;
                                } else {
                                    echo "<";
                                }
                            } else {
                                echo "<";
                            }
                        }
                        else{
                                echo "<";
                            }
                        }
                    }
                ?></div>
        </div>
        <br>
        <?php
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
        return $extras;
    }
/*
    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;
        $idQuestionTest=$this->get('idQuestion');
        ?>
        <div class="questionTest" value=<?php echo "'".$idQuestionTest."'" ?> type="FB">
            <div class="questionText"><?php echo ttQTFB_TEXT ?>
                <br>
            </div>
            <div class="questionAnswers"><?php
                echo $this->get('translation');
                ?></div>
        </div>
        <br>
        <?php
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
        return $extras;
    }
*/







    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion){
        global $config; 
        global $log;  
        $questionScore = 0;
        $db = new sqlDB();
        $db2 = new sqlDB();
        if (($db->qAnswerSet($this->get('idQuestion'), null, $idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))) {
        $countingtore = 1;
        $graphicScore = 0;
        $count=0;
        $inc = 0;
        foreach ($answerSet as $idAnswer => $answer) {
            $studentResponse = strtolower(trim($answered[$inc]));
            $find="false";
            if(strcmp($studentResponse, "")!=0){
                $count++;
                $db2->qSelect('TranslationAnswers', 'fkAnswer', $answer['idAnswer']);
                while (($traslation = $db2->nextRowAssoc()) && $find=="false") {
                    if($studentResponse==strtolower($traslation['translation'])){
                        $find="true";
                    }
                }
            }
            if ($find == "true") {
                    $vector[$inc] = 1;
                    $graphicScore += round(($answer['score'] * $scale), 1);
                }
            else $vector[$inc] = 0;
            $inc++; 
        }
        $questionClass = 'emptyQuestion';
        if ((count($answered) != 0) && $count>0)
            $questionClass = ($graphicScore > 0) ? 'rightQuestion' : 'wrongQuestion';
        ?>
        <div class="questionTest <?= $questionClass . ' ' . $lastQuestion ?>" value="<?= $this->get('idQuestion') ?>" type="FB">
            <div class="questionText" onclick="showHide(this);">
                <span class="responseQuestion"></span>
                <?= $null = "";
                $demand = $this->get('translation');
                //calcolo la length dell'array "demand"
                $length = strlen($demand);
                //indice per il numero di textbox
                $textboxCount = 0;
                $counting = 1;
                for ($i = 0; $i < $length; $i++) {
                    if ($demand[$i] != "<") {
                        //stampo la lettera nella posizione $i dell'array "demand"
                        echo "$demand[$i]";
                    } else {
                        if (isset($demand[$i + 5])) {
                            $ok = true;
                            if (!($ok && (strcmp($demand[$i + 1], "i") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 2], "n") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 3], "p") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 4], "u") == 0))) $ok = false;
                            if (!($ok && (strcmp($demand[$i + 5], "t") == 0))) $ok = false;
                            if ($ok == true) {
                                $ok = 0;
                                $okT = 0;
                                $j = 0;
                                for ($j = $i; $j < $length && $ok < 11; $j++) {

                                    if ($demand[$j] == "t" && $ok == 0) $ok++;
                                    elseif ($demand[$j] == "y" && $ok == 1) $ok++;
                                    elseif ($demand[$j] == "p" && $ok == 2) $ok++;
                                    elseif ($demand[$j] == "e" && $ok == 3) $ok++;
                                    elseif ($demand[$j] == "=" && $ok == 4) $ok++;
                                    elseif ($ok == 5) $ok++;
                                    elseif ($demand[$j] == "t" && $ok == 6) $ok++;
                                    elseif ($demand[$j] == "e" && $ok == 7) $ok++;
                                    elseif ($demand[$j] == "x" && $ok == 8) $ok++;
                                    elseif ($demand[$j] == "t" && $ok == 9) $ok++;
                                    elseif ($ok == 10) $ok++;
                                    else {
                                        $ok = 0;
                                        if ($okT >= 5 || $demand[$j]==">") {
                                            $j = $length;
                                        }
                                    }
                                    $okT = $ok;
                                }
                                if ($ok == 11) {

                                    if ($vector[$textboxCount] == 1) {
                                        $colore = "#AFFAB4";
                                    } else {
                                        $colore = "#F4A8A8";
                                    }
                                    $textbox = '<input type="text" id="rispostas" style="background-color:' . $colore . ';" readonly value="' . $answered[$textboxCount] . '">';
                                    echo $textbox;
                                    $textboxCount++;
                                    //incremento countingtore
                                    $counting++;
                                    while ($j < $length && $demand[$j] != ">") {
                                        $j++;
                                    }
                                    $i = $j;
                                } else {
                                    echo "<";
                                }
                            } else {
                                echo "<";
                            }
                        }
                        else{
                                echo "<";
                            }
                        }
                    }
                ?>
                <span class="responseScore"><?= number_format($graphicScore, 1); ?></span>
                </div>
                <div class="questionAnswers hidden">
                <?php
                    $i=0;
                    $inc=0;
                    foreach($answerSet as $idAnswer => $answer) {
                        $answerdClass = "";
                        if( $vector[$i]=="1"){
                            $right_wrongClass ='rightAnswer';
                            ?>

                        <div STYLE="background:#90ee90;" class="<?php echo $answerdClass ?>">
                            <?php
                            echo ttAnswer." ".$countingtore.": ";
                            echo strtolower(trim($answered[$inc]));
                            $answerdClass = 'answered';
                            $questionScore += round(($answer['score'] * $scale), 1);
                            ?>
                            <label class="score"><?php echo round($answer['score'] * $scale, 1)?></label>
                            </div><?php
                        }else
                        {
                            ?>

                            <div STYLE="background:lightcoral;" class="<?php echo $answerdClass ?>">
                                <?php
                                echo ttAnswer." ".$countingtore.": ";
                                echo strtolower(trim($answer['translation']));
                                ?>
                                <label class="score"><?php echo "0" ?></label>
                            </div>
                            <?php

                        }
                        $i++;
                        $inc++;
                        $countingtore++;
                        ?><br><?php
                    }
                ?>
                <label class="questionScore"><?php echo number_format($questionScore, 1); ?></label>
                <div class="clearer"></div>
            </div>
        </div>
        <?php
            }else{
                die(ttEAnswers);
            }
        }

    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion){
        $this->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);
    }

        public function getScoreFromGivenAnswer()
        {
            global $log;
            //$log->append("FB_getScoreFromGivenAnswer()");
            $score=0;
            $answerr = json_decode(stripslashes($this->get('answer')), true);
            //number of replies
            $countingrisposte=count($answerr);
            for ($m=0;$m<$countingrisposte;$m++){
                $rispostautente[$m]=strtolower(trim($answerr[$m]));
                //$log->append("FBRead:".$rispostautente[$m]);
            }
            //ID demand
            $idAns=$this->get('idQuestion');
            //$log->append("idAns:".$idAns);
            $db = new sqlDB();
            $db2 = new sqlDB();
            //check if exists
            if($pippo=$db->qSelect('Answers', 'fkQuestion', $idAns)) {
                //$log->append("FB inside first if");
                $rowGsfgA=0;
                //will scroll the answers
                while ($result = $db->nextRowAssoc()) {
                    //$log->append("Sto ciclando nel while, row:".$rowGsfgA);
                    $id = $result['idAnswer'];
                    //$log->append("id readed:".$id);
                    if ($db2->qSelect('TranslationAnswers', 'fkAnswer', $id)) {
                        //$log->append("Sono dentro il secondo if");
                        $find="false";
                        while (($traslation = $db2->nextRowAssoc()) && $find=="false") {
                            //$log->append("ciclo nel secondo while");
                            $trasl = strtolower(trim($traslation['translation']));
                            //$log->append("trasl:".$trasl);
                            if ($rispostautente[$rowGsfgA] == $trasl) {
                                $score += $result['score'];
                                //$log->append("score:".$score);
                                $find="true";
                            }
                            //$log->append("score:".$score);
                        }
                        $rowGsfgA++;
                    }else{
                        $error=$db->getError();
                        $log->append("Debug(crash):".$error);
                        die($error);
                    }
                }
            }else{
                $error=$db->getError();
                $log->append("Debug(crash):".$error);
                die($error);
            }
            $log->append("The score is:".$score);
            return $score;
        }
    }
