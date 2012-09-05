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
class checkTokenOrRegister extends CoreAuthenticationDevice {

  public function __construct() {
    parent::__construct();
  }

  public function __default() {
  }

  public function __authenticationFailed() {
    if($_POST) {
      if($this->usernameExists($_POST['registrationUsername'])) {
        $this->statusCode = STATUS_CODE_AUTHENTICATION_REGISTRATION_FAILED;
        $this->answer = $this->errorHandler->getError('auth', 'device_auth_username_exists');
      } else {
        require_once 'modules/api/registration.php';
        $registration = new registration();
        if($registration->doRegistration($_POST, $_SERVER)) {
          $this->statusCode = STATUS_CODE_REGISTRATION_OK;
        } else {
          $this->statusCode = STATUS_CODE_AUTHENTICATION_REGISTRATION_FAILED;
          $this->answer = $registration->answer;
        }
      }
    } else {
      $this->statusCode = STATUS_CODE_AUTHENTICATION_FAILED;
      $this->answer = $this->errorHandler->getError('auth', 'device_auth_invalid_token');
    }
  }

  public function check() {
    $this->statusCode = STATUS_CODE_OK;
  }
  
  public function usernameExists($username) {
    $username_clean = getCleanedUsername($username);
    $result = pg_execute($this->dbConnection, "get_user_row_by_username_clean", array($username_clean));
    
    if($result && pg_num_rows($result) > 0) {
      return true;
    }
    return false;
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
  
?>
