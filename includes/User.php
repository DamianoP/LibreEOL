<?php
/**
 * File: User.php
 * User: Masterplan
 * Date: 3/15/13
 * Time: 3:26 PM
 * Desc: Class with all user informations
 */

class User {

    // User informations
    public $id;         // User's ID on database
    public $name;       // User's name
    public $surname;    // User's surname
    public $email;      // User's e-mail
    public $lang;       // System language selected by user
    public $role;       // User's role Student (s), Teacher (t), Admin (a) or Teacher/Admin (ta)

    /**
     * @name    User
     * @param   $result   Array     User's information recovered from dabatase
     * @descr   Create an User class instance
     */
    public function User($result=null){
        global $config;

        // Initialize class vars
        if($result != null){
            $this->id       = $result['id'];
            $this->name     = $result['name'];
            $this->surname  = $result['surname'];
            $this->email    = $result['email'];
            $this->lang     = $result['lang'];
            $this->role     = $result['role'];
        }else{
            $this->id       = '';
            $this->name     = '';
            $this->surname  = '';
            $this->email    = '';
            $this->lang     = $config['systemLang'];
            $this->role     = '?';
        }

    }

}