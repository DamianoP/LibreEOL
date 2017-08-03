<?php
/**
 * File: QT_PL.php
 * User: Gmarsi
 * Date: 15/03/2015
 * Time: 12:41
 * Desc:
 */

class QT_PL extends Question
{


    public function createNewQuestion()
    {
        $idQuestion = parent::createNewQuestion();
        return $idQuestion;
    }

    public function printQuestionEditForm($action, $readonly)
    {
        global $config, $user;

        $db = new sqlDB();
        if (!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))) {
            die($db->getError());
        }
        ?>
        <ul xmlns="http://www.w3.org/1999/html">
            <li><a id="questionTab" href="#question-tab"><?= ttQuestion ?></a></li>
            <?php if ($action == 'show') echo '<li><a id="subTab" href="#subquestions">' . ttQuestionsubPL . '</a></li>'; ?>


        </ul>

        <div id="question-tab">

            <!-- Print question language tabs and textareas user by ckeditor -->
            <?php $this->printQuestionTabsAndTextareas($action); ?>

            <!-- Print question's extras list -->
            <?php $this->printQuestionExtraForm($action); ?>

            <div class="clearer bSpace"></div>

            <!-- Print hidden field for question's type (PL) -->
            <input type="hidden" id="questionType" value="PL">

            <!-- Print all other question's info -->
            <?php $this->printQuestionInfoEditForm($action, $readonly) ?>

            <div class="clearer bSpace"></div>

            <!-- Print buttons for question panel -->
            <?php $this->printQuestionEditButtons($action, $readonly);?>


            <div class="clearer"></div>

        </div>


        <?php if ($action == 'show') { ?>

        <div id="subquestions">
            <div class="bSpace" id="subquestionsTableContainer">
                <div class="smallButtons">
                    <div id="newSubquestion_PL">
                        <img class="icon" src="<?= $config['themeImagesDir'] . 'new.png' ?>"/><br/>
                        <?= ttNew ?>
                    </div>
                    <div id="deleteSubQuestion">
                        <img class="icon" id="btnNewAnswer" src="<?= $config['themeImagesDir'] . 'delete.png' ?>"/><br/>
                        <?= ttDelete ?>
                    </div>
                </div>

                <?php $this->printSubquestionsTable($this->get('idQuestion'), $_SESSION['idSubject']) ?>
            </div>
            <div class="clearer"></div>
            <div id="answers">
                <div class="bSpace" id="answersTableContainer">
                    <div class="smallButtons">
                        <div id="newAnswer_PL">
                            <img class="icon" id="btnNewAnswer" src="<?= $config['themeImagesDir'] . 'new.png' ?>"/><br/>
                            <?= ttNew ?>
                        </div>

                    </div>
                    <table id="answersTable" class="stripe hover">
                        <thead>
                        <tr>
                            <td><?echo ttScore?></td>
                            <td><?echo ttText?></td>
                            <td>ID</td>
                        </tr>
                        </thead>
                        <tbody id="bodyAnswerTable">
                        </tbody>
                    </table>
                </div>
            </div>
            <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(true);"><?= ttExit ?></a>
        </div>






    <?php }

        $this->printQuestionTypeLibrary();
        echo '<script> initialize_PL(); </script>';

    }
    public function printSubquestionsTable($idQuestion)
    { ?>

        <table id="subquestionsTable" class="stripe hover">
            <thead>
            <tr>
            <td><?echo ttText ?></td>
            <td><?echo ttText ?></td>
            <td>ID</td>

            </tr>
            </thead>
            <tbody>
            <?php
            global $log;
            $db = new sqlDB();
            //if (($db->qsubquestionsettestPL($this->get('idQuestion'))) && ($subSet = $db->getResultAssoc())) {

           // }
            $db->qsubquestionsettestPL($this->get('idQuestion'),$this->get('fkLanguage'),$_SESSION['idSubject']);
            $subSet = $db->getResultAssoc();

            for ($a = 0; $a < count($subSet); $a++) {
                echo '<tr>
                    <td>' . ($subSet[$a]['translation']) . '</td>
                            <td>' . ($subSet[$a]['translation']) . '</td>
                            <td>' . ($subSet[$a]['sub_questions']) . '</td>

                      </tr>';


            }?>
            </tbody>
        </table>

    <?php
    }

    public function printAnswersTable($idQuestion, $idSubject)
    {
        ?>

        <table id="answersTable" class="stripe hover">
            <thead>
            </thead>
            <tbody>
            <?php
           $a=0;
            $db = new sqlDB();
            if ($db->qAnswerSet($idQuestion, null, $idSubject)) {

                while ($answer = $db->nextRowAssoc()) {
                 
                    echo '<tr >

                              <td>' . strip_tags($answer['translation']) . '</td>

                              <td>' . $answer['score'] . '</td>


                          </tr>'; }




            }
            ?>
            </tbody>
        </table>

    <?php
    }


    /**
     *
     */
    public function printQuestionPreview()
    {

        global $config;
        global $log;

        $db = new sqlDB();
        if (($db->qsubquestionsettestPL($this->get('idQuestion'),$this->get('fkLanguage'), $_SESSION['idSubject'])) && ($subSet = $db->getResultAssoc())) {
             //var_dump($subSet);
        }


        ?>

        <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="PL">
            <div class="questionText"><?= $this->get('translation') ?></div>


            <div class="questionAnswers">
                <?php
                for ($a = 0; $a < count($subSet); $a++) {


                if (($db->qAnswerSetPL($subSet[$a]['sub_questions'], $this->get('fkLanguage'), $_SESSION['idSubject'])) && ($answerSet = $db->getResultAssoc())) {

                $questionAnswers = '';




                // -------  Add extra buttons  ------- //
                $extra = '';
                if (strpos($this->get('extra'), 'c') !== false)
                    $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
                if (strpos($this->get('extra'), 'p') !== false)
                    $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';
                ?>






                <?= $subSet[$a]['translation']?>


                <select id="prova">
                    <?php
                    foreach ($answerSet as $answer) {



                        if ($answer['fkLanguage'] != $this->getRAsspc($a, 'fkLanguage'))
                            $class = 'mainLang';

                        $questionAnswers .= '<div>


                                        <option id="prova" value="' . $answer['idAnswer'] . '">' . $answer['translation'] . '</option>

                                     </div>';

                    }

                    echo $questionAnswers;

                    }

                    ?>


            </div>

            </select>
            <br/>
            <?php
            } ?>

        </div>


        <?php
    }







    public function printQuestionInTest($idSubject, $answered, $extras)
    {

        global $config;
        global $log;

        $db = new sqlDB();
        if (($db->qsubquestionsettestPL($this->get('idQuestion'),$this->get('fkLanguage'), $idSubject)) && ($subSet = $db->getResultAssoc())) {
             //var_dump($subSet);
        }


        ?>

        <div class="questionTest" value="<?= $this->get('idQuestion') ?>" type="PL">
        <div class="questionText"><?= $this->get('translation') ?></div>
            <div class="questionAnswers">
                <?php
                $i=0;
                for ($a = 0; $a < count($subSet); $a++) {
                    if (($db->qAnswerSetPL($subSet[$a]['sub_questions'], $this->get('fkLanguage'),$idSubject)) && ($answerSet = $db->getResultAssoc())) {
                    $questionAnswers = '';
                // -------  Add extra buttons  ------- //
                    $extra = '';
                    if (strpos($this->get('extra'), 'c') !== false)
                        $extra .= '<img class="extraIcon calculator" src="' . $config['themeImagesDir'] . 'QEc.png' . '">';
                    if (strpos($this->get('extra'), 'p') !== false)
                        $extra .= '<img class="extraIcon periodicTable" src="' . $config['themeImagesDir'] . 'QEp.png' . '">';
                    ?>
                    <?= $subSet[$a]['translation']?>
                    <select id="prova">
                        <?php
                        foreach ($answerSet as $answer) {
                            if ($answer['fkLanguage'] != $this->getRAsspc($a, 'fkLanguage'))
                                $class = 'mainLang';
                            if(isset($answered[$i]) && $answered[$i]==$answer['idAnswer']){
                                $questionAnswers .= '<div>
                                        <option selected="selected" id="prova" value="' . $answer['idAnswer'] . '">' . $answer['translation'] . '</option>
                                     </div>';
                                $i++;
                            }
                            else{
                                $questionAnswers .= '<div>
                                        <option id="prova" value="' . $answer['idAnswer'] . '">' . $answer['translation'] . '</option>
                                     </div>';
                            } 

                        }                          
                        echo $questionAnswers;

                        }
                    ?>
                    </div>
                    </select>
                    <br/>
                    <?php
                } ?> </div>
        </div>
        <?php
    }




    public function printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion)
    {
        global $config;
        global $log;

        $db = new sqlDB();
        if (($db->qsubquestionsettestPL($this->get('idQuestion'),$this->get('fkLanguage'),$idSubject)) && ($subSet = $db->getResultAssoc())) {
            // var_dump($subSet);
        }
        for ($a = 0; $a < count($subSet); $a++) {
            $questionAnswers = "";
            $questionClass = 'emptyQuestion';
            $questionScore = 0;
            if (($db->qAnswerSetPL($subSet[$a]['sub_questions'], $this->getRA('fkLanguage'),$idSubject)) && ($answerSet = $db->getResultAssoc('idAnswer'))) {
                //var_dump($answerSet);
                foreach ($answerSet as $idAnswer => $answer) {
                    //var_dump($idAnswer);
                    //var_dump($answer['score']." ----- ");
                    $answer['score'] = str_replace(',', '.', $answer['score']);
                    $answerdClass = "";
                    $right_wrongClass = ($answer['score'] > 0) ? 'rightAnswer' : 'wrongAnswer';

                    if (in_array($idAnswer, $answered)) {
                        $questionScore += round(($answer['score'] * $scale), 1);
                        $answerdClass = 'answered';
                    }

                    $questionAnswers .= '<div class="' . $answerdClass . '">
                                         <span value="' . $idAnswer . '" class="responsePL ' . $right_wrongClass . '"></span>
                                         <label>' . $answer['translation'] . '</label>
                                         <label class="score">' . round($answer['score'] * $scale, 1) . '</label>
                                     </div>';
                }

                $questionAnswers .= '<label class="questionScore">' . $questionScore . '</label>
                                 <div class="clearer"></div>';

                if (count($answered) != 0)
                    $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';
                ?>

                <div class="questionTest <?= $questionClass . ' ' . $lastQuestion ?>"
                     value="<?= $this->get('idQuestion') ?>" type="PL">
                    <div class="questionText" onclick="showHide(this);">
                        <span class="responseQuestion"></span>
                        <?= $this->get('translation') ?>
                        <span class="responseScore"><?= number_format($questionScore, 1); ?></span>
                        <br/>
                        <?php $b = $a + 1;
                        print(ttQuestionsubPL." n°" . $b); ?>    <br/> <?= $subSet[$a]['translation'] ?>
                        <br/>
                        <br/>
                    </div>
                    <div class="questionAnswers hidden">
                        <?php
                        print("Risposte"); ?>
                        <br/>
                        <br/>
                        <?= $questionAnswers ?></div>
                </div>

            <?php

            }
        }
    }


    public function printQuestionInView($idSubject, $answered, $scale, $lastQuestion)
    {
        $this->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);
    }
    public  function getScoreFromGivenAnswer(){
        $score = 0;
        global $log;
        $answer = json_decode(stripslashes($this->get('answer')), true);

        if(count($answer) > 0){
            $db = new sqlDB();
            if($db->qSelect('Answers', 'idAnswer', $answer)){
                $tempScore = 0;
                while($result = $db->nextRowAssoc()){
                    $result['score'] = str_replace(',', '.', $result['score']);
                    $tempScore += $result['score'];
                }
                $score = $tempScore;
            }else die($db->getError());
        }

        return $score;
    }
}