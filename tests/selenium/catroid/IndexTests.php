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
    $this->selenium->start();
  }

  public function tearDown()
  {
    $this->selenium->stop();
  }
  
  public function ajaxWait($waitfor)
  {
    // Loop initialization.
    for ($second = 0; $second <=600;$second++) {

     // If loop is reached 60 seconds then break the loop.
     if ($second >= 600) break;

     // Search for element "link=ajaxLink" and if available then break loop.
     try 
     {        
       if (($this->selenium->isElementPresent($waitfor))&&(!($this->selenium->isTextPresent("loading..."))))
       break; 
     } catch (Exception $e) {}
     sleep(1);
    }
  }
  
   public function doUpload() {    
     

     
     for($i=1; $i< 20; $i++)
     {
       $fileName = 'test.zip';
       $testFile = dirname(__FILE__).'/testdata/'.$fileName;
       $fileChecksum = md5_file($testFile);
       $fileSize = filesize($testFile);
       $fileType = 'application/x-zip-compressed';
      
       $formData = array('projectTitle'=>'unitTest'.$i, 'projectDescription'=>'unitTestDescription'.$i, 'fileChecksum'=>$fileChecksum);
       $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
                          'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
       $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
       $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
       $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENTION;
      
       //test qrcode image generation
       $this->assertTrue(is_file(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$insertId.PROJECTS_QR_EXTENTION));
       $this->assertNotEquals(0, $insertId);
       $this->assertTrue(is_file($filePath));
       $this->assertEquals(200, $this->upload->statusCode);
       $this->assertTrue($this->upload->projectId > 0);
       $this->assertTrue($this->upload->fileChecksum != null);
       $this->assertEquals(md5_file($testFile), $this->upload->fileChecksum);
       $this->assertTrue(is_string($this->upload->answer));
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
    
    //test page title
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    
    // test catroid header text
    $this->assertTrue($this->selenium->isElementPresent("xpath=//img[@class='catroidLettering']"));
    
     // test logo link
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='webHeadLogo']/a"));
    $this->selenium->click("xpath=//div[@class='aIndexWebLogoLeft']/a");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());

    //test catroid download link
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='webHeadTitleName']/a"));
    $this->selenium->click("xpath=//div[@class='webHeadTitleName']/a");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Catroid_0-4-3d.apk"));
    $this->assertTrue($this->selenium->isTextPresent("Paintroid_0.6.4b.apk"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    //test home link
    $this->selenium->click("xpath=//a[1]"); //clicks first link on page (should be the home link)
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/index/", $this->selenium->getLocation());
    
    //test links to details page
    
    $this->ajaxWait("id=page0");
    $this->selenium->waitForCondition("", 2000); // loading...
    $this->selenium->click("xpath=//a[@class='projectListDetailsLink']");    
    $this->selenium->waitForPageToLoad(10000);    
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    $this->selenium->goBack();    
    $this->ajaxWait("id=page0");
    $this->selenium->waitForPageToLoad(10000);
    
    
    $this->selenium->click("xpath=//a[@class='projectListDetailsLinkBold']");
    $this->selenium->waitForPageToLoad(10000);
    
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    
  }
  
  public function testPageNavigation()
  {
    $this->doUpload();
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    
    //test page title
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    $this->assertFalse($this->selenium->isVisible("fewerProjects"));
    $this->assertTrue($this->selenium->isVisible("moreProjects"));
    $this->ajaxWait("id=page0");
    $this->selenium->waitForCondition("", 2000); // loading...
    
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page0']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page1']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page2']"));
    $this->selenium->click("moreProjects");    
    $this->ajaxWait("id=page2");
    $this->selenium->waitForCondition("", 2000); // loading...
    
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page2']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page3']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page4']"));
    $this->assertTrue($this->selenium->isVisible("fewerProjects"));
    // test session
    $this->selenium->refresh();
    $this->ajaxWait("id=page2");
    $this->selenium->waitForCondition("", 2000); // loading...
    
    $this->selenium->waitForPageToLoad("10000");
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page2']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page3']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page4']"));
    $this->assertTrue($this->selenium->isVisible("fewerProjects"));
            
    $this->selenium->click("fewerProjects");
    $this->ajaxWait("id=page0");
    $this->selenium->waitForCondition("", 2000); // loading...
    
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page0']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page1']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page2']"));
    $this->assertFalse($this->selenium->isVisible("fewerProjects"));
    
    // test header click
    $this->assertTrue($this->selenium->isVisible("moreProjects"));
    $this->selenium->click("moreProjects");    
    $this->ajaxWait("id=page2");
    $this->selenium->waitForCondition("", 2000); // loading...
    
    $this->selenium->click("aIndexWebLogoLeft");
    $this->ajaxWait("id=page0");
    $this->selenium->waitForCondition("", 2000); // loading...
    
    $this->selenium->waitForPageToLoad("10000");
    $this->selenium->waitForCondition("", 2000); // necessary for loading delay
    
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page0']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page1']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='page2']"));
    $this->assertFalse($this->selenium->isVisible("fewerProjects"));
    
    $this->deleteUploadedProjects();
  }   
  
}
?>

