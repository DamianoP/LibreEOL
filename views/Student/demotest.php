<?php
/**
 * File: index.php
 * User: Cricco95
 * Date: 24/09/2018
 * Time: 19:42 PM
 * Desc: Student's Homepage
 */

global $user;
?>
<script type="text/javascript" src="libs/jquery.redirect.js"></script>
<script type="text/javascript" src="libs/Subject/Showsubjectinfoandexamsdemotest.js"></script>
<script type="text/javascript" src="libs/Student/DemoTest.js"></script>
<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
  <div>
    <?php
    // Controllo la lingua dell'utente
    $db = new sqlDB();
    $lang="EN";
    if($db->qSelect('Users', 'idUser', $user->id)){
      if($row = $db->nextRowAssoc()) {
        switch ($row['fkLanguage']) {
          case 1:
            $lang = "EN";
            break;
          case 2:
            $lang = "IT";
            break;
          case 3:
            $lang = "RU";
            break;
          case 4:
            $lang = "ES";
            break;
          case 5:
            $lang = "DE";
            break;
          case 6:
            $lang = "PL";
            break;
          case 7:
            $lang = "FR";
            break;
          case 8:
            $lang = "GR";
            break;
          case 9;
            $lang = "SI";
            break;
        }
      }
    }

    $_SESSION['demotest_lang'] = $lang;

    // Ottengo la lista dei file XML della lingua dell'utente
    //$directories = glob('demotest/*');

    if($lang!="EN")
	$directories=array(0 => "demotest/EN", 1 => "demotest/".$lang);
    else
        $directories=array("demotest/EN");


    if(!empty($directories)) {
      openBox(ttSubjects, 'left', 'subjectList');
    
      $readedSubjects = array();
      $subjectsFiles = array();

      // Leggo il topic di ogni XML
      foreach($directories as $dir) {
        $files = glob($dir.'/XML/*.xml');
        foreach($files as $file) {
          $xml = simplexml_load_file($file);
          $topic = $xml->xpath("/questestinterop/item/itemmetadata/qmd_topic")[0];
          // Regex
          preg_match('/([^\\\\]+$)/m', $topic, $newTopic);
          $topic = $newTopic[0];
          // Salvo la combinazione topic-file
          array_push($readedSubjects, $topic);
          array_push($subjectsFiles, $file);
        }
      }

      // Genero la lista dei subject
      if(!empty($readedSubjects)){
        echo '<div class="list"><ul>';
        $i=-1;
        foreach($readedSubjects as $index=>$subject) {
          $i++;
          $file = $subjectsFiles[$i];
          echo '<form id="form'.$i.'" action="index.php?page=student/dodemotest" method="POST">';
          #echo '<li><a class="showSubjectInfoAndExams" onclick="form'.$i.'.submit();" value="'.$subject.'">'.$subject.'</a><input type="hidden" name="file" value="'.$file.'"></li>';-->
          echo '<li><a class="showSubjectInfoAndExams" onclick="showSubjectInfoAndExams(this);" value=\'{ "subject":"'.$subject.'", "file":"'.$file.'"}\'>'.$subject.'</a><input type="hidden" name="file" value="'.$file.'"></li>';
          echo '</form>';
        }
        echo '</ul></div></form>';
      }else{
        die();
      }
      closeBox();

      // Info box
      openBox(ttInfo, 'right', 'subjectInfoAndExams');
      ?>
      <form class="infoEdit" onsubmit="return false;"></form>
      <?php
      closeBox();
    }
    ?>
    
    <div class="clearer"></div>
  </div>
</div>
