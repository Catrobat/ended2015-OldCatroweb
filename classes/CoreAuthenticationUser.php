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
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

abstract class CoreAuthenticationUser extends CoreAuthentication {
    function __construct() {
        parent::__construct();
    }
    
    abstract public function __authenticationFailed();

    function authenticate() {
      //return false;
      if($this->session->userLogin_userId > 0) {
        //if($this->session->userLogin_userId > 0 && !$this->isBlockedUser($this->session->userLogin_username)) {
        //todo: fix // isBlockedUser($this->session->userLogin_userNickname);
        return true;
      } else {
        $requesturi = $_SERVER['REQUEST_URI'];
        if(strpos("/", $requesturi) == 0)
          $requesturi = substr($requesturi, 1);
         
        header("Location: ".BASE_PATH."catroid/login?requesturi=".$requesturi);
        exit;
      }
    }

    private function isBlockedUser($user) {
      return true;
      $badUser = false;
      if ($user) {
        $query = "SELECT b.user_id, u.username FROM b.blocked_cusers, u.cusers where u.username like '".$user."'";
        $result = pg_query($this->dbConnection, $query) or die('db query_failed '.pg_last_error());
        if(pg_num_rows($result))
        $badUser = true;
      }
      if ($badUser) {
        $this->errorHandler->showErrorPage('viewer', 'user_is_blocked', '', 'blocked_user');
      }
      return !$badUser;
    }

   function __destruct() {
        parent::__destruct();
    }
}

?>