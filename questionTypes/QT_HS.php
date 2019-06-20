<?php
/**
 * File: QT_HS.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: Class for Multiple Choice questions
 */

class QT_HS extends Question {

    public function createNewQuestion(){
        $idQuestion = parent::createNewQuestion();
        return $idQuestion;
    }

    public function printQuestionEditForm($action, $readonly){
        global $config, $user, $URL;
        global $log;

        $db = new sqlDB();
        if(!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))){
            die($db->getError());
        }

        //$URL = $this->get('translation');

       // $log->append(var_export($URL, true));
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

            <br>
            <div align="center">
                <button onclick="CKEDITOR.tools.callFunction(126,this);return false;"> <?php echo ttInsertImage ?></button>
            </div>



            <div class="clearer bSpace"></div>

            <!-- Print hidden field for question's type (HS) -->
            <input type="hidden" id="questionType" value="HS">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly); ?>

            <div class="clearer"></div>

        </div>



        <?php if($action == 'show'){ ?>
        <div id="answers">

            <div class="bSpace" id="answersTableContainer">
                <div class="smallButtons">
                    <div id="newAnswer_HS">
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
        echo '<script> initialize_HS(); </script>';
    }

    public function printAnswersTable($idQuestion, $idSubject){ ?>

        <table id="answersTable" class="stripe hover">
            <thead>
                <tr>
                    <th class="aScore"><?= ttScore ?></th>
                    <th class="aText"><?= ttCoord ?></th>
                    <th class="aAnswerID">answerID</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $score = array(0 => 0 , 1 => 1);

            $db = new sqlDB();
            if($db->qAnswerSet($idQuestion, null, $idSubject)){
                while($answer = $db->nextRowAssoc()){
                    echo '<tr>
                              <td>'.$score[$answer['score']].'</td>
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

        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $_SESSION['idSubject'])) && ($answerSet = $db->getResultAssoc())){

            $risp = $this->get('translation');
            //$log->append(var_export($risp, true));
            $pos = strpos($risp, 'img');
            $domanda = substr($risp, 0 , ($pos - 1) );      //STAMPA LA DOMANDA
            //$log->append(var_export($domanda, true));

            $link = strstr($risp, 'img');                   //STAMPA L'IMMAGINE NELLA RISPOSTA
            //$log->append(var_export($pos, true));
            $questionAnswers = '<'.$link ;

            // -------  Add extra buttons  ------- //
            $extra = '';
            if(strpos($this->get('extra'), 'c') !== false)
                $extra .= '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
            if(strpos($this->get('extra'), 'p') !== false)
                $extra .= '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';
            ?>

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="HS">
                <div class="questionText"><?= $domanda.$extra ?></div>
                <div class="questionAnswers">
                    <div id="contentContainer" class="contentContainer" onclick="getClickPosition(event)">
                        <img id="thing" src="themes/default/images/smiley_red.png" />
                        <?= $questionAnswers ?>
                    </div>
                </div>
            </div>

        <?php
        }
    }

    public function printQuestionInTest($idSubject, $answered, $extras){
        global $config;
        global $log;

        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), $this->get('fkLanguage'), $idSubject)) && ($answerSet = $db->getResultAssoc())){

            $risp = $this->get('translation');
            //$log->append(var_export($risp, true));
            $pos = strpos($risp, 'img');
            $domanda = substr($risp, 0 , ($pos - 1) );      //STAMPA LA DOMANDA
            //$log->append(var_export($domanda, true));

            $link = strstr($risp, 'img');                   //STAMPA L'IMMAGINE NELLA RISPOSTA
            //$log->append(var_export($pos, true));
            $questionAnswers = '<'.$link ;

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

            <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="HS">
                <div class="questionText"><?= $domanda.$extra ?></div>
                <div class="questionAnswers">
                    <div id="contentContainer" style="position:relative;" class="contentContainer" onclick="getClickPosition(this,event)" >
                        <?= $questionAnswers ?>                        
                    <?php 
                    //style="position: relative; left:'.$answered[0].'px; top:'.$answered[1].'px;"
                        if(isset($answered[0]) && isset($answered[1])){
                            echo '<img class="hscursor" id="thing" style="position: absolute;" src="themes/default/images/smiley_red.png" onload="riposiziona(this,'.$answered[0].','.$answered[1].');"/>';
                        }else{                                
                            echo '<img class="hscursor" id="thing" src="themes/default/images/smiley_red.png" />';
                        }
                    ?>
                    </div>
                </div>
            </div>

            <?php
            return $extras;
        }else{
            die($db->getError());
        }
    }

    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion){
        global $config;
        global $log;

        $questionAnswers = '';
        $questionScore = 0;
        $questionClass = 'emptyQuestion';
//        $log->append(var_export($answered, true));

        $db = new sqlDB();
        if(($db->qAnswerSet($this->get('idQuestion'), null, $idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))){
            //$log->append(var_export($answerSet, true));
            foreach($answerSet as $idAnswer => $answer){
                $answerdClass = "";
                $right_wrongClass = ($answer['score'] > 0) ? 'rightAnswer' : 'wrongAnswer';

                $risultato = explode(',', $answer['translation'], 4 );

                $answered[0]=$answered[0]+13;
                $answered[1]=$answered[1]+13;  
                if(($answered[0] >= intval($risultato[0])) && ($answered[0] <= intval($risultato[2])) ) {
                    if(($answered[1] >= intval($risultato[1])) && ($answered[1] <= intval($risultato[3])) ) {
                        $questionScore += round(($answer['score']* $scale ), 1);
                        $answerdClass = 'answered';
                    }
                }
                $answered[0]=$answered[0]-13;
                $answered[1]=$answered[1]-10;  
                $questionAnswers .= '<div class="'.$answerdClass.'">
                                         <span value="'.$idAnswer.'" class="responseHS '.$right_wrongClass.'"></span>
                                         <label>'.$answer['translation'].'</label>
                                         <label class="score">'.round($answer['score'] * $scale, 1).'</label>
                                     </div>';
            }
            $questionAnswers .= '<label class="questionScore">'.$questionScore.'</label>
                                 <div class="clearer"></div>';

            if(count($answered) != 0)
                $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';

            $risp = $this->get('translation');
            $pos = strpos($risp, 'img');
            $domanda = substr($risp, 0 , ($pos - 1) );      //STAMPA LA DOMANDA
            $link = strstr($risp, 'img');                   //STAMPA L'IMMAGINE NELLA RISPOSTA
            $immagine = '<'.$link ;
            ?>

            <div class="questionTest <?= $questionClass.' '.$lastQuestion ?>" value="<?= $this->get('idQuestion') ?>" type="HS">
                <div class="questionText" onclick="showHide(this);">
                    <span class="responseQuestion"></span>

                    <?= $domanda ?>

                    <span class="responseScore"><?= number_format($questionScore, 1); ?></span>

                </div>

                <div class="questionAnswers hidden">
                    <div id="contentContainer" style="position:relative">
                        <img id="thing" style="position:absolute;" src="themes/default/images/smiley_red.png" 
                        onload="riposizionaInCorrezione(this,<?php echo $answered[0].','.$answered[1]?>)" />
                        <?= $immagine ?>
                        <p class="rettangoloRispostaStudente" 
                            style="position:absolute;left:<?= $risultato[0]?>px;top:<?= $risultato[1]-15 ?>px;height:<?php $hpx=$risultato[3]-$risultato[1]; echo $hpx."px"; ?>;width:<?php $wpx=$risultato[2]-$risultato[0]; echo $wpx."px"; ?>; border:3px solid green;"
                        </p>
                    </div>
                    <?= $questionAnswers ?>
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

    public function getScoreFromGivenAnswer(){
        $score = 0;
        global $log;
        //$answerr = $this->get('answer');                                      //risposta studente
        $answerrdecode = json_decode(($this->get('answer')), true);             //0 -> x  1-> y
        $idQuest=$this->get('idQuestion');
        //$log->append(var_export($answerrdecode, true));
        //$log->append(var_export($answerrdecode[0], true));

    //if(count($answerr) > 0){
            $db = new sqlDB();
            $db2 = new sqlDB();
            if ($pippo=$db->qSelect('Answers', 'fkQuestion', $idQuest)) {
                while ($result = $db->nextRowAssoc() ) {
                    $id=$result['idAnswer'];
                    if ($db2->qSelect('TranslationAnswers', 'fkAnswer', $id)) {
                        while ($traslation = $db2->nextRowAssoc()) {
                            //$log->append(var_export($traslation['translation'], true));
                            $risultato = explode(',', $traslation['translation'], 4 );
                            //$log->append(var_export($risultato, true));
                            //$log->append(var_export($answerrdecode, true));
                            $answerrdecode[0]=$answerrdecode[0]+13;
                            $answerrdecode[1]=$answerrdecode[1]+13;
                            if(($answerrdecode[0] >= intval($risultato[0])) && ($answerrdecode[0] <= intval($risultato[2])) ) {
                                if(($answerrdecode[1] >= intval($risultato[1])) && ($answerrdecode[1] <= intval($risultato[3])) ) {
                                    //$log->append(var_export("RISPOSTA GIUSTA", true));
                                    $score += $result['score'];
                                }
                            }
                        }
                    }else die($db->getError());
                }
            }else die($db->getError());
            //}


        return $score;
    }

}
