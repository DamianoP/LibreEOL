<?php
/**
 * File: selectlanguage.php
 * User: Masterplan
 * Date: 4/19/13
 * Time: 10:22 AM
 * Desc: Selects language to manage
 */

global $config, $user;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div>

        <?php

        echo '<div class="msgCenter"> '.ttMSelectLanguage.'</div>';

        openBox(ttLanguages, 'center', 'langList', array('new'));

        $scan = scandir($config['systemLangsXml']);
        echo '<div class="list"><ul>';
        for($index = 0; $index < count($scan); $index++){
            if($scan[$index] != '.' && $scan[$index] != '..'){

                $file = new DOMDocument();
                $file->load($config['systemLangsXml'].$scan[$index]);
                $langFrom = $file->getElementById('name')->nodeValue.' ('.$file->getElementById('alias')->nodeValue.')';

                if(file_exists($config['systemLangsDir'].$file->getElementById('alias')->nodeValue.'/')){
                    echo '<li><a class="selectLanguage" value="'.$file->getElementById('alias')->nodeValue.'">'.$file->getElementById('name')->nodeValue.'</a></li>';
                }else{
                    echo '<li><a class="selectLanguage" style="color: red" value="'.$file->getElementById('alias')->nodeValue.'">'.$file->getElementById('name')->nodeValue.'</a></li>';
                }
            }
        }
        echo '</ul></div>';

        closeBox();

        echo '<form action="" method="post" id="languageForm">
                  <input type="hidden" name="alias" value="">
              </form>';

        ?>

        <div class="clearer"></div>
    </div>
</div>