<?php
/**
 * File: language.php
 * User: Masterplan
 * Date: 7/5/13
 * Time: 12:51 PM
 * Desc: Shows language edit page
 */

global $config, $user;

$xmlFrom = new DOMDocument();
$xmlFrom->load($config['systemLangsXml'].$user->lang.'.xml');
$langFrom = $xmlFrom->getElementById('name')->nodeValue.' ('.strtoupper($xmlFrom->getElementById('alias')->nodeValue).')';

$xmlTo = new DOMDocument();
$xmlTo->load($config['systemLangsXml'].$_POST['alias'].'.xml');
$langTo = $xmlTo->getElementById('name')->nodeValue.' ('.strtoupper($xmlTo->getElementById('alias')->nodeValue).')';

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div>

        <?php openBox(ttLanguage.": $langFrom --> $langTo", 'normal', 'language'); ?>

        <input type="hidden" name="langAlias" id="langAlias" value="<?= $xmlTo->getElementById('alias')->nodeValue ?>">

        <table id="translationsTable">
            <thead>
                <tr>
                    <th class="translationID">ID</th>
                    <th class="langFrom"><?= $langFrom ?></th>
                    <th class="langTo"><?= $langTo ?></th>
                </tr>
            </thead>
            <tbody>

        <?php
        $textsFrom = $xmlFrom->getElementsByTagName('text');
        for($index = 0; $index < $textsFrom->length; $index++){
            $translationID = $textsFrom->item($index)->getAttribute('id');
            $xmlToElement = $xmlTo->getElementById($translationID);
            $translation = ($xmlToElement != null)? trim(str_replace('\n', "\n", $xmlToElement->nodeValue)) : '';
            $filled = ($translation == '')? 'red' : 'green';

            echo '<tr>
                      <td>'.$translationID.'</td>
                      <td>'.str_replace('\n', '¶<br/>', $textsFrom->item($index)->nodeValue).'</td>
                      <td><span class="value hidden">'.$translation.'</span><textarea class="language '.$filled.'">'.$translation.'</textarea></td>
                  </tr>';
        }
        ?>

            </tbody>
        </table>

        <a class="ok button right lSpace tSpace" id="update" onclick="saveLanguageFiles();"><?= ttSaveLanguageFiles ?></a>
<!--        <a class="normal button right lSpace tSpace" id="save" onclick="saveLanguageXML();">--><?//= ttSaveLanguageXML ?><!--</a>-->
        <a class="normal button left tSpace" id="cancel" onclick="window.location = 'index.php?page=admin/selectlanguage';"><?= ttCancel ?></a>

        <div class="clearer"></div>

        <?php closeBox(); ?>

        <div class="clearer"></div>

    </div>
</div>