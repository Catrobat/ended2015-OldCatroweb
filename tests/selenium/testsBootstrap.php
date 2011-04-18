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

session_start();
/* Set TESTS_BASE_PATH to your catroid www-root */
//define('TESTS_BASE_PATH','http://dev.catroid.localhost/');
//define('TESTS_BASE_PATH','http://catroidwebtest.ist.tugraz.at/');
//define('TESTS_BASE_PATH','http://localhost/catroweb/');
define('TESTS_BASE_PATH','http://localhost/');
define('TESTS_SLOW_MODE',true);
define('TEST_MODE',true);
define('TESTS_SLOW_MODE_SPEED','1000');
define('TESTS_BROWSER', '*firefox');
//define('TESTS_BROWSER', '*iexplore');
//define('TESTS_BROWSER', '*googlechrome');

$_SERVER['SERVER_NAME'] = '127.0.0.1';
spl_autoload_register('__autoload');
set_include_path(get_include_path() . PATH_SEPARATOR . './pear/');

require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once '../../config.php';
require_once '../../passwords.php';
set_include_path(get_include_path() . PATH_SEPARATOR . CORE_BASE_PATH.'classes/');
function __autoload($class) {
  //include_once CORE_BASE_PATH.'classes/'.$class.'.php';
  include_once $class.'.php';
}
$connection = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS)
    or die('Connection to Database failed: ' . pg_last_error());
?>