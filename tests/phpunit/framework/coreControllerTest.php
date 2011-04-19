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

require_once('frameworkTestsBootstrap.php');

class coreControllerTest extends PHPUnit_Framework_TestCase
{
  protected $controller;

  protected function setUp() {
    $this->controller = new CoreController();
  }

  /**
   * @dataProvider getDataDefault
   */
  public function testControllerParseURL($getData) {
    /* TEST DEFAULT MVC PARAMETERS */
    $this->controller->parseURL($getData);
    $this->assertEquals(MVC_DEFAULT_MODULE, $this->controller->module);
    $this->assertEquals(MVC_DEFAULT_CLASS, $this->controller->class);
    $this->assertEquals(MVC_DEFAULT_VIEW, $this->controller->view);
  }

  /**
   * @dataProvider getDataTestPage
   */
  public function testControllerExecute($getData, $serverData) {
    $_SERVER['PHP_AUTH_USER'] = $serverData['PHP_AUTH_USER'];
    $_SERVER['PHP_AUTH_PW'] = $serverData['PHP_AUTH_PW'];
    $this->controller->parseURL($getData);
    
    // TODO  pg_prepare() fails
    //$this->assertTrue($this->controller->execute());
  }

  /* *** DATA PROVIDERS *** */
  public function getDataDefault() {
    return array(
    array(array()),
    array(array('module'=>'nonExistingModule', 'class'=>'nonExistingClass', 'method'=>'nonExistingMethod', 'view'=>'nonExistingView'))
    );
  }

  public function getDataTestPage() {
    return array(
    array(array('module'=>'test', 'class'=>'test', 'method'=>'test', 'view'=>'html'), array('PHP_AUTH_USER'=>'', 'PHP_AUTH_PW'=>'')),
    array(array('module'=>'test', 'class'=>'testAdminAuthentication', 'method'=>'test', 'view'=>'html'), array('PHP_AUTH_USER'=>'admin', 'PHP_AUTH_PW'=>'cat.roid.web'))
    );
  }
}
?>
