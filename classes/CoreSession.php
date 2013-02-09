<?php
/**
  * Catroid: An on-device visual programming system for Android devices
  * Copyright (C) 2010-2013 The Catrobat Team
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

class CoreSession {
  private static $instance;
  public static $sessionID;

  private function __construct() {
    if(session_id() == "") {
      session_name('catroweb');
      // set cookie params:     ($lifetime, $path, $domain, $secure, $httponly);
      session_set_cookie_params(SESSION_LIFETIME, '/', substr(BASE_PATH,strpos(BASE_PATH,'://') + 3, -1), false, true);
      session_start();
      session_regenerate_id();
    }
    self::$sessionID = session_id();
  }

  public static function getInstance() {
    if(!isset(self::$instance)) {
      $className = __CLASS__;
      self::$instance = new $className;
    }
    return self::$instance;
  }

  public function writeClose() {
    session_write_close();
    return;
  }

  public function destroy() {
    foreach($_SESSION as $var => $val) {
      $_SESSION[$var] = null;
    }
    session_destroy();
  }

  public function clear() {
	  foreach($_SESSION as $var => $val) {
      $_SESSION[$var] = null;
    }
  }

  public function __clone() {

  }

  public function __get($var) {
    if(isset($_SESSION[$var])) {
      return $_SESSION[$var];
    } else {
      return false;
    }
  }

  public function __set($var, $val) {
    return($_SESSION[$var] = $val);
  }

  public function __destruct() {
    session_write_close();
  }
}
?>