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

class MenuTests extends PHPUnit_Framework_TestCase
{
  private $selenium;
  protected $upload;
  protected $insertIDArray = array();
  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->selenium = new Testing_Selenium("*firefox", $path);
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
  

  
  public function testHeaderButtonsIndex()
  {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    
     //test page title
     
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    $this->assertFalse($this->selenium->isVisible("headerSearchBox"));
    $this->assertFalse($this->selenium->isVisible("headerCancelSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerMenuButton"));      
    
    
    $this->selenium->click("headerSearchButton");
    $this->selenium->waitForPageToLoad(100);
    $this->assertTrue($this->selenium->isVisible("headerSearchBox"));
    $this->assertTrue($this->selenium->isVisible("headerCancelSearchButton"));
    $this->assertFalse($this->selenium->isVisible("headerSearchButton"));
    $this->assertFalse($this->selenium->isVisible("headerMenuButton"));
    
    $this->selenium->click("headerCancelSearchButton");
    $this->selenium->waitForPageToLoad(100);
    $this->assertFalse($this->selenium->isVisible("headerSearchBox"));
    $this->assertFalse($this->selenium->isVisible("headerCancelSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerMenuButton"));
    
    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/menu/", $this->selenium->getLocation());    
  }
  
  public function testHeaderButtons()
  {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("xpath=//a[@class='license'][4]");    
    $this->selenium->waitForPageToLoad(10000);
    
     //test page title
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    $this->assertFalse($this->selenium->isVisible("headerSearchBox"));
    $this->assertFalse($this->selenium->isVisible("headerCancelSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerMenuButton"));
    
    $this->selenium->click("headerSearchButton");
    $this->selenium->waitForPageToLoad(100);
    $this->assertTrue($this->selenium->isVisible("headerSearchBox"));
    $this->assertTrue($this->selenium->isVisible("headerCancelSearchButton"));
    $this->assertFalse($this->selenium->isVisible("headerSearchButton"));
    $this->assertFalse($this->selenium->isVisible("headerMenuButton"));
    
    $this->selenium->click("headerCancelSearchButton");
    $this->selenium->waitForPageToLoad(100);
    $this->assertFalse($this->selenium->isVisible("headerSearchBox"));
    $this->assertFalse($this->selenium->isVisible("headerCancelSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerSearchButton"));
    $this->assertTrue($this->selenium->isVisible("headerMenuButton"));
    
    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/menu/", $this->selenium->getLocation());
    
  }
}
?>

