<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('frameworkTestsBootstrap.php');

class coreFrameworkTest extends PHPUnit_Framework_TestCase
{
  protected $testModel;

  protected function setUp() {
    require_once('frameworkTestModel.php');
    $this->testModel = new frameworkTestModel();
  }

  public function testModelDataArray() {
    $this->assertEquals(10, $this->testModel->testValue);
    $this->testModel->__default();
    $this->assertEquals(22, $this->testModel->testValue);
  }

  public function testDatabaseResource() {
    $this->assertTrue(is_resource($this->testModel->dbConnection));
    $this->assertEquals('pgsql link', get_resource_type($this->testModel->dbConnection));
  }

  public function testModelProperties() {
    $this->assertEquals('frameworkTestModel', $this->testModel->name);
    $this->assertTrue($this->testModel->isValid($this->testModel));
  }

  public function testErrorHandler() {
    $this->assertTrue($this->testModel->errorHandler instanceof CoreErrorHandler);
    $this->assertTrue(is_array($this->testModel->errorHandler->getErrors()));
    $this->assertEquals('unknown error: "nonExistingCode"!', $this->testModel->errorHandler->getError('nonExistingType', 'nonExistingCode'));
    $this->assertTrue(is_string($this->testModel->errorHandler->getError('db', 'query_failed')));
  }

  public function testMailHandler() {
    $mailSubject = "This is a mail subject";
    $mailText = "This is some text for the email body.";
    $this->assertTrue($this->testModel->mailHandler instanceof CoreMailHandler);
    $this->assertFalse($this->testModel->mailHandler->sendAdministrationMail('', ''));
    $this->assertFalse($this->testModel->mailHandler->sendAdministrationMail('', $mailText));
    $this->assertFalse($this->testModel->mailHandler->sendAdministrationMail($mailSubject, ''));
    //$this->assertFalse($this->testModel->mailHandler->sendAdministrationMail($mailSubject, $mailText));    
  }

  public function testPreparedStatements() {
    $statementsXmlFile = CORE_BASE_PATH.XML_PATH.'prepared_statements.xml';
    $this->assertTrue(file_exists($statementsXmlFile));
    $xml = simplexml_load_file($statementsXmlFile);
    foreach($xml->children() as $query) {
      $attributes = $query->attributes();
      $stmtName = strval($attributes['name']);
      $result = pg_query_params('SELECT name FROM pg_prepared_statements WHERE name = $1', array($stmtName));
      $this->assertNotEquals(0, pg_num_rows($result));
    }
  }

  public function testSession() {
    $testValue = 'myTestValueStoredIntoSession';
    $anotherTestValue = 'anotherTestValueStoredIntoSession';
    $_SESSION['testValue'] = $testValue;
    $this->assertEquals($testValue, $this->testModel->session->testValue);
    $this->testModel->session->testValue = $anotherTestValue;
    $this->assertEquals($anotherTestValue, $_SESSION['testValue']);
  }

  public function testCss() {
    $testCss = 'myTestCss.css';
    $numCss = 5;
    $this->assertEquals(null, $this->testModel->getCss());
    for($i=0;$i<$numCss;$i++) {
      $this->testModel->addCss($testCss);
    }
    $cssCounter = 0;
    while($css = $this->testModel->getCss()) {
      $this->assertEquals($testCss, $css);
      $cssCounter++;
    }
    $this->assertEquals($numCss, $cssCounter);
    $this->assertEquals(null, $this->testModel->getCss());
  }

  public function testJs() {
    $testJs = 'myTestJs.js';
    $numJs = 5;
    $this->assertEquals(null, $this->testModel->getJs());
    for($i=0;$i<$numJs;$i++) {
      $this->testModel->addJs($testJs);
    }
    $jsCounter = 0;
    while($js = $this->testModel->getJs()) {
      $this->assertEquals($testJs, $js);
      $jsCounter++;
    }
    $this->assertEquals($numJs, $jsCounter);
    $this->assertEquals(null, $this->testModel->getJs());
  }
  
  /**
   * @dataProvider badWords
   */
  public function testBadwordsFilterBad($badWord) {
    $this->assertEquals(1, $this->testModel->badWordsFilter->areThereInsultingWords($badWord));
  }
  
  /**
   * @dataProvider goodWords
   */
  public function testBadwordsFilterGood($goodWord) {
    $this->assertEquals(0, $this->testModel->badWordsFilter->areThereInsultingWords($goodWord));
  }

  /* DATA PROVIDERS */
  public function badWords() {
    $badWords = array(
          array("fuck"), 
          array("shit"),
          array("ass"),
          array("this is a sucking text with some really bad words in it. so go home asshole!"),
          array("f*uck"));
    return $badWords;
  }
  
  public function goodWords() {
    $goodWords = array(
          array("test"), 
          array("catroid"),
          array("here comes some text which does not have any insulting word inside."),
          array("project"));
    return $goodWords;
  }
}
?>
