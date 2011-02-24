<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* Set TESTS_BASE_PATH in testsBootstrap.php to your catroid www-root */
require_once 'testsBootstrap.php';

class LoginTests extends PHPUnit_Framework_TestCase
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
  
  /**
   * @dataProvider loginData
   */
  public function testLogin($user, $pass, $wrongUser, $wrongPass)
  {
    $this->selenium->open(TESTS_BASE_PATH.'catroid/login/');
    $this->selenium->waitForPageToLoad(10000);
    $this->assertEquals('Login', $this->selenium->getText("xpath=//div[@class='webMainContentTitle']"));
    
    //wiki username creation
    $wikiUsername = ucfirst(strtolower($user));
    
    //check if we are not logged in to board & wiki
    $this->assertFalse($this->selenium->isTextPresent("Hello $user!"));
    $this->assertFalse($this->selenium->isTextPresent("You are logged in"));
    
    $this->selenium->click("aBoardLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);   
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertFalse($this->selenium->isTextPresent("Logout"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("aWikiLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);   
    $this->assertFalse($this->selenium->isTextPresent($wikiUsername));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    // test login
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='loginUsername']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='loginPassword']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='loginSubmit']"));
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@name='logoutSubmit']"));
    
    $this->selenium->type("xpath=//input[@name='loginUsername']", $user);
    $this->selenium->type("xpath=//input[@name='loginPassword']", $pass);
    
    $this->selenium->click("xpath=//input[@name='loginSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("CATROID Login successfull!"));
    $this->assertTrue($this->selenium->isTextPresent("BOARD Login successfull!"));
    $this->assertTrue($this->selenium->isTextPresent("WIKI Login successfull!"));
    
    $this->assertTrue($this->selenium->isTextPresent("Hello $user!"));
    $this->assertTrue($this->selenium->isTextPresent("You are logged in"));
    
    $this->selenium->click("aBoardLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);   
    $this->assertFalse($this->selenium->isTextPresent("Login"));
    $this->assertTrue($this->selenium->isTextPresent("Logout"));
    $this->assertTrue($this->selenium->isTextPresent($user));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("aWikiLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);   
    $this->assertTrue($this->selenium->isTextPresent($wikiUsername));
    $this->selenium->click("xpath=//li[@id='pt-preferences']/a");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertEquals('Preferences', $this->selenium->getText("firstHeading"));
    $this->assertFalse($this->selenium->isTextPresent("Not logged in"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    // test logout
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@name='loginUsername']"));
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@name='loginPassword']"));
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@name='loginSubmit']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='logoutSubmit']"));
    
    $this->selenium->click("xpath=//input[@name='logoutSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("CATROID Logout successfull!"));
    $this->assertTrue($this->selenium->isTextPresent("BOARD Logout successfull!"));
    $this->assertTrue($this->selenium->isTextPresent("WIKI Logout successfull!"));
    
    $this->assertFalse($this->selenium->isTextPresent("Hello $user!"));
    $this->assertFalse($this->selenium->isTextPresent("You are logged in"));
    
    $this->selenium->click("aBoardLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertFalse($this->selenium->isTextPresent("Logout"));   
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("aWikiLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);   
    $this->assertFalse($this->selenium->isTextPresent($wikiUsername));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    // test login with wrong user/pass
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='loginUsername']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='loginPassword']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='loginSubmit']"));
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@name='logoutSubmit']"));
    
    $this->selenium->type("xpath=//input[@name='loginUsername']", $wrongUser);
    $this->selenium->type("xpath=//input[@name='loginPassword']", $wrongPass);
    
    $this->selenium->click("xpath=//input[@name='loginSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The catroid authentication failed."));
    
    $this->assertFalse($this->selenium->isTextPresent("Hello $user!"));
    $this->assertFalse($this->selenium->isTextPresent("You are logged in"));
    
    $this->selenium->click("aBoardLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);   
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertFalse($this->selenium->isTextPresent("Logout"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("aWikiLink");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent($wikiUsername));   
    $this->selenium->close();
    $this->selenium->selectWindow(null);
  }

  /* *** DATA PROVIDERS *** */
  public function loginData() {
    $returnArray = array(
      array('catroweb', 'cat.roid.web', 'wrongUser', 'wrongPassword')
    );
    return $returnArray;
  }

}
?>

