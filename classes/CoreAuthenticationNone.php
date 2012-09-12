<?php
/**
 *    Catroid: An on-device graphical programming language for Android devices
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

abstract class CoreAuthenticationNone extends CoreAuthentication {
  public function __construct() {
    parent::__construct();
  }

  public function authenticate() {
    if($this->requestFromBlockedIp()) {
      $this->errorHandler->showErrorPage('viewer', 'ip_is_blocked');
    }
    if(isset($_REQUEST['token']) && strlen($_REQUEST['token']) != 0) {
      $authToken = strtolower($_REQUEST['token']);
      $result = pg_execute($this->dbConnection, "get_user_device_login", array($authToken));
    
      if($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        pg_free_result($result);
    
        if(is_numeric($user['id']) && $user['id'] >= 0) {
          $this->session->userLogin_userId = $user['id'];
          $this->session->userLogin_userNickname = $user['username'];
        }
      }
    }
    return true;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>