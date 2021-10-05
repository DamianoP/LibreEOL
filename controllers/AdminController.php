<?php
/**
 * File: UserController.php
 * User: Masterplan
 * Date: 4/19/13
 * Time: 10:04 AM
 * Desc: Controller for all Admin's operations
 */

class AdminController extends Controller{

    /**
     *  @name   AdminController
     *  @descr  Creates an instance of AdminController class
     */
    public function AdminController(){}

    /**
     * @name    executeAction
     * @param   $action         String      Name of requested action
     * @descr   Executes action (if exists and if user is allowed)
     */
    public function executeAction($action){
        global $user, $log;

        // If have necessary privileges execute action
        if ($this->getAccess($user, $action, $this->accessRules())) {
            $action = 'action'.$action;
            $this->$action();
            // Else, if user is not logged bring him the to login page
        }elseif($user->role == '?'){
            header('Location: index.php?page=login');
            // Otherwise: Access denied
        }else{
            Controller::error('AccessDenied');
        }
    }

    /**
     *  @name   actionIndex
     *  @descr  Shows admin index page
     */
    private function actionIndex(){
        global $engine, $user;

        $user->role = 'a';
        $_SESSION['user'] = serialize($user);

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionExit
     *  @descr  Exits from Admin, resets Teacher+Admin role and shows (Teacher) index page
     */
    private function actionExit(){
        global $user;

        $db = new sqlDB();
        if($db->qSelect('Users', 'idUser', $user->id)){
            if($row = $db->nextRowAssoc()){
                if($row['role'] == 'at'){
                    $user->role = 'at';
                    $_SESSION['user'] = serialize($user);
                }
                header('Location: index.php');
            }else{
                die(ttEUserNotFound);
            }
        }else{
            die($db->getError());
        }
    }

    /*******************************************************************
     ********************************************************************
     ***                                                              ***
     ***                            System                            ***
     ***                                                              ***
     ********************************************************************
     *******************************************************************/

    /**
     *  @name   actionSystemconfiguration
     *  @descr  Shows system's configuration page
     */
    private function actionSystemconfiguration(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionUpdatesystemconfiguration
     *  @descr  Update system configuration
     */
    private function actionUpdatesystemconfiguration(){
        global $log;

        $configFile = fopen("../includes/config.php", "r") or die("Unable to open file!");
        $configText = fread($configFile, filesize("../includes/config.php"));
        fclose($configFile);


        try{

            $configText = preg_replace("#themeName'[ ]*][ ]*=[ ]*'.*'#", "themeName'] = '".$_POST['skin']."'", $configText);
            $configText = preg_replace("#systemLogo'[ ]*][ ]*=[ ]*'.*'#", "systemLogo'] = '".$_POST['logo']."'", $configText);
            $configText = preg_replace("#systemTitle'[ ]*][ ]*=[ ]*'.*'#", "systemTitle'] = '".$_POST['title']."'", $configText);
            $configText = preg_replace("#systemHome'[ ]*][ ]*=[ ]*'.*'#", "systemHome'] = '".$_POST['home']."'", $configText);
            $configText = preg_replace("#systemEmail'[ ]*][ ]*=[ ]*'.*'#", "systemEmail'] = '".$_POST['email']."'", $configText);
            $configText = preg_replace("#systemLang'[ ]*][ ]*=[ ]*'.*'#", "systemLang'] = '".$_POST['language']."'", $configText);
            $configText = preg_replace("#systemTimeZone'[ ]*][ ]*=[ ]*'.*'#", "systemTimeZone'] = '".$_POST['timezone']."'", $configText);
            $configText = preg_replace("#dbType'[ ]*][ ]*=[ ]*'.*'#", "dbType'] = '".$_POST['dbType']."'", $configText);
            $configText = preg_replace("#dbHost'[ ]*][ ]*=[ ]*'.*'#", "dbHost'] = '".$_POST['dbHost']."'", $configText);
            $configText = preg_replace("#dbPort'[ ]*][ ]*=[ ]*'.*'#", "dbPort'] = '".$_POST['dbPort']."'", $configText);
            $configText = preg_replace("#dbName'[ ]*][ ]*=[ ]*'.*'#", "dbName'] = '".$_POST['dbName']."'", $configText);
            $configText = preg_replace("#dbUsername'[ ]*][ ]*=[ ]*'.*'#", "dbUsername'] = '".$_POST['dbUsername']."'", $configText);
            $configText = preg_replace("#dbPassword'[ ]*][ ]*=[ ]*'.*'#", "dbPassword'] = '".$_POST['dbPassword']."'", $configText);

            $configFile = fopen("../includes/config.php", "w") or die("Unable to open file!");
            fwrite($configFile, $configText);
            fclose($configFile);

            echo "ACK";

        }catch(Exception $ex){
            $log->append(var_export($ex, true));
            echo var_export($ex, true);
        }

    }

     /*******************************************************************
     ********************************************************************
     ***                                                              ***
     ***                           Languages                          ***
     ***                                                              ***
     ********************************************************************
     *******************************************************************/

    /**
     *  @name   actionSelectlanguage
     *  @descr  Shows language selection page
     */
    private function actionSelectlanguage(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionLanguage
     *  @descr  Shows language edit page
     */
    private function actionLanguage(){
        global $engine;

        if(isset($_POST['alias'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header('Location: index.php?page=admin/selectlanguage');
        }
    }
    /**
     *  @name   actionEmail
     *  @descr send mail
     *
     */
    private function actionErroremail(){
        global $user, $log;

        if((isset($_POST['idquestion'])) && (isset($_POST['notes'])) ) {

            if ($_POST['idquestion'] == '') { //se non inseriscono id quindi solo una segnalazione varia
                $to = ""; //destinatatio email
                $subject = "Segnalazione da LibreEol";//oggetto
                $message = $_POST['notes'];//note
                $headers = 'from' . $user->email . "\r\n" .
                    'Reply-To:' . $user->email . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

            } else {

                $to = "osvaldo.gervasi@unipg.it"; //destinatatio email
                $subject = "Modifica domanda n." . $_POST['idquestion']; //oggetto email
                $message = $_POST['notes'];//note
                $headers = 'from' . $user->email . "\r\n" .
                    'Reply-To:' . $user->email . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

            }

            if (!mail($to, $subject, $message, $headers)) {
                $log->append("Errore. Nessun messaggio inviato.");
                echo "NACK";
            }
            else{
                $log->append("MSG INVIATO CORRETTAMENTE");
                echo "ACK";
            }

        }
        else
            echo "NACK";

    }
    /**
     *  @name   actionSavelanguage
     *  @descr  Saves XML or PHP/Javascript file of requested language
     */
    private function actionSavelanguage(){
        global $log, $config;

        if((isset($_POST['alias'])) && (isset($_POST['constants'])) && (isset($_POST['translations']))){

            global $log, $config;
            $xml = new DOMDocument('1.0', 'utf-8');
             $xml->formatOutput = true;
             $xml->preserveWhiteSpace = false;
            $xml->load($config['systemLangsXml'].$_POST['alias'].'.xml');
            $constants = json_decode($_POST['constants'], true);
            $translations = json_decode($_POST['translations'], true);
	    for($index = 0; $index < count($constants); $index++)
                if($xml->getElementById($constants[$index]))
                    $xml->getElementById($constants[$index])->nodeValue = str_replace("\n", '\n', $translations[$index]);
                else{
                    $language = $xml->getElementsByTagName("language")->item(0);
                    $newString=$xml->createElement('text',$translations[$index]);
                    $attribute=$xml->createAttribute('id');
                    $attribute->value =$constants[$index];
                    $newString->appendChild($attribute);
                    $language->appendChild($newString);
                }
            if($xml->save($config['systemLangsXml'].$_POST['alias'].'.xml')){
                $xml = new DOMDocument();
                $phpText = "<?php\n";
                $jsText = "\n";
                $xml->load($config['systemLangsXml'].$_POST['alias'].'.xml');
                $texts = $xml->getElementsByTagName('text');

                for($index = 0; $index < $texts->length; $index++){

                    $id = $texts->item($index)->getAttribute('id');
                    if (strpos($id,'ttMail') === false)
                        $value = str_replace('\n', '<br/>', $texts->item($index)->nodeValue);
                    else
                        $value = $texts->item($index)->nodeValue;

                    $jsText .= "var $id = \"".str_replace('"', '\"', $value)."\";\n";

                    $phpText .= "define('$id' , \"".str_replace('"', '\"', $value)."\");\n";
                }
                if (!file_exists($config['systemLangsDir'].$_POST['alias'])) {
                    mkdir($config['systemLangsDir'].$_POST['alias'], 0766, true);
                }
		else{
		  if (file_exists($config['systemLangsDir'].$_POST['alias'].'/lang.php')) 
			unlink($config['systemLangsDir'].$_POST['alias'].'/lang.php');
		  if (file_exists($config['systemLangsDir'].$_POST['alias'].'/lang.js')) 
			unlink($config['systemLangsDir'].$_POST['alias'].'/lang.js');
		}

                $fP = fopen($config['systemLangsDir'].$_POST['alias'].'/lang.php', "w");
                chmod($config['systemLangsDir'].$_POST['alias'].'/lang.php', 0766);
                $fJ = fopen($config['systemLangsDir'].$_POST['alias'].'/lang.js', "w");
                chmod($config['systemLangsDir'].$_POST['alias'].'/lang.js', 0766);

                $write = false;
                $attemps = 10;

                while(($attemps > 0) && (!$write)){
                    if((flock($fP, LOCK_EX)) && (flock($fJ, LOCK_EX))){
                        ftruncate($fP, 0);
                        ftruncate($fJ, 0);
                        fwrite($fP, $phpText);
                        fwrite($fJ, $jsText);
                        fflush($fP);
                        fflush($fJ);
                        flock($fP, LOCK_UN);
                        flock($fJ, LOCK_UN);
                        $write = true;
                    }else{
                        $attemps--;
                    }
                }

                fclose($fP);
                fclose($fJ);

                echo 'ACK';
            }else
                echo 'If the files are well formatted, write permissions are not enough, give chmod 0766 to '.$config['systemLangsXml'].$_POST['alias'].'.xml';

        }else
            $log->append(__FUNCTION__." : Params not set - ".var_export($_POST));
    }

    /**
     *  @name   actionNewlanguage
     *  @descr  Creates a new XML language file
     */
    private function actionNewlanguage(){
        global $engine, $log, $config;

        if((isset($_POST['description'])) && (isset($_POST['alias']))){

            $alias = strtolower($_POST['alias']);
            $description = ucfirst(strtolower($_POST['description']));

            if(file_exists($config['systemLangsDir'].$alias.'/')){
                echo '0';
            }else{
                $db = new sqlDB();
                if($db->qCreateLanguage($alias, $description)){
                    if((mkdir($config['systemLangsDir'].$alias.'/')) &&
                       (copy($config['systemLangsDir'].'en/lang.php', $config['systemLangsDir'].$alias.'/lang.php')) &&
                       (copy($config['systemLangsDir'].'en/lang.js', $config['systemLangsDir'].$alias.'/lang.js')) &&
                       (copy($config['systemLangsXml'].'en.xml', $config['systemLangsXml'].$alias.'.xml'))){
                        $xml = new DOMDocument();
                        $xml->load($config['systemLangsXml'].$alias.'.xml');
                        $xml->getElementById('alias')->nodeValue = $alias;
                        $xml->getElementById('name')->nodeValue = $description;
                        $xml->save($config['systemLangsXml'].$alias.'.xml');
                        echo 'ACK';
                    }else{
                        unlink($config['systemLangsDir'].$alias.'/lang.php');
                        unlink($config['systemLangsDir'].$alias.'/lang.js');
                        unlink($config['systemLangsXml'].$alias.'.xml');
                        rmdir($config['systemLangsDir'].$alias.'/');
                    }
                }else{
                    echo ttEDatabase;
                }
            }
        }else{
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }
    }

     /*******************************************************************
     ********************************************************************
     ***                                                              ***
     ***                             Rooms                            ***
     ***                                                              ***
     ********************************************************************
     *******************************************************************/

    /**
     *  @name   actionDeleteroom
     *  @descr  Deletes requested room
     */
    private function actionDeleteroom(){
        global $log;

        if(isset($_POST['idRoom'])){
            if($_POST['idRoom'] != '0'){
                $db = new sqlDB();
                if($db->qDeleteRoom($_POST['idRoom'])){
                    if($db->numAffectedRows() > 0){
                        echo 'ACK';
                    }else{
                        die(ttERoomUsed);   // Error: Room used by at least one exam
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttERoomAllDelete);      // Error: 'All' cannot be deleted
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewroom
     *  @descr  Shows page to create a new room
     */
    private function actionNewroom(){
        global $engine;

        if((isset($_POST['name'])) && (isset($_POST['desc'])) &&
            (isset($_POST['ipStart'])) && (isset($_POST['ipEnd']))){

            $db = new sqlDB();
            $ipStart = ip2long($_POST['ipStart']);
            $ipEnd = ip2long($_POST['ipEnd']);
            if($db->qNewRoom($_POST['name'], $_POST['desc'], $ipStart, $ipEnd)){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }
    }

    /**
     *  @name   actionRooms
     *  @descr  Shows rooms edit page
     */
    private function actionRooms(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionShowroominfo
     *  @descr  Shows info about a room
     */
    private function actionShowroominfo(){
        global $log;

        if(isset($_POST['idRoom'])){
            $db = new sqlDB();
            if($db->qSelect('Rooms', 'idRoom', $_POST['idRoom'])){
                if($row = $db->nextRowEnum()){
                    $row['3'] = long2ip($row['3']);
                    $row['4'] = long2ip($row['4']);
//                     echo json_encode($row, JSON_UNESCAPED_UNICODE);   // For PHP >= 5.4.0
                    $json = str_replace('\\/', '/', json_encode($row));  // Use this for
                    echo $json;                                          // PHP < 5.4.0
                }
            }else{
                echo "NACK";
                $log->append(__FUNCTION__." : ".$db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdateroominfo
     *  @descr  Saves edited informations about a room
     */
    private function actionUpdateroominfo(){
        global $log;

        if((isset($_POST['idRoom'])) &&
            (isset($_POST['name'])) && (isset($_POST['desc'])) &&
            (isset($_POST['ipStart'])) && (isset($_POST['ipEnd']))){
            if($_POST['idRoom'] != '0'){
                $db = new sqlDB();
                $ipStart = ip2long($_POST['ipStart']);
                $ipEnd = ip2long($_POST['ipEnd']);
                if($db->qUpdateRoomInfo($_POST['idRoom'], $_POST['name'], $_POST['desc'], $ipStart, $ipEnd)){
                    if($db->numAffectedRows() > 0){
                        echo "ACK";
                    }else{
                        die(ttERoomUsed);     // Error: Room used by at least one exam
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEEditRoomAll);          // Error: 'All' cannot be edited
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

     /*******************************************************************
     ********************************************************************
     ***                                                              ***
     ***                             Users                            ***
     ***                                                              ***
     ********************************************************************
     *******************************************************************/

    /**
     *  @name   actionLostpassword
     *  @descr  Shows form for reset account password
     */
    private function actionLostpassword(){
        global $log, $config, $engine;
        if(isset($_POST['email'])){
            $db = new sqlDB();
            if(($db->qSelect('Users', 'email', $_POST['email'])) && ($db->numResultRows() != 0)){
                $token = randomPassword(10).strtotime('now');
                $token = sha1($token);
                if($db->qNewToken($_POST['email'], 'p', $token)){
                    $message = str_replace('_LINK_', $config['systemHome'].'index.php?page=admin/setpassword&t='.$token, ttMailLostPassword);
                    $message = str_replace('\n', "\n", $message);
                    mail($_POST['email'], ttResetPassword, $message,'From: '.$config['systemTitle'].' <'.$config['systemEmail'].'>','-f '.$config['systemEmail']);

                    echo 'ACK';
                }
            }else
                die(ttEEmailNotRegistered); // Error: Email not registered
        }else{
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }
    }

    /**
     *  @name   actionNewteacher
     *  @descr  Shows form to add new teacher/teacher-administrator
     */
    private function actionNewteacher(){
        global $log, $config, $engine;

        if((isset($_POST['name'])) && (isset($_POST['surname'])) &&
           (isset($_POST['email'])) && (isset($_POST['role']))){
            $db = new sqlDB();
            if(($db->qSelect('Users', 'email', $_POST['email'])) && ($db->numResultRows() == 0)){
                $token = sha1(randomPassword(10).strtotime('now'));
                if($db->qNewUser($_POST['name'], $_POST['surname'], $_POST['email'], $token, $_POST['role'], $_POST['group'],$_POST['subgroup'])){
                    $message = str_replace('_SYSTEMNAME_', $config['systemTitle'], ttMailNewTeacher);
                    $message = str_replace('\n', "\n", $message);
                    $message .= "\n\n".$config['systemHome'].'index.php?page=admin/setpassword&t='.$token;
                    mail($_POST['email'], ttAccountActivation, $message,'From: '.$config['systemTitle'].' <'.$config['systemEmail'].'>','-f '.$config['systemEmail']);

                    echo 'ACK';
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEEmailAlreadyRegistered);   // Error: Email already registered
            }
        }else{
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }
    }

    /**
     *  @name   actionNewstudent
     *  @descr  Shows form to add new student
     */
    private function actionNewstudent(){
        global $log, $config, $engine, $user, $ajaxSeparator;

        if((isset($_POST['name'])) && (isset($_POST['surname'])) &&
           (isset($_POST['email'])) && (isset($_POST['password']))){
            $db = new sqlDB();
            if(($db->qSelect('Users', 'email', $_POST['email'])) && ($db->numResultRows() == 0)){
                if($user->role == '?'){
                    $password = $_POST['password'];
                }else{
                    $password = randomPassword(8);
                }
                if(($db->qNewUser($_POST['name'], $_POST['surname'], $_POST['email'], null, 's', $_POST['group'], $_POST['subgroup'], sha1($password))) & ($student = $db->nextRowEnum())){
                    $message = str_replace('_USERNAME_', $_POST['name'], ttMailCredentials);
                    $message = str_replace('_USEREMAIL_', $_POST['email'], $message);
                    $message = str_replace('_USERPASSWORD_', $password, $message);
                    $message = str_replace('\n', "\n", $message);
                    mail($_POST['email'], ttAccountActivation, $message,'From: '.$config['systemTitle'].' <'.$config['systemEmail'].'>','-f '.$config['systemEmail']);

                    if($user->role == '?'){
                        if($userInfo = $db->qLogin($_POST['email'], sha1($_POST['password']))){
                            if($userInfo != null){
                                $_SESSION['logged'] = true;
                                $_SESSION['user'] = serialize(new User($userInfo));
                            }
                        }else{
                            die($db->getError());
                        }
                    }

                    echo 'ACK'.$ajaxSeparator.$student[0];
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEEmailAlreadyRegistered);   // Error: Email already registered
            }
        }else{
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }
    }

    /**
     *  @name   actionProfile
     *  @descr  Shows profile page of user's account
     */
    private function actionProfile(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionSetpassword
     *  @descr  Shows page to insert the first password and activate user's account
     *          or sets a new password after reset operation
     */
    private function actionSetpassword(){
        global $log, $engine, $config, $user;

        if(isset($_GET['t'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }elseif((isset($_POST['token'])) && (isset($_POST['password']))){
            $db = new sqlDB();
            if(($db->qSelect('Tokens', 'value', $_POST['token'])) && ($token = $db->nextRowAssoc()) &&
               ($db->qSelect('Users', 'email', $token['email'])) && ($userInfo = $db->nextRowAssoc()) &&
               ($db->qUpdateProfile($userInfo['idUser'], null, null, null,null,null, sha1($_POST['password']))) &&
               ($db->qDelete('Tokens', 'value', $_POST['token']))){
                $message = str_replace('_USERNAME_', $userInfo['name'], ttMailCredentials);
                $message = str_replace('_USEREMAIL_', $userInfo['email'], $message);
                $message = str_replace('_USERPASSWORD_', $_POST['password'], $message);
                $message = str_replace('\n', "\n", $message);
                mail($userInfo['email'], ttNewCredentials, $message,'From: '.$config['systemTitle'].' <'.$config['systemEmail'].'>','-f '.$config['systemEmail']);

                if($user->role == '?'){
                    if($userLog = $db->qLogin($userInfo['email'], sha1($_POST['password']))){
                        if($userLog != null){
                            $_SESSION['logged'] = true;
                            $_SESSION['user'] = serialize(new User($userLog));
                        }
                    }else{
                        die($db->getError());
                    }
                }

                echo 'ACK';
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }
    private function actionErrorquestion(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionUpdateprofile
     *  @descr  Updates user's information
     */
    private function actionUpdateprofile(){
        global $user, $log;

        if((isset($_POST['name'])) && (isset($_POST['surname'])) &&
           (isset($_POST['oldPassword'])) && (isset($_POST['newPassword']))){

            $db = new sqlDB();
            $password = null;
            if(($_POST['oldPassword'] != '') && ($_POST['newPassword'] != '')){
                if(($db->qSelect('Users', 'idUser', $user->id)) && ($userInfo = $db->nextRowAssoc())){
                    if($userInfo['password'] == sha1($_POST['oldPassword'])){
                        $password = sha1($_POST['newPassword']);
                    }else{
                        die(ttEOldPasswordWrong);
                    }
                }else{
                    die($db->getError());
                }
            }
            if($db->qUpdateProfile($user->id, $_POST['name'], $_POST['surname'], $_POST['group'], $_POST['subgroup'], null, $password)){
                $user->name = $_POST['name'];
                $user->surname = $_POST['surname'];
                $_SESSION['user'] = serialize($user);
                echo 'ACK';
            }else{
                die(ttEDatabase);
            }
        }elseif(isset($_POST['lang'])){
            $db = new sqlDB();
            if($db->qUpdateProfile($user->id, null, null, null, null, null, null, $_POST['lang'])){
                if(($db->qSelect('Languages')) && ($allLangs = $db->getResultAssoc('idLanguage'))){
                    $user->lang = $allLangs[$_POST['lang']]['alias'];
                    $_SESSION['user'] = serialize($user);
                    echo 'ACK';
                }else{
                    die(ttEDatabase);
                }
            }else{
                die(ttEDatabase);
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionEditstudent
     *  @descr  Shows EditStudentPage
     */

    private function actionEditstudent(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionEditteacher
     *  @descr  Shows Edit Admin/Teacher Page
     */
    private function actionEditteacher(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionShowstudentinfo
     *  @descr  Show info about a student
     */
    private function actionShowstudentinfo(){
        global $engine, $user, $log;

        if((isset($_POST['action'])) && (isset($_POST['idStudent']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShowteacherinfo
     *  @descr  Show info about a admin/teacher
     */
    private function actionShowteacherinfo(){
        global $engine, $user, $log;

        if((isset($_POST['action'])) && (isset($_POST['idTeacher']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdatestudentinfo
     *  @descr  Edit the student in the db
     */
    private function actionUpdatestudentinfo(){
        global $log;
        if((isset($_POST['name'])) && (isset($_POST['surname'])) &&
            (isset($_POST['email'])) && (isset($_POST['group'])) && 
            (isset($_POST['subgroup'])) && (isset($_POST['idStudent']))
            && (isset($_POST['password']))){
            $db = new sqlDB();
            if($db->qUpdateStudentInfo($_POST['idStudent'],$_POST['name'], $_POST['surname'], $_POST['email'], $_POST['group'],$_POST['subgroup'],$_POST['role'],$_POST['password'])){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdatestudentinfo
     *  @descr  Edit the admin/teacher in the db
     */
    private function actionUpdateteacherinfo(){
        global $log;

        if((isset($_POST['name'])) && (isset($_POST['surname'])) &&  (isset($_POST['role'])) &&
            (isset($_POST['email'])) && (isset($_POST['group'])) && (isset($_POST['subgroup'])) && (isset($_POST['idTeacher']))){
            $db = new sqlDB();
            if($db->qUpdateTeacherInfo($_POST['idTeacher'],$_POST['name'], $_POST['surname'], $_POST['email'], $_POST['group'],$_POST['subgroup'],$_POST['role'])){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }


    /**
     *  @name   actionDeleteuser
     *  @descr  Delete user from the db
     */

    private function actionDeleteuser(){
        global $log;
        if(isset($_POST['idUser'])){
            $db = new sqlDB();
            if($db->qDeleteUser($_POST['idUser'])){
                echo 'ACK';
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewGroup
     *  @descr  Shows addgroup page
     */
    private function actionNewgroup(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionAddnewgroup
     *  @descr  Add a new group
     */

    private function actionAddnewgroup(){
        global $log;

        if(isset($_POST['group'])){
            $db = new sqlDB();
            if($db->qNewGroup($_POST['group'])){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionAddnewsubgroup
     *  @descr  Add a new subgroup
     */
    private function actionNewsubgroup(){
        global $log;

        if(isset($_POST['group']) && isset($_POST['subgroup']) && isset($_POST['description']) ){
            $db = new sqlDB();
            if($db->qNewSubgroup($_POST['group'], $_POST['subgroup'],$_POST['description'])){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewGroup
     *  @descr  Shows addgroup page
     */
    private function actionEditgroup(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionShowgroupinfo
     *  @descr  Show showgroupinfo page
     */
    private function actionShowgroupinfo(){
        global $engine, $user, $log;

        if((isset($_POST['action']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdategroupinfo
     *  @descr  Updates the group info
     */
    private function actionUpdategroupinfo(){
        global $log;

        if((isset($_POST['idGroup']))&& (isset($_POST['groupName']))){
            $db = new sqlDB();
            if($db->qUpdateGroupInfo($_POST['idGroup'],$_POST['groupName'])){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdatesubgroupinfo
     *  @descr  Updates the subgroup info
     */
    private function actionUpdatesubgroupinfo(){
        global $log;
        if((isset($_POST['idSubgroup']))&& (isset($_POST['subgroupName'])) && (isset($_POST['fkGroup'])) && (isset($_POST['description']))){
            $db = new sqlDB();
            if($db->qUpdateSubgroupInfo($_POST['idSubgroup'],$_POST['subgroupName'], $_POST['fkGroup'], $_POST['description'])){
                echo "ACK";
            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    private function actionAcceptprivacy(){
        global $user, $log;
        $db = new sqlDB();
        if($db->qAcceptPrivacy())
            echo "ACK";
        else
            echo "NACK";
    }

    /**
     * @name   accessRules
     * @descr  Returns all access rules for User controller's actions:
     *  array(
     *     array(
     *       (allow | deny),                                     Parameter
     *       'actions' => array('*' | 'act1', ['act2', ....]),   Actions
     *       'roles'   => array('*' | '?' | 'a' | 't' | 's')     User's Role
     *     ),
     *  );
     * @return array
     */
    private function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('Index', 'Exit', 'Newteacher', 'Rooms', 'Showroominfo', 'Newroom', 'Updateroominfo',
                                   'Deleteroom','Selectlanguage', 'Language', 'Savelanguage', 'Newlanguage', 'Systemconfiguration',
                                   'Updatesystemconfiguration','Editstudent','Showstudentinfo','Updatestudentinfo',
                                    'Editteacher','Showteacherinfo','Updateteacherinfo','Newsubgroup','Newgroup','Addnewgroup',
                                    'Showgroupinfo','Editgroup','Updategroupinfo','Updatesubgroupinfo','Deleteuser'
            ),
                'roles'   => array('a'),
            ),
            array(
                'allow',
                'actions' => array('Profile', 'Updateprofile','Acceptprivacy'),
                'roles'   => array('a', 't', 's','e'),
            ),
            array(
                'allow',
                'actions' => array('Newstudent'),
                'roles'   => array('?', 'a', 't'),
            ),
            array(
                'allow',
                'actions' => array('Setpassword', 'Lostpassword'),
                'roles'   => array('?'),
            ),
            array(
                'allow',
                'actions' => array('Errorquestion', 'Erroremail'),
                'roles'   => array('a','e','t'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }
}


