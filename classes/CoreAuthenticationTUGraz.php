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


abstract class CoreAuthenticationTUGraz extends CoreAuthentication {
  public function __construct() {
    parent::__construct();
  }

  public function authenticate() {
    if(isset($_SERVER["REMOTE_ADDR"])) {
      $ip = $_SERVER["REMOTE_ADDR"];
      if(strcmp($ip, '127.0.0.1') == 0 || strcmp(substr($ip, 0, 6), '129.27') == 0) {
        return true;
      }
    }
    $this->errorHandler->showErrorPage('auth', 'not_a_tugraz_ip');
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>