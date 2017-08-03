<?php
/**
 * File: systemconfiguration.php
 * User: Masterplan
 * Date: 27/10/14
 * Time: 19:24
 * Desc: Show web page to edit system's configurations
 */

global $config, $log;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <br/><br/>
    <?php openBox(ttConfiguration, 'center-700px', 'systemConfiguration'); ?>

    <form class="infoEdit" onsubmit="return false;">
        <div class="columnLeft">
            <h2 class="center"><?= ttSystemConfiguration ?></h2>

            <label class="bSpace"><?= ttSkin ?> : </label>
            <dl class="dropdownInfo bSpace" id="configurationTheme">
                <dt class="writable">
                    <span><?= $config['themeName'] ?><span class="value"><?= $config['themeName'] ?></span></span>
                </dt>
                <dd><ol>
                        <?php
                        $themesDir = scandir($config['themesDir']);
                        foreach($themesDir as $theme){
                            if($theme[0] != '.'){
                                echo '<li>'.$theme.'<span class="value">'.$theme.'</span></li>';
                            }
                        }
                        ?>
                </ol></dd>
            </dl>
            <div class="clearer"></div>

            <label class="b2Space"><?= ttLogo ?> : </label>
            <input class="writable" type="text" id="configurationLogo" value="<?= $config['systemLogo'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttTitle ?> : </label>
            <input class="writable" type="text" id="configurationTitle" value="<?= $config['systemTitle'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttHomepage ?> : </label>
            <input class="writable" type="text" id="configurationHome" value="<?= $config['systemHome'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttEmail ?> : </label>
            <input class="writable" type="text" id="configurationEmail" value="<?= $config['systemEmail'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttLanguage ?> : </label>
            <?php
            $db = new sqlDB();
            (($db->qSelect('Languages')) && ($allLangs = $db->getResultAssoc('alias'))) or die($db->getError());
            ?>
            <dl class="dropdownInfo bSpace" id="configurationLanguage">
                <dt class="writable">
                    <span><?= $allLangs[$config['systemLang']]['description'] ?><span class="value"><?= $config['systemLang'] ?></span></span>
                </dt>
                <dd><ol>
                        <?php
                        foreach($allLangs as $lang){
                            echo '<li>'.$lang['description'].'<span class="value">'.$lang['alias'].'</span></li>';
                        }
                        ?>
                </ol></dd>
            </dl>
            <div class="clearer"></div>

            <label class="b2Space"><?= ttTimeZone ?> : </label>
            <dl class="dropdownInfo bSpace" id="configurationTimeZone">
                <dt class="writable">
                    <span><?= $config['systemTimeZone'] ?><span class="value"><?= $config['systemTimeZone'] ?></span></span>
                </dt>
                <dd><ol>
                        <?php
                        $timeZones = DateTimeZone::listIdentifiers();
                        foreach($timeZones as $timeZoneIndex => $timeZone){
                            echo '<li>'.$timeZone.'<span class="value">'.$timeZone.'</span></li>';
                        }
                        ?>
                </ol></dd>
            </dl>
            <div class="clearer"></div>

        </div>
        <div class="columnRight">
            <h2 class="center"><?= ttDatabaseConfiguration ?></h2>

            <label class="b2Space"><?= ttType ?> : </label>
            <?php
                $allDBTypes = array('mysql' => 'MySQL');
            ?>
            <dl class="dropdownInfo bSpace" id="configurationDBType">
                <dt class="writable">
                    <span><?= $allDBTypes[$config['dbType']] ?><span class="value"><?= $config['dbType'] ?></span></span>
                </dt>
                <dd><ol>
                        <?php
                        foreach($allDBTypes as $DBType => $DBDescrption){
                            echo '<li>'.$DBDescrption.'<span class="value">'.$DBType.'</span></li>';
                        }
                        ?>
                </ol></dd>
            </dl>
            <div class="clearer"></div>

            <label class="b2Space"><?= ttHost ?> : </label>
            <input class="writable" type="text" id="configurationDBHost" value="<?= $config['dbHost'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttPort ?> : </label>
            <input class="writable" type="text" id="configurationDBPort" value="<?= $config['dbPort'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttName ?> : </label>
            <input class="writable" type="text" id="configurationDBName" value="<?= $config['dbName'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttUser ?> : </label>
            <input class="writable" type="text" id="configurationDBUsername" value="<?= $config['dbUsername'] ?>">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttPassword ?> : </label>
            <input class="writable" type="text" id="configurationDBPassword" value="<?= $config['dbPassword'] ?>">
            <div class="clearer"></div>
        </div>

        <div class="clearer"></div>

        <div class="center tSpace">
            <a class="button ok bSpace" id="saveConfiguration" onclick="saveConfiguration(new Array(askConfirmation = true));"><?= ttSave ?></a>
        </div>
    </form>

    <?php closeBox() ?>
</div>