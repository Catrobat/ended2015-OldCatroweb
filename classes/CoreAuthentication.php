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

abstract class CoreAuthentication extends CoreModule {
  function __construct() {
    parent::__construct();
    isset($_REQUEST["module"]) ? $vmodule = $_REQUEST["module"] : $vmodule = MVC_DEFAULT_MODULE;
    isset($_REQUEST["class"]) ? $vclass = $_REQUEST["class"] : $vclass = MVC_DEFAULT_CLASS;
    
    if (getenv("REMOTE_ADDR"))  {
      $this->requestFromBlockedIp($vmodule, $vclass);
      
      $this->requestFromTemporaryBlockedIp($vmodule, $vclass);
    }
    
    /*echo("UserID: ".$this->session->userLogin_userId);
    if (isset($this->session->userLogin_userId) && $this->session->userLogin_userId > 0) { // , $vmodule, $vclass)) { //todo: fix - user session is unknown at this time
      // $this->errorHandler->showErrorPage('viewer', 'user_is_blocked', '', 'blocked_user');
      $this->requestFromBlockedUser($this->session->userLogin_userId, $vmodule, $vclass);
    }*/
  }

  abstract function authenticate();

  function __destruct() {
    parent::__destruct();
  }

  
  
  private function requestFromBlockedIp($vmodule, $vclass) {
  	$badIp = false;
  	if(($vmodule != "catroid") || in_array($vclass, getUserBlockClassWhitelistArray())) {
  		return;
  	}
  	 
  	$ip = $_SERVER["REMOTE_ADDR"];
  	$query = "SELECT ip_address FROM blocked_ips WHERE substr('$ip', 1, length(ip_address)) = ip_address";
  	$result = pg_query($this->dbConnection, $query) or die('db query_failed '.pg_last_error());
  
  	if(pg_num_rows($result)) {
  		$badIp = true;
  	}
  
  	if ($badIp) {
  		$this->errorHandler->showErrorPage('viewer', 'ip_is_blocked', '');
  	}
  }
  
  
  private function requestFromTemporaryBlockedIp($vmodule, $vclass) {
    $badIp = false;
    if(($vmodule != "catroid") || in_array($vclass, getUserBlockClassWhitelistArray())) {
      return;
    }
     
    $ip = $_SERVER["REMOTE_ADDR"];
    $query = "EXECUTE is_ip_blocked_temporarily('$ip')";
    $result = pg_query($this->dbConnection, $query) or die('db query_failed '.pg_last_error());
    
    if(pg_num_rows($result)) {
      $badIp = true;
    }

    if ($badIp) {
      $this->errorHandler->showErrorPage('viewer', 'ip_is_blocked_temporary', '', 'doAReload');
    }
  }

  
  private function requestFromBlockedUser($userId, $vmodule, $vclass) {
    $badUser = true;
    
    if(($vmodule != "catroid") || in_array($vclass, getIpBlockClassWhitelistArray())) {
      return;
    }
    
    if ($userId) {
        $query = "SELECT b.user_id FROM blocked_cusers b WHERE b.user_id = ".$userId;
        $result = pg_query($this->dbConnection, $query) or die('db query_failed '.pg_last_error());
        if(pg_num_rows($result))
          $badUser = true;
    }
    if ($badUser) {
      $this->errorHandler->showErrorPage('viewer', 'user_is_blocked', '', 'blocked_user');
    }
  }
  
}

?>