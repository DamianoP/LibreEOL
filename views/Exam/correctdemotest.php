<?php
/**
 * File: correct.php
 * User: Masterplan
 * Date: 5/5/13
 * Time: 3:51 PM
 * Desc: Shows test correction page
 */
global $config;

$questions = $_POST['question'];

function correctAnswer($ident, $answers) {
  // Parse the XML
  $filename = $_SESSION['file'];
  if (file_exists($filename)) {
    $xml = simplexml_load_file($filename);
  } else {
    die("Error");
  }
  // Find the ident
  foreach($xml->xpath("/questestinterop/item") as $item) {
    // If the question is found...
    if($ident == $item['ident']) {
      // If the question type is Multiple Choice...
      $type = $item->itemmetadata->qmd_itemtype;
      switch ($type) {
        case "Multiple Choice":
          $type = "MC";
          break;
        case "Multiple Response":
          $type = "MR";
          break;
        case "Numeric":
          $type = "NM";
          break;
        case "Hot Spot":
          $type = "HS";
          break;
        case "Fill in the blanks":
          $type = "FB";
          break;
        case "Pull-down list":
          $type = "PL";
          break;
        case "Text Match":
          $type = "TM";
          break;
      }
      // If question type is Multiple Choice
      if ($type == 'MC') {
        // Check answer
        foreach ($item->resprocessing->respcondition as $respcondition) {
          if (strcmp($respcondition['title'], 'default') != 0 ) {
            // Check if answer is empty
            if (empty($answers)) {
              return array(NULL, $type);
            // Check if answer is correct
            } elseif ($answers[0] == $respcondition->conditionvar->varequal) {
              return array(true, $type);
            } else {
              return array(false, $type);
            }
          }
        }
      } elseif ($type == 'MR') {
        // Check if answer array is empty
        if (empty($answers)) {
          return array(NULL, $type);
        } else {
          foreach ($item->resprocessing->respcondition as $respcondition) {
            if ((strcmp($respcondition['title'], 'header') != 0) && (strcmp($respcondition['continue'], 'Yes')) == 0) {
              if (in_array($respcondition->conditionvar->varequal, $answers) == false) {
                return array(false, $type);
              }            
            }
          }
          return array(true, $type);
        }
      // If question type is Pull-down list
      } elseif ($type == 'PL') {
        // Instantiate counter
        $j = 0;
        // Get array length
        $length = count($answers)-1;
        $pulldown_answers = $item->resprocessing->respcondition;
        // Check if answer is correct
        for($i=0; $i<$length; $i++) {
          if($answers[$i] == $pulldown_answers[$i]->conditionvar->varequal) {
            $j++;
          }
        }
        if($j == $length) {
          return array(true, $type);
        } else {
          return array(false, $type);
        }
      // If question type is Numeric
      } elseif($type == 'NM') {
        foreach($item->resprocessing->respcondition as $respcondition) {
          if(strcmp($respcondition['title'], 'default') != 0) {
            // Check answer
            foreach($respcondition->conditionvar->or->vargte as $answer) {
              // Check if answer is empty
              if ($answers[0] == NULL) {
                return array(NULL, $type.'_1');
              // Check if answer is correct
              } elseif((float) $answers[0] == (float) $answer) {
                return array(true, $type.'_1');
              } else {
                return array(false, $type.'_1');
              }
            }
            // Check if answer is empty
            if ($answers[0] == NULL) {
              return array (NULL, $type.'_0');
            // Check if answer is correct
            } elseif ((float) $answers[0] == (float) $respcondition->conditionvar->varequal) {
              return array(true, $type.'_0');
            } else {
              return array(false, $type.'_0');
            }
          }
        }
      } elseif($type == 'TM') {
        $temp = array();
        foreach($item->resprocessing->respcondition as $respcondition) {
          // Check answer
          $count = $count + count($respcondition->conditionvar->or);
          if ($count > 0) {
            foreach($respcondition->conditionvar->or->varequal as $answer) {
              array_push($temp, $answer);
            }
          } else {
            foreach($respcondition->conditionvar->varequal as $answer) {
              array_push($temp, $answer);
            }
          }
        }

        // Check if answer is empty
        if ($answers[0] === NULL) {
          return array(NULL, $type);
        // Check if answer is correct
        }
        foreach($temp as $element) {
          if(($answers[0] == $element)) {
            return array(true, $type);
          }
        }

        return array(false, $type);
      } elseif($type == 'HS') {
        $tmp_coords = str_replace(' ', ',', $item->resprocessing->respcondition->conditionvar->varinside);
        $correct_coords = explode(',', $tmp_coords);
        $answer_coords = explode(' ', $answers[0]);
        //print_r($correct_coords);
        //print_r($answer_coords);
        if(($answer_coords[0] >= $correct_coords[0]) && ($answer_coords[0] <= $correct_coords[2]) && ($answer_coords[1] >= $correct_coords[1]) && ($answer_coords[1] <= $correct_coords[3])) {
          return array(true, $type);
        }
        return array(false, $type);
      }
    }
  }
}

function printQuestion($ident) {
  $filename = $_SESSION['file'];
  if (file_exists($filename)) {
    $xml = simplexml_load_file($filename);
  } else {
    die("Error");
  }
  foreach($xml->xpath("/questestinterop/item") as $item) {
    // If the question is found...
    if($ident == $item['ident']) {
      foreach($item->presentation->material as $material) {
        $question = str_replace("<BR>", "", $material->mattext);
        $question = str_replace('<img class="extraIcon calculator" src="themes/default/images/QEc.png">', "", $question);
        echo $question;
      }
    }
  }
}

function getScore($question_ident, $answer_ident, $type) {
  // Parse the XML
  $filename = $_SESSION['file'];
  if (file_exists($filename)) {
    $xml = simplexml_load_file($filename);
  } else {
    die("Error");
  }
  if($type == 'MC') {
    // Find the ident
    foreach($xml->xpath("/questestinterop/item") as $item) {
      if($question_ident == $item['ident']) {
        $correct_answer = $item->resprocessing->respcondition->conditionvar->varequal;
        if (strcmp($correct_answer, $answer_ident)==0) {
          $points = floatval($item->resprocessing->respcondition->setvar);
        } else {
          $points = 0.0;
        }
      }
    }
    return $points;
  } elseif($type == 'PL') {
    foreach($xml->xpath("/questestinterop/item") as $item) {
      if($question_ident == $item['ident']) {
        $correct_answer = array();
        foreach($item->resprocessing->respcondition as $respcondition) {
          array_push($correct_answer, $respcondition->conditionvar->varequal);
          //print_r($correct_answer);
        }
        if (strcmp($correct_answer, $answer_ident)==0) {
          $points = floatval($item->resprocessing->respcondition->setvar);
        } else {
          $points = 0.0;
        }
      }
    }
  }
}

?>

<script type="text/javascript" src="libs/Exam/CorrectDemoTest.js"></script>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<br><br><br>
<div>
<div id="main">
<?php
$db = new sqlDB();
if(($db->qTestDetails(null, $_POST['idTest'])) && ($testInfo = $db->nextRowAssoc())){
    global $log;
    $studentName = $testInfo['name'].' '.$testInfo['surname'];
    echo $studentName;
    openBox(ttTest.': '.$studentName.' ('.$scoreTest.')', 'normal', 'correct', array('showHide'));
}
$finalScore = 0;
// For each question, check if it is correct or not
$i=-1;
foreach($questions as $question) {
  $i++;
  // Grab the question ident
  $ident = end($question);
  // Grab the answers array
  $answers = array_slice($question, 0, count($question)-1);
  $response = correctAnswer($ident, $answers);
  $correct = $response[0];
  $type = $response[1];

  // Print green or red box
  if($correct) {
  ?>
    <div class="questionTest rightQuestion" type="<?php echo $type; ?>">
  <?php
  } elseif ($correct === false) {
  ?>
    <div class="questionTest wrongQuestion" type="<?php echo $type; ?>">
  <?php
  } elseif ($correct === NULL) {
  ?>
    <div class="questionTest" type="<?php echo $type; ?>">
  <?php
  }
  ?> 
      <div class="questionText" onclick="showHide(this);">
        <span class="responseQuestion"></span>
        <?php printQuestion($ident); ?>
        <br><br><span class="responseScore"></span>
        <br>
      </div>
      <div class="questionAnswers hidden">
        <?php
        
          // Se la domanda è a scelta multipla...
          if($type == 'MC') {
            // Parse the XML
            $filename = $_SESSION['file'];
            if (file_exists($filename)) {
              $xml = simplexml_load_file($filename);
            } else {
              die("Error");
            }
            foreach($xml->xpath("/questestinterop/item") as $item) {
              // Se l'ID della domanda corrisponde...
              if($ident == $item['ident']) {
                // Salvo la risposta corretta
                foreach($item->resprocessing->respcondition as $respcondition) {
                  if(strcmp($respcondition['title'], "default") != 0 ) {
                    $correct_answer = (string) $respcondition->conditionvar->varequal;
                  }
                }
                $total_score = 0;
                // Per ogni possibile risposta...
                foreach($item->presentation->response_lid->render_choice->response_label as $answer) {
                  $answer_ident = (string) $answer['ident'];
                  // Se la risposta è anche corretta, allora...
                  if(strcmp($answer_ident, $correct_answer) == 0) {
                    // Se la risposta corretta è anche quella che ho dato, allora...
                    if(strcmp($answer_ident, $question[0]) == 0) {
                      $total_score = 1;
                      $finalScore = $finalScore + $total_score;
                    ?>
                      <div class="answered">
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                        <label class="score">1</label>
                      </div>
                    <?php
                    // Se la risposta corretta non è quella data, allora...
                    } else {
                    ?> 
                      <div>
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                        <label class="score">1</label>
                      </div>
                    <?php
                    }
                  // Se la risposta non è corretta, allora...
                  } else {
                    // Se la risposta è quella che ho dato, allora...
                    if(strcmp($answer_ident, $question[0]) == 0) {
                    ?>
                      <div class="answered">
                        <span class="responseMC wrongAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                        <label class="score">0</label>
                      </div>
                    <?php
                    } else {
                    ?>
                      <div>
                        <span class="responseMC wrongAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                        <label class="score">0</label>
                      </div>
                    <?php
                    }
                  }               
                }
                echo '<br><label class="questionScore">'.$total_score.'</label>';
                echo '<div class="clearer"></div>';
              }
            }
          } elseif ($type == 'MR') {
            // Parse the XML
            $filename = $_SESSION['file'];
            if (file_exists($filename)) {
              $xml = simplexml_load_file($filename);
            } else {
              die("Error");
            }

            // Cerco la domanda
            foreach($xml->xpath("/questestinterop/item") as $item) {
              // Se l'ID della domanda corrisponde...
              if($ident == $item['ident']) {
                // Salvo le risposte corrette
                $correct_answer = array();
                foreach($item->resprocessing->respcondition as $respcondition) {
                  if(strcmp($respcondition['title'], 'header') != 0 && (strcmp($respcondition['continue'], 'Yes')) == 0) {
                    $answer = (string) $respcondition->conditionvar->varequal;
                    array_push($correct_answer, $answer);
                  }
                }   

                // Per ogni risposta...
                $count = count($correct_answer);
                $total_score = 0;
                foreach($item->presentation->response_lid->render_choice->response_label as $answer) {
                  $answer_ident = (string) $answer['ident'];
                  foreach($answer->material->mattext as $answer_text) {
                    // Se la risposta è quella che ho dato...
                    if (in_array($answer_ident, $answers)) {
                      if (in_array($answer_ident, $correct_answer)) {
                        ?>
                        <div class="answered">
                          <span class="responseMC rightAnswer"></span>
                          <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                          <label class="score"><?php $total_score = $total_score + (1/$count); echo round((1/$count), 1); ?></label>
                        </div>
                        <?php
                      } else {
                        ?>
                        <div class="answered">
                          <span class="responseMC wrongAnswer"></span>
                          <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                          <label class="score">0</label>
                        </div>
                        <?php
                      }
                    } else {
                      if (in_array($answer_ident, $correct_answer)) {
                        ?>
                        <div>
                          <span class="responseMC rightAnswer"></span>
                          <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                          <label class="score"><?php echo round((1/$count), 1); ?></label>
                        </div>
                        <?php
                      } else {
                        ?>
                        <div>
                          <span class="responseMC wrongAnswer"></span>
                          <label><?php echo str_replace("<BR>", "", $answer->material->mattext); ?></label>
                          <label class="score">0</label>
                        </div>
                        <?php
                      }
                    }
                  }                
                }
                echo '<br><label class="questionScore">'.round($total_score,1).'</label>';
                echo '<div class="clearer"></div>';
              }
            }
            $finalScore=$finalScore+$total_score;
          } elseif($type == 'PL') {
            // Parse the XML
            $filename = $_SESSION['file'];
            if (file_exists($filename)) {
              $xml = simplexml_load_file($filename);
            } else {
              die("Error");
            }
            foreach($xml->xpath("/questestinterop/item") as $item) {
              // Se l'ID della domanda corrisponde...
              if($ident == $item['ident']) {
                // Salvo le risposte corrette
                $correct_answer = array();
                foreach($item->resprocessing->respcondition as $respcondition) {
                  if(strcmp($respcondition['title'], 'default') != 0) {
                    $answer = (string) $respcondition->conditionvar->varequal;
                    array_push($correct_answer, $answer);
                  }
                }
              }
            }
            $count = count($question)-1;
            $total_score = 0;
            for($j=0; $j<count($question)-1; $j++) {
             
              foreach($xml->xpath("/questestinterop/item") as $item) {
                if($ident == $item['ident']) {
                  foreach($item->presentation->response_lid[$j]->render_choice->response_label as $response_label) {
                    if(strcmp($response_label['ident'], $question[$j]) == 0) {
                      $displayed_answer = (string) $response_label->material->mattext;
                    }
                  }
                }
              }
              if(strcmp($question[$j], $correct_answer[$j]) == 0) {
              ?>
                <div class="answered">
                  <span class="responseMC rightAnswer"></span>
                  <label><?php echo str_replace("<BR>", "", $displayed_answer); ?></label>
                  <label class="score"><?php $total_score = $total_score + (1/$count); echo round(1/$count, 1); ?></label>
                </div>
                <br>
              <?php
              } else {
                foreach($xml->xpath("/questestinterop/item") as $item) {
                  if($ident == $item['ident']) {
                    foreach($item->presentation->response_lid[$j]->render_choice->response_label as $response_label) {
                      if(strcmp($response_label['ident'], $correct_answer[$j]) == 0) {
                        $displayed_correct_answer = (string) $response_label->material->mattext;
                      }
                    }
                  }
                }
              ?>
                <div>
                  <span class="responseMC rightAnswer"></span>
                  <label><?php echo str_replace("<BR>", "", $displayed_correct_answer); ?></label>
                  <label class="score"><?php echo round(1/$count, 1); ?></label>
                </div>
                <div class="answered">
                  <span class="responseMC wrongAnswer"></span>
                  <label><?php echo str_replace("<BR>", "", $displayed_answer); ?></label>
                </div>
                <br>
              <?php
              }
            }
            echo '<br><label class="questionScore">'.round($total_score,1).'</label>';
            echo '<div class="clearer"></div>';
            $finalScore=$finalScore+$total_score;
          } elseif(strpos($type, 'NM') !== false) {
            // Parse the XML
            $filename = $_SESSION['file'];
            if (file_exists($filename)) {
              $xml = simplexml_load_file($filename);
            } else {
              die("Error");
            }
            foreach($xml->xpath("/questestinterop/item") as $item) {
              // Se l'ID della domanda corrisponde...
              if($ident == $item['ident']) {
                // Salvo la risposta corretta
                if(strcmp($type, 'NM_0') == 0) {
                  $answer = (float) $item->resprocessing->respcondition->conditionvar->varequal;
                  $correct_answer = $answer;
                } elseif(strcmp($type, 'NM_1') == 0) {
                  $correct_answer = array();
                  foreach($item->resprocessing->respcondition as $respcondition) {
                    if(strcmp($respcondition['title'], 'default') != 0) {
                      foreach($respcondition->conditionvar->or->vargte as $answer) {
                        $answer = (float) $answer;
                        array_push($correct_answer, $answer);
                      }
                    }
                  }
                }
                $total_score = 0;
                if(strcmp($type, 'NM_0') == 0) {
                  if($correct_answer == (float) $question[0]) {
                    $total_score = 1;
                    $finalScore=$finalScore+$total_score;
                    ?>
                      <div class="answered">
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $answer); ?></label>
                        <label class="score">1</label>
                      </div>
                    <?php
                  } else {
                    ?>
                      <div class="answered">
                        <span class="responseMC wrongAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $question[0]); ?></label>
                        <label class="score">0</label>
                      </div>
                      <div>
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $answer); ?></label>
                      </div>
                    <?php
                  }
                  echo '<br><label class="questionScore">'.$total_score.'</label>';
                  echo '<div class="clearer"></div>';
                } elseif(strcmp($type, 'NM_1') == 0) {
                  if(in_array((float) $question[0], $correct_answer)) {
                    $total_score = 1;
                    $finalScore=$finalScore+$total_score;
                    ?>
                      <div class="answered">
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $question[0]); ?></label>
                        <label class="score">1</label>
                      </div>
                    <?php
                  } else {
                    ?>
                      <div class="answered">
                        <span class="responseMC wrongAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $question[0]); ?></label>
                        <label class="score">0</label>
                      </div>
                      <div>
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $correct_answer[0]); ?></label>
                      </div>
                    <?php
                    
                  }
                  echo '<br><label class="questionScore">'.$total_score.'</label>';
                  echo '<div class="clearer"></div>';
                }
              }
            }
          } elseif(strpos($type, 'TM') !== false) {
            // Parse the XML
            $filename = $_SESSION['file'];
            if (file_exists($filename)) {
              $xml = simplexml_load_file($filename);
            } else {
              die("Error");
            }
            foreach($xml->xpath("/questestinterop/item") as $item) {
              // Se l'ID della domanda corrisponde...
              if($ident == $item['ident']) {
                // Salvo la risposta corretta
                $temp = array();
                foreach($item->resprocessing->respcondition as $respcondition) {
                  $count = $count + count($respcondition->conditionvar->or);
                  // Check answer
                  if ($count > 0) {
                    foreach($respcondition->conditionvar->or->varequal as $answer) {
                      array_push($temp, $answer);
                    }
                  } else {
                    foreach($respcondition->conditionvar->varequal as $answer) {
                      array_push($temp, $answer);
                    }
                  }
                }
                $total_score = 0;
                if(in_array($question[0], $temp)) {
                  $total_score = 1;
                  $finalScore=$finalScore+$total_score;
                  ?>
                      <div class="answered">
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $answer); ?></label>
                        <label class="score">1</label>
                      </div>
                    <?php
                } else {
                  ?>
                      <div class="answered">
                        <span class="responseMC wrongAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $question[0]); ?></label>
                        <label class="score">0</label>
                      </div>
                      <div>
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo str_replace("<BR>", "", $temp[0]); ?></label>
                      </div>
                    <?php
                }
                echo '<br><label class="questionScore">'.$total_score.'</label>';
                echo '<div class="clearer"></div>';
                /*
                if(strcmp($type, 'NM_0') == 0) {
                  if($correct_answer == (float) $question[0]) {
                    ?>
                      <div class="answered">
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo $answer; ?></label>
                      </div>
                    <?php
                  } else {
                    ?>
                      <div class="answered">
                        <span class="responseMC wrongAnswer"></span>
                        <label><?php echo $question[0]; ?></label>
                      </div>
                      <div>
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo $answer; ?></label>
                      </div>
                    <?php
                  }
                } elseif(strcmp($type, 'NM_1') == 0) {
                  if(in_array((float) $question[0], $correct_answer)) {
                    ?>
                      <div class="answered">
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo $question[0]; ?></label>
                      </div>
                    <?php
                  } else {
                    ?>
                      <div class="answered">
                        <span class="responseMC wrongAnswer"></span>
                        <label><?php echo $question[0]; ?></label>
                      </div>
                      <div>
                        <span class="responseMC rightAnswer"></span>
                        <label><?php echo $correct_answer[0]; ?></label>
                      </div>
                    <?php
                  }
                }
                */
              }
            }
          } elseif(strpos($type, 'HS') !== false) {
            // Parse the XML
            $filename = $_SESSION['file'];
            if (file_exists($filename)) {
              $xml = simplexml_load_file($filename);
            } else {
              die("Error");
            }
            foreach($xml->xpath("/questestinterop/item") as $item) {
              // Se l'ID della domanda corrisponde...
              if($ident == $item['ident']) {
                echo '<div id="contentContainer" style="position:relative;">';
                $image = '<img src="'.$item->presentation->response_xy->render_hotspot->material->matimage['uri'].'" width="'.$item->presentation->response_xy->render_hotspot->material->matimage['width'].'" height="'.$item->presentation->response_xy->render_hotspot->material->matimage['height'].'">';
                $answer_coords = explode(' ', $answers[0]);
                echo '<img id="thing" style="position: absolute; top: 225px; left: 676.375px;" src="themes/default/images/smiley_red.png" onload="riposizionaInCorrezione(this,'.$answer_coords[0].','.$answer_coords[1].')">';
                echo $image;
                $tmp_coords = str_replace(' ', ',', $item->resprocessing->respcondition->conditionvar->varinside);
                $correct_coords = explode(',', $tmp_coords);
                

                echo '<p class="rettangoloRispostaStudente" style="position:absolute;left:'.$correct_coords[0].'px;top:'.$correct_coords[1].'px;height:'.$correct_coords[3].'px;width:'.$correct_coords[2].'px; border:3px solid green;" <="" p=""></p>';
                echo '</div>';
                echo '<div class="">';
                echo '<span value="30463" class="responseHS rightAnswer"></span>';
                echo '<label>'.$correct_coords[0].','.$correct_coords[1].','.$correct_coords[2].','.$correct_coords[3].'</label>';
                if($correct) {
                  $finalScore = $finalScore+1;
                  echo '<label class="score">1</label>';
                  echo '</div>';
                  echo '<label class="questionScore">1</label>';
                  echo '<div class="clearer"></div>';
                } else {
                  echo '<label class="score">0</label>';
                  echo '</div>';
                  echo '<label class="questionScore">0</label>';
                  echo '<div class="clearer"></div>';
                }
                //echo '</div>';
                //echo '</div>';
              }
            }
          }
        ?>
      </div>
    </div>
    <?php
}
?>
</div>







            <?php
            
            $total_questions = count($xml->xpath("/questestinterop/item"));
            $scale = $xml->xpath("/questestinterop/scale")[0];
  
            ?>
            <div id="lastLine">
                <div id="finalScorePanel">
                    <table id="finalScore">
                        <tr>
                            <td class="sLabel">Total Score</td>
                            <td class="sScore"><label id="scorePre"><?php echo round($finalScore,1); ?></label></td>
                            <td>+</td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttBonus ?></td>
                            <td class="sScore"><label id="scoreBonus">0</label></td>
                            <td>=</td>
                        </tr>
                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?php echo "Final Score"; ?></td>
                            <?php echo "<td>".round($finalScore,1)."/".$total_questions."</td>";?>
                        </tr>
                        <tr>
                            <td class="sLabel"></td>
                            <?php echo "<td>".round(($scale*$finalScore)/$total_questions,1)."/".$scale."</td>";?>
                        </tr>
                    </table>

                </div>
            </div>
            <div class="clearer"></div>
</div>

<script>
jQuery('.responseScore').each(function() {
  var currentElement = $(this);
	var obj = currentElement.parent().next().find('.questionScore');
	var value = obj.text()
	currentElement.text(value);
});

jQuery('.wrongQuestion').each(function() {
  var currentElement = $(this);
	var obj = currentElement.children().find('.responseScore');
	var value = parseFloat(obj.text())
  console.log(value);
  if(value > 0) {
    currentElement.removeClass('wrongQuestion');
    currentElement.addClass('rightQuestion');
  }
});
</script>
