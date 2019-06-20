<?php

global $user, $log, $config, $lang;

session_start();

$file = $_POST['file'];
$_SESSION['file'] = $file;
$lang = $_SESSION['demotest_lang'];

$questionTypesLibDir = opendir($config['systemQuestionTypesLibDir']);
while (($script = readdir($questionTypesLibDir)) !== false)
    if(substr($script, 0, 2) == 'QT')
        echo '<script type="text/javascript" src="'.$config['systemQuestionTypesLibDir'].$script.'"></script>';
closedir($questionTypesLibDir);

// Parse the XML
$filename = '../public/'.$file;

if (file_exists($filename)) {
  $xml = simplexml_load_file($filename);
} else {
  die("Error");
}
?>


<script type="text/javascript" src="libs/Student/DoDemoTest.js"></script>
<script>
function updateNumericValue() {
  document.getElementById("inputNumber").setAttribute("value", document.getElementById("inputNumber").value);
}
</script>
<div id="navbar">
  <?php printMenu(); ?>
</div>
<div id="countdown"></div>
<div id="main">
  <div id="contenitoreDelTest">
    <div class="boxNormal" style="" id="test">
      <div class="box">
        <div class="boxTopCenter"><?php $item = $xml->xpath("/questestinterop/item/itemmetadata/qmd_topic")[0]; echo $item . " Test"; ?></div>
      </div>
      <div class="boxContent">
<?php
$pull_down = false;

echo '<form method="POST" action="index.php?page=exam/correctdemotest">';
$z = -1;
// Itero ogni domanda
foreach($xml->xpath("/questestinterop/item") as $item) {
    unset($image);
    $z++;
    // Salvo il tipo di domanda
    $type = $item->itemmetadata->qmd_itemtype;
    // Setto il tipo di domanda
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

    // Salvo l'ident della domanda
    $ident = $item['ident'];

    // Stampa la domanda
    $displayed_question = '';
    if($type != "HS") {
      foreach($item->presentation->material as $material) {
        $displayed_question = $displayed_question . $material->mattext;
      }
    } else {
      foreach($item->presentation->material as $material) {
        $displayed_question = $displayed_question . $material->mattext;
      }
      //$displayed_question = $displayed_question . '<img src="'.$item->presentation->response_xy->render_hotspot->material->matimage['uri'].'">';
      $image = '<img src="'.$item->presentation->response_xy->render_hotspot->material->matimage['uri'].'" width="'.$item->presentation->response_xy->render_hotspot->material->matimage['width'].'" height="'.$item->presentation->response_xy->render_hotspot->material->matimage['height'].'">';
    }

    //$image = $item->presentation->material[1]->mattext;
    $displayed_answers = array();
    $answers_values = array();
    $answers_ident = array();
    $multiple_choices = array();

    if ($type == 'MC' || $type == 'MR') {
      // Per ogni domanda...
      foreach($item->presentation->response_lid->render_choice->response_label as $answer) {
        // Inserisci il testo nell'array
        array_push($displayed_answers, $answer->material->mattext);
        array_push($answers_ident, $answer['ident']);
      }
    } elseif ($type == 'PL') {
      $j=-1;
      // Per ogni domanda...
      foreach($item->presentation->response_lid as $answer) {
        // Inserisco la domanda nell'array delle domande
        array_push($displayed_answers, $answer->material->mattext);
        $i=-1;
        $j++;
        $temp_array = array();
        $temp_array2 = array();
        // Per ogni risposta multipla...
        foreach($answer->render_choice->response_label as $multiple_choice) {
          $i++;
          // Inserisco l'opzione selezionabile nell'array
          $temp_array[$i] = $multiple_choice->material->mattext;
          $temp_array2[$i] = array();
          array_push($temp_array2[$i], $multiple_choice['ident']);
        }
        $answers_ident = $temp_array2;
        $multiple_choices[$j] = array();
        array_push($multiple_choices[$j], $temp_array);
        
        //array_push($answers_ident, $temp_array);
      }
    } 
    echo '<div class="questionTest" value="' . $ident . '" type="' . $type . '">';
    //echo '<div class="questionTest" type="' . $type . '">';
    echo '<div class="questionText"><p>' . $displayed_question . '</p>';
    if(isset($image) && $type != 'HS') {
      echo $image;
    }
    echo '</div>';
    echo '<div class="questionAnswers">';
    if($type == 'HS') {
      echo '<div id="contentContainer" style="position:relative;" class="contentContainer" onclick="getClickPosition(this,event); updateCoordinates(this);">';
      echo $image;
      echo '<img class="hscursor" id="thing" src="themes/default/images/smiley_red.png" style="position: absolute; left: 751.438px; top: 196px;">';
      echo '</div>';
    }
    echo '<div>';
    
    $i=-1;
    foreach($displayed_answers as $answer) {
      $i++;
      if ($type == 'MC') {
        //echo '<input type="radio" name="'.$ident.'" value="'.$answers_ident[$i].'">';
        echo '<input type="radio" name="question['.$z.'][]" value="'.$answers_ident[$i].'">';
        echo '<label>' . $answer . '</label>';
        echo '<br>';
      } else if ($type == 'PL') {
        //echo '<select name="'.$ident.'_'.$i.'">';
        echo '<select name="question['.$z.'][]">';
        for($t=0; $t<count($multiple_choices[$i][0]); $t++) {
          //echo '<option value="'.$multiple_choices[$i][0][$t].'">' . $multiple_choices[$i][0][$t] . '</option>';
          echo '<option value="'.$answers_ident[$t][0][0].'">' . $multiple_choices[$i][0][$t] . '</option>';
        }
        echo '</select>';  
        echo '<label>' . $answer . '</label>';        
        echo '<br>';
      } else if ($type == 'MR') {
        //echo '<input type="checkbox" name="'.$ident.'" value="'.$answer.'">';
        echo '<input type="checkbox" name="question['.$z.'][]" value="'.$answers_ident[$i].'">';
        echo '<label>' . $answer . '</label>';
        echo '<br>';
      } else if ($type == 'TM') {
        echo '<input type="text" name="question['.$z.'][]" value="'.$answers_ident[$i].'">';
        echo '<br>';
      }
    }

    if ($type == 'NM') {
      echo '<input id="inputNumber" type="text" value="" name="question['.$z.'][]" onkeydown="updateNumericValue()">';
    } elseif ($type == 'TM') {
      echo '<input id="inputText" type="text" value="" name="question['.$z.'][]" onkeydown="updateNumericValue()">';
    } elseif($type == 'HS') {
      echo '<input type="hidden" name="question['.$z.'][]" value="coords">'; 
    }
    echo '<input type="hidden" name="question['.$z.'][]" value="'.$ident.'">'; 
    if($type != 'HS') {
      echo '</div>';
      echo '</div>';
    } else {
      echo '</div>';
      echo '</div>';
      echo '<br>';
    }
    echo '</div>';
}

?>
<div class="contenitoreBottoneTest">
          <input class="bottoneTest" id="submitTest" name="submit" type="submit" value="<?= ttSubmit ?>">
          <br><br>
          <p>&copy; ECTN Association - All rights reserved - www.echemtest.net</p>
          <br><br>
        </div>
</form>
        
      </div>
    </div>
    <div class="extra" id="calculator" style="display:none">
      <span class="extraTitle">Calculator</span>
      <span class="extraClose" title="Close"></span>
      <object width="100%" height="100%" data="extra/EChem_calc.swf"></object>
    </div>
  </div>
</div>

<script type="application/javascript">
    var countdown = new Countdown({
        time	    : 3295,
        width		: 210,
        height		: 50,
        inline		: true,
        target		: "countdown",
        style 		: "flip",
        rangeHi		: "hour",
        rangeLo		: "second",
        padding 	: 0.4,
        onComplete	: countdownComplete,
        labels		: 	{
            font 	: "Arial",
            color	: "#ffffff",
            weight	: "normal"
        }
    });
</script>

<script> 
//questa funzione evita che amazon blocchi la sessione dell'utente
//var idSetAttuale=;

setInterval(function keepAlive_EOL() {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "index.php", true);
  	xhttp.send();
    }, 300000);


/*
//questa funzione salva lo stato dell'esame sul server ogni X secondi
setInterval(function(){ 
    controllaCambiamenti();
    //controllaCambiamenti(idSetAttuale); 
}, 10000);
*/
</script>

<script>
function updateCoordinates(div) {

    var currentElement = $(div);
    var coordinates = currentElement.children('.hscursor').first().attr('style');
    coordinates = coordinates.match(/(?=left: ).*/g)[0];
    coordinates = coordinates.substring(0, coordinates.length-1);
    coordinates = coordinates.split("; ");
    coordinateX = coordinates[0].split(": ")[1].replace('px', '');
    coordinateY = coordinates[1].split(": ")[1].replace('px', '');

	  var obj = currentElement.next().find('input').first();

   obj.attr('value', coordinateX + " " + coordinateY);
  

}
</script>
