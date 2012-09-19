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
  public function __construct() {
    parent::__construct();
    $this->loadModule('common/userFunctions');
  }

  public function __default() {
  }

  public function loginOrRegister() {
    if($_POST) {
      if($this->userFunctions->checkUserExists($_POST['registrationUsername'])) {
        try {
          $username = (isset($_POST['registrationUsername'])) ? checkUserInput(trim($_POST['registrationUsername'])) : '';
          if($username == '') {
            throw new Exception($this->errorHandler->getError('registration', 'username_missing'),
                STATUS_CODE_LOGIN_MISSING_USERNAME);
          }
        
          if(!isset($_POST['registrationPassword']) || $_POST['registrationPassword'] == '') {
            throw new Exception($this->errorHandler->getError('registration', 'password_missing'),
                STATUS_CODE_LOGIN_MISSING_PASSWORD);
          }
        
          $this->userFunctions->login($username, $_POST['registrationPassword']);
        
          if($this->requestFromBlockedIp()) {
            throw new Exception($this->errorHandler->getError('viewer', 'ip_is_blocked'),
                STATUS_CODE_AUTHENTICATION_FAILED);
          }
          if($this->requestFromBlockedUser()) {
            throw new Exception($this->errorHandler->getError('viewer', 'user_is_blocked'),
                STATUS_CODE_AUTHENTICATION_FAILED);
          }
        
          $this->statusCode = STATUS_CODE_OK;
        } catch(Exception $e) {
          $this->userFunctions->logout();
          $this->statusCode = $e->getCode();
          $this->answer = $e->getMessage();
        }
      } else {
        try {
          $this->userFunctions->register($_POST);
          $this->userFunctions->login($_POST['registrationUsername'], $_POST['registrationPassword']);
        
          $this->statusCode = STATUS_CODE_OK;
          $this->answer = $this->languageHandler->getString('registration_success');
        } catch(Exception $e) {
          $this->statusCode = STATUS_CODE_AUTHENTICATION_REGISTRATION_FAILED;
          $this->answer = $e->getMessage();
        }
      }
    }
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
