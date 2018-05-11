<?php
/**
 * File: essential.php
 * User: Masterplan
 * Date: 3/24/13
 * Time: 7:21 PM
 * Desc: All system's essential PHP files, classes, functions, ...
 */

// Configuration file
require_once(__DIR__.'/config.php');
// Log class
require_once(__DIR__.'/Log.php');
// Database class
require_once(__DIR__.'/sqlDB.php');
// Render class
require_once(__DIR__.'/Engine.php');
// User class
require_once(__DIR__.'/User.php');
// Controller class
require_once(__DIR__.'/Controller.php');

global $config;
// Question class
require_once($config['systemQuestionTypesClassDir'].'Question.php');
// Answer class
require_once($config['systemQuestionTypesClassDir'].'Answer.php');

$ajaxSeparator = "_-^SEPARATOR^-_";

/**
 * @name    getQuestionTypes
 * @return  Array
 * @descr   Returns all available question type
 */
function getQuestionTypes(){
    $types = array(
        'MC',       # Multiple Choice
        'MR',       # Multiple Response
        'YN',       # Yes/No
        'TF',       # True/False
        'ES',        # Essay
        'NM',       # Numeric
        'TM',       # Text Match
        'HS',       # Hotspot
        'PL',       # Pull Down
        'FB',       # Fill In Blanks

    );
    return $types;
}

/**
 * @name    getSystemDifficulties
 * @return  Array
 * @descr   Returns difficulties values with names
 */
function getSystemDifficulties(){
    return array('1' => 'easy',
                 '2' => 'medium',
                 '3' => 'hard',);
}

/**
 * @name    getMaxQuestionDifficulty
 * @return  Integer
 * @descr   Returns max difficulty for a question
 */
function getMaxQuestionDifficulty(){
    return 3;
}

/**
 * @name    getScoreTypes
 * @return  Array
 * @descr   Returns all scoreTypes for exams
 */
function getScoreTypes(){
    $scoreTypes = array(
        '10',       // Tenths
        '30',       // Thirtieths
        '100'       // Hundredth
    );
    return $scoreTypes;
}

/**
 * @name    getQuestionExtras
 * @return  Array
 * @descr   Returns all available question extra
 */
function getQuestionExtras(){
    $extra = array(
        'c',        // Calculator
        'p'         // Periodic Table
    );
    return $extra;
}

/**
 * @name    tt
 * @param   $text       String      Constant name
 * @return  $text       String      Constant value
 * @descr   Simple trick to use constant in HEREDOC
 */
$tt = 'tt';
function tt($text){
    return $text;
}

/**
 * @name    openBox
 * @param   $title      String      Box's title
 * @param   $css        String      Box's class-style
 * @param   $id         String      Box's DOM id
 * @param   $buttons    String      Buttons to add in box header
 * @descr   Create the head of a box
 */
function openBox($title = '', $css = 'normal', $id = '', $buttons = null){
    global $config;

    $css = explode('-', $css);
    $style = '';
    $class = $css[0];
    if(count($css) > 1)
        $style = 'width: '.$css[1];

    if($class == 'left')
        $class = 'boxLeftS';
    elseif($class == 'right')
        $class = 'boxRightS';
    elseif($class == 'center')
        $class = 'boxCenterS';
    elseif($class == 'normal')
        $class = 'boxNormal';
    elseif($class == 'small')
        $class = 'boxNormalS';
    if($id != '')
        echo '<div class="'.$class.'" style="'.$style.'" id="'.$id.'">';
    else
        echo '<div class="'.$class.'" style="'.$style.'">';

    echo '<div class="box">
            <div class="boxTopCenter">'.$title.'</div>';

    if($buttons != null){
        echo '<div class="smallButtons">';
        foreach ($buttons as &$button) {
            $button = explode('-', $button);
            $type = $id = $button[0];
            if(count($button) > 1)
                $id = $button[1];
            echo '<div id="'.$id.'">
                     <img class="icon" src="'.$config['themeImagesDir'].$type.'.png"/><br/>
                     '.constant('tt'.ucfirst($type)).'
                  </div>';
        }
        echo '</div>';
    }
    echo '</div>
        <div class="boxContent">';
}

/**
 * @name    closeBox
 * @descr   Create the footer of a box
 */
function closeBox(){
    global $config;

    echo '</div>
          <div class="boxBottom">
              <div class="boxBottomCenter"></div>
          </div>
      </div>';

}

/**
 * @name    printMenu
 * @descr   Print the user's menu
 */
function printMenu(){
    global $user;

    if($user->role == 't')
        teacherMenu();
    elseif($user->role == 'e')
        eteacherMenu();
    elseif($user->role == 'er')
        eteacherMenu();
    elseif($user->role == 'a')
        adminMenu();
    elseif($user->role == 's')
        studentMenu();
    elseif($user->role == 'at')
        adminTeacherMenu();
    elseif($user->role == '?')
        guestMenu();

    if($user->role != '?'){
        help();
        dropdownSystemLanguage();
        echo '</ul>';
    }
}

/**
 * @name    adminMenu
 * @descr   Create the admin menu on page
 */
function adminMenu(){
    global $tt, $config; ?>

<ul class="topnav">
    <li><a href="index.php"><?= ttHome ?></a></li>
    <li>
        <a class="trigger"><?= ttSubjects ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=subject"><?= ttSubjects ?></a></li>
        </ul>
    </li>
    <li>
        <a class="trigger"><?= ttUsers ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=admin/newteacher"><?= ttNewTeacher ?>/<?= ttAdministrator ?></a></li>
            <li><a href="index.php?page=admin/editteacher"><?= ttEditTeacher ?></a></li>
            <li><a href="index.php?page=admin/newstudent"><?= ttNewStudent ?></a></li>
            <li><a href="index.php?page=admin/editstudent"><?= ttEditStudent ?></a></li>
        </ul>
    </li>
    <li>
        <a class="trigger"><?= ttGroup ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=admin/newgroup"><?= ttNewGroup ?>/<?= ttSubgroup ?></a></li>
            <li><a href="index.php?page=admin/editgroup"><?= ttEditGroup ?>/<?= ttSubgroup ?></a></li>
        </ul>
    </li>
    <li><a href="<?= $config['systemFileManagerDir']?>filemanager.php?type=certificate"><?= ttCertificates ?></a></li>
    <li>
        <a class="trigger"><?= ttSystem ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=admin/selectlanguage"><?= ttLanguages ?></a></li>
            <li><a href="index.php?page=admin/systemconfiguration"><?= ttConfiguration ?></a></li>
            <li><a href="index.php?page=admin/rooms"><?= ttRooms ?></a></li>
        </ul>
    </li>
    <li>
        <a class="trigger"><?= ttImportQM ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=importqm/importpage"><?= ttImport ?></a></li>
        </ul>
    </li>

    <li><a href="index.php?page=admin/profile"><?= ttProfile ?></a></li>
    <li><a href="index.php?page=admin/exit" style="color: red"><?= ttExit ?></a></li>

<?php
}

/**
 * @name    teacherMenu
 * @descr   Create the teacher menu on page
 */
function teacherMenu(){
    global $tt; ?>

<ul class="topnav">
    <li><a href="index.php"><?= ttHome ?></a></li>
    <li>
        <a class="trigger"><?= ttSubjects ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=subject"><?= ttSelectSubject ?></a></li>
            <li><a href="index.php?page=question"><?= ttTopicsAndQuestions ?></a></li>
        </ul>
    </li>
    <li>
        <a class="trigger"><?= ttExams ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=exam/exams"><?= ttMyExams ?></a></li>
            <li><a href="index.php?page=exam/settings"><?= ttSettings ?></a></li>
        </ul>
    </li>
    <li><a href="index.php?page=report"><?= ttReport ?></a></li>
    <li><a href="index.php?page=admin/profile"><?= ttProfile ?></a></li>
    <li><a href="index.php?page=admin/errorquestion" > <?=ttErrorquestion ?></a></li>


<?php
}


/**
 * @name    teacherMenu
 * @descr   Create the teacher menu on page
 */
function eteacherMenu(){
    global $tt; ?>

    <ul class="topnav">
    <li><a href="index.php"><?= ttHome ?></a></li>
    <li>
        <a class="trigger"><?= ttSubjects ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=subject/index2"><?= ttSelectSubject ?></a></li>
            <li><a href="index.php?page=question/index2"><?= ttTopicsAndQuestions ?></a></li>
        </ul>
    </li>
    <li>
        <a class="trigger"><?= ttExams ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=exam/exams"><?= ttMyExams ?></a></li>
            <li><a href="index.php?page=exam/settings"><?= ttSettings ?></a></li>
        </ul>
    </li>
    <li><a href="index.php?page=report"><?= ttReport ?></a></li>
    <li><a href="index.php?page=admin/profile"><?= ttProfile ?></a></li>
    <li><a href="index.php?page=admin/errorquestion" > <?=ttErrorquestion ?></a></li>


<?php
}


/**
 * @name    adminTeacherMenu
 * @descr   Create the adminTeacher menu on page
 */
function adminTeacherMenu(){
    global $tt; ?>

<ul class="topnav">
    <li><a href="index.php"><?= ttHome ?></a></li>
    <li>
        <a class="trigger"><?= ttSubjects ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=subject"><?= ttSelectSubject ?></a></li>
            <li><a href="index.php?page=question"><?= ttTopicsAndQuestions ?></a></li>
        </ul>
    </li>
    <li>
        <a class="trigger"><?= ttExams ?></a>
        <ul class="subnav">
            <li><a href="index.php?page=exam/exams"><?= ttMyExams ?></a></li>
            <li><a href="index.php?page=exam/settings"><?= ttSettings ?></a></li>
        </ul>
    </li>
    <li><a href="index.php?page=report"><?= ttReport ?></a></li>
    <li><a href="index.php?page=admin/profile"><?= ttProfile ?></a></li>
    <li><a href="index.php?page=admin" style="color: red"><?= ttAdministration ?></a></li>
    <li><a href="index.php?page=admin/errorquestion" > <?=ttErrorquestion ?></a></li>

<?php
}




/**
 * @name    studentMenu
 * @descr   Print the student menu on page
 */
function studentMenu(){
    global $tt; ?>

<ul class="topnav">
    <li><a href="index.php"><?= ttHome ?></a></li>
    <li><a href="index.php?page=admin/profile"><?= ttProfile ?></a></li>
    <li><a href="index.php?page=student/votes"><?= ttMyExams ?></a></li>

<?php
}

/**
 * @name    dropdownSystemLanguage
 * @descr   Print the dropdown menu for change system language
 * MINIMAL VERSION in case of troubles with languages ...
 */
/*
function dropdownSystemLanguage(){
    global $config, $user;

    $db = new sqlDB();
    if($langs = $db->qGetAllLanguages()){
        $keys = array_keys($langs);
        echo '<dl class="dropdownSystemLanguage">';
        echo '<dt><span><img class="bigFlag" src="'.$config['themeFlagsDir'].$user->lang.'.gif"></span></dt>';
        echo '<dd><ul>';
        if($user->lang=="en")
            echo '<li><img class="bigFlag" src="'.$config['themeFlagsDir'].'it'.'.gif"><span class="value">'.'2'.'</span></li>';        
        else
            echo '<li><img class="bigFlag" src="'.$config['themeFlagsDir'].'en'.'.gif"><span class="value">'.'1'.'</span></li>';        
        echo '</ul></dd></dl>';
    }
}
*/
function dropdownSystemLanguage(){
    global $config, $user;
    $db = new sqlDB();
    if($langs = $db->qGetAllLanguages()){
        $keys = array_keys($langs);
        echo '<dl class="dropdownSystemLanguage">';
        echo '<dt><span><img class="bigFlag" src="'.$config['themeFlagsDir'].$user->lang.'.gif"></span></dt>';
        echo '<dd><ul>';
        $index = 0;
        while($index < count($langs)){
            echo '<li><img class="bigFlag" src="'.$config['themeFlagsDir'].$langs[$keys[$index]].'.gif"><span class="value">'.$keys[$index].'</span></li>';
            $index++;
        }
        echo '</ul></dd></dl>';
    }
}
/**
 * @name    guestMenu
 * @descr   Print the guest menu on page
 */
function guestMenu(){
    global $tt;

    echo '<ul id="break"></ul>';
}

/**
 * @name    printTestSettings
 * @param   $idSubject     Integer     Subject's ID
 * @descr   print an updated Test Settings dropdown
 */


function printTestSettings($idSubject) {
    global $log;

    $database = new sqlDB();
    if($database->qSelect('TestSettings', 'fkSubject', $idSubject, 'name')){
        if($row = $database->nextRowAssoc()){
            echo '<dt><span>'.$row['name'].'<span class="value">'.$row['idTestSetting'].'</span></span></dt>';
            echo '<dd><ol>';
            echo '<li>'.$row['name'].'<span class="value">'.$row['idTestSetting'].'</span></li>';
            while($row = $database->nextRowAssoc()){
                echo '<li>'.$row['name'].'<span class="value">'.$row['idTestSetting'].'</span></li>';
            }
            echo '</ol></dd>';
        }else{
            echo '<dt><span>------<span class="value">-1</span></span></dt>';
        }
    }else{
        $log->append($database->getError());
        die('1');           // Error: db error
    }
}
/**
 * @name    randomPassword
 * @param   $length     Integer     Length of requested random passoword
 * @return  String
 * @descr   Generate a random password
 */
function randomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

/**
 * @name    help
 * @descr   Print the help menu
 */
function help(){    
    global $config;
    echo '<img class="help" id="help" src="'.$config['themeImagesDir'].'help2.png" onclick="helpjs()">';
}
