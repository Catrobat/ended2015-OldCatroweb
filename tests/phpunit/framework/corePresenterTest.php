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

class corePresenterTest extends PHPUnit_Framework_TestCase
{
    protected $testModel;

    protected function setUp() {
      require_once('frameworkTestModel.php');
      $this->testModel = new frameworkTestModel();
    }
    
    public function testViewHelperGetLink() {
      $myLink = 'my/link';
      $myLinkText = 'myLinkText';
      $myLinkClass = 'myLinkClass';
      $view = new CorePresenter_html($this->testModel);
      $testLink = $view->viewHelper->getLink($myLink, $myLinkText, $myLinkClass);
      $this->assertTrue(is_int(strpos($testLink, $myLink)));
      $this->assertTrue(is_int(strpos($testLink, $myLinkText)));
      $this->assertTrue(is_int(strpos($testLink, $myLinkClass)));
    }

    public function testHtmlPresenter() {
      $view = new CorePresenter_html($this->testModel);
      $this->assertFalse($view->display());
      $this->assertEquals(10, $view->testValue);
    }

    public function testJsonPresenter() {
      $view = new CorePresenter_json($this->testModel);
      $tmpAssocArray = json_decode($view->getJsonString(), true);
      $this->assertEquals(10, $tmpAssocArray['testValue']);
    }

    public function testHttpPresenter() {
      $view = new CorePresenter_http($this->testModel);
      $this->assertEquals(500, $view->getStatusCode());
    }

    public function testXmlPresenter() {
      $view = new CorePresenter_xml($this->testModel);
      $xml = simplexml_load_string($view->getXmlString());
      $values = $xml->children();
      $this->assertEquals(10, intval($values[0]));
    }
}
?>
