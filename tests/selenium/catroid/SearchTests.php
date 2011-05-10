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

class SearchTests extends PHPUnit_Framework_TestCase
{
  private $selenium;
  protected $labels;
  protected $upload;
  protected $insertIDArray = array();

  public function setUp() {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->selenium = new Testing_Selenium(TESTS_BROWSER, $path);
    require_once CORE_BASE_PATH.'modules/api/upload.php';
    $this->upload = new upload();
    if (TESTS_SLOW_MODE==TRUE) {
      $this->selenium->setSpeed(TESTS_SLOW_MODE_SPEED);
    } else {
      $this->selenium->setSpeed(1);
    }
    
    $labels = array();
    $labels['websitetitle'] = "Catroid Website";
    $labels['title'] = "Search Results";
    $labels['prevButton'] = "« Previous";
    $labels['nextButton'] = "Next »";
    $labels['loadingButton'] = "<img src='".BASE_PATH."images/symbols/ajax-loader.gif' /> loading...";
    $this->labels = $labels;
    
    $this->selenium->start();
  }

  public function tearDown() {    
    $this->deleteUploadedProjects();
    $this->selenium->stop();
  }

  public function ajaxWait() {
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
  
  public function testSpecialChars() {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("headerSearchButton");

    $specialString = 'äöü"$%&/=?`+*~#_-.:,;|';
    $this->doUpload($specialString, 1);
    $this->doUpload("", 2);

    for($i=0; $i<mb_strlen($specialString, 'UTF-8'); $i++) {
      $char = mb_substr($specialString, $i, 1,'UTF-8');
                
      $this->selenium->type("searchQuery", $char);
      $this->selenium->click("xpath=//input[@class='webHeadSearchSubmit']");    
      $this->ajaxWait();
      $this->assertEquals("unitTest1", $this->selenium->getText("xpath=//a[@class='projectListDetailsLinkBold']"));
      $this->assertFalse($this->selenium->isTextPresent("unitTest2"));
    }

    $this->selenium->type("searchQuery", "unitTest1");
    $this->selenium->click("xpath=//input[@class='webHeadSearchSubmit']");    
    $this->ajaxWait();
    $this->assertEquals("unitTest1", $this->selenium->getText("xpath=//a[@class='projectListDetailsLinkBold']"));
    $this->assertFalse($this->selenium->isTextPresent("unitTest2"));
  }
  
  public function testPageNavigation() {
    $noSearchResultKeywords = $this->randomLongStrings();
    $this->doUpload($noSearchResultKeywords[2][0], PROJECT_PAGE_LOAD_MAX_PROJECTS*(PROJECT_PAGE_SHOW_MAX_PAGES+1));
    
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
  
    //test page title
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    $this->assertFalse($this->selenium->isVisible("fewerProjects"));
    $this->assertTrue($this->selenium->isVisible("moreProjects"));
    $this->assertFalse($this->selenium->isVisible("headerSearchBox"));
    $this->selenium->click("headerSearchButton");
    
    $this->assertTrue($this->selenium->isVisible("headerSearchBox"));
    
    $this->selenium->type("searchQuery", $noSearchResultKeywords[0][0]);
    $this->selenium->click("xpath=//input[@class='webHeadSearchSubmit']");    
    $this->ajaxWait();    
    
    $this->assertFalse($this->selenium->isVisible("fewerProjects"));
    $this->assertFalse($this->selenium->isVisible("moreProjects"));
    $this->assertTrue($this->selenium->isTextPresent("Your search returned no results"));
    
    $this->selenium->click("headerCancelSearchButton");    
    $this->assertTrue($this->selenium->isTextPresent("Newest Projects"));
    $this->selenium->click("headerSearchButton");
    
    $this->selenium->type("searchQuery", $noSearchResultKeywords[2][0]);
    $this->selenium->click("xpath=//input[@class='webHeadSearchSubmit']");
    $this->ajaxWait();
    $this->assertFalse($this->selenium->isTextPresent("Your search returned no results"));
    
    for($i=0; $i<PROJECT_PAGE_SHOW_MAX_PAGES; $i++) {
      $this->selenium->click("moreProjects");
      $this->ajaxWait();
      $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".$noSearchResultKeywords[2][0]." - ".($i+2)."/", $this->selenium->getTitle());
    }
    
    $this->assertTrue($this->selenium->isVisible("fewerProjects"));
    $this->assertTrue($this->selenium->isTextPresent($this->labels['prevButton']));
    $this->selenium->click("fewerProjects");    
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".$noSearchResultKeywords[2][0]." - ".($i)."/", $this->selenium->getTitle());
    
    // test session
    $this->selenium->refresh();
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".$noSearchResultKeywords[2][0]." - ".($i)."/", $this->selenium->getTitle());
    
    //test links to details page
    $this->selenium->click("xpath=//a[@class='projectListDetailsLink'][1]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - ".$this->labels['title']." - ".$noSearchResultKeywords[2][0]." - ".($i)."/", $this->selenium->getTitle());

     // test header click
    $this->selenium->click("aIndexWebLogoLeft");
    $this->ajaxWait();
    $this->assertRegExp("/".$this->labels['websitetitle']." - Newest Projects - 1/", $this->selenium->getTitle());
  }
  
  public function testSearchForHiddenProject() {
    $searchString = $this->randomLongStrings();
    $this->doUpload($searchString[0][0], 1);
    
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    
    $this->selenium->click("headerSearchButton");
    $this->selenium->type("searchQuery", $searchString[0][0]);
    $this->selenium->click("xpath=//input[@class='webHeadSearchSubmit']");
    $this->ajaxWait();
    $this->assertEquals("unitTest1", $this->selenium->getText("xpath=//a[@class='projectListDetailsLinkBold']"));
    
    $this->selenium->click("xpath=//a[@class='projectListDetailsLink'][1]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    
    $this->selenium->click("reportAsInappropriateButton");
    $this->selenium->type("reportInappropriateReason", "need to hide this project");
    $this->selenium->click("reportInappropriateReportButton");
    
    $this->selenium->click("aIndexWebLogoLeft");
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    
    $this->selenium->type("searchQuery", $searchString[0][0]);
    $this->selenium->click("xpath=//input[@class='webHeadSearchSubmit']");
    $this->ajaxWait();
    $this->assertTrue($this->selenium->isTextPresent("Your search returned no results"));
  }

  public function doUpload($description,$projectcount) {
     for($i=1; $i<= $projectcount; $i++)
     {
       $fileName = 'test.zip';       
       $testFile = dirname(__FILE__).'/testdata/'.$fileName;
       $fileChecksum = md5_file($testFile);
       $fileSize = filesize($testFile);
       $fileType = 'application/x-zip-compressed';      
       
       $formData = array('projectTitle'=>'unitTest'.$i, 'projectDescription'=>$description, 'fileChecksum'=>$fileChecksum);
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
  
  public function deleteUploadedProjects()  {
     foreach ($this->insertIDArray as $insertId)
     {
       $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENTION;
       // test deleting from database
       $this->upload->removeProjectFromFilesystem($filePath);    
       $this->assertFalse(is_file($filePath));
       @unlink(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$insertId.PROJECTS_QR_EXTENTION);
       $this->assertFalse(is_file(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$insertId.PROJECTS_QR_EXTENTION));
       //test deleting from filesystem
       $this->upload->removeProjectFromDatabase($insertId);
       $query = "SELECT * FROM projects WHERE id='$insertId'";
       $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
       $this->assertEquals(0, pg_num_rows($result));
    }
  }
   
  public function randomLongStrings() {
    $returnArray = array();
    $strLen = 200;
    $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

    for($i=0;$i<5;$i++) {
      $str = '';
      for($j=0;$j<$strLen;$j++) {
        $str .= $chars[rand(0, count($chars)-1)];
      }
      $returnArray[$i] = array($str);
    }
    return $returnArray;
  }
}
?>

