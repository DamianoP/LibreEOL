<?php
/**
 * File: Log.php
 * User: Masterplan
 * Date: 3/15/13
 * Time: 4:06 PM
 * Desc: Class for system loggin
 */

class Log {

    private $systemLog;

    public function Log($config = null) {
        if($config != null)
            $this->systemLog = $config['systemLog'];
        else
            $this->systemLog = "../logs/system.log";
    }

    public function append($text){
        file_put_contents($this->systemLog, date('m/d/Y H:i:s').' - '.$text."\n", FILE_APPEND | LOCK_EX);
    }
}

class QLog {

    private $qlog;

    public function QLog($config = null) {
        if(config != null)
            $this->qlog = $config['qLog'];
        else
            $this->qlog = "../logs/question.log";
    }

    public function append($text){
        file_put_contents($this->qlog, date('Y/m/d H:i:s').' - '.$text."\n", FILE_APPEND | LOCK_EX);
    }
}
