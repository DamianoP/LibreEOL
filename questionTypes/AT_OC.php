<?php
/**
 * File: AT_OC.php
 * User: Anis Ben Hamida
 * Date: 02/03/2021
 * Desc: Class for answer in On Click questions
 */

class AT_OC extends Answer {

    public function printAnswerEditForm($action){
        global $config;

        $db = new sqlDB();
        if(!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))){
            die($db->getError());
        }

        $this->printAnswerTabsAndTextareas();
        ?>

        <div class="clearer bSpace"></div>

        <!-- Print all other answer's info -->
        <?php $this->printAnswerInfoEditForm($action) ?>

        <!-- Print buttons for answer panel -->
        <?php $this->printAnswerEditButtons($action); ?>

        <div class="clearer"></div>

        <?php $this->printAnswerTypeLibrary(); ?>

        <?php
    }

    public function printAnswerInfoEditForm($action){
        global $log;
        ?>

        <dl class="dropdownInfo scoreOC tSpace right" id="answerScore">
            <dt class="tSpace writable">
                <?php
                $score = $this->get('score');
                if($score == 0 || $score == null || !isset($score)){
                    echo "<span>Nessuna Posizione<span class='value'>0</span></span>";
                }
                else {
                    $position = explode("*",$score)[0];
                    echo"<span>".$position."<span class='value'>".$score."</span></span>";
                }
                ?>
            </dt>
            <dd>
                <ol>
                    <?php
                    session_start();
                    if(isset($_SESSION['counter'])){
                        $numberItemOC = $_SESSION['counter'];
                        $scoreSingle = round(1/($numberItemOC-1),2);
                        echo "<li>".ttNoPosOC."<span class=\"value\">0</span></li>";
                        if($numberItemOC != 0) {
                            for ($i = 1; $i < $numberItemOC; $i++) {
                                echo "<li>" . $i . "<span class=\"value\">" . $i . "*" . $scoreSingle . "</span></li>";
                            }
                        }
                    }
                    unset($element);
                ?>
                </ol>
            </dd>
        </dl>


        <label class="right tSpace"><?= ttPosition ?> :</label><div class="clearer"></div>
        <div class="clearer bSpace"></div>

        <?php
    }

    public function getAnswerRowInTable(){
        $position = explode("*", $this->get('score'))[0];
        return array($position, $this->get('translation'), $this->get('idAnswer'));
    }

    public function getAnswerScore(){
        return $this->get('score');
    }
    public function getScoreFromGivenAnswer(){

    }
}
