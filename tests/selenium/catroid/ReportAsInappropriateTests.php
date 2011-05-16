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

class UploadTests extends PHPUnit_Framework_TestCase
{
  private $selenium;
  private $adminpath;
  
  public function setUp() {
    $this->selenium = new Testing_Selenium(TESTS_BROWSER, TESTS_BASE_PATH);
    $this->adminpath = 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin';
    
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
  
  /**
   * @dataProvider loginDataAndReportOwnProject
   */
  public function testReportOwnProjectAsInappropriate($projectTitle, $projectDescription, $projectSource, $username, $password, $token) {
    // login first
    $this->selenium->open(TESTS_BASE_PATH.'catroid/login/');
    $this->selenium->waitForPageToLoad(10000);
    $this->assertEquals('Login', $this->selenium->getText("xpath=//div[@class='webMainContentTitle']"));
    $this->selenium->type("xpath=//input[@name='loginUsername']", $username);
    $this->selenium->type("xpath=//input[@name='loginPassword']", $password);
    $this->selenium->click("xpath=//input[@name='loginSubmit']");
    $this->selenium->waitForPageToLoad(10000);

    // upload project
    $projectTitle = "Testproject for report as inappropriate ".rand(1,9999);
    $response = $this->uploadTestProject($projectTitle, $projectDescription, $projectSource, $token);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
    $this->assertEquals(200, $response->statusCode);
    $projectId = $response->projectId;
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 2000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));    
    
    // goto details page
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$projectId);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 2000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));    
    $this->assertTrue($this->selenium->isTextPresent($projectDescription));    

    // report as inappropriate not visible
    $this->assertFalse($this->selenium->isElementPresent("xpath=//button[@id='reportAsInappropriateButton']"));
    
    $this->assertTrue($this->selenium->isElementPresent("xpath=//button[@id='headerMenuButton']"));
    $this->selenium->click("xpath=//button[@id='headerMenuButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 2000);
    
    $this->selenium->click("xpath=//button[@id='menuLogoutButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 2000);
    
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$projectId);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));    
    $this->assertTrue($this->selenium->isTextPresent($projectDescription));    
    // report as inappropriate visible again after logout
    $this->assertTrue($this->selenium->isElementPresent("xpath=//button[@id='reportAsInappropriateButton']"));
        
    // delete project
    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsEditProjects");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    // $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='deleteButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));
    
    $this->selenium->open(TESTS_BASE_PATH.'catroid/logout');
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
  }
  
  /**
   * @dataProvider loginDataAndReportOwnProjectAnonymous
   */
  public function testReportAnonymousProjectAsInappropriate($projectTitle, $projectDescription, $projectSource, $username, $password, $token) {
    // upload project
    $projectTitle = "Testproject for report as inappropriate (anonymous user) ".rand(1,9999);
    $response = $this->uploadTestProject($projectTitle, $projectDescription, $projectSource, $token);
    $this->assertEquals(200, $response->statusCode);
    $projectId = $response->projectId;
    $this->selenium->waitForPageToLoad(10000);
    
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 2000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));    
    
    // goto details page
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$projectId);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 2000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));    
    $this->assertTrue($this->selenium->isTextPresent($projectDescription));    

    // report as inappropriate
    $this->assertTrue($this->selenium->isElementPresent("reportAsInappropriateButton"));
    $this->selenium->click("reportAsInappropriateButton");

    $this->assertTrue($this->selenium->isVisible("reportInappropriateReason"));
    $this->assertTrue($this->selenium->isVisible("reportInappropriateReportButton"));
    $this->assertTrue($this->selenium->isVisible("reportInappropriateCancelButton"));
    
    $this->selenium->click("reportAsInappropriateButton");
    $this->selenium->type("reportInappropriateReason", "my selenium reason");
    $this->selenium->click("reportInappropriateReportButton");
    $this->selenium->waitForPageToLoad(10000);
    
    $this->assertFalse($this->selenium->isVisible("reportInappropriateReason"));
    $this->assertTrue($this->selenium->isTextPresent("You reported this project as inappropriate!"));
    
    // project is hidden
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 2000);
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));    
    
    // delete project
    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsEditProjects");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    // $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='deleteButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));
  }
    
  private function createToken($username, $password) {
    return md5(md5($username).":".md5($password));
  }
  
  // upload a test project via cURL-request
  private function uploadTestProject($title, $description, $source, $token) {
    $uploadTestFile = dirname(__FILE__);
    if(strpos($uploadTestFile, '\\') != false) {
      $uploadTestFile.= '\testdata\\'.$source;
    } else {
      $uploadTestFile.= '/testdata/'.$source;
    }

    $uploadpath= TESTS_BASE_PATH.'api/upload/upload.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_URL, $uploadpath);
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array(
        "upload"=>"@$uploadTestFile",
        "token"=>$token,
    		"projectTitle"=>$title,
    	  "projectDescription"=>$description,
        "fileChecksum"=>md5_file($uploadTestFile)
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    return(json_decode(curl_exec($ch)));
  }

  /* *** DATA PROVIDER *** */
  public function loginDataAndReportOwnProject() {
    $returnArray = array(
      array('testing project upload with user id', 'some description for my test project connected to my user id after registration and login at catroid.org.', 'test2.zip', 'catroweb', 'cat.roid.web', $this->createToken('catroweb','cat.roid.web'))
      );
    return $returnArray;
  }
  
  public function loginDataAndReportOwnProjectAnonymous() {
    $returnArray = array(
      array('testing project upload without user id', 'some description for my test project connected to anonymous user id (0) after registration and login at catroid.org.', 'test2.zip', '', '', "0")
      );
    return $returnArray;
  }
  
}

?>