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

require_once('testsBootstrap.php');

class internationalizationTest extends PHPUnit_Framework_TestCase
{
  protected $obj;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/index.php';
    $this->obj = new index();
  }

  public function testParseErrorMessage() {
    $this->assertTrue($this->obj->errorHandler->checkParamCount('test {*--*} test {*ÜÜ*} test {*1*}, {**} {*2*}, {*3*}, {*4*}, {*5*}, {*6*}, {*7*}, {*8*}, {*9*}, {*10*}, {*11*};', 11));    
    $this->assertEquals('test test test', $this->obj->errorHandler->parseErrorMessage('test test test', array()));
    $this->assertEquals('test one test two test', $this->obj->errorHandler->parseErrorMessage('test {*1*} test {*2*} test', array('one', 'two')));
    $this->assertEquals('test test test a, b, c, d, e, f, g, h, i, j, k;', $this->obj->errorHandler->parseErrorMessage('test test test {*1*}, {*2*}, {*3*}, {*4*}, {*5*}, {*6*}, {*7*}, {*8*}, {*9*}, {*10*}, {*11*};', array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k')));
  }
}
?>
