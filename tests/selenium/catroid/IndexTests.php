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

/* Set TESTS_BASE_PATH in testsBootstrap.php to your catroid www-root */
require_once 'testsBootstrap.php';

class IndexTests extends PHPUnit_Framework_TestCase
{
  private $selenium;
  protected $upload;
  protected $insertIDArray = array();
  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->selenium = new Testing_Selenium(TESTS_BROWSER, $path);
    require_once CORE_BASE_PATH.'modules/catroid/upload.php';
    $this->upload = new upload();
    if (TESTS_SLOW_MODE==TRUE) {
      $this->selenium->setSpeed(TESTS_SLOW_MODE_SPEED);
    } else {
      $this->selenium->setSpeed(1);
    }
    
    $labels = array();
    $labels['websitetitle'] = "Catroid Website";
    $labels['title'] = "Newest Projects";
    $labels['prevButton'] = "« Newer";
    $labels['nextButton'] = "Older »";
    $labels['loadingButton'] = "loading...";
    $this->labels = $labels;
    
    $this->selenium->start();
  }

  public function tearDown()
  {
    $this->deleteUploadedProjects();
    $this->selenium->stop();
  }

  public function ajaxWait()
  {
    for($second = 0; $second <= 6; $second++) {
      if($second >= 6) break;
      try {
        if($this->selenium->isElementPresent("xpath=//input[@id='ajax-loader'][@value='off']")) {
          break;
        }
      } catch (Exception $e) {}
      sleep(1);
    }
  }

  public function doUpload() {
    for($i=1; $i<PROJECT_PAGE_LOAD_MAX_PROJECTS+PROJECT_PAGE_SHOW_MAX_PROJECTS; $i++) {      
      $jsonResponse = $this->uploadTestProject('unitTest'.$i, 'unitTestDescription'.$i);
      $insertId = $jsonResponse->projectId;
      array_push($this->insertIDArray, $insertId);
    }
  }

  public function deleteUploadedProjects()
  {
    foreach ($this->insertIDArray as $insertId)
    {
      $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENTION;
      // test deleting from database
      $this->upload->removeProjectFromFilesystem($filePath);
      $this->assertFalse(is_file($filePath));
      //test deleting from filesystem
      $this->upload->removeProjectFromDatabase($insertId);
      $query = "SELECT * FROM projects WHERE id='$insertId'";
      $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
      $this->assertEquals(0, pg_num_rows($result));
    }
  }

  public function testIndexPage() {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();

    //test page title
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    $this->assertTrue($this->selenium->isTextPresent($this->labels['title']));

    // test catroid header text
    $this->assertTrue($this->selenium->isElementPresent("xpath=//img[@class='catroidLettering']"));

    // test logo link
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='webHeadLogo']"));
    $this->selenium->click("xpath=//div[@id='aIndexWebLogoLeft']");
    $this->ajaxWait();
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());

    //test catroid download link
    $this->assertTrue($this->selenium->isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));
    $this->selenium->click("xpath=//a[@id='aIndexWebLogoMiddle']");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Catroid_0-4-3d.apk"));
    $this->assertTrue($this->selenium->isTextPresent("Paintroid_0.6.4b.apk"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    //test links to details page
    $this->selenium->click("xpath=//a[@class='projectListDetailsLink']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();

    $this->selenium->click("xpath=//a[@class='projectListDetailsLinkBold']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    $this->selenium->click("xpath=//a[@id='aIndexWebLogoLeft']");
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();

    //test home link
    $this->selenium->click("xpath=//div[@id='aIndexWebLogoLeft']");
    $this->ajaxWait();
    $this->assertRegExp("/catroid\/index/", $this->selenium->getLocation());
  }

  public function testPageNavigation()
  {
    $this->doUpload();
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();

    //test page title
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    $this->assertFalse($this->selenium->isVisible("fewerProjects"));
    $this->assertTrue($this->selenium->isVisible("moreProjects"));
    $this->assertTrue($this->selenium->isTextPresent($this->labels['nextButton']));
    
    $clickCount = ceil(PROJECT_PAGE_SHOW_MAX_PROJECTS / PROJECT_PAGE_LOAD_MAX_PROJECTS);
    for($i=0; $i<$clickCount; $i++) {
      $this->selenium->click("moreProjects");
      $this->ajaxWait();
      $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".($i+2)."/", $this->selenium->getTitle());
    }

    $this->assertTrue($this->selenium->isVisible("fewerProjects"));
    $this->assertTrue($this->selenium->isTextPresent($this->labels['prevButton']));
    $this->selenium->click("fewerProjects");    
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".($i)."/", $this->selenium->getTitle());

    // test session
    $this->selenium->refresh();
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".($i)."/", $this->selenium->getTitle());
    
    //test links to details page
    $this->selenium->click("xpath=//a[@class='projectListDetailsLink']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".($i)."/", $this->selenium->getTitle());
    
    // test header click
    $this->selenium->click("aIndexWebLogoLeft");
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - 1/", $this->selenium->getTitle());
  }

  // upload a test project via cURL-request
  private function uploadTestProject($title, $description = '')
  {
    $uploadTestFile = dirname(__FILE__);
    if(strpos($uploadTestFile, '\\') != false) {
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

    return($response);
  }

}
?>

