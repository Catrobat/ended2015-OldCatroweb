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

  public function setUp() {
    $this->selenium = new Testing_Selenium(TESTS_BROWSER, TESTS_BASE_PATH);
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
   * @dataProvider validProjectsForUpload
   */
  public function testUploadTest($projectTitle, $projectDescription, $projectSource)
  {     
    $response = $this->uploadTestProject($projectTitle, $projectDescription, $projectSource);
    $this->assertEquals(200, $response->statusCode);
  }

  // upload a test project via cURL-request
  private function uploadTestProject($title, $description = '', $source = 'test.zip')
  {
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
        "token"=>"0",
        "upload"=>"@$uploadTestFile",
        "projectTitle"=>$title,
    	"projectDescription"=>$description,
        "fileChecksum"=>md5_file($uploadTestFile)
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    return(json_decode(curl_exec($ch)));
  }

  /* *** DATA PROVIDER *** */
  public function validProjectsForUpload() {
    $returnArray = array(
      array('testing project upload', 'some description for my test project.', 'test.zip'),
      array('my test project with spaces', 'some description for my test project.', 'test.zip'),
      array(('my spÄc1al c´har t3ßt pröjec+'), 'some description with -äöüÜÖÄß- for my test project.%&()[]{}_|~#', 'test.zip'),
      array('my_test_project_with_looong_description', 'some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. some description for my test project. ', 'test.zip'),
      array('project with thumbnail', 'this project has its own thumbnail inside the zip', 'test2.zip')
      );
      
    return $returnArray;
  }
}
?>
