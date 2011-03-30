<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('testsBootstrap.php');

class errorPageTest extends PHPUnit_Framework_TestCase
{
  protected $obj;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/errorPage.php';
    $this->obj = new errorPage();
  }
  
  public function test__default() {
    $extraInfo = 'myTestExtraInfoString';
    $_SESSION['errorType'] = 'db';
    $_SESSION['errorCode'] = 'query_failed';
    $_SESSION['errorExtraInfo'] = $extraInfo;
    $this->obj->__default();
    
    $this->assertTrue(is_string($this->obj->errorMessage));    
    $this->assertEquals((strpos($this->obj->errorMessage, $extraInfo) != false), DEVELOPMENT_MODE);
  }
}
?>
