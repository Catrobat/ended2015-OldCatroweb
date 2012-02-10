<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class loginOrRegister extends CoreAuthenticationNone {

  public $login = null;
  public $registration = null;
  
  public function __construct() {
    parent::__construct();
    $this->setupBoard();
  }

  public function __default() {
  }

  public function loginOrRegister() {
    if($_POST) {
      if($this->usernameExists($_POST['registrationUsername'])) {
        $this->answer .= "username exists: ".$_POST['registrationUsername'];
        
        $this->statusCodePart = "login";        
        
        require_once 'modules/api/login.php';
        $login = new login();
        if($login->doLogin(array('loginUsername'=>$_POST['registrationUsername'], 'loginPassword'=>$_POST['registrationPassword']))) {
          $this->answer .= $login->answer;
          $this->statusCode = 200;
          return true;
        } else {
          $this->answer .= $login->answer;
          $this->statusCode = 500;
          return false;
        }
      }
      else {
        $this->answer .= "username NOT exists: ".$_POST['registrationUsername'];

        $this->statusCodePart = "registration";
        
        require_once 'modules/api/registration.php';
        $registration = new registration();
        if($registration->doRegistration(array('registrationUsername'=>$_POST['registrationUsername'], 
        																			 'registrationPassword'=>$_POST['registrationPassword'],
        																			 'registrationEmail'=>$_POST['registrationEmail'],
                                               'registrationCountry'=>$_POST['registrationCountry']), $_SERVER)) {
          $this->answer .= $registration->answer;
          $this->statusCode = 200;
          return true;
        } else {
          $this->answer .= $registration->answer;
          $this->statusCode = 500;
          return false;
        }
      }
    }
  }
  
  private function usernameExists($user) {
    //get_user_row_by_username_clean
    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');

    $username_clean = utf8_clean_string($user);
    $user = trim($user);

    $result = pg_execute($this->dbConnection, "get_user_row_by_username_clean", array($username_clean)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(!$result) {
      $this->statusCode = 503; //601
      return false;
    }
    if(pg_num_rows($result) > 0) {
      return true;
    } else {
      return false;
    }
    
  }
  
  

  public function __destruct() {
    parent::__destruct();
  }
}
?>
