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
  
  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->selenium = new Testing_Selenium("*firefox", $path);
    
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

  /**
  * @dataProvider registrationData
  */
  public function testMenuButtons($regData)
  {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    
    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);
    
    $this->assertTrue($this->selenium->isVisible("menuProfileButton"));
    $this->assertTrue($this->selenium->isVisible("menuForumButton"));
    $this->assertTrue($this->selenium->isVisible("menuWikiButton"));
    
    $this->assertTrue($this->selenium->isVisible("menuWallButton"));
    $this->assertTrue($this->selenium->isVisible("menuLoginButton"));
    $this->assertTrue($this->selenium->isVisible("menuSettingsButton"));
    
    
    $this->assertFalse($this->selenium->isEditable("menuProfileButton"));
    $this->assertFalse($this->selenium->isEditable("menuWallButton"));
    $this->assertFalse($this->selenium->isEditable("menuSettingsButton"));
    
    $this->selenium->click("menuForumButton");        
    $this->selenium->selectWindow("board");   
    $this->selenium->waitForPageToLoad(10000); 
    
    $this->assertRegExp("/addons\/board/", $this->selenium->getLocation());
    $this->assertTrue($this->selenium->isTextPresent(("Board index")));
    $this->assertTrue($this->selenium->isTextPresent(("Login")));
    
    $this->selenium->close();
    $this->selenium->selectWindow(null);    
    
    $this->selenium->click("menuWikiButton");
    $this->selenium->waitForPageToLoad(1000);
    $this->selenium->selectWindow("wiki");    
        
    $this->assertRegExp("/wiki\/Main_Page/", $this->selenium->getLocation());
    $this->assertTrue($this->selenium->isTextPresent(("Main Page")));
    $this->assertFalse($this->selenium->isTextPresent( $regData['registrationUsername']));    
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("menuLoginButton");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/login/", $this->selenium->getLocation());
    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);
    
    $this->selenium->click("menuLoginButton");
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->type("xpath=//input[@name='loginUsername']", $regData['registrationUsername']);
    $this->selenium->type("xpath=//input[@name='loginPassword']", $regData['registrationPassword']);
    
    $this->selenium->click("xpath=//input[@name='loginSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isVisible("menuLogoutButton"));    
    $this->assertFalse($this->selenium->isVisible("menuLoginButton"));      
    
    
    $this->selenium->click("menuForumButton");
    $this->selenium->selectWindow("board");    
    $this->selenium->waitForPageToLoad(10000);    
    $this->assertRegExp("/addons\/board/", $this->selenium->getLocation());
    $this->assertTrue($this->selenium->isTextPresent(("Board index")));
    $this->assertTrue($this->selenium->isTextPresent(($regData['registrationUsername'])));    
    $this->selenium->close();
    $this->selenium->selectWindow(null);            
    
    $this->selenium->click("menuWikiButton");
    $this->selenium->selectWindow("wiki");    
    $this->selenium->waitForPageToLoad(10000);    
    $this->assertRegExp("/wiki\/Main_Page/", $this->selenium->getLocation());
    $this->assertTrue($this->selenium->isTextPresent(("Main Page")));
    $this->assertTrue($this->selenium->isElementPresent("pt-userpage"));    
    $this->selenium->close();
    $this->selenium->selectWindow(null);
 
    $this->assertFalse($this->selenium->isEditable("menuProfileButton"));
    $this->assertFalse($this->selenium->isEditable("menuWallButton"));
    $this->assertFalse($this->selenium->isEditable("menuSettingsButton"));    
     
    $this->selenium->click("menuLogoutButton");
    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/index/", $this->selenium->getLocation());
    
    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000); 
    
    $this->assertTrue($this->selenium->isVisible("menuLoginButton"));    
    $this->assertFalse($this->selenium->isVisible("menuLogoutButton")); 
  }
  
    /* *** DATA PROVIDERS *** */
  public function registrationData() {
    $random = rand(100, 999999);
    $dataArray = array(
    array(
    array('registrationUsername'=>'catroweb', 'registrationPassword'=>'cat.roid.web'))
    );
    return $dataArray;
  }
  
}
?>

