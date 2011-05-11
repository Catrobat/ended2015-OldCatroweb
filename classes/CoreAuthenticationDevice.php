<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
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

abstract class CoreAuthenticationDevice extends CoreAuthentication {
  function __construct() {
    parent::__construct();
  }

  function authenticate() {
    if(isset($_REQUEST['token'])) {
      $authToken = strtolower($_REQUEST['token']);
      $query = "EXECUTE get_user_device_login('$authToken');";
      $result = pg_query($query);
      if($result && pg_num_rows($result)) {
        $user = pg_fetch_assoc($result);
        if(is_numeric($user['id']) && $user['id'] >= 0) {
          $this->session->userLogin_userId = $user['id'];
          $this->session->userLogin_userNickname = $user['username'];
          return true;
        } else {
          //invalid userID
          $this->statusCode = 603;
          $this->answer = $this->errorHandler->getError('auth', 'device_auth_invalid_user_id');
          $this->jsonAnswer();
          return false;
        }
      } else {
        //no user found for this token; token wrong/outdated
        $this->statusCode = 602;
        $this->answer = $this->errorHandler->getError('auth', 'device_auth_invalid_token');
        $this->jsonAnswer();
        return false;
      }
    } else {
      //POST-var 'token' not set
      $this->statusCode = 601;
      $this->answer = $this->errorHandler->getError('auth', 'device_auth_token_not_set');
      $this->jsonAnswer();
      return false;
    }
  }
  
  private function jsonAnswer() {
    $json = json_encode($this->data);
    header("Content-Type: application/json");
    echo $json;
    exit();
  }

  function __destruct() {
    $this->session->userLogin_userId = 0;
    $this->session->userLogin_userNickname = '';
    parent::__destruct();
  }
}

?>