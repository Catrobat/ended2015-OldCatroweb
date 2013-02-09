<?php
/*
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
    if(DATABASE_CONNECTION_PERSISTENT) {
      $this->assertEquals('pgsql link persistent', get_resource_type($this->testModel->dbConnection));
    } else {
      $this->assertEquals('pgsql link', get_resource_type($this->testModel->dbConnection));
    }
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

  public function testLanguageHandlerCheckParamCount() {
    $msg = "this is a test message with a {*firstParam*} and a {*secondParam*} in it";
    $this->assertTrue($this->testModel->languageHandler->checkParamCount($msg, 2));
    $msg = "this is a test message without parameters.";
    $this->assertTrue($this->testModel->languageHandler->checkParamCount($msg, 0));
  }

  /**
   * @dataProvider languageHandlerMsgs
   */
  public function testLanguageHandlerParseString($msg, $expectedMsg, $args) {
    $this->assertEquals($expectedMsg, $this->testModel->languageHandler->parseString($msg, $args));
  }

  public function testMailHandler() {
    $mailSubject = "This is a mail subject";
    $mailText = "This is some text for the email body.";
    $this->assertTrue($this->testModel->mailHandler instanceof CoreMailHandler);
    $this->assertFalse($this->testModel->mailHandler->sendAdministrationMail('', ''));
    $this->assertFalse($this->testModel->mailHandler->sendAdministrationMail('', $mailText));
    $this->assertFalse($this->testModel->mailHandler->sendAdministrationMail($mailSubject, ''));
  }

  public function testPreparedStatements() {
    $statementsXmlFile = CORE_BASE_PATH.XML_PATH.'prepared_statements.xml';
    $this->assertTrue(file_exists($statementsXmlFile));
    $xml = simplexml_load_file($statementsXmlFile);
    foreach($xml->children() as $query) {
      $attributes = $query->attributes();
      $stmtName = strval($attributes['name']);
      $this->assertLessThan(64, strlen($stmtName),'ERROR: prepared statement name \''.$stmtName.'\' is too long! (max length: 63 characters)');
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
    $this->assertEquals("", $this->testModel->getCss());
    $this->testModel->addCss('notExistingFile.css');
    $this->assertEquals("", $this->testModel->getCss());
    $this->testModel->addCss('index.css');
    $this->assertNotEquals("", $this->testModel->getCss());
  }

  public function testGlobalCss() {
    $this->assertEquals("", $this->testModel->getGlobalCss());
    $this->testModel->addGlobalCss('notExistingFile.css');
    $this->assertEquals("", $this->testModel->getGlobalCss());
    $this->testModel->addGlobalCss('index.css');
    $this->assertNotEquals("", $this->testModel->getGlobalCss());
  }

  public function testJs() {
    $this->assertEquals("", $this->testModel->getJs());
    $this->testModel->addJs('notExistingFile.js');
    $this->assertEquals("", $this->testModel->getJs());
    $this->testModel->addJs('index.js');
    $this->assertNotEquals("", $this->testModel->getJs());
  }

  public function testGlobalJs() {
    $this->assertEquals("", $this->testModel->getGlobalJs());
    $this->testModel->addGlobalJs('notExistingFile.js');
    $this->assertEquals("", $this->testModel->getGlobalJs());
    $this->testModel->addGlobalJs('index.js');
    $this->assertNotEquals("", $this->testModel->getGlobalJs());
  }

  /*
   * corePreseterTests
   */

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

  /**
   * @dataProvider forbiddenClassNames
   */
  public function testForbiddenClassNames($className) {
    $this->assertFalse(is_file(CORE_BASE_PATH.'modules/api/'.$className.'.php'));
    $this->assertFalse(is_file(CORE_BASE_PATH.'modules/admin/'.$className.'.php'));
    $this->assertFalse(is_file(CORE_BASE_PATH.'modules/catroid/'.$className.'.php'));
    $this->assertFalse(is_file(CORE_BASE_PATH.'modules/test/'.$className.'.php'));
  }

  /**
   * @dataProvider forbiddenModuleNames
   */
  public function testForbiddenModuleNames($moduleName) {
    $this->assertFalse(is_dir(CORE_BASE_PATH.'modules/'.$moduleName));
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
    array("backslash\\"),
    array("catroid"),
    array("here comes some text which does not have any insulting word inside."),
    array("project"));
    return $goodWords;
  }

  public function languageHandlerMsgs() {
    $msgs = array(
    array("this is a test message with variable {*firstParam123*} and {*secondParam012*} in it.", "this is a test message with variable numbers: 12345 and characters: abcde in it.", array('numbers: 12345', 'characters: abcde')),
    array("message without parameters", "message without parameters", array()),
    array("message without parameters but with special chars like äÖüß and _[] {}*<br>", "message without parameters but with special chars like äÖüß and _[] {}*<br>", array()),
    array("here come some special chars: {*specialChars*}", "here come some special chars: {[}]*_Üöß^", array("{[}]*_Üöß^"))
    );
    return $msgs;
  }

  public function forbiddenClassNames() {
    $names = array(
    array(substr(DEFAULT_DEV_ERRORS_FILE, 0, strpos(DEFAULT_DEV_ERRORS_FILE, '.'))),
    array(substr(DEFAULT_PUB_ERRORS_FILE, 0, strpos(DEFAULT_PUB_ERRORS_FILE, '.'))),
    array(substr(DEFAULT_TEMPLATE_LANGUAGE_FILE, 0, strpos(DEFAULT_TEMPLATE_LANGUAGE_FILE, '.')))
    );
    return $names;
  }

  public function forbiddenModuleNames() {
    $names = array(
    array('errors'),
    array('addons'),
    array('classes'),
    array('images'),
    array('include'),
    array('install'),
    array('modules'),
    array('pootle'),
    array('resources'),
    array('sql'),
    array('tests'),
    array('viewer')
    );
    return $names;
  }
}
?>
