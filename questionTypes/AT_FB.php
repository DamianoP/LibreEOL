<?php
/**
 * File: AT_FB.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: Class for answer in Numeric questions
 */

/** Classe copiata NM */
class AT_FB extends Answer
{

    public function printAnswerEditForm($action)
    {
        global $config;

        $db = new sqlDB();
        if (!($db->qSelect('Languages')) || !($this->allLangs = $db->getResultAssoc('idLanguage'))) {
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

    public function printAnswerInfoEditForm($action)
    {
        global $log;

        ?>
        <dl class="dropdownInfo scoreFB tSpace right" id="answerScore">
            <dt class="tSpace writable">
                <span><?= $this->get('score') ?><span class="value"><?= $this->get('score') ?></span></span>
            </dt>
            <dd>
                <ol>
                    <li>0.1<span class="value">0.1</span></li>
                    <li>0.2<span class="value">0.2</span></li>
                    <li>0.3<span class="value">0.3</span></li>
                    <li>0.4<span class="value">0.4</span></li>
                    <li>0.5<span class="value">0.5</span></li>
                    <li>0.6<span class="value">0.6</span></li>
                    <li>0.7<span class="value">0.7</span></li>
                    <li>0.8<span class="value">0.8</span></li>
                    <li>0.9<span class="value">0.9</span></li>
                    <li>1.0<span class="value">1.0</span></li>
                </ol>
            </dd>
        </dl>

        <label class="right tSpace"><?= ttScore ?> :</label>
        <div class="clearer"></div>
        <div class="clearer bSpace"></div>

    <?php
    }

    public function getAnswerRowInTable()
    {
        return array($this->get('score'), $this->get('translation'), $this->get('idAnswer'));
    }

    public function getAnswerScore()
    {
        return $this->get('score');
    }

    public function getScoreFromGivenAnswer()
    {
        global $log;
        $log->append("FB_getScoreFromGivenAnswer()");

        $score=0;
        $answerr = json_decode(stripslashes($this->get('answer')), true);
            //numero di riposte
            $contarisposte=count($answerr);
            $log->append("Il numero di risposte contate viene ora riportato:".$contarisposte);
            for ($m=0;$m<$contarisposte;$m++){
                $rispostautente[$m]=strtolower(trim($answerr[$m]));
                $log->append("Risposta letta:".$rispostautente[$m]);
            }
            //ID della domanda
            $idAns=$this->get('idQuestion');

            $log->append("idAns:".$idAns);
            $db = new sqlDB();
            $db2 = new sqlDB();
            //controllo se esiste
            if($pippo=$db->qSelect('Answers', 'fkQuestion', $idAns)) {

                $log->append("Sono dentro il primo if");
                $giro=0;
                //scorro le risposte
                while ($result = $db->nextRowAssoc()) {

                    $log->append("Sto ciclando nel while, giro:".$giro);
                    //salvo dentro $id l'id della risposta corrente
                    $id = $result['idAnswer'];
                    $log->append("id letto:".$id);
                    if ($db2->qSelect('TranslationAnswers', 'fkAnswer', $id)) {
                        $log->append("Sono dentro il secondo if");
                        while ($traslation = $db2->nextRowAssoc()) {
                            $log->append("ciclo nel secondo while");
                            $trasl = strtolower(trim($traslation['translation']));
                            $log->append("trasl:".$trasl);
                            if ($rispostautente[$giro] == $trasl) {
                                $score += $result['score'];
                                $log->append("score:".$score);
                            }
                            $log->append("score:".$score);
                        }
                        $giro++;
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