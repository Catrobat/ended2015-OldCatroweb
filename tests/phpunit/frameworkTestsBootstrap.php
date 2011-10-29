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

  session_start();
  $_SERVER['SERVER_NAME'] = '127.0.0.1';
  $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
  $_SERVER['REQUEST_URI'] = 'just/a/test';
  spl_autoload_register('__autoload');
  require_once(dirname(__FILE__).'/../../config.php');
  require_once(dirname(__FILE__).'/../../passwords.php');
  require_once(dirname(__FILE__).'/../../commonFunctions.php');
  function __autoload($class) {
    include_once CORE_BASE_PATH.'classes/'.$class.'.php';
  }
  define('UNITTESTS', true);
?>