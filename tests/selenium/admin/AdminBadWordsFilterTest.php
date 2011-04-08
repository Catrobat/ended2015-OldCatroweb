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

/* Set TESTS_BASE_PATH to your catroid www-root */
require_once 'testsBootstrap.php';

class AdminBadWordsFilterTest extends PHPUnit_Framework_TestCase
{
  private $selenium;
  private $unapprovedWord = null;
  private $adminpath;
  
  public function setUp() {
    if($this->unapprovedWord == null) {
      $this->unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft".$this->randomString();
    }
    $this->adminpath='http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin';
    	
    $path= 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin/';
    $this->selenium = new Testing_Selenium(TESTS_BROWSER, $path);
    if(TESTS_SLOW_MODE) {
      $this->selenium->setSpeed(TESTS_SLOW_MODE_SPEED);
    } else {
      $this->selenium->setSpeed(1);
    }
    $this->selenium->start();
  }

  public function tearDown()
  {
    $this->selenium->stop();
  }

  public function testApproveButtonGood()
  {
    $this->uploadProjectWithAnUnapprovedWord();

    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsApproveWords");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->select("meaning", "label=good");
    $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='approveButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The word was succesfully approved!"));

    $this->deletePreviouslyUploadedProjectAndUnapporvedWord();
  }

  public function testApproveButtonBad()
  {
    $this->uploadProjectWithAnUnapprovedWord();

    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsApproveWords");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->select("meaning", "label=bad");
    $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='approveButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The word was succesfully approved!"));

    $this->deletePreviouslyUploadedProjectAndUnapporvedWord();
  }

  public function testApproveButtonNoSelection()
  {
    $this->uploadProjectWithAnUnapprovedWord();

    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsApproveWords");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='approveButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Error: no word meaning selected!"));

    $this->deletePreviouslyUploadedProjectAndUnapporvedWord();
  }

  public function testDeleteButton()
  {
    $this->uploadProjectWithAnUnapprovedWord();

    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsApproveWords");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='deleteButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The word was succesfully deleted!"));
    $this->assertFalse($this->selenium->isTextPresent($this->unapprovedWord));

    $this->deletePreviouslyUploadedProjectAndUnapporvedWord();
  }

  private function uploadProjectWithAnUnapprovedWord() {
    $this->deleteWord($this->unapprovedWord);
    $this->uploadTestProject($this->unapprovedWord);
  }

  private function deletePreviouslyUploadedProjectAndUnapporvedWord() {
    $this->selenium->selectWindow(null);
    $this->selenium->open($this->adminpath);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsEditProjects");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='deleteButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent($this->unapprovedWord));

    $this->deleteWord($this->unapprovedWord);
  }

  private function deleteWord($word) {
    $query = "DELETE FROM wordlist WHERE word='$word'";
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    if($result) {
      pg_free_result($result);
    }
  }

  // upload a test project via cURL-request
  private function uploadTestProject($title, $description = '')
  {
    $uploadTestFile = dirname(__FILE__);
    if(strpos($uploadTestFile, '\\') >= 0) {
      $uploadTestFile.= '\testdata\test.zip';
    } else {
      $uploadTestFile.= '/testdata/test.zip';
    }

    $uploadpath= TESTS_BASE_PATH.'catroid/upload/upload.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_URL, $uploadpath);
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array(
        "upload"=>"@$uploadTestFile",
        "projectTitle"=>$title,
    	"projectDescription"=>$description,
        "fileChecksum"=>md5_file($uploadTestFile)
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $response = json_decode(curl_exec($ch));
    $this->assertEquals(200, $response->statusCode);
  }
  
  private function randomString() {
    $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    $string = '';
    for($i=0;$i<8;$i++) {
      $string .= $chars[rand(0, count($chars)-1)];
    }
    return $string;
  }
}
?>

