<?php
/**
 * File: logintest.php
 * User: Masterplan
 * Date: 5/2/13
 * Time: 11:02 PM
 * Desc: Shows login form to requested exam, after client verification
 */

global $config, $user;

$db = new sqlDB();
if($db->qRoomsExam($_POST['idExam'])){
    // Verify if student's client is authorized
    $ipAddress = $_SERVER['REMOTE_ADDR'] == "::1" ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
    $ipAddress = ip2long($ipAddress);
    if($db->numResultRows() == 0)
        $authorized = true;
    else{
        $authorized = false;
        while(($room = $db->nextRowAssoc()) && (!$authorized)){
            $authorized = checkIP($ipAddress, $room['ipStart'], $room['ipEnd']);
        }
    }
    if($authorized){
        if($db->qCheckRegistration($_POST['idExam'], $user->id)){
            if($test = $db->nextRowAssoc()){
                switch($test['status']){
                    case 's' : // The student already has a questions set, so set idSet SESSION
                               // and go to test without password check
                               $_SESSION['idSet'] = $test['fkSet'];
                               header("Location: index.php?page=student/test"); break;
                    case 'e' :
                    case 'a' : // Test already submitted, exit
                               die(ttETestAlreadySubmitted); break;
                    case 'b' : // Test Blocked
                               die(ttBlocked); break;
                    default  : // If user doesn't has a question's set in his test, show password form ?>
                               <div id="passwordDiv">
                                   <form name="login" class="login" id="passwordForm">
                                       <label for="password"><?= ttPassword ?></label>
                                       <input type="password" class="text" name="password" value="" id="password" />
                                       <p id="result"></p>
                                       <input type="hidden" name="idExam" value="<?= $_POST['idExam'] ?>" id="idExam"/>
                                       <a class="normal button" id="startTest" onclick="startTest();"><?= ttStartTest ?></a>
                                   </form>
                               </div>
                               <div id="dialogError"><p></p></div>
                               <div id="dialogConfirm"><p></p></div>
                <?php
                }
            }else{
                die(ttETestNotFound);
            }
        }else{
            die(ttEDatabase);
        }
    }else{
        die(ttENotAuthorized);
    }
}

/**
 * @name    checkIP
 * @param   $ip         Integer     ip2long Client IP address
 * @param   $start      Integer     ip2long Start IP address
 * @param   $end        Integer     ip2long End IP address
 * @return  String
 * @descr   Check the IP address range
 */
function checkIP($ip, $start, $end){
    $ack = false;
    if(($start == 0) && ($end == 0)){
        $ack = true;
    }elseif($ip >= $start && $ip <= $end){
        $ack = true;
    }
    return $ack;
}