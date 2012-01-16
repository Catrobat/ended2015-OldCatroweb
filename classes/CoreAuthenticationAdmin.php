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

abstract class CoreAuthenticationAdmin extends CoreAuthentication {
    function __construct() {
      parent::__construct();
    }

    function authenticate() {
      if(!isset($_SERVER['PHP_AUTH_USER']) ||
        $_SERVER['PHP_AUTH_USER']!=ADMIN_AREA_USER ||
        md5($_SERVER['PHP_AUTH_PW'])!=ADMIN_AREA_PASS) {
        $this->session->adminUser = false;
        header('WWW-Authenticate: Basic realm="ADMINISTRATION AREA"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Access to administration area denied! You need username and password!<br/>';
        exit();
      } else {
        $this->session->adminUser = true;
        return true;
      }
    }

    function __destruct() {
      parent::__destruct();
    }
}
?>