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

class AdminEditProjectsTests extends PHPUnit_Framework_TestCase
{
  private $selenium;
  private $adminpath;
  private $homepage;
  
  public function setUp() {
    $this->homepage=TESTS_BASE_PATH."catroid/index";
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
  
  private function ajaxWait() {
    for($second = 0; $second <= 600; $second++) {
      if($second >= 600) break;
      try {
        if($this->selenium->isElementPresent("xpath=//input[@id='ajax-loader'][@value='off']")) {
          break;
        }
      } catch (Exception $e) {}
      sleep(1);
    }
  }

  public function tearDown() {
    $this->selenium->stop();
  }

  public function testDeleteButton() {
    $projectTitle = "Testproject for AdminEditProjects Upload Test Title DELETE";
    $project = $this->uploadTestProject($projectTitle);
    $projectId = $project->projectId;
    
    // check that project is shown on index-page
    $this->selenium->open($this->homepage);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    
    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsEditProjects");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - List of available projects"));
    $this->assertTrue($this->selenium->isTextPresent("ID"));
    $this->assertTrue($this->selenium->isTextPresent("Title"));
    $this->assertTrue($this->selenium->isTextPresent("Upload Time"));
    $this->assertTrue($this->selenium->isTextPresent("Upload IP"));
    $this->assertTrue($this->selenium->isTextPresent("Downloads"));
    $this->assertTrue($this->selenium->isTextPresent("Flagged"));
    $this->assertTrue($this->selenium->isTextPresent("Visible"));
    $this->assertTrue($this->selenium->isTextPresent("Delete"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@id='delete".$projectId."']"));
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    $this->selenium->click("xpath=//input[@id='delete".$projectId."']");
    $this->selenium->getConfirmation();
    // $this->assertEquals($this->selenium->getConfirmation(), "Delete project '".$projectTitle+"'?");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The project was succesfully deleted!"));
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@id='delete".$projectId."']"));
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));

    // check that project is not shown on index-page
    $this->selenium->open($this->homepage);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isElementPresent("xpath=//img[@id='aIndexWebLogoLeft']"));
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));
  }
 
  public function testInvisibleButton() {
    $projectTitle = "Testproject for AdminEditProjects Upload Test Title INVISIBLE";
    $project = $this->uploadTestProject($projectTitle);
    $projectId = $project->projectId;
    
    // project is shown on index-page
    $this->selenium->open($this->homepage);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    
    // toggle project visibility to "hidden"
    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsEditProjects");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - List of available projects"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@id='toggle".$projectId."']"));
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    // $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@id='toggle".$projectId."']");
    $this->selenium->getConfirmation();
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isTextPresent("The project was succesfully set to state invisible"));

    // project is NOT shown on index-page
    $this->selenium->open($this->homepage);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));
    // $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    
    // toggle project visibility to "visible"
    $this->selenium->open($this->adminpath);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsEditProjects");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - List of available projects"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@id='toggle".$projectId."']"));
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    // $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@id='toggle".$projectId."']");
    $this->selenium->getConfirmation();
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The project was succesfully set to state visible"));

    // project is shown again on index-page
    $this->selenium->open($this->homepage);
    $this->selenium->waitForPageToLoad(10000);
        $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));

    // and delete project
    $this->selenium->open($this->adminpath);
    $this->selenium->click("aAdministrationTools");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("aAdminToolsEditProjects");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - List of available projects"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@id='delete".$projectId."']"));
    $this->assertTrue($this->selenium->isTextPresent($projectTitle));
    // $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@id='delete".$projectId."']");
    $this->selenium->getConfirmation();
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The project was succesfully deleted!"));
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@id='delete".$projectId."']"));
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));
    
    // and finally project is NOT shown on index-page
    $this->selenium->open($this->homepage);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->waitForCondition("", 5000);
    $this->assertFalse($this->selenium->isTextPresent($projectTitle));
  }

  // upload a test project via cURL-request
  private function uploadTestProject($title, $description = '')
  {
    $uploadTestFile = dirname(__FILE__);
    if(strpos($uploadTestFile, '\\') != false) {
      $uploadTestFile.= '\testdata\test2.zip';
    } else {
      $uploadTestFile.= '/testdata/test2.zip';
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
    return $response;
  }
  
}
?>