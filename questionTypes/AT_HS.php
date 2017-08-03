<?php
/**
 * File: AT_HS.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: * Desc: Class for answer in Multiple Choice questions
 */

class AT_HS extends Answer {

    public function printAnswerEditForm($action){
        global $config;
        global $log;
        //$log->append(var_export($config, true));


        ?>

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
        $canvas = "";
        $idQuestion = $_POST['idQuestion'];
        //$log->append(var_export($idQuestion, true));
        $idAnswer = $_POST['idAnswer'];
        //$log->append(var_export("idAnswer", true));
        //$log->append(var_export($idAnswer, true));
        $db2 = new sqlDB();
        if (($_POST['action'] == 'show') ){
            if ($db2->qSelect('TranslationAnswers', 'fkAnswer', $idAnswer)) {
                $traslation = $db2->getResultAssoc();
                $log->append(var_export("traslation=", true));
                $log->append(var_export($traslation, true));
                $log->append(var_export($traslation[0]['translation'], true));
                $risultato = explode(',', $traslation[0]['translation'], 4);
                $log->append(var_export($risultato, true));
                $canvas = '<canvas id="myCanvas">
		        Your browser does not support the HTML5 canvas tag.
	            </canvas>';
            }
        }else{
            $risultato =  array("-", "-", "-", "-");
        }


        $db = new sqlDB();
        if ($translation=$db->qSelect('TranslationQuestions', 'fkQuestion', $idQuestion)) {
            //$log->append(var_export($translation, true));
            $question = $db->getResultAssoc();
            //$log->append(var_export($question, true));
            $a = $question[0]['translation'];

            //$log->append(var_export($a, true));
            $risp = $question[0]['translation'];
            //$log->append(var_export($risp, true));
            $pos = strpos($risp, 'img');
            $domanda = substr($risp, 0 , ($pos - 1) );      //STAMPA LA DOMANDA
            //$log->append(var_export($domanda, true));

            $link = strstr($risp, 'img');                   //STAMPA L'IMMAGINE NELLA DOMANDA
            //$log->append(var_export($pos, true));
            $immagine = '<'.$link ;

        }

        ?>

        <div class="clearer bSpace">
            <div width="500" height="500">
                <div>
                    <?php echo $domanda; ?> </div>
                <br>
                <div id="contentContainerCrop" onclick="jcrop()">
                    <?php echo $canvas; ?>
                    <?php echo $immagine; ?>
                </div>
                <div id="coords" class="coords">
                <table style="margin-top: 1em;">
                    <thead>
                    <tr>
                        <th style="font-size: 110%; text-align: left; padding-left: 0.1em;" colspan="2">
                            <?= ttCoord ?>
                        </th>
                        <th style="font-size: 110%;text-align: left; padding-left: 0.1em;" colspan="2">
                            <?= ttSizeWin ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="width: 10%;"><b>X<sub>1</sub>:</b></td>
                        <td style="width: 30%;"><input type="text" value= <?php echo $risultato[0] ?> id="x1"></td>
                        <td style="width: 20%;"><b><?= ttWidth ?></b></td>
                        <td><input type="text" id="w" value=<?php echo ($risultato[2]-$risultato[0]) ?>></td>
                    </tr>
                    <tr>
                        <td><b>Y<sub>1</sub>:</b></td>
                        <td><input type="text" value=<?php echo $risultato[1] ?> id="y1"></td>
                        <td><b><?= ttHeight?></b></td>
                        <td><input type="text" value=<?php echo ($risultato[3]-$risultato[1]) ?> id="h"></td>
                    </tr>
                    <tr>
                        <td><b>X<sub>2</sub>:</b></td>
                        <td><input type="text" value=<?php echo $risultato[2] ?> id="x2"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><b>Y<sub>2</sub>:</b></td>
                        <td><input type="text" value=<?php echo $risultato[3] ?> id="y2"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    <?php
    }

    public function getAnswerRowInTable(){
        $score = array(0 => ttWrongArea, 1 => ttRightArea);

        return array($score[$this->get('score')], $this->get('translation'), $this->get('idAnswer'));
    }

    public function getAnswerScore(){
        return $this->get('score');
    }
    public function getScoreFromGivenAnswer() {}
}