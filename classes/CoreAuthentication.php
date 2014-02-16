<?php
/**
  * Catroid: An on-device visual programming system for Android devices
  * Copyright (C) 2010-2014 The Catrobat Team
  * (<http://developer.catrobat.org/credits>)
  * 
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU Affero General Public License as
  * published by the Free Software Foundation, either version 3 of the
  * License, or (at your option) any later version.
  * 
  * An additional term exception under section 7 of the GNU Affero
  * General Public License, version 3, is available at
  * http://developer.catrobat.org/license_additional_term
  * 
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU Affero General Public License for more details.
  * 
  * You should have received a copy of the GNU Affero General Public License
  * along with this program. If not, see <http://www.gnu.org/licenses/>.
  */


abstract class CoreAuthentication extends CoreModule {
  public function __construct() {
    parent::__construct();
  }

  abstract function authenticate();

  public function __destruct() {
    parent::__destruct();
  }

  protected function requestFromBlockedIp() {
    $vmodule = (isset($_REQUEST["module"]) ? $_REQUEST["module"] : MVC_DEFAULT_MODULE);
    $vclass = (isset($_REQUEST["class"]) ? $_REQUEST["class"] : MVC_DEFAULT_CLASS);

    if(($vmodule == "admin") || in_array($vclass, getIpBlockClassWhitelistArray())) {
      return false;
    }

    if(isset($_SERVER['REMOTE_ADDR'])) {
      $ip = $_SERVER['REMOTE_ADDR'];
       
      $result = pg_execute($this->dbConnection, "admin_is_blocked_ip", array($ip));
      if($result && pg_num_rows($result) > 0) {
        pg_free_result($result);
        return true;
      }
    }
    return false;
  }

  protected function requestFromTemporarilyBlockedIp() {
    $vmodule = (isset($_REQUEST["module"]) ? $_REQUEST["module"] : MVC_DEFAULT_MODULE);
    $vclass = (isset($_REQUEST["class"]) ? $_REQUEST["class"] : MVC_DEFAULT_CLASS);

    if(isset($_SERVER['REMOTE_ADDR'])) {
      $ip = $_SERVER['REMOTE_ADDR'];
       
      $result = pg_execute($this->dbConnection, "is_ip_blocked_temporarily", array($ip));
      if($result && pg_num_rows($result) > 0) {
        pg_free_result($result);
        return true;
      }
    }
    return false;
  }

  protected function requestFromBlockedUser() {
    $vmodule = (isset($_REQUEST["module"]) ? $_REQUEST["module"] : MVC_DEFAULT_MODULE);
    $vclass = (isset($_REQUEST["class"]) ? $_REQUEST["class"] : MVC_DEFAULT_CLASS);

    if(($vmodule == "admin") || in_array($vclass, getIpBlockClassWhitelistArray())) {
      return false;
    }

    if($this->session->userLogin_userId > 0) {
      $result = pg_execute($this->dbConnection, "admin_is_blocked_user_by_id", array($this->session->userLogin_userId));
      if($result && pg_num_rows($result) > 0) {
        pg_free_result($result);
        return true;
      }
    }
    return false;
  }
}

?>
